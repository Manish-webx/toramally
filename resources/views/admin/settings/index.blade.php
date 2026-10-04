@extends('admin.layout', ['title' => 'Atelier Settings', 'header' => 'Atelier & Store Configuration'])

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf

    <div class="row g-4">
        <!-- Contact & Atelier Store Info -->
        <div class="col-12 col-lg-6">
            <div class="card-custom">
                <div class="card-title">Atelier Contact &amp; Channels</div>

                <div class="mb-3">
                    <label class="form-label-custom">WhatsApp Business Number</label>
                    <input type="text" name="whatsapp" class="form-control-custom" value="{{ $settings['whatsapp'] ?? '' }}" placeholder="+91 98765 43210">
                    <div style="font-size:11.5px;color:var(--muted);margin-top:3px">Used across direct inquiry buttons and client communication.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Direct Phone</label>
                    <input type="text" name="phone" class="form-control-custom" value="{{ $settings['phone'] ?? '' }}" placeholder="+91 33 2475 0000">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Public Contact Email</label>
                    <input type="email" name="email_public" class="form-control-custom" value="{{ $settings['email_public'] ?? '' }}" placeholder="concierge@toramally.com">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Orders Alert Email</label>
                    <input type="email" name="orders_alert_email" class="form-control-custom" value="{{ $settings['orders_alert_email'] ?? '' }}" placeholder="orders@toramally.com">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Kolkata Flagship Store Address</label>
                    <textarea name="address_store" rows="3" class="form-control-custom">{{ $settings['address_store'] ?? '' }}</textarea>
                </div>
            </div>

            <div class="card-custom">
                <div class="card-title">Social &amp; Maps</div>

                <div class="mb-3">
                    <label class="form-label-custom">Instagram Profile URL</label>
                    <input type="url" name="instagram" class="form-control-custom" value="{{ $settings['instagram'] ?? '' }}">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Facebook Page URL</label>
                    <input type="url" name="facebook" class="form-control-custom" value="{{ $settings['facebook'] ?? '' }}">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Google Maps Link</label>
                    <input type="url" name="maps_url" class="form-control-custom" value="{{ $settings['maps_url'] ?? '' }}">
                </div>
            </div>
        </div>

        <!-- Business, GST & Currency Rules -->
        <div class="col-12 col-lg-6">
            <div class="card-custom">
                <div class="card-title">Business &amp; Invoicing Details</div>

                <div class="mb-3">
                    <label class="form-label-custom">Registered Business Name</label>
                    <input type="text" name="business_name" class="form-control-custom" value="{{ $settings['business_name'] ?? 'House of Tōramally' }}">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Workshop Address (Lucknow)</label>
                    <textarea name="business_address" rows="2" class="form-control-custom">{{ $settings['business_address'] ?? '' }}</textarea>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label-custom">GSTIN</label>
                        <input type="text" name="gstin" class="form-control-custom" value="{{ $settings['gstin'] ?? '' }}" placeholder="09AAAAA0000A1Z5">
                    </div>
                    <div class="col-6">
                        <label class="form-label-custom">State &amp; State Code</label>
                        <input type="text" name="business_state" class="form-control-custom" value="{{ $settings['business_state'] ?? 'Uttar Pradesh' }}">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label-custom">Order Prefix</label>
                        <input type="text" name="order_prefix" class="form-control-custom" value="{{ $settings['order_prefix'] ?? 'TM-' }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label-custom">Invoice Prefix</label>
                        <input type="text" name="invoice_prefix" class="form-control-custom" value="{{ $settings['invoice_prefix'] ?? 'TM' }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Commission Threshold (INR)</label>
                    <input type="number" name="commission_threshold" class="form-control-custom" value="{{ $settings['commission_threshold'] ?? 85000 }}">
                    <div style="font-size:11.5px;color:var(--muted);margin-top:3px">Bespoke designs above this estimated amount automatically require house consultation.</div>
                </div>
            </div>

            <div class="card-custom">
                <div class="card-title">Analytics &amp; SEO</div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label-custom">Google Analytics ID</label>
                        <input type="text" name="ga_id" class="form-control-custom" value="{{ $settings['ga_id'] ?? '' }}" placeholder="G-XXXXXXXXXX">
                    </div>
                    <div class="col-6">
                        <label class="form-label-custom">Meta Pixel ID</label>
                        <input type="text" name="meta_pixel_id" class="form-control-custom" value="{{ $settings['meta_pixel_id'] ?? '' }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">SEO Default Meta Title</label>
                    <input type="text" name="seo_home_title" class="form-control-custom" value="{{ $settings['seo_home_title'] ?? '' }}">
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">SEO Default Description</label>
                    <textarea name="seo_home_desc" rows="2" class="form-control-custom">{{ $settings['seo_home_desc'] ?? '' }}</textarea>
                </div>

                <button type="submit" class="btn-atelier btn-atelier-primary full" style="width:100%;justify-content:center;padding:12px">
                    Save Atelier Settings
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
