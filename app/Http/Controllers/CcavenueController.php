<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Services\CcavenueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CcavenueController extends Controller
{
    protected CcavenueService $ccavenue;

    public function __construct(CcavenueService $ccavenue)
    {
        $this->ccavenue = $ccavenue;
    }

    /**
     * Initiate payment for an Order and auto-redirect to CCAvenue Gateway.
     */
    public function pay(string $orderNo)
    {
        $order = Order::where('order_no', $orderNo)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        // If order is already paid, redirect directly to received page
        if ($order->payment_status === 'Paid') {
            return redirect()->route('received', ['order' => $order->order_no])
                ->with('status', 'This order has already been paid and confirmed.');
        }

        if (!$this->ccavenue->isConfigured()) {
            // If CCAvenue credentials not yet configured, show informative notice
            return view('pages.payment-failed', [
                'order' => $order,
                'errorTitle' => 'Gateway Setup in Progress',
                'errorMessage' => 'CCAvenue payment credentials are not yet configured in the Atelier settings or .env file. Please configure Merchant ID, Access Code, and Working Key.',
            ]);
        }

        try {
            $payload = $this->ccavenue->getPaymentPayload($order);
            return view('pages.ccavenue-redirect', $payload);
        } catch (\Throwable $e) {
            Log::error('CCAvenue Initiation Error: ' . $e->getMessage(), ['order_no' => $order->order_no]);
            return view('pages.payment-failed', [
                'order' => $order,
                'errorTitle' => 'Payment Initiation Error',
                'errorMessage' => 'Unable to initiate secure CCAvenue payment session: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Local Mock Gateway Simulator screen.
     */
    public function mockGateway(Request $request)
    {
        $encRequest = $request->input('encRequest');
        $accessCode = $request->input('access_code');

        $params = [];
        if ($encRequest) {
            $decrypted = $this->ccavenue->decrypt($encRequest);
            parse_str($decrypted, $params);
        }

        $orderNo = $params['order_id'] ?? $request->input('order_id', 'ORD-TEST');
        $amount  = $params['amount'] ?? $request->input('amount', '0.00');
        $order   = Order::with('customer', 'items')->where('order_no', $orderNo)->first();

        return view('pages.ccavenue-mock', [
            'params'     => $params,
            'order'      => $order,
            'orderNo'    => $orderNo,
            'amount'     => $amount,
            'accessCode' => $accessCode,
        ]);
    }

    /**
     * Process Mock Gateway simulation and build encrypted CCAvenue callback.
     */
    public function mockProcess(Request $request)
    {
        $action = $request->input('action', 'success'); // success, failure, cancel
        $orderNo = $request->input('order_id');
        $amount = (float) $request->input('amount', 0);
        $paymentMode = $request->input('payment_mode', 'Credit Card');
        $order = Order::where('order_no', $orderNo)->first();

        if ($action === 'cancel') {
            $encResp = $this->ccavenue->encrypt('order_id=' . urlencode($orderNo) . '&order_status=Aborted&status_message=Transaction+cancelled+by+user');
            return view('pages.ccavenue-mock-callback', [
                'redirect_url' => route('ccavenue.cancel'),
                'encResp'      => $encResp,
            ]);
        }

        $trackingId = 'TEST_' . date('Ymd') . '_' . rand(100000, 999999);
        $bankRefNo  = 'BNK' . rand(10000000, 99999999);

        $respParams = [
            'order_id'         => $orderNo,
            'tracking_id'      => $trackingId,
            'bank_ref_no'      => $bankRefNo,
            'order_status'     => $action === 'success' ? 'Success' : 'Failure',
            'failure_message'  => $action === 'failure' ? 'Declined by test bank: Insufficient funds or invalid test OTP.' : '',
            'payment_mode'     => $paymentMode,
            'card_name'        => 'Test Visa / MasterCard',
            'status_code'      => $action === 'success' ? '200' : '400',
            'status_message'   => $action === 'success' ? 'Transaction Successful' : 'Transaction Declined',
            'currency'         => 'INR',
            'amount'           => number_format($amount > 0 ? $amount : ($order ? $order->total_inr : 0), 2, '.', ''),
            'merchant_param1'  => $order ? (string) $order->id : '',
            'merchant_param2'  => $order ? (string) ($order->customer_id ?? '') : '',
            'merchant_param3'  => 'web_order',
            'merchant_param4'  => $order ? ($order->access_token ?? '') : '',
        ];

        $respData = '';
        foreach ($respParams as $k => $v) {
            $respData .= $k . '=' . urlencode((string) $v) . '&';
        }
        $respData = rtrim($respData, '&');
        $encResp  = $this->ccavenue->encrypt($respData);

        return view('pages.ccavenue-mock-callback', [
            'redirect_url' => route('ccavenue.response'),
            'encResp'      => $encResp,
        ]);
    }

    /**
     * Handle CCAvenue POST Response Callback (Redirect URL).
     */
    public function response(Request $request)
    {
        $encResp = $request->input('encResp');

        if (empty($encResp)) {
            return redirect()->route('home')->with('error', 'Invalid or empty payment response received.');
        }

        $data = $this->ccavenue->parseResponse($encResp);

        Log::info('CCAvenue Response Decrypted: ', $data);

        $orderNo     = $data['order_id'] ?? null;
        $orderStatus = strtolower($data['order_status'] ?? '');
        $trackingId  = $data['tracking_id'] ?? null;
        $bankRefNo   = $data['bank_ref_no'] ?? null;
        $paymentMode = $data['payment_mode'] ?? 'CCAvenue';
        $failureMsg  = $data['failure_message'] ?? ($data['status_message'] ?? 'Transaction was not successful.');
        $amount      = (float) ($data['amount'] ?? 0);
        $currency    = $data['currency'] ?? 'INR';

        // Locate order
        $order = null;
        if ($orderNo) {
            $order = Order::where('order_no', $orderNo)->first();
        }
        if (!$order && !empty($data['merchant_param1'])) {
            $order = Order::find($data['merchant_param1']);
        }

        if (!$order) {
            return redirect()->route('home')->with('error', 'Associated order not found for this transaction.');
        }

        // Check response status
        if ($orderStatus === 'success') {
            // Successful payment
            $order->update([
                'payment_status' => 'Paid',
                'status'         => 'Received',
                'gateway_code'   => 'ccavenue',
                'total_charged'  => (int) round($amount > 0 ? $amount : $order->total_inr),
            ]);

            // Record payment transaction
            Payment::create([
                'order_id'           => $order->id,
                'gateway_code'       => 'ccavenue',
                'gateway_ref'        => $trackingId,
                'gateway_payment_id' => $bankRefNo ?: $trackingId,
                'amount'             => (int) round($amount > 0 ? $amount : $order->total_inr),
                'currency'           => $currency,
                'status'             => 'captured',
                'verified'           => true,
                'raw_json'           => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);

            // Add Order History record
            OrderStatusHistory::create([
                'order_id'          => $order->id,
                'status'            => 'Received',
                'note'              => "Payment of ₹" . number_format($order->total_inr) . " successfully received via CCAvenue (Tracking ID: {$trackingId}, Mode: {$paymentMode}).",
                'customer_notified' => true,
            ]);

            // Ensure patron is logged in for smooth order viewing
            if (!Auth::check() && $order->customer) {
                Auth::login($order->customer, true);
            }

            return redirect()->route('received', [
                'order'   => $order->order_no,
                'payment' => 'success',
                'txn'     => $trackingId,
            ]);
        } else {
            // Failed / Aborted payment
            $order->update([
                'payment_status' => 'Payment Failed',
            ]);

            Payment::create([
                'order_id'           => $order->id,
                'gateway_code'       => 'ccavenue',
                'gateway_ref'        => $trackingId,
                'gateway_payment_id' => $bankRefNo ?: $trackingId,
                'amount'             => (int) round($amount > 0 ? $amount : $order->total_inr),
                'currency'           => $currency,
                'status'             => $orderStatus ?: 'failed',
                'verified'           => false,
                'raw_json'           => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);

            OrderStatusHistory::create([
                'order_id'          => $order->id,
                'status'            => $order->status,
                'note'              => "CCAvenue payment attempt unsuccessful. Status: " . ucfirst($orderStatus) . ". Reason: " . $failureMsg,
                'customer_notified' => false,
            ]);

            return view('pages.payment-failed', [
                'order'        => $order,
                'errorTitle'   => 'Payment Incomplete',
                'errorMessage' => $failureMsg ?: 'The payment transaction could not be completed or was declined by the bank.',
                'trackingId'   => $trackingId,
            ]);
        }
    }

    /**
     * Handle CCAvenue POST Cancel Callback.
     */
    public function cancel(Request $request)
    {
        $encResp = $request->input('encResp');
        $data = $encResp ? $this->ccavenue->parseResponse($encResp) : [];

        $orderNo = $data['order_id'] ?? null;
        $order = $orderNo ? Order::where('order_no', $orderNo)->first() : null;

        if ($order) {
            $order->update(['payment_status' => 'Cancelled']);

            OrderStatusHistory::create([
                'order_id'          => $order->id,
                'status'            => $order->status,
                'note'              => 'Customer cancelled the CCAvenue payment session.',
                'customer_notified' => false,
            ]);
        }

        return view('pages.payment-failed', [
            'order'        => $order,
            'errorTitle'   => 'Payment Cancelled',
            'errorMessage' => 'You cancelled the payment transaction. Your bag items are safe and you can retry whenever you are ready.',
        ]);
    }
}
