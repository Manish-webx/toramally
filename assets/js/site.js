/* Tōramally — site behaviour.
 * Pages are built by PHP; this file adds the interactive parts:
 * menus and drawers, search, currency switch, bag, product page, filters,
 * the bespoke builder, the wedding date check and form sending.
 * Settings come from window.TM, printed by templates/layout/layout.php.
 */
(function(){
"use strict";
const TM = window.TM || {};
const D = window.TMDraw;
const $ = (s,r=document)=>r.querySelector(s);
const $$ = (s,r=document)=>[...r.querySelectorAll(s)];
const esc = s=>String(s??"").replace(/[&<>"']/g,c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));
const store = {
  get(k,d){try{const v=localStorage.getItem("tm_"+k);return v?JSON.parse(v):d}catch(e){return d}},
  set(k,v){try{localStorage.setItem("tm_"+k,JSON.stringify(v))}catch(e){}}
};
const url = p => (TM.base||"") + "/" + String(p).replace(/^\//,"");
const WA_ICON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M4 20l1.2-3.9A8 8 0 1 1 8 19.1L4 20z"/><path d="M9 9.5c.3 2 2.3 4.2 4.6 4.8l1.1-1.1 1.8.8-.4 1.6c-3.6.3-7.8-3.4-8-7.2l1.6-.4.8 1.8L9.4 9"/></svg>';
const waLink = t => "https://wa.me/"+(TM.whatsapp||"")+"?text="+encodeURIComponent(t);
function toast(msg){const t=$("#toast");if(!t)return;t.textContent=msg;t.classList.add("on");clearTimeout(t._h);t._h=setTimeout(()=>t.classList.remove("on"),2800)}
function track(ev,data){ if(store.get("consent")!=="accept")return; (window.dataLayer=window.dataLayer||[]).push({event:ev,...data}); if(window.gtag)gtag("event",ev,data||{}); }

/* ---------- Links from the old one-page preview (#/shop/men …) ---------- */
if(location.hash.startsWith("#/")){
  let h=location.hash.slice(2);
  const [p,qs]=h.split("?"); const q=new URLSearchParams(qs||""); const s=q.get("s");
  let to=p;
  if(p.startsWith("p/")) to="products/"+p.slice(2);
  else if(p==="house"&&s) to="house/"+s;
  else if(p==="help") to = s==="faq"?"faq":(s||"shipping");
  else if(p==="legal") to = s||"privacy";
  else if(qs) to=p+"?"+qs;
  location.replace(url(to));
}

/* ---------- Money and currency ----------
 * Prices are printed as <span class="money" data-inr="23000">. Switching
 * currency rewrites them from the fixed table, without reloading. */
let CUR = TM.cur || "INR";
function convert(inr,cur){cur=cur||CUR; if(cur==="INR")return Math.round(inr); const r=TM.currencies[cur]||1, st=TM.rounding[cur]||1; return Math.round(inr/r/st)*st;}
function money(inr){return CUR+" "+convert(inr)}
function moneyHTML(inr){return `<span class="money" data-inr="${inr}">${money(inr)}</span>`}
function refreshMoney(root){$$(".money[data-inr]",root||document).forEach(e=>e.textContent=money(+e.dataset.inr))}
$$("[data-cur]").forEach(s=>{ s.value=CUR; s.addEventListener("change",()=>{
  CUR=s.value; document.cookie="tm_cur="+CUR+";path=/;max-age=31536000;samesite=lax";
  $$("[data-cur]").forEach(x=>x.value=CUR); refreshMoney(); renderBag(); builderRedraw&&builderRedraw();
  $$("[data-intl-note]").forEach(n=>n.hidden=CUR==="INR"); track("currency_change",{currency:CUR}); toast("Prices now shown in "+CUR+".");
});});

/* ---------- Drawers, sheets, search panel ---------- */
let lastFocus=null;
function openLayer(id){
  const el=document.getElementById(id); if(!el)return;
  $$(".drawer.on,.sheet.on").forEach(d=>{if(d!==el){d.classList.remove("on");d.setAttribute("aria-hidden","true")}});
  if(!el.classList.contains("on"))lastFocus=document.activeElement;
  el.classList.add("on"); el.setAttribute("aria-hidden","false"); $("#scrim").classList.add("on");
  $$(`[aria-controls="${id}"]`).forEach(b=>b.setAttribute("aria-expanded","true"));
  if(id==="bagDrawer")renderBag();
  setTimeout(()=>{const f=el.querySelector("button,a,input,select,textarea");f&&f.focus()},60);
}
function closeLayers(){
  $$(".drawer.on,.sheet.on").forEach(d=>{d.classList.remove("on");d.setAttribute("aria-hidden","true")});
  $("#scrim").classList.remove("on"); $("#searchPanel").classList.remove("on");
  $$("[aria-expanded]").forEach(b=>b.setAttribute("aria-expanded","false"));
  if(lastFocus&&document.contains(lastFocus))lastFocus.focus(); lastFocus=null;
}
document.addEventListener("click",e=>{
  const t=e.target.closest("[data-open],[data-close],[data-open-search],[data-close-search],[data-sharepage],[data-resetcookie],[data-track]");
  if(!t)return;
  if(t.matches("[data-open]")){e.preventDefault();openLayer(t.dataset.open)}
  else if(t.matches("[data-close]")){e.preventDefault();closeLayers()}
  else if(t.matches("[data-open-search]")){e.preventDefault();lastFocus=t;$("#searchPanel").classList.add("on");$("#scrim").classList.add("on");setTimeout(()=>$("#q").focus(),80)}
  else if(t.matches("[data-close-search]")){closeLayers()}
  else if(t.matches("[data-sharepage]")){ if(navigator.share)navigator.share({title:document.title,url:location.href}).catch(()=>{}); else navigator.clipboard?.writeText(location.href).then(()=>toast("Link copied.")) }
  else if(t.matches("[data-resetcookie]")){store.set("consent",null);$("#cookie").classList.add("on")}
  if(t.dataset.track)track(t.dataset.track,{craft:t.dataset.craft,ctx:t.dataset.ctx});
});
$("#scrim")?.addEventListener("click",closeLayers);
document.addEventListener("keydown",e=>{if(e.key==="Escape")closeLayers()});
window.addEventListener("scroll",()=>$("#hdr")?.classList.toggle("shrink",window.scrollY>40),{passive:true});

/* ---------- Search (server: /api/search) ---------- */
let sTimer=null;
function doSearch(term){
  const out=$("[data-sres]"); if(!term.trim()){out.innerHTML="";return}
  clearTimeout(sTimer); sTimer=setTimeout(async()=>{
    try{
      const r=await fetch(url("api/search?q="+encodeURIComponent(term))); const j=await r.json();
      const groups=Object.entries(j.results||{}).filter(([,l])=>l.length);
      if(!groups.length){ track("search_zero_results",{term}); out.innerHTML=`<p style="margin-top:16px">Nothing found for “${esc(term)}”. Try Belgian, Miniature or Wedding.</p><p class="serif-i" style="font-size:22px">Not quite what you imagined? <a href="${url("bespoke/build")}">Commission it.</a></p>`; return;}
      track("search",{term});
      out.innerHTML=groups.map(([g,l])=>`<h4>${g}</h4>${l.map(h=>`<a href="${esc(h.h)}">${esc(h.t)}</a>`).join("")}`).join("");
    }catch(e){ out.innerHTML=`<p>Search is resting. <a href="${url("search?q="+encodeURIComponent(term))}">Try the full search page.</a></p>` }
  },180);
}
$("#q")?.addEventListener("input",e=>doSearch(e.target.value));
$("#q")?.addEventListener("keydown",e=>{if(e.key==="Enter"){e.preventDefault();const a=$("[data-sres] a");location.href=a?a.href:url("search?q="+encodeURIComponent(e.target.value))}});
$$("[data-s]").forEach(b=>b.onclick=()=>{$("#q").value=b.dataset.s;doSearch(b.dataset.s)});

/* ---------- Forms ----------
 * Any <form data-form="kind"> is sent to /api/form with its files.
 * Errors are shown under each field; success shows the calm "Received" note. */
async function postForm(endpoint, fd){
  fd.append("_token",TM.csrf);
  fd.append("_csrf",TM.csrf);
  const r=await fetch(url("api/"+endpoint),{method:"POST",body:fd,headers:{"Accept":"application/json","X-CSRF-TOKEN":TM.csrf}});
  let j={}; try{j=await r.json()}catch(e){j={ok:false,error:"Something went wrong. Please try again."}}
  return j;
}
function showFieldErrors(form,fields){
  $$(".field",form).forEach(f=>f.classList.remove("bad"));
  let first=null;
  (fields||[]).forEach(n=>{const i=form.querySelector(`[name="${n}"],[name="${n}[]"]`); if(i){i.closest(".field")?.classList.add("bad"); first=first||i;}});
  first&&first.focus();
}
function validate(form){
  let bad=[];
  $$("[name]",form).forEach(i=>{ if(i.type==="hidden"||i.classList.contains("hp-input"))return;
    const empty=i.required&&!String(i.value).trim(), badMail=i.type==="email"&&i.value&&!/^\S+@\S+\.\S+$/.test(i.value);
    i.closest(".field")?.classList.toggle("bad",empty||badMail); if(empty||badMail)bad.push(i);});
  if(bad.length){bad[0].focus();return false} return true;
}
$$("form[data-form]").forEach(f=>f.addEventListener("submit",async e=>{
  e.preventDefault(); if(!validate(f))return;
  const btn=$("button[type=submit]",f); if(btn.disabled)return; btn.disabled=true;
  const fd=new FormData(f); fd.set("kind",f.dataset.form);
  const j=await postForm("form",fd); btn.disabled=false;
  const msg=$("[data-formok]",f);
  if(j.ok){ f.reset(); $$("[data-upnames]",f).forEach(x=>x.textContent=""); msg.textContent=j.message; msg.hidden=false; msg.focus(); track(f.dataset.form==="appointment"?"appointment_submit":"form_submit",{kind:f.dataset.form}); }
  else { showFieldErrors(f,j.fields); toast(j.error||"Please check the form."); }
}));
/* Upload ref. Image fields: check type, size and count before sending. */
function bindUploads(root){
  $$("input[type=file][data-upload]",root||document).forEach(i=>i.addEventListener("change",()=>{
    const files=[...i.files], fld=i.closest(".field"), err=$("[data-uperr]",fld), names=$("[data-upnames]",fld);
    const bad=files.find(x=>!/\.(jpe?g|png|pdf|webp)$/i.test(x.name)||x.size>20*1024*1024);
    if(bad||files.length>5){fld.classList.add("bad");err.textContent=files.length>5?"Please choose up to five files.":"We could not upload that file. Please use a JPG, PNG or PDF under 20 MB.";i.value="";names.textContent="";return}
    fld.classList.remove("bad"); names.textContent=files.map(x=>x.name).join(", ");
  }));
}
bindUploads();

/* ---------- Newsletter ---------- */
$("[data-newsletter]")?.addEventListener("submit",async e=>{
  e.preventDefault(); const f=e.currentTarget, i=$("input[type=email]",f), m=$("[data-newsmsg]");
  if(!/^\S+@\S+\.\S+$/.test(i.value)){m.textContent="Please enter a valid email address.";i.focus();return}
  const fd=new FormData(f); const j=await postForm("newsletter",fd);
  m.textContent=j.ok?j.message:(j.error||"Please try again."); if(j.ok){i.value="";track("newsletter_signup",{})}
});

/* ---------- Bag & Checkout (Requires Customer Account) ---------- */
let BAG=store.get("bag",[]);
let bagAuthMode="register";

function bagSave(){store.set("bag",BAG);const n=BAG.reduce((a,b)=>a+b.qty,0);$$("[data-bagcount]").forEach(e=>{e.textContent=n;e.hidden=!n})}
function bagAdd(item){
  const same=BAG.find(b=>b.key===item.key&&b.colour===item.colour&&b.size===item.size&&!b.custom&&!item.custom);
  if(same)same.qty++; else BAG.push({...item,qty:1,id:Date.now()});
  bagSave(); openLayer("bagDrawer"); track("add_to_bag",{item:item.key,price:item.price});
}
function renderBag(){
  const body=$("[data-bagbody]"), foot=$("[data-bagfoot]"); if(!body)return;
  if(foot){ foot.innerHTML=""; foot.style.display="none"; }
  if(!BAG.length){ body.innerHTML=`<p class="serif-i" style="font-size:24px;margin-top:24px">Your bag is empty.</p><p>Begin with a silhouette.</p><div class="row"><a class="btn" href="${url("shop")}">Shop</a><a class="btn ghost" href="${url("craft")}">Craft</a></div>`; return; }
  const total=BAG.reduce((a,b)=>a+b.price*b.qty,0);

  let authHTML = "";
  if(TM.customer){
    const c = TM.customer;
    const addr = c.address;
    authHTML = `<div style="background:var(--ivory-100,#eee9de);padding:14px;border-radius:4px;margin:14px 0 16px;border:1px solid var(--line)">
      <div class="row" style="justify-content:space-between;align-items:center;margin-bottom:6px">
        <span class="label brass" style="font-size:11px">Signed In</span>
        <a href="${url('account')}" class="tlink" style="font-size:13px">My Account</a>
      </div>
      <div style="font-weight:500;font-size:15px">${esc(c.name)}</div>
      <div class="small muted">${esc(c.email)}${c.phone ? ' · ' + esc(c.phone) : ''}</div>
      ${addr ? `<div class="small muted" style="margin-top:6px;border-top:1px dashed var(--line);padding-top:6px"><strong>Delivery location:</strong> ${esc(addr.line1)}, ${esc(addr.city)} - ${esc(addr.postcode)}</div>` : ''}
    </div>
    <button class="btn full" data-checkout>Confirm &amp; Place Order Request</button>`;
  } else {
    authHTML = `<div style="background:var(--ivory-100,#eee9de);padding:14px;border-radius:4px;margin:14px 0 16px;border:1px solid var(--line)">
      <div style="font-family:var(--serif);font-size:18px;margin-bottom:4px">Account Required to Order</div>
      <p class="small muted" style="margin:0 0 12px">Please create an account with your contact and delivery location details to order handcrafted pairs.</p>
      
      <div class="row" style="gap:8px;margin-bottom:14px">
        <button type="button" class="btn small ${bagAuthMode==='register'?'':'ghost'}" data-authmode="register" style="flex:1;text-align:center">Create Account</button>
        <button type="button" class="btn small ${bagAuthMode==='login'?'':'ghost'}" data-authmode="login" style="flex:1;text-align:center">Sign In</button>
      </div>

      ${bagAuthMode === 'register' ? `
        <div style="display:flex;flex-direction:column;gap:10px">
          <div class="row" style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
            <label class="field"><span>First Name *</span><input data-bfname autocomplete="given-name" placeholder="First name"><div class="err">Required</div></label>
            <label class="field"><span>Last Name *</span><input data-blname autocomplete="family-name" placeholder="Last name"><div class="err">Required</div></label>
          </div>
          <label class="field"><span>Email Address *</span><input type="email" data-bemail autocomplete="email" placeholder="name@domain.com"><div class="err">Valid email required</div></label>
          <label class="field"><span>Phone / WhatsApp *</span><input type="tel" data-bphone autocomplete="tel" placeholder="+91 98765 43210"><div class="err">Phone number required</div></label>
          <label class="field"><span>Create Password (min 6 chars) *</span><input type="password" data-bpass autocomplete="new-password" placeholder="••••••••"><div class="err">Minimum 6 characters</div></label>
          <label class="field"><span>Delivery Address (Location / Street) *</span><input data-bline1 autocomplete="address-line1" placeholder="Flat, Street, Area"><div class="err">Delivery address required</div></label>
          <div class="row" style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
            <label class="field"><span>City *</span><input data-bcity autocomplete="address-level2" placeholder="City"><div class="err">City required</div></label>
            <label class="field"><span>State *</span><input data-bstate autocomplete="address-level1" placeholder="State"><div class="err">State required</div></label>
          </div>
          <div class="row" style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
            <label class="field"><span>Pincode *</span><input data-bpostcode autocomplete="postal-code" placeholder="Pincode"><div class="err">Pincode required</div></label>
            <label class="field"><span>Country *</span><input data-bcountry value="India"><div class="err">Country required</div></label>
          </div>
          <button class="btn full" data-checkout style="margin-top:6px">Create Account &amp; Order</button>
        </div>
      ` : `
        <div style="display:flex;flex-direction:column;gap:10px">
          <label class="field"><span>Email Address *</span><input type="email" data-lemail autocomplete="email" placeholder="name@domain.com"><div class="err">Email required</div></label>
          <label class="field"><span>Password *</span><input type="password" data-lpass autocomplete="current-password" placeholder="••••••••"><div class="err">Password required</div></label>
          <button class="btn full" data-login-checkout style="margin-top:6px">Sign In &amp; Order</button>
        </div>
      `}
    </div>`;
  }

  body.innerHTML=BAG.map(b=>`<div style="display:grid;grid-template-columns:88px 1fr;gap:14px;padding:16px 0;border-bottom:1px solid var(--line)">
    <div style="background:var(--ivory-50);display:grid;place-items:center;aspect-ratio:1">${b.img?`<img src="${esc(b.img)}" alt="" style="width:100%;height:100%;object-fit:cover">`:D.shoeSVG({shape:b.shape,colour:b.colour,craft:b.craft,art:b.art,initials:b.initials,gold:b.gold})}</div>
    <div><div style="font-family:var(--serif);font-size:20px">${esc(b.name)}</div><div class="small muted">${esc([b.colour,b.size,b.detail].filter(Boolean).join(", "))}</div><div class="small muted">${esc(b.note||"")}</div>
    <div class="row" style="justify-content:space-between;margin-top:6px"><span class="price" style="font-size:16px">${moneyHTML(b.price)}${b.qty>1?` × ${b.qty}`:""}</span><button class="tlink" data-rmbag="${b.id}">Remove</button></div></div></div>`).join("")
   +`<label class="field" style="margin-top:20px"><span>Gift note (optional)</span><textarea data-gift maxlength="300" style="min-height:70px">${esc(store.get("gift",""))}</textarea></label>`
   +`<div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--line)">
       <div class="row" style="justify-content:space-between"><span class="label">Subtotal</span><span class="price">${moneyHTML(total)}</span></div>
       ${CUR!=="INR"?`<p class="small muted" style="margin:8px 0 0">International orders: duties are paid on delivery. We will send an estimate with your confirmation.</p>`:""}
       <p class="small muted" style="margin:8px 0 6px">The house personally crafts each pair and confirms your order with a bespoke invoice.</p>
       ${authHTML}
     </div>`;

  $$("[data-rmbag]").forEach(x=>x.onclick=()=>{BAG=BAG.filter(b=>String(b.id)!==x.dataset.rmbag);bagSave();renderBag()});
  const g=$("[data-gift]"); if(g)g.oninput=()=>store.set("gift",g.value);

  $$("[data-authmode]").forEach(b=>b.onclick=()=>{
    bagAuthMode = b.dataset.authmode;
    renderBag();
  });

  // Login & Checkout handler
  const btnLogin=$("[data-login-checkout]");
  if(btnLogin){
    btnLogin.onclick=async e=>{
      const lem=$("[data-lemail]"), lpw=$("[data-lpass]");
      let bad=false;
      [lem, lpw].forEach(i=>{const x=!i.value.trim();i.closest(".field").classList.toggle("bad",x);bad=bad||x});
      if(bad){(!lem.value.trim()?lem:lpw).focus();return;}
      if(btnLogin.disabled)return; btnLogin.disabled=true;
      
      const logFd=new FormData(); logFd.set("email",lem.value); logFd.set("password",lpw.value);
      const logRes=await postForm("login",logFd);
      if(!logRes.ok){
        btnLogin.disabled=false;
        toast(logRes.error||"Invalid credentials. Please check your email and password.");
        return;
      }
      TM.customer=logRes.customer;
      TM.customer.address=logRes.address;
      
      // Now place the order
      const fd=new FormData();
      fd.set("currency",CUR);
      fd.set("gift",store.get("gift",""));
      fd.set("bag",JSON.stringify(BAG));
      const ordRes=await postForm("order",fd);
      btnLogin.disabled=false;
      if(ordRes.ok){
        BAG=[]; bagSave(); store.set("gift","");
        location.href=ordRes.redirect || url("received");
      } else {
        toast(ordRes.error||"Please try again.");
      }
    };
  }

  // Register or Authenticated Checkout handler
  const btnCheckout=$("[data-checkout]");
  if(btnCheckout){
    btnCheckout.onclick=async e=>{
      const btn=e.currentTarget;
      const fd=new FormData();
      fd.set("currency",CUR);
      fd.set("gift",store.get("gift",""));
      fd.set("bag",JSON.stringify(BAG));

      if(!TM.customer){
        const fn=$("[data-bfname]"), ln=$("[data-blname]"), em=$("[data-bemail]"), ph=$("[data-bphone]"),
              pw=$("[data-bpass]"), l1=$("[data-bline1]"), ct=$("[data-bcity]"), st=$("[data-bstate]"),
              pc=$("[data-bpostcode]"), co=$("[data-bcountry]");
        
        let bad=false;
        [fn, ln, em, ph, pw, l1, ct, st, pc].forEach(i=>{
          if(!i)return;
          const x=!i.value.trim() || (i.type==="email" && !/^\S+@\S+\.\S+$/.test(i.value)) || (i.type==="password" && i.value.length < 6);
          i.closest(".field")?.classList.toggle("bad",x);
          bad=bad||x;
        });

        if(bad){
          const firstBad=$(".field.bad input",body);
          if(firstBad)firstBad.focus();
          toast("Please fill in all required registration and address fields.");
          return;
        }

        fd.set("first_name",fn.value);
        fd.set("last_name",ln.value);
        fd.set("email",em.value);
        fd.set("phone",ph.value);
        fd.set("password",pw.value);
        fd.set("line1",l1.value);
        fd.set("city",ct.value);
        fd.set("state",st.value);
        fd.set("postcode",pc.value);
        fd.set("country",co?co.value:"India");
      }

      if(btn.disabled)return; btn.disabled=true; track("begin_checkout",{value:total});
      const j=await postForm("order",fd);
      btn.disabled=false;
      if(j.ok){
        BAG=[]; bagSave(); store.set("gift","");
        location.href=j.redirect || url("received");
      } else {
        if(j.fields){ showFieldErrors(body, j.fields); }
        toast(j.error||"Please try again.");
      }
    };
  }
}
bagSave();

/* ---------- Product page ---------- */
const PD=$("#productData") && JSON.parse($("#productData").textContent);
let SIZESYS=store.get("sizesys","UK");
function sizeLabel(uk,sys,women){ const n=parseFloat(String(uk).replace("UK ","")); if(isNaN(n))return uk;
  if(sys==="UK")return "UK "+n; if(sys==="India")return "India "+n; if(sys==="EU")return "EU "+(n+(women?33:34)); return "US "+(n+(women?2:1)); }
function applySizeSys(sel,women){ if(!sel)return; [...sel.options].forEach(o=>{if(o.value&&o.value.startsWith("UK"))o.textContent=sizeLabel(o.value,SIZESYS,women)}); }
if(PD){
  let colour=PD.colours[0].name, view="side";
  const gal=$("[data-gal]"), sel=$("#sizeSel"), women=PD.women;
  applySizeSys(sel,women); $$("[data-sys]").forEach(b=>b.setAttribute("aria-pressed",b.dataset.sys===SIZESYS));
  const visual=(v)=>{ const imgs=PD.images[colour]||PD.images["*"]||{};
    if(v==="box") return `<div style="width:60%">${D.boxSVG()}</div>`;
    if(imgs[v]) return `<img src="${esc(imgs[v].src)}" alt="${esc(imgs[v].alt)}">`;
    if(v==="macro") return D.macroSVG(PD.craft,colour);
    return D.shoeSVG({...PD.drawing,colour,craft:PD.craft,alt:PD.name+" in "+colour}); };
  const draw=()=>{gal.innerHTML=`<div class="draw">${visual(view)}</div>`; gal.classList.remove("zoom")};
  $$("[data-view]").forEach(b=>b.onclick=()=>{view=b.dataset.view;$$("[data-view]").forEach(x=>x.setAttribute("aria-pressed",x===b));draw()});
  $$("[data-col]").forEach(b=>b.onclick=()=>{colour=b.dataset.col;$$("[data-col]").forEach(x=>x.setAttribute("aria-pressed",x===b));$("[data-colname]").textContent=colour;
    const t=$$("[data-view]"); t[0].innerHTML=`<div class="draw">${visual("side")}</div>`; t[1].innerHTML=`<div class="draw macro-slot">${visual("macro")}</div>`; draw();
    const diff=+(b.dataset.diff||0); $$("[data-price] .money").forEach(m=>{m.dataset.inr=PD.price+diff}); refreshMoney();});
  const zoom=e=>{ if(gal.classList.contains("zoom")){gal.classList.remove("zoom");return} const r=gal.getBoundingClientRect(), s=gal.firstElementChild; const x=e.clientX?((e.clientX-r.left)/r.width*100):50, y=e.clientY?((e.clientY-r.top)/r.height*100):50; s.style.transformOrigin=`${x}% ${y}%`; gal.classList.add("zoom")};
  gal.addEventListener("click",zoom); gal.addEventListener("keydown",e=>{if(e.key==="Enter"||e.key===" "){e.preventDefault();zoom(e)}});
  $$("[data-sys]").forEach(b=>b.onclick=()=>{SIZESYS=b.dataset.sys;store.set("sizesys",SIZESYS);$$("[data-sys]").forEach(x=>x.setAttribute("aria-pressed",x===b));const v=sel.value;applySizeSys(sel,women);sel.value=v;});
  sel&&sel.addEventListener("change",()=>$("#sizeField").classList.remove("bad"));
  const add=()=>{
    if(sel&&!sel.value){$("#sizeField").classList.add("bad");sel.focus();if(!matchMedia("(min-width:1024px)").matches)sel.scrollIntoView({block:"center"});return}
    const size=sel?sel.value:(PD.ready[0]||"");
    const mto = PD.avail!=="Ready to Ship" || (sel&&!PD.ready.includes(size));
    const c=PD.colours.find(x=>x.name===colour)||{price_diff:0}; const img=(PD.images[colour]||PD.images["*"]||{}).side;
    bagAdd({key:PD.slug,name:PD.name,colour:PD.service?"":colour,size,price:PD.price+(+c.price_diff||0),shape:PD.drawing.shape,craft:PD.craft,art:PD.drawing.art,img:img?img.src:null,
      note:PD.service?"":(mto?`Made to Order, ${PD.lead[0]} to ${PD.lead[1]} weeks`:"Ready to Ship")});
  };
  $$("[data-add]").forEach(b=>b.onclick=add);
  const sb=$("#stickyBuy"); if(sb){sb.classList.add("on");sb.setAttribute("aria-hidden","false")}
  track("view_item",{item:PD.slug,craft:PD.craft,price:PD.price});
}

/* ---------- Listing filters ----------
 * Each filter change reloads the page with the filters in the address,
 * so any filtered view can be shared or bookmarked. */
const shop=$("[data-shop]");
if(shop){
  const sheet=$("[data-sheetfilters]"), rail=$("[data-filterrail]");
  if(sheet&&rail)sheet.innerHTML=rail.innerHTML;
  const q=new URLSearchParams(location.search);
  const go=()=>{const s=q.toString();sessionStorage.setItem("tm_scroll",JSON.stringify({p:location.pathname,y:0}));location.href=location.pathname+(s?"?"+s:"")};
  const toggle=(k,v)=>{const cur=(q.get(k)||"").split("|").filter(Boolean);const i=cur.indexOf(v);i>-1?cur.splice(i,1):cur.push(v);cur.length?q.set(k,cur.join("|")):q.delete(k);track("filter_apply",{facet:k,value:v});go()};
  document.addEventListener("change",e=>{const i=e.target.closest("[data-f]");if(i)toggle(i.dataset.f,i.value)});
  $$("[data-rm]").forEach(b=>b.onclick=()=>toggle(b.dataset.rm,b.dataset.v));
  $$("[data-silchip]").forEach(b=>b.onclick=()=>toggle("sil",b.dataset.silchip));
  $$("[data-clear]").forEach(b=>b.onclick=()=>{[...q.keys()].filter(k=>k!=="sort").forEach(k=>q.delete(k));go()});
  const s=$("[data-sort]"); if(s)s.onchange=()=>{q.set("sort",s.value);go()};
  // Returning from a product restores the scroll position.
  const saved=JSON.parse(sessionStorage.getItem("tm_scroll")||"null");
  if(saved&&saved.p===location.pathname+location.search&&saved.y)window.scrollTo(0,saved.y);
  $$(".card",shop).forEach(c=>c.addEventListener("click",()=>sessionStorage.setItem("tm_scroll",JSON.stringify({p:location.pathname+location.search,y:window.scrollY}))));
}

/* ---------- Wedding page ---------- */
$$("[data-wtab]").forEach(t=>t.addEventListener("click",e=>{e.preventDefault();const i=t.dataset.wtab;
  $$("[data-wtab]").forEach(x=>x.setAttribute("aria-current",x===t)); $$("[data-wpanel]").forEach(p=>p.hidden=p.dataset.wpanel!==i);}));
const wd=$("[data-wdate]");
if(wd){
  const today=new Date(); today.setHours(0,0,0,0); wd.min=today.toISOString().slice(0,10);
  wd.addEventListener("change",()=>{ const ans=$("[data-wans]"); if(!wd.value){ans.innerHTML="";return}
    const days=Math.round((new Date(wd.value)-today)/864e5), w=Math.floor(days/7); track("wedding_date_check",{weeks:w});
    let h;
    if(days<0) h="That date has passed. Choose a date ahead.";
    else if(w>=14) h=`<strong>Yes, comfortably.</strong> ${w} weeks leaves time for anything: custom artwork, carving and pairs for the family. Begin early and you will have time for a fitting.`;
    else if(w>=10) h=`<strong>Yes.</strong> ${w} weeks allows made-to-order pairs, house artwork and most commissions with custom artwork. Carving would be tight; let us talk about it.`;
    else if(w>=6) h=`<strong>Yes, for made-to-order pairs.</strong> ${w} weeks allows house designs in patina, scarring, miniature and tattoo with initials. Custom artwork is tight; message us today.`;
    else if(w>=2) h=`<strong>Ready pairs, yes.</strong> ${w} weeks is shorter than our making time, but ready-to-ship pairs can be personalised with initials. Message us and we will see what can be done.`;
    else h="<strong>Very close.</strong> Visit the Kolkata house or message us for ready pairs in your size.";
    ans.innerHTML=`<div class="notice" style="margin-top:4px">${h}<div class="row" style="margin-top:12px">${w>=6?`<a class="btn" href="${url("bespoke/build")}">Build a wedding pair</a>`:`<a class="btn" href="${url("shop?avail=Ready+to+Ship&occasion=Wedding")}">Ready pairs</a>`}<a class="btn ghost wa" target="_blank" rel="noopener" href="${waLink("Hello Tōramally, our wedding date is "+wd.value+".")}">${WA_ICON}Plan your wedding pairs</a></div></div>`; });
}

/* ---------- Bespoke builder ---------- */
let builderRedraw=null;
const BR=$("[data-builder]");
if(BR){
  const CFG=JSON.parse($("#builderData").textContent);
  const {SIL,CRAFT,ART,ADDON}=CFG, STEPS=["Silhouette","Colour","Craft","Artwork","Personalise","Size","Review"];
  const COLOURS=D.COLOURS, craftOf=s=>TM.crafts.find(c=>c.slug===s)||{name:s,scale:"",what:""};
  const def=()=>({step:0,sil:"",colour:"",craft:"",art:"",custom:false,hex:"",initials:"",place:"Heel",gold:false,nails:false,size:"",notes:""});
  let B=store.get("build",null)||def(); let FILES=[];
  const q=new URLSearchParams(location.search);
  if(q.get("b")){try{B={...def(),...JSON.parse(decodeURIComponent(escape(atob(q.get("b")))))}}catch(e){}}
  else if(CFG.from){const f=CFG.from;B={...def(),sil:SIL.find(x=>x[0]===f.sil)?f.sil:"Belgian loafer",colour:f.colour,craft:CRAFT[f.craft]?f.craft:"",art:f.art||"",step:CRAFT[f.craft]?3:1}}
  else if(q.get("craft")&&CRAFT[q.get("craft")]){B={...def(),craft:q.get("craft")}}
  const shape=()=> (SIL.find(x=>x[0]===B.sil)||[,"loafer"])[1];
  const isAcc=()=>["belt","wallet"].includes(shape());
  const est=()=>{ const s=SIL.find(x=>x[0]===B.sil); if(!s)return null; const acc=isAcc();
    let p=s[2], w=[4,6]; if(B.craft){const c=CRAFT[B.craft];p+=acc?Math.round(c[0]*.5):c[0];w=c[1].slice()}
    if(B.custom){p+=ADDON.custom;w=[w[0]+2,w[1]+2]} if(B.colour==="Custom"){w=[w[0]+1,w[1]+1]}
    if(B.initials)p+=ADDON.initials; if(B.gold)p+=ADDON.gold; if(B.nails&&!acc)p+=ADDON.nails;
    return {price:p,weeks:w,commission:B.custom||B.craft==="carving"||p>=TM.threshold||B.colour==="Custom"}; };
  const prevColour=()=> B.colour&&B.colour!=="Custom"?B.colour:(/^#?[0-9a-f]{6}$/i.test(B.hex)?(B.hex[0]==="#"?B.hex:"#"+B.hex):"Oxblood");
  const shareUrl=()=>location.origin+url("bespoke/build")+"?b="+btoa(unescape(encodeURIComponent(JSON.stringify(B))));
  const msg=()=>{const e=est()||{price:0};return "Hello Tōramally, I am building a pair:\n"+[["Silhouette",B.sil],["Colour",B.colour==="Custom"?"Custom "+B.hex:B.colour],["Craft",B.craft&&craftOf(B.craft).name],["Artwork",B.custom?"My own artwork":B.art],["Initials",B.initials&&(B.initials+(B.gold?" in gold":""))],["Brass nails",B.nails?"Yes":""],["Size",B.size]].filter(r=>r[1]).map(r=>r.join(": ")).join("\n")+(e.price?"\nEstimated: "+money(e.price):"")+"\n"+shareUrl()};
  const save=()=>store.set("build",B);
  const preview=()=>{$("[data-prev]").innerHTML=D.shoeSVG({shape:shape(),colour:prevColour(),craft:B.craft||"patina",art:B.art==="lattice"?"":B.art,initials:B.initials,gold:B.gold,nails:B.nails,alt:"Preview of your pair"})};
  const uploadField=()=>`<div class="field"><span>Reference</span><div class="upload"><input type="file" id="bup" accept=".jpg,.jpeg,.png,.pdf" multiple data-upload><label class="btn ghost" for="bup">Upload ref. Image</label><span class="small muted" data-upnames>${FILES.map(f=>esc(f.name)).join(", ")}</span></div><div class="err" data-uperr></div></div>`;
  const draw=()=>{
    const e=est(), s=B.step, acc=isAcc(); preview();
    $("[data-prog]").innerHTML=STEPS.map((x,i)=>`${i?"<span></span>":""}<b class="${i<s?"done":i===s?"now":""}" title="${x}"></b>`).join("");
    const nav=(ok=true)=>`<div class="row" style="justify-content:space-between;margin-top:24px"><button class="btn ghost" data-back ${s===0?"disabled":""}>Back</button><button class="btn" data-next ${ok?"":"disabled"}>Continue</button></div>`;
    const rows=[["Silhouette",B.sil],["Colour",B.colour==="Custom"?`Custom ${B.hex}`:B.colour],["Craft",B.craft&&craftOf(B.craft).name],["Artwork",B.custom?"Your own artwork":(ART[B.craft]||[]).find(a=>a[0]===B.art)?.[1]],["Initials",B.initials&&`${B.initials}${B.gold?", gold":""}, ${B.place}`],["Brass nails",B.nails&&"Yes"],["Size",B.size]].filter(r=>r[1]);
    const summ=`<div class="acc" style="margin-top:24px"><details><summary>Your Tōramally${e?` <span class="price" style="font-size:16px;margin-left:auto;margin-right:12px">Estimated ${moneyHTML(e.price)}</span>`:""}</summary><div class="in summary"><dl>${rows.map(([k,v])=>`<dt>${k}</dt><dd>${esc(v)}</dd>`).join("")||"<dt>Nothing chosen yet</dt><dd></dd>"}</dl><div style="margin-top:12px"><a class="tlink wa" target="_blank" rel="noopener" href="${waLink(msg())}">${WA_ICON}Discuss your commission</a></div></div></details></div>`;
    let h=`<p class="label brass">Step ${s+1} of 7</p><h1 class="h2" style="margin:6px 0 20px">${["Choose a silhouette","Choose a colour","Choose a craft","Choose the artwork","Add your mark","Your size","Your Tōramally"][s]}</h1>`;
    if(s===0) h+=`<div class="opts">${SIL.map(([n,sh,pr])=>`<button class="opt" data-pick="sil" data-v="${n}" aria-pressed="${B.sil===n}">${D.shoeSVG({shape:sh,colour:COLOURS[B.colour]?B.colour:"Cognac",craft:"patina"})}<span class="t">${n}</span><span class="s">from ${moneyHTML(pr+2000)}</span></button>`).join("")}</div>${nav(!!B.sil)}`;
    if(s===1) h+=`<div class="opts" style="grid-template-columns:repeat(3,1fr)">${[...Object.keys(COLOURS),"Custom"].map(n=>`<button class="opt" data-pick="colour" data-v="${n}" aria-pressed="${B.colour===n}"><span class="rect-sw" style="background:${COLOURS[n]||"repeating-linear-gradient(45deg,#e6dcc6 0 6px,#d6c7a8 6px 12px)"}">${COLOURS[n]?D.macroSVG("patina",n):""}</span><span class="s" style="color:var(--charcoal)">${n}</span></button>`).join("")}</div>
      ${B.colour==="Custom"?`<div style="margin-top:16px"><label class="field"><span>Colour code or description</span><input data-hex value="${esc(B.hex)}" placeholder="#3A5F4B, or 'the green of old bottle glass'"></label>${uploadField()}<p class="small muted">We reply with a photograph of a real swatch for your approval.</p></div>`:""}${nav(!!B.colour)}`;
    if(s===2) h+=`<div class="opts">${Object.keys(CRAFT).map(k=>`<button class="opt" data-pick="craft" data-v="${k}" aria-pressed="${B.craft===k}"><span style="aspect-ratio:1;display:block;overflow:hidden">${D.macroSVG(k,COLOURS[B.colour]?B.colour:D.CRAFT_COLOUR[k])}</span><span class="t">${craftOf(k).name}</span><span class="s">${craftOf(k).scale}. +${moneyHTML(acc?Math.round(CRAFT[k][0]*.5):CRAFT[k][0])}</span></button>`).join("")}</div><p class="small muted" style="margin-top:12px">${B.craft?esc(craftOf(B.craft).what):"Velvet is a separate everyday line and is not built here."}</p>${nav(!!B.craft)}`;
    if(s===3){ if(!ART[B.craft]) h+=`<p>${esc(craftOf(B.craft||"patina").name)} carries no artwork. The colour is the art. Continue to add your mark.</p>${nav(true)}`;
      else h+=`<div class="opts">${ART[B.craft].map(([k,t])=>`<button class="opt" data-pick="art" data-v="${k}" aria-pressed="${!B.custom&&B.art===k}"><span class="t">${t}</span><span class="s">House ${B.craft==="scarring"?"pattern":"design"}. Included.</span></button>`).join("")}<button class="opt" data-pick="art" data-v="__custom" aria-pressed="${B.custom}"><span class="t">Your own</span><span class="s">Sketch, pattern or inspiration. +${moneyHTML(ADDON.custom)}, proof before making.</span></button></div>
        ${B.custom?`<div style="margin-top:16px">${uploadField()}<label class="field"><span>Describe the artwork</span><textarea data-notes>${esc(B.notes)}</textarea></label></div>`:""}${nav(!!(B.art||B.custom))}`; }
    if(s===4) h+=`<label class="field"><span>Initials (up to three letters)</span><input data-init maxlength="3" value="${esc(B.initials)}" autocomplete="off"><div class="err">Letters A to Z only.</div></label>
      ${acc?"":`<div class="field"><span>Placement</span><div class="toggle" role="group">${["Heel","Insole","Tongue"].map(p=>`<button type="button" data-place="${p}" aria-pressed="${B.place===p}">${p}</button>`).join("")}</div></div>`}
      <label class="row" style="padding:10px 0;border-top:1px solid var(--line)"><input type="checkbox" data-gold ${B.gold?"checked":""} style="accent-color:var(--green-900);width:18px;height:18px"> Gold initials <span class="small muted">+${moneyHTML(ADDON.gold)}</span></label>
      ${acc?"":`<label class="row" style="padding:10px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line)"><input type="checkbox" data-nails ${B.nails?"checked":""} style="accent-color:var(--green-900);width:18px;height:18px"> Brass nail monogram on the sole <span class="small muted">+${moneyHTML(ADDON.nails)}</span></label>`}
      <p class="small muted" style="margin-top:12px">Initials add ${moneyHTML(ADDON.initials)}. Leave blank for none.</p>${nav(true)}`;
    if(s===5){ if(shape()==="wallet") h+=`<p>Wallets are one size.</p>${nav(true)}`;
      else if(shape()==="belt") h+=`<label class="field" id="bSizeF"><span>Waist</span><select data-size><option value=""></option>${["80 cm","85 cm","90 cm","95 cm","100 cm","105 cm","110 cm"].map(v=>`<option ${B.size===v?"selected":""}>${v}</option>`).join("")}</select><div class="err">Please choose a size.</div></label>${nav(true)}`;
      else { const opts=[]; for(let n=5;n<=12;n+=.5)opts.push("UK "+n);
        h+=`<div class="row" style="justify-content:space-between;margin-bottom:10px"><span class="label">Size</span><div class="toggle" role="group" aria-label="Size system">${["India","UK","EU","US"].map(x=>`<button type="button" data-sys="${x}" aria-pressed="${SIZESYS===x}">${x}</button>`).join("")}</div></div>
        <div class="field" id="bSizeF"><select data-size aria-label="Size"><option value=""></option>${opts.map(u=>`<option value="${u}" ${B.size===u?"selected":""}>${sizeLabel(u,SIZESYS,false)}</option>`).join("")}</select><div class="err">Please choose a size.</div></div>
        <div class="row small"><a href="${url("size-guide")}">Find your size</a><a class="tlink wa" target="_blank" rel="noopener" href="${waLink("Hello Tōramally, I am building a pair and need help with my size.")}">${WA_ICON}Help me find my size</a></div>${nav(true)}`; } }
    if(s===6){
      h+=`<div class="summary"><dl>${[["Silhouette",B.sil],["Colour",B.colour==="Custom"?`Custom ${B.hex}`:B.colour],["Craft",craftOf(B.craft||"patina").name],["Artwork",B.custom?"Your own artwork, proof before making":((ART[B.craft]||[]).find(a=>a[0]===B.art)||[,"None"])[1]],["Initials",B.initials?`${B.initials}${B.gold?", gold":""}${acc?"":", "+B.place}`:"None"],["Brass nails",B.nails?"Yes":"No"],["Size",B.size||(shape()==="wallet"?"One size":"To confirm")]].map(([k,v])=>`<dt>${k}</dt><dd>${esc(v)}</dd>`).join("")}</dl></div>
      <div class="est"><span class="label">Estimated</span><span class="price" style="font-size:24px">${moneyHTML(e.price)}</span></div>
      <p class="small">Estimated delivery: ${e.weeks[0]} to ${e.weeks[1]} weeks from ${e.commission?"your approval of the design proof":"order"}.</p>
      ${e.commission?`<p class="small muted">Custom work is quoted by the house after a short conversation. The estimate becomes final once confirmed.</p>
        <label class="field"><span>Name</span><input data-cn autocomplete="name"><div class="err">Please add your name.</div></label>
        <label class="field"><span>Email or WhatsApp number</span><input data-cc autocomplete="email"><div class="err">Please add a way to reach you.</div></label>`:""}
      <button class="btn full" data-finish>${e.commission?"Commission this pair":"Add to Bag"}</button>
      <div class="notice ok" data-bdone hidden tabindex="-1" style="margin-top:16px"></div>
      <div class="row" style="justify-content:space-between;margin-top:14px"><button class="tlink" data-back>Back</button><button class="tlink" data-share>Copy link to this design</button><button class="tlink" data-reset>Start again</button></div>`; }
    $("[data-stepbody]").innerHTML=h+(s<6?summ:""); bind(); save();
  };
  const bind=()=>{
    $$("[data-pick]").forEach(b=>b.onclick=()=>{const k=b.dataset.pick,v=b.dataset.v;
      if(k==="art"){ if(v==="__custom"){B.custom=true;B.art=""} else {B.custom=false;B.art=v} }
      else { B[k]=v; if(k==="craft"){B.art=(ART[v]||[[""]])[0][0];B.custom=false} }
      draw(); });
    const nx=$("[data-next]"); if(nx)nx.onclick=()=>{
      if(B.step===5&&shape()!=="wallet"&&!B.size){$("#bSizeF").classList.add("bad");$("[data-size]").focus();return}
      track("builder_step_complete",{step:STEPS[B.step]}); B.step=Math.min(6,B.step+1); draw(); $("[data-stepbody]").scrollIntoView({block:"nearest"}) };
    $$("[data-back]").forEach(b=>b.onclick=()=>{B.step=Math.max(0,B.step-1);draw()});
    const hx=$("[data-hex]"); if(hx)hx.oninput=()=>{B.hex=hx.value;save();preview()};
    const nt=$("[data-notes]"); if(nt)nt.oninput=()=>{B.notes=nt.value;save()};
    const it=$("[data-init]"); if(it)it.oninput=()=>{const ok=/^[A-Za-z]*$/.test(it.value);it.closest(".field").classList.toggle("bad",!ok);if(ok){B.initials=it.value.toUpperCase();it.value=B.initials;save();preview()}};
    $$("[data-place]").forEach(b=>b.onclick=()=>{B.place=b.dataset.place;draw()});
    const g=$("[data-gold]"); if(g)g.onchange=()=>{B.gold=g.checked;draw()};
    const n=$("[data-nails]"); if(n)n.onchange=()=>{B.nails=n.checked;draw()};
    const sz=$("[data-size]"); if(sz)sz.onchange=()=>{B.size=sz.value;$("#bSizeF").classList.remove("bad");save()};
    $$("[data-sys]",BR).forEach(b=>b.onclick=()=>{SIZESYS=b.dataset.sys;store.set("sizesys",SIZESYS);draw()});
    const up=$("#bup"); if(up){ bindUploads(BR); up.addEventListener("change",()=>{ if(up.files.length)FILES=[...up.files]; }); }
    const sh=$("[data-share]"); if(sh)sh.onclick=async()=>{try{await navigator.clipboard.writeText(shareUrl());toast("Link copied. It reopens this exact design.")}catch(e){prompt("Copy this link",shareUrl())}};
    const rs=$("[data-reset]"); if(rs)rs.onclick=()=>{B=def();FILES=[];draw()};
    const fin=$("[data-finish]"); if(fin)fin.onclick=async()=>{
      const e=est();
      if(e.commission){ const nm=$("[data-cn]"),cc=$("[data-cc]"); let bad=false; [nm,cc].forEach(i=>{const x=!i.value.trim();i.closest(".field").classList.toggle("bad",x);bad=bad||x}); if(bad){(!nm.value.trim()?nm:cc).focus();return}
        if(fin.disabled)return; fin.disabled=true; track("commission_submit",{value:e.price});
        const fd=new FormData(); fd.set("name",nm.value); fd.set("contact",cc.value); fd.set("build",JSON.stringify(B)); fd.set("estimate",e.price);
        FILES.forEach(f=>fd.append("files[]",f));
        const j=await postForm("commission",fd); fin.disabled=false;
        const box=$("[data-bdone]"); if(j.ok){box.textContent=j.message;box.hidden=false;box.focus();FILES=[];} else toast(j.error||"Please try again.");
      } else {
        bagAdd({key:"build-"+Date.now(),name:`Your ${B.sil}`,colour:B.colour,size:B.size,price:e.price,shape:shape(),craft:B.craft||"patina",art:B.art,initials:B.initials,gold:B.gold,custom:true,
          detail:[craftOf(B.craft||"patina").name,B.initials&&`initials ${B.initials}`].filter(Boolean).join(", "),note:`Made to Order, ${e.weeks[0]} to ${e.weeks[1]} weeks`});
      }
    };
  };
  builderRedraw=draw; track("begin_commission",{}); draw();
}

/* ---------- Scroll to a section (e.g. /house/story) ---------- */
const st=document.body.dataset.scroll; if(st){const el=document.getElementById(st); if(el)setTimeout(()=>el.scrollIntoView(),40);}

/* ---------- Announcement and cookie notice ---------- */
const an=$("#announce"); if(an&&!store.get("annX",false))an.hidden=false;
$("#announceX")?.addEventListener("click",()=>{an.hidden=true;store.set("annX",true)});
function loadTags(){ // Google Analytics and Meta Pixel load only after consent
  if(TM.ga&&!window.gtag){const s=document.createElement("script");s.async=true;s.src="https://www.googletagmanager.com/gtag/js?id="+encodeURIComponent(TM.ga);document.head.appendChild(s);window.dataLayer=window.dataLayer||[];window.gtag=function(){dataLayer.push(arguments)};gtag("js",new Date());gtag("config",TM.ga);}
  if(TM.pixel&&!window.fbq){!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init',TM.pixel);fbq('track','PageView');}
}
const consent=store.get("consent",null);
if(!consent)$("#cookie")?.classList.add("on"); else if(consent==="accept")loadTags();
$$("[data-cookie]").forEach(b=>b.addEventListener("click",()=>{store.set("consent",b.dataset.cookie);$("#cookie").classList.remove("on");if(b.dataset.cookie==="accept")loadTags()}));
})();
