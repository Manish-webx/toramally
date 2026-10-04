/* Tōramally — drawings.
 * Draws the shoe illustrations, craft macro textures and the fish emblem as SVG.
 * These stand in for photography: once a product has photographs uploaded in
 * the admin, the page shows the photographs instead and this file is not used for it.
 * Any element with data-draw, data-macro, data-emblem or data-box is filled on page load.
 */
(function(){
"use strict";
const COLOURS = {
  "Black":"#1f1c19","Oxblood":"#4b1719","Burgundy":"#5e1f2c","Tobacco":"#6a4528","Cognac":"#8f4b21",
  "Dark Brown":"#3c2619","Tan":"#a66e3d","Forest Green":"#26412f","Navy":"#1e2b45","Beige":"#c9b28f","Ivory":"#e9e0cc"
};
const esc = s=>String(s??"").replace(/[&<>"']/g,c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));
const craftOf = id=>((window.TM&&TM.crafts)||[]).find(c=>c.slug===id)||{name:id};

let UID=0;
function hexToRgb(h){h=h.replace("#","");return[parseInt(h.slice(0,2),16),parseInt(h.slice(2,4),16),parseInt(h.slice(4,6),16)]}
function mix(h,amt){ // amt -1..1 : darken / lighten
  const [r,g,b]=hexToRgb(h), t=amt<0?0:255, p=Math.abs(amt);
  const f=c=>Math.round(c+(t-c)*p).toString(16).padStart(2,"0"); return "#"+f(r)+f(g)+f(b);
}
function lum(h){const [r,g,b]=hexToRgb(h);return (0.299*r+0.587*g+0.114*b)/255}

function emblem(light){
  const c = light?"#cdb47f":"#9C7A3C";
  return `<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M7 32c7-10 20-15 33-10.5l2 .8 12-8.3-3.6 18 3.6 18-12-8.3-2 .8C27 47 14 42 7 32z" fill="none" stroke="${c}" stroke-width="2" stroke-linejoin="round"/><circle cx="17" cy="30" r="2.2" fill="${c}"/><path d="M24 23.5c3.2 5 3.2 12 0 17" fill="none" stroke="${c}" stroke-width="1.5"/><path d="M30 27c2 1.5 2 3.5 0 5m0 0c2 1.5 2 3.5 0 5M36 26c2 1.8 2 4 0 6m0 0c2 1.8 2 4 0 6" fill="none" stroke="${c}" stroke-width="1.1"/><path d="M13 36.5c2 1.3 4.5 1.5 7 .5" fill="none" stroke="${c}" stroke-width="1.1"/></svg>`;
}

const SHAPES = {
  loafer:{upper:"M44 176 L44 124 C44 104 58 96 80 96 L118 99 C146 101 166 108 190 108 C212 108 226 100 244 97 C292 91 332 110 356 134 C370 148 374 164 368 176 Z",
    det:`<path d="M232 101 C252 128 302 131 348 124" stroke-dasharray="3 3"/><path d="M240 99 q9 -9 19 -1 q-9 9 -19 1z" fill="rgba(0,0,0,.25)"/><path d="M48 132 C80 130 100 134 112 176"/>`, heel:true, vamp:[300,126]},
  penny:{upper:"M44 176 L44 124 C44 104 58 96 80 96 L118 99 C146 101 166 108 190 108 C212 108 226 100 244 97 C292 91 332 110 356 134 C370 148 374 164 368 176 Z",
    det:`<path d="M226 104 C262 120 300 122 346 116"/><path d="M230 112 C262 128 300 130 344 124"/><path d="M262 118 q12 -6 22 1" stroke-width="1.6"/><path d="M232 101 C252 128 302 131 348 124" stroke-dasharray="3 3" opacity=".6"/><path d="M48 132 C80 130 100 134 112 176"/>`, heel:true, vamp:[310,132]},
  oxford:{upper:"M44 176 L44 116 C44 96 60 86 84 86 L150 88 C172 88 186 84 200 76 L214 74 C232 80 252 88 270 94 C312 104 342 118 358 136 C370 150 374 164 368 176 Z",
    det:`<path d="M201 78 C197 112 190 142 176 176"/><path d="M204 80 L244 104"/><g fill="currentColor" stroke="none"><circle cx="209" cy="84" r="1.6"/><circle cx="218" cy="89" r="1.6"/><circle cx="227" cy="94" r="1.6"/><circle cx="236" cy="99" r="1.6"/></g><path d="M209 84 L227 94 M218 89 L236 99" stroke-width="1.3"/><path d="M322 108 C308 130 308 156 318 176"/><path d="M48 128 C80 124 104 130 118 176"/>`, heel:true, vamp:[270,138]},
  wholecut:{upper:"M44 176 L44 116 C44 96 60 86 84 86 L150 88 C172 88 186 84 200 76 L214 74 C232 80 252 88 270 94 C312 104 342 118 358 136 C370 150 374 164 368 176 Z",
    det:`<path d="M204 80 L244 104"/><g fill="currentColor" stroke="none"><circle cx="209" cy="84" r="1.6"/><circle cx="218" cy="89" r="1.6"/><circle cx="227" cy="94" r="1.6"/><circle cx="236" cy="99" r="1.6"/></g><path d="M209 84 L227 94 M218 89 L236 99" stroke-width="1.3"/><path d="M48 130 C60 128 66 140 66 176" stroke-dasharray="2 3"/>`, heel:true, vamp:[290,140]},
  adelaide:{upper:"M44 176 L44 116 C44 96 60 86 84 86 L150 88 C172 88 186 84 200 76 L214 74 C232 80 252 88 270 94 C312 104 342 118 358 136 C370 150 374 164 368 176 Z",
    det:`<path d="M198 80 C192 118 232 132 250 98"/><path d="M204 80 L244 104"/><g fill="currentColor" stroke="none"><circle cx="209" cy="84" r="1.6"/><circle cx="218" cy="89" r="1.6"/><circle cx="227" cy="94" r="1.6"/></g><path d="M322 108 C308 130 308 156 318 176"/><path d="M48 128 C80 124 104 130 118 176"/>`, heel:true, vamp:[150,140]},
  derby:{upper:"M44 176 L44 116 C44 96 60 86 84 86 L150 88 C172 88 186 84 200 76 L214 74 C232 80 252 88 270 94 C312 104 342 118 358 136 C370 150 374 164 368 176 Z",
    det:`<path d="M190 80 C196 104 216 116 250 116"/><path d="M196 84 L240 104"/><g fill="currentColor" stroke="none"><circle cx="202" cy="87" r="1.6"/><circle cx="212" cy="92" r="1.6"/><circle cx="222" cy="97" r="1.6"/></g><path d="M48 128 C80 124 104 130 118 176"/>`, heel:true, vamp:[140,140]},
  mule:{upper:"M138 176 L144 122 C168 106 204 99 244 96 C292 92 332 110 356 134 C370 148 374 164 368 176 Z",
    det:`<path d="M232 101 C252 128 302 131 348 124" stroke-dasharray="3 3"/>`, heel:true, insole:true, vamp:[290,128]},
  slipper:{upper:"M120 176 L128 140 C164 124 220 116 268 118 C314 120 348 140 362 160 C368 168 368 174 366 176 Z",
    det:`<path d="M140 146 C200 132 260 128 300 132" stroke-dasharray="2 3"/>`, heel:false, insole:true, flat:true, vamp:[290,148]},
  flat:{upper:"M52 176 L52 152 C52 142 60 136 70 136 L140 140 C184 142 222 132 262 128 C312 126 350 142 364 160 C370 170 368 176 364 176 Z",
    det:`<path d="M140 140 C180 146 220 138 262 130" /><path d="M62 144 C66 158 66 168 64 176"/>`, heel:false, flat:true, vamp:[300,152]},
  heel:{upper:"M68 116 C66 100 70 88 78 82 L112 104 C150 128 196 140 250 148 C300 154 340 162 360 174 C368 180 364 188 352 188 L200 188 C160 184 120 168 96 150 L70 134 Z",
    det:`<path d="M112 104 C140 130 190 142 250 148"/>`, heelShoe:true, vamp:[300,168]},
  belt:{upper:"M24 108 H334 L360 108 L378 128 L360 148 H24 Z",
    det:`<g fill="rgba(0,0,0,.45)" stroke="none"><circle cx="286" cy="128" r="3"/><circle cx="302" cy="128" r="3"/><circle cx="318" cy="128" r="3"/></g><path d="M28 113 H356" stroke-dasharray="3 3" opacity=".6"/><path d="M28 143 H356" stroke-dasharray="3 3" opacity=".6"/>`, acc:true, vamp:[170,128]},
  wallet:{upper:"M92 52 H308 Q320 52 320 64 V196 Q320 208 308 208 H92 Q80 208 80 196 V64 Q80 52 92 52 Z",
    det:`<path d="M86 58 H314 M86 202 H314" stroke-dasharray="3 3" opacity=".6"/><path d="M200 52 V208" opacity=".5"/>`, acc:true, vamp:[260,130]},
  pebble:{upper:"M100 150 C90 100 150 64 220 70 C290 76 320 120 304 158 C290 192 230 204 170 198 C124 194 106 176 100 150 Z", det:"", acc:true, vamp:[205,135]}
};

function artwork(kind, cx, cy, c, scale=1){
  const g = (s)=>`<g transform="translate(${cx} ${cy}) scale(${scale})">${s}</g>`;
  const gold="#c9a55a", red="#9b2d2a", leaf="#4f6b3a", ivory="#efe6cf", blue="#2c4a78";
  if(kind==="peacock") return g(`
    <path class="ink" d="M-58 18 C-40 -6 -10 -16 20 -12" stroke="${gold}" stroke-width="1.3" fill="none"/>
    ${[-40,-20,0,20].map((x,i)=>`<g transform="translate(${x} ${-6-i*2}) rotate(${-30+i*12})"><ellipse class="paint" rx="7" ry="11" fill="${leaf}" style="animation-delay:${1.6+i*.15}s"/><ellipse class="paint" rx="4.5" ry="7" fill="${blue}" style="animation-delay:${1.7+i*.15}s"/><ellipse class="paint" rx="2" ry="3.2" fill="${gold}" style="animation-delay:${1.8+i*.15}s"/></g>`).join("")}
    <path class="ink" d="M26 -8 C34 -14 44 -12 46 -2 C47 6 40 12 30 12 C26 12 22 8 24 2" stroke="${ivory}" stroke-width="1.2" fill="none"/>
    <circle class="paint" cx="38" cy="-4" r="1.5" fill="${ivory}" style="animation-delay:2.2s"/>
    <path class="ink" d="M-54 22 C-30 26 0 24 36 16" stroke="${gold}" stroke-width="1" fill="none"/>`);
  if(kind==="mandala"){
    let s="";
    for(let i=0;i<12;i++){const a=i*30;s+=`<path class="ink" d="M0 -8 C6 -18 6 -28 0 -36 C-6 -28 -6 -18 0 -8" transform="rotate(${a})" stroke="${c}" stroke-width="1" fill="none"/>`;}
    s+=`<circle class="ink" r="8" stroke="${c}" stroke-width="1" fill="none"/><circle class="ink" r="40" stroke="${c}" stroke-width=".8" fill="none" stroke-dasharray="1 3"/><circle class="ink" r="44" stroke="${c}" stroke-width="1" fill="none"/>`;
    for(let i=0;i<24;i++){const a=i*15*Math.PI/180;s+=`<circle class="paint" cx="${(50*Math.cos(a)).toFixed(1)}" cy="${(50*Math.sin(a)).toFixed(1)}" r="1.2" fill="${c}"/>`}
    return g(s);
  }
  if(kind==="snake"){
    let s=`<path d="M-70 10 C-50 -20 -20 -20 0 0 C20 20 50 20 70 -10" stroke="rgba(0,0,0,.45)" stroke-width="16" fill="none" stroke-linecap="round" transform="translate(1.5 1.5)"/><path d="M-70 10 C-50 -20 -20 -20 0 0 C20 20 50 20 70 -10" stroke="${c}" stroke-width="16" fill="none" stroke-linecap="round"/>`;
    for(let t=0;t<=1;t+=.05){const x=-70+140*t,y=Math.sin(t*Math.PI*2+ Math.PI*.8)*14;s+=`<path d="M${x-4} ${y-3} q4 5 8 0" stroke="rgba(255,240,210,.45)" stroke-width="1" fill="none"/><path d="M${x-4} ${y+1} q4 5 8 0" stroke="rgba(0,0,0,.4)" stroke-width="1" fill="none"/>`}
    s+=`<circle cx="72" cy="-12" r="2" fill="#111"/>`;
    return g(s);
  }
  if(kind==="mahi"){
    return g(`<path class="ink" d="M-40 0c10-14 28-20 45-14l14-10-4 24 4 24-14-10C-12 20-30 14-40 0z" fill="none" stroke="${gold}" stroke-width="2"/><circle class="paint" cx="-26" cy="-2" r="2.4" fill="${gold}"/><path class="ink" d="M-16 -9c4 6 4 12 0 18" stroke="${gold}" fill="none" stroke-width="1.4"/>`);
  }
  // botanical (default)
  let s=`<path class="ink" d="M-60 20 C-40 0 -20 -4 0 -2 C24 0 40 -12 56 -20" stroke="${leaf}" stroke-width="1.4" fill="none"/>`;
  const leaves=[[-46,8,-40],[-30,-2,30],[-12,-4,-25],[10,-3,35],[30,-8,-30],[46,-15,30]];
  leaves.forEach(([x,y,r],i)=>{s+=`<ellipse class="paint" cx="${x}" cy="${y}" rx="7" ry="3" transform="rotate(${r} ${x} ${y})" fill="${leaf}" style="animation-delay:${1.5+i*.1}s"/>`});
  [[-22,-10,red],[18,-10,red],[52,-24,gold],[-52,14,gold],[0,4,ivory]].forEach(([x,y,col],i)=>{
    for(let k=0;k<5;k++){const a=k*72*Math.PI/180;s+=`<circle class="paint" cx="${(x+4*Math.cos(a)).toFixed(1)}" cy="${(y+4*Math.sin(a)).toFixed(1)}" r="3" fill="${col}" style="animation-delay:${2+i*.12}s"/>`}
    s+=`<circle class="paint" cx="${x}" cy="${y}" r="1.8" fill="${gold}" style="animation-delay:${2.3+i*.1}s"/>`;
  });
  return g(s);
}

/* The pair, drawn. opts: shape, colour (name or hex), craft, art, initials, gold, nails, view ('side'|'macro'|'top') */
function shoeSVG(o={}){
  const id="s"+(++UID), sh=SHAPES[o.shape]||SHAPES.loafer;
  const base = COLOURS[o.colour]||o.colour||COLOURS.Oxblood;
  const craft=o.craft||"patina";
  const dark=mix(base,-.45), light=mix(base,.28), ink=lum(base)<.28?"#d8c69b":"#1b1512";
  const sole = craft==="velvet"?"#3a2a1f":"#2a1e16";
  let defs=`<clipPath id="${id}c"><path d="${sh.upper}"/></clipPath>
   <linearGradient id="${id}g" x1="0" x2="1" y1="0" y2="0"><stop offset="0" stop-color="${sh.acc?base:dark}"/><stop offset=".35" stop-color="${base}"/><stop offset=".7" stop-color="${base}"/><stop offset="1" stop-color="${sh.acc?base:dark}"/></linearGradient>
   <linearGradient id="${id}v" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".14"/><stop offset=".5" stop-color="#fff" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".22"/></linearGradient>
   <filter id="${id}b" x="0" y="0" width="100%" height="100%"><feTurbulence type="fractalNoise" baseFrequency="${craft==="velvet"?"0.9 0.9":"0.01 0.22"}" numOctaves="2" seed="${UID%9}"/><feColorMatrix values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 ${craft==="velvet"?".55":".9"} -.2"/></filter>
   <radialGradient id="${id}s" cx=".85" cy=".35" r=".3"><stop offset="0" stop-color="#fff" stop-opacity="${craft==="velvet"?".05":".32"}"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></radialGradient>`;
  let over="";
  if(craft==="scarring"){
    defs+=`<pattern id="${id}p" width="12" height="12" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><path d="M0 0H12M0 0V12" stroke="${light}" stroke-width=".9" fill="none"/><path d="M6 6h0" stroke="${light}" stroke-width="2.2" stroke-linecap="round"/></pattern>`;
    over+=`<rect x="0" y="0" width="400" height="240" fill="url(#${id}p)" opacity=".75"/>`;
  }
  const [vx,vy]=sh.vamp;
  if(craft==="miniature"||craft==="bespoke") over+=artwork(o.art||"botanical",vx,vy,ink,o.artScale||(sh.acc?1.1:.9));
  if(craft==="tattoo") over+=artwork(o.art||"mandala",vx,vy,ink,sh.acc?1:.6);
  if(craft==="carving") over+=artwork(o.art||"snake",vx,vy,mix(base,.12),sh.acc?1:.8);
  if(craft==="velvet"&&!sh.acc) over+=`<path d="M${vx-18} ${vy-12} h34 l4 12 -4 12 h-34z" fill="${mix(base,-.6)}" stroke="#b08d4f" stroke-width=".8"/><path d="M${vx-12} ${vy} h24" stroke="#b08d4f" stroke-width=".8"/>`;
  let extra="";
  if(sh.insole) extra+=`<path d="M40 176 L40 166 C40 162 44 160 50 160 L150 162 L148 176 Z" fill="#c8b48f"/><path d="M50 164 L146 166" stroke="#a8946f" stroke-width=".8"/>`;
  let soleSvg="";
  if(sh.heelShoe){
    soleSvg=`<path d="M70 134 L96 150 C120 168 160 184 200 188 L352 188 C362 188 366 186 364 182" fill="none" stroke="${sole}" stroke-width="5" stroke-linecap="round"/><path d="M66 118 L72 136 L84 140 L88 198 L82 198 Z" fill="${sole}"/>`;
  } else if(!sh.acc){
    soleSvg=`<path d="M38 176 H370 C380 176 382 186 372 190 H38 Z" fill="${sole}"/>${sh.heel?`<path d="M38 176 H104 V198 H44 C40 198 38 196 38 193 Z" fill="${sole}"/><path d="M38 190 H104" stroke="#000" stroke-opacity=".3"/>`:""}<path d="M40 181 H368" stroke="#c9a55a" stroke-opacity=".35" stroke-dasharray="2 2"/>`;
    if(o.nails){ for(let x=48;x<100;x+=7) soleSvg+=`<circle cx="${x}" cy="194" r="1.4" fill="#c9a55a"/>`; }
  }
  let init="";
  if(o.initials){
    const col=o.gold?"#d6b36a":mix(base,.45);
    const pos = sh.acc?[vx-90,vy+6]:(sh.heelShoe?[96,130]:(sh.insole?[92,172]:[70,150]));
    init=`<text x="${pos[0]}" y="${pos[1]}" font-family="Cormorant Garamond, serif" font-style="italic" font-size="${sh.acc?22:13}" fill="${col}" text-anchor="middle">${esc(o.initials)}</text>`;
  }
  const buckle = o.shape==="belt"?`<rect x="18" y="100" width="34" height="56" rx="4" fill="none" stroke="#b08d4f" stroke-width="5"/><path d="M35 108 V148" stroke="#b08d4f" stroke-width="3"/>`:"";
  const shadow = `<ellipse cx="205" cy="${sh.acc?214:204}" rx="${sh.acc?150:175}" ry="6" fill="#000" opacity=".08"/>`;
  return `<svg viewBox="0 0 400 240" role="img" aria-label="${esc(o.alt||((o.colour||"")+" "+(craftOf(craft)?.name||"")+" "+(o.shape||"")))}"><defs>${defs}</defs>${shadow}${extra}
   <path d="${sh.upper}" fill="url(#${id}g)"/>
   <g clip-path="url(#${id}c)"><rect width="400" height="240" filter="url(#${id}b)" opacity="${craft==="patina"?.55:craft==="velvet"?.5:.3}"/><rect width="400" height="240" fill="url(#${id}v)"/><rect width="400" height="240" fill="url(#${id}s)"/>${over}</g>
   <g fill="none" stroke="${mix(base,-.6)}" stroke-width="1" color="${mix(base,-.7)}">${sh.det}</g>
   <path d="${sh.upper}" fill="none" stroke="${mix(base,-.55)}" stroke-width="1"/>${soleSvg}${buckle}${init}</svg>`;
}

/* Macro texture tiles for the ladder */
function macroSVG(craft, colour){
  const id="m"+(++UID), base=COLOURS[colour]||colour||"#5e1f2c", dark=mix(base,-.5), light=mix(base,.3), ink=lum(base)<.28?"#d8c69b":"#1b1512";
  let body="";
  const noise=(f,a)=>`<filter id="${id}n"><feTurbulence type="fractalNoise" baseFrequency="${f}" numOctaves="3" seed="3"/><feColorMatrix values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 ${a} -.15"/></filter>`;
  let defs=`<radialGradient id="${id}r" cx=".35" cy=".3" r=".9"><stop offset="0" stop-color="${light}"/><stop offset=".55" stop-color="${base}"/><stop offset="1" stop-color="${dark}"/></radialGradient>`;
  if(craft==="velvet"){ defs+=noise("1.1",".7"); body=`<rect width="200" height="200" fill="url(#${id}r)"/><rect width="200" height="200" filter="url(#${id}n)"/><path d="M0 150 C60 120 140 170 200 130 V200 H0Z" fill="#000" opacity=".12"/>`; }
  else if(craft==="patina"){ defs+=noise("0.008 0.18",".9"); body=`<rect width="200" height="200" fill="url(#${id}r)"/><rect width="200" height="200" filter="url(#${id}n)" opacity=".7"/><ellipse cx="70" cy="60" rx="60" ry="26" fill="#fff" opacity=".12" transform="rotate(-20 70 60)"/>`; }
  else if(craft==="scarring"){ defs+=`<pattern id="${id}p" width="22" height="22" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><path d="M0 0H22M0 0V22" stroke="${light}" stroke-width="1.4"/><circle cx="11" cy="11" r="2" fill="${light}"/></pattern>`; body=`<rect width="200" height="200" fill="url(#${id}r)"/><rect width="200" height="200" fill="url(#${id}p)" opacity=".85"/>`; }
  else if(craft==="miniature"){ body=`<rect width="200" height="200" fill="url(#${id}r)"/>${artwork("botanical",100,104,ink,2.1)}`; }
  else if(craft==="tattoo"){ body=`<rect width="200" height="200" fill="url(#${id}r)"/>${artwork("mandala",100,100,ink,1.6)}`; }
  else if(craft==="carving"){ body=`<rect width="200" height="200" fill="url(#${id}r)"/>${artwork("snake",100,100,mix(base,.15),1.35)}`; }
  else { body=`<rect width="200" height="200" fill="url(#${id}r)"/><circle cx="100" cy="100" r="54" fill="none" stroke="#c9a55a" stroke-width="1"/><circle cx="100" cy="100" r="48" fill="none" stroke="#c9a55a" stroke-width=".6" stroke-dasharray="1 3"/><text x="100" y="116" text-anchor="middle" font-family="Cormorant Garamond,serif" font-style="italic" font-size="46" fill="#d6b36a">R·K</text>`; }
  return `<svg viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice" role="img" aria-label="${esc((craftOf(craft)||{}).name||craft)} macro detail"><defs>${defs}</defs>${body}</svg>`;
}
const CRAFT_COLOUR={velvet:"Forest Green",patina:"Cognac",scarring:"Dark Brown",miniature:"Oxblood",tattoo:"Tan",carving:"Tobacco",bespoke:"Black"};

function boxSVG(){return `<svg viewBox="0 0 120 80" aria-hidden="true"><path d="M10 30 L60 18 L110 30 L60 42Z" fill="#6b4a2c"/><path d="M10 30 V62 L60 74 V42Z" fill="#8a6440"/><path d="M110 30 V62 L60 74 V42Z" fill="#74532f"/><path d="M22 30 L60 21 L98 30 L60 39Z" fill="#1F3D2B"/><path d="M40 28 q6 -6 12 0 q-6 6 -12 0M62 27 q6 -6 12 0 q-6 6 -12 0" fill="none" stroke="#9C7A3C" stroke-width=".8"/></svg>`}

/* Fill every placeholder on the page (and inside any element passed in). */
function hydrate(root){
  root=root||document;
  root.querySelectorAll("[data-draw]").forEach(el=>{ if(el.dataset.done)return; try{const o=JSON.parse(el.dataset.draw); o.alt=el.getAttribute("aria-label")||""; el.innerHTML=shoeSVG(o); el.firstElementChild&&el.firstElementChild.setAttribute("aria-hidden","true"); el.dataset.done=1;}catch(e){} });
  root.querySelectorAll("[data-macro]").forEach(el=>{ if(el.dataset.done)return; try{const o=JSON.parse(el.dataset.macro); el.innerHTML=macroSVG(o.craft,o.colour); el.firstElementChild.setAttribute("aria-hidden","true"); el.dataset.done=1;}catch(e){} });
  root.querySelectorAll("[data-emblem]").forEach(el=>{ el.outerHTML=emblem(el.hasAttribute("data-emblem-light")); });
  root.querySelectorAll("[data-box]").forEach(el=>{ el.innerHTML=boxSVG(); el.removeAttribute("data-box"); });
  root.querySelectorAll(".draw-anim path.ink").forEach(p=>{try{p.style.setProperty("--len",Math.ceil(p.getTotalLength()))}catch(e){}});
}
window.TMDraw={shoeSVG,macroSVG,emblem,boxSVG,hydrate,COLOURS,CRAFT_COLOUR,mix,lum};
if(document.readyState!=="loading")hydrate(); else document.addEventListener("DOMContentLoaded",()=>hydrate());
})();
