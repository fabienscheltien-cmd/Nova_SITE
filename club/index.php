<?php
/* NOVA CLUB — la porte d'entrée : console, séquence, puis invitation */
require __DIR__ . '/app/bootstrap.php';
header('Cache-Control: no-store');
try { $me = current_member(); } catch (Throwable $e) { $me = null; }
$hh = (int)date('G'); $salut = ($hh >= 18 || $hh < 5) ? 'Bonsoir.' : 'Bonjour.';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
<title>NOVA Club — NOVA Serenity</title>
<link rel="icon" type="image/png" sizes="32x32" href="assets/favicon-32.png"/>
<link rel="icon" type="image/png" sizes="192x192" href="assets/favicon-192.png"/>
<link rel="apple-touch-icon" href="assets/apple-touch-icon.png"/>
<meta name="description" content="NOVA Club — l'espace privé des événements NOVA Serenity. Entrée sur invitation."/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,400;6..96,500&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300&family=Nunito+Sans:wght@300;400;600&family=Barlow+Condensed:wght@500;600&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--primary-dark:#0e1a2b;--primary-blue:#3b5998;--accent-gold:#c9a961;--light-bg:#f8f9fa;--white:#fff;--text-dark:#152a47;--text-light:#152a47;--card-dark:#0f213b;--line:#e5e7eb}
html{scroll-padding-top:110px}
body{font-family:'Segoe UI','Roboto','Inter',-apple-system,BlinkMacSystemFont,sans-serif;line-height:1.6;color:var(--text-dark);overflow-x:hidden;background:var(--white)}
a{color:var(--primary-blue)}
img{max-width:100%;height:auto}
.visually-hidden{position:absolute!important;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
.skip{position:absolute;left:-9999px;top:0;z-index:2000;background:var(--accent-gold);color:var(--primary-dark);padding:.8rem 1.2rem;font-weight:600}
.skip:focus{left:0}
header>nav{position:fixed;width:100%;background:rgba(10,22,40,.97);backdrop-filter:blur(10px);padding:1rem 0;z-index:1000;box-shadow:0 2px 10px rgba(0,0,0,.1)}
.nav-container{max-width:1400px;margin:0 auto;padding:0 2rem;display:flex;justify-content:space-between;align-items:center;position:relative}
.logo{display:flex;align-items:center;position:absolute;left:2rem}
.logo img{height:60px;width:auto}
.nav-links{display:flex;gap:1.8rem;list-style:none;margin:0 auto}
.nav-links a{color:var(--white);text-decoration:none;font-size:.95rem;transition:color .3s;position:relative}
.nav-links a:hover,.nav-links a[aria-current]{color:var(--accent-gold)}
.nav-links a::after{content:'';position:absolute;bottom:-5px;left:0;width:0;height:2px;background:var(--accent-gold);transition:width .3s}
.nav-links a:hover::after,.nav-links a[aria-current]::after{width:100%}
.nav-linkedin{position:absolute;right:2rem;display:flex;align-items:center;gap:.6rem;color:var(--white);text-decoration:none;font-size:.95rem;transition:color .3s}
.nav-linkedin:hover{color:var(--accent-gold)}
a:focus-visible,button:focus-visible,summary:focus-visible{outline:2px solid var(--accent-gold);outline-offset:4px}
.page-hero{background:linear-gradient(135deg,#0a1628 0%,#1e3a5f 100%);color:var(--white);padding:9rem 2rem 4rem;text-align:center}
.page-hero .inner{max-width:900px;margin:0 auto}
.page-hero h1{font-size:2.8rem;font-weight:300;line-height:1.2;margin-bottom:1.2rem}
.page-hero .lead{font-size:1.15rem;opacity:.88;max-width:720px;margin:0 auto}
.breadcrumb{font-size:.9rem;margin-bottom:1.5rem;opacity:.85}
.breadcrumb ol{list-style:none;display:flex;flex-wrap:wrap;justify-content:center;gap:.4rem}
.breadcrumb li+li::before{content:'/';margin-right:.4rem;opacity:.6}
.breadcrumb a{color:var(--accent-gold);text-decoration:none}
.meta{display:flex;flex-wrap:wrap;justify-content:center;gap:.3rem 1.5rem;font-size:.95rem;opacity:.85;margin-top:1rem}
.cat{align-self:flex-start;display:inline-block;padding:.25rem .9rem;border-radius:50px;font-size:.85rem;font-weight:600;color:var(--primary-dark);background:var(--cat,#c9a961)}
.btn{padding:.9rem 2rem;border-radius:50px;font-size:1rem;text-decoration:none;display:inline-block;transition:all .3s;border:0;cursor:pointer}
.btn-primary{background:var(--accent-gold);color:var(--primary-dark);font-weight:600}
.btn-primary:hover{background:#d4b36a;transform:translateY(-2px);box-shadow:0 5px 20px rgba(201,169,97,.4)}
.btn-ghost{background:transparent;color:var(--white);border:1px solid rgba(255,255,255,.4)}
.btn-ghost:hover{border-color:var(--accent-gold);color:var(--accent-gold)}
footer{background:var(--primary-dark);color:var(--white);padding:3rem 2rem 1rem;text-align:center}
.footer-tagline{font-size:1.2rem;color:var(--accent-gold);font-style:italic;margin-bottom:1rem}
.footer-nav ul{list-style:none;display:flex;flex-wrap:wrap;justify-content:center;gap:.5rem 1.8rem;margin-top:1.6rem}
.footer-nav a{color:rgba(255,255,255,.85);text-decoration:none;font-size:.95rem}
.footer-nav a:hover{color:var(--accent-gold)}
.footer-bottom{margin-top:2rem;padding-top:2rem;border-top:1px solid rgba(255,255,255,.1);font-size:.9rem;opacity:.8}
.footer-bottom p{margin-bottom:.4rem}
.footer-bottom a{color:var(--accent-gold);text-decoration:none}
.footer-bottom a:hover{text-decoration:underline}
.footer-logo{margin-bottom:1.2rem}
@media (max-width:1250px){.nav-linkedin-text{display:none}}

.footer-facts-wrap{max-width:1100px;margin:2.2rem auto 0;padding-top:2rem;border-top:1px solid rgba(255,255,255,.1);text-align:left}
.footer-facts-title{color:#c9a961;font-weight:600;font-size:1rem;margin:0 0 1.2rem}
.footer-facts{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.3rem 2.2rem;margin:0}
.footer-facts div{border-left:2px solid rgba(201,169,97,.35);padding-left:.9rem}
.footer-facts dt{color:#c9a961;font-size:.82rem;font-weight:600;margin-bottom:.25rem}
.footer-facts dd{margin:0;color:rgba(255,255,255,.82);font-size:.9rem;line-height:1.5}
.footer-facts a{color:#fff;text-decoration:underline;text-decoration-color:rgba(201,169,97,.6);text-underline-offset:3px}
.footer-facts a:hover{color:#c9a961}
@media (max-width:900px){.footer-facts{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:560px){.footer-facts{grid-template-columns:1fr}}

@media (max-width:900px){
.nav-container{flex-wrap:wrap;row-gap:.75rem;padding:0 1rem}
.logo{position:static}.logo img{height:44px}
.nav-linkedin{position:static;margin-left:auto}
.nav-links{order:3;width:100%;margin:0;gap:1.25rem;overflow-x:auto;white-space:nowrap;padding-bottom:.25rem;scrollbar-width:none}
.nav-links::-webkit-scrollbar{display:none}
.page-hero{padding-top:10rem}
.page-hero h1{font-size:2rem}
}
@media (prefers-reduced-motion:reduce){*{transition:none!important;animation:none!important}html{scroll-behavior:auto}}

.nova-lockup{display:inline-flex;flex-direction:column;align-items:flex-start;gap:3px;color:#8fb6dc;--nl-h:40px}
.nova-lockup .nl-mark{display:block;height:var(--nl-h);width:auto}
.nova-lockup .nl-word{display:block;height:calc(var(--nl-h) * 860 / 1482);width:auto}
.nova-lockup.grand{--nl-h:56px}
.logo a{display:inline-flex;text-decoration:none}
@media (min-width:901px){.nav-container{min-height:70px}.nav-container>.logo{top:50%;transform:translateY(-50%)}}
@media (max-width:1250px){.nav-linkedin-text{display:inline}}
@media (max-width:1000px){.nav-linkedin-text{display:none}}
.nav-toggle{display:none;background:none;border:1px solid rgba(255,255,255,.25);color:#fff;border-radius:8px;padding:.45rem .7rem;font-size:1.2rem;line-height:1;cursor:pointer}
@media (max-width:600px){.page-hero{padding-left:1.1rem;padding-right:1.1rem}.article-wrap{padding:2.5rem 1.1rem}.related,.blog-list{padding-left:1.1rem!important;padding-right:1.1rem!important}.card{padding:1.5rem}}
@media (max-width:900px){
.nova-lockup{--nl-h:32px}
.nav-toggle{display:inline-block;order:2;margin-left:.8rem}
.nav-linkedin{order:1;margin-left:auto!important}
.nav-links{display:none!important;flex-direction:column;align-items:flex-start;gap:.2rem!important;overflow:visible!important;white-space:normal!important;padding:.8rem 0 .4rem!important;border-top:1px solid rgba(255,255,255,.12)}
.nav-links.ouvert{display:flex!important}
.nav-links a{display:block;padding:.55rem 0;font-size:1rem}
.nav-links a::after{display:none}
}
.footer-nap{margin-top:1.8rem;font-size:.88rem;opacity:.75}.footer-nap a{color:inherit;text-decoration:none}
body{color:#152a47}
.p-hero{position:relative;overflow:hidden;background:radial-gradient(120% 140% at 85% 20%,#1e3a5f 0%,#0e1a2b 55%,#0a1320 100%);color:#fff;padding:9.5rem 2rem 5rem}
.p-hero::after{content:'';position:absolute;right:-12%;top:-30%;width:60%;aspect-ratio:1;border-radius:50%;background:radial-gradient(circle,var(--pole) 0%,transparent 62%);opacity:.14;pointer-events:none}
.p-hero .wrap{position:relative;z-index:1;display:grid;grid-template-columns:1.15fr .85fr;gap:3rem;align-items:center}
.brand-tag .bt{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.brand-tag{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem 1rem;border-radius:50px;background:rgba(255,255,255,.06);border:1px solid var(--pole);color:var(--pole);font-weight:600;font-size:.88rem;letter-spacing:.5px}
.p-hero h1{font-size:clamp(2rem,3.6vw,3rem);font-weight:300;line-height:1.15;margin:1.2rem 0 1rem}
.p-hero h1 strong{font-weight:600;color:var(--pole)}
.p-hero .lead{font-size:1.12rem;opacity:.85;max-width:620px}
.p-hero .ctas{display:flex;flex-wrap:wrap;gap:.8rem;margin-top:2rem}
.emblem{position:relative;justify-self:center;width:min(360px,80vw);aspect-ratio:1}
.emblem .ring{position:absolute;inset:0;border-radius:50%;border:1px solid rgba(255,255,255,.12)}
.emblem .ring.r2{inset:12%;border-color:rgba(255,255,255,.08)}
.emblem .ring.r3{inset:24%;border:2px solid var(--pole);box-shadow:0 0 60px -10px var(--pole)}
.emblem .core{position:absolute;inset:32%;border-radius:50%;background:#0f213b;display:grid;place-items:center}
.emblem .core svg{width:46%;height:46%;fill:none;stroke:var(--pole);stroke-width:1.5;stroke-linecap:round;stroke-linejoin:round}
.emblem .chip{position:absolute;padding:.45rem .9rem;border-radius:50px;background:rgba(15,33,59,.92);border:1px solid rgba(255,255,255,.14);font-size:.82rem;white-space:nowrap;backdrop-filter:blur(6px)}
.emblem .chip::before{content:'';display:inline-block;width:7px;height:7px;border-radius:50%;background:var(--pole);margin-right:.5rem;vertical-align:middle}
.emblem .c1{top:6%;left:-8%}.emblem .c2{top:44%;right:-14%}.emblem .c3{bottom:6%;left:0}
.proofband{background:#fff;border-bottom:1px solid #e8ecf1}
.proofband ul{list-style:none;max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(5,1fr)}
.proofband li{padding:1.6rem 1rem;text-align:center;border-left:1px solid #e8ecf1}
.proofband li:first-child{border-left:0}
.proofband strong{display:block;font-size:2rem;font-weight:300;color:#0e1a2b;line-height:1}
.proofband strong em{font-style:normal;color:var(--accent-gold)}
.proofband span{display:block;margin-top:.4rem;font-size:.85rem;color:#152a47}
.proofband small{display:block;font-size:.78rem;opacity:.7}
.band{padding:5rem 2rem;background:#fff}
.band.alt{background:#f5f7fa}
.band .wrap,.p-hero .wrap{max-width:1200px;margin:0 auto}
.band h2{display:flex;align-items:center;gap:.9rem;font-size:clamp(1.5rem,2.4vw,2rem);color:#0e1a2b;margin-bottom:2.2rem;line-height:1.25}
.band h2 .bar{width:6px;height:1.6em;border-radius:3px;background:linear-gradient(var(--pole),var(--accent-gold));flex-shrink:0}
.items{list-style:none;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem}
@media (max-width:900px){.items{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (max-width:560px){.items{grid-template-columns:1fr}}
.card-item{display:flex;gap:.9rem;align-items:flex-start;background:#fff;border:1px solid #e3e8ef;border-radius:14px;padding:1.1rem 1.2rem;font-size:1rem;line-height:1.55;transition:transform .25s,box-shadow .25s}
.band:not(.alt) .card-item{background:#f8fafc}
.card-item:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(14,26,43,.08)}
.ico{width:24px;height:24px;flex-shrink:0;fill:none;stroke:var(--pole-dark);stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.ico.check{padding:3px;border-radius:50%;background:var(--pole-soft);width:26px;height:26px}
.feats{list-style:none;display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1.3rem}
.feat{background:#fff;border:1px solid #e3e8ef;border-top:3px solid var(--pole);border-radius:16px;padding:1.6rem 1.5rem;transition:transform .25s,box-shadow .25s}
.feat:hover{transform:translateY(-4px);box-shadow:0 14px 30px rgba(14,26,43,.1)}
.feat .ico{width:42px;height:42px;padding:9px;border-radius:12px;background:var(--pole-soft);margin-bottom:1rem}
.feat h3{font-size:1.1rem;color:#0e1a2b;margin-bottom:.5rem}
.feat p{font-size:.96rem;line-height:1.6;color:#152a47}
.steps{list-style:none;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.2rem;counter-reset:s;position:relative}
.step{position:relative;background:#fff;border:1px solid #e3e8ef;border-radius:16px;padding:1.6rem 1.4rem 1.4rem}
.step-num{display:grid;place-items:center;width:44px;height:44px;border-radius:50%;background:#0e1a2b;color:var(--accent-gold);font-weight:700;font-size:1.1rem;margin-bottom:1rem;box-shadow:0 0 0 5px var(--pole-soft)}
.step p{font-size:1rem;line-height:1.55}
.panel{background:#fff;border:1px solid #e3e8ef;border-left:4px solid var(--pole);border-radius:14px;padding:1.6rem 1.8rem}
.band.alt .panel{background:#fff}
.panel p{font-size:1.06rem;line-height:1.8}
.toolgrid{list-style:none;display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:1.2rem}
.toolcard{display:block;background:#0f213b;border:1px solid rgba(255,255,255,.08);border-top:3px solid var(--t);border-radius:16px;padding:1.5rem;color:rgba(255,255,255,.85);text-decoration:none;transition:transform .25s}
.toolcard:hover{transform:translateY(-4px)}
.toolcard .tl{display:block;height:22px;width:auto;margin-bottom:.3rem;--nova-letter:#8fb6dc}
.toolcard b{display:block;color:var(--t);letter-spacing:3px;font-size:1.1rem}
.toolcard i{display:block;font-style:normal;color:var(--t);text-transform:uppercase;font-size:.75rem;letter-spacing:.5px;margin:.8rem 0 .4rem}
.toolcard span{font-size:.93rem;line-height:1.55}
.toolcard em{display:block;margin-top:1rem;font-style:normal;color:var(--t);font-weight:600;font-size:.88rem}
.faq-block .faq-item{background:#fff;border:1px solid #e3e8ef;border-radius:12px;margin-bottom:.8rem}
.faq-block summary{cursor:pointer;padding:1.15rem 1.4rem;font-weight:600;color:#0e1a2b;list-style:none;display:flex;justify-content:space-between;gap:1rem}
.faq-block summary::-webkit-details-marker{display:none}
.faq-block summary::after{content:'+';color:var(--accent-gold);font-size:1.3rem;line-height:1}
.faq-block details[open] summary::after{content:'−'}
.faq-block details p{padding:0 1.4rem 1.2rem;margin:0}
.reads{list-style:none;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem}
.reads a{display:flex;flex-direction:column;gap:.6rem;height:100%;background:#fff;border:1px solid #e3e8ef;border-radius:14px;padding:1.2rem 1.3rem;color:#0e1a2b;text-decoration:none;font-weight:600;line-height:1.4;transition:transform .25s,box-shadow .25s}
.reads a small{font-weight:400;font-size:.8rem;color:var(--accent-gold);text-transform:uppercase;letter-spacing:1px}
.reads a:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(14,26,43,.08)}
.cta-band{background:linear-gradient(135deg,#0e1a2b,#1e3a5f);color:#fff;padding:4rem 2rem}
.cta-band .wrap{max-width:1000px;margin:0 auto;display:flex;flex-wrap:wrap;gap:1.5rem;align-items:center;justify-content:space-between}
.cta-band h2{font-size:1.7rem;font-weight:300}
.cta-band p{opacity:.85;margin-top:.4rem}
.cta-band a.tel{color:var(--accent-gold);text-decoration:none}
.others{background:#fff;padding:4rem 2rem 5rem}
.others .wrap{max-width:1200px;margin:0 auto}
.others h2{text-align:center;font-size:1.5rem;color:#0e1a2b;margin-bottom:1.6rem}
.others ul{list-style:none;display:grid;grid-template-columns:repeat(3,1fr);gap:1.2rem}
.others a{display:flex;gap:1rem;align-items:center;background:#f5f7fa;border-radius:16px;padding:1.3rem 1.4rem;text-decoration:none;color:#0e1a2b;border-top:3px solid var(--oc);transition:transform .25s}
.others a:hover{transform:translateY(-3px)}
.others a svg{width:36px;height:36px;padding:7px;border-radius:10px;background:#0e1a2b;fill:none;stroke:var(--oc);stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0}
.others small{display:block;font-size:.8rem;opacity:.7}
@media (max-width:980px){.p-hero .wrap{grid-template-columns:1fr}.emblem{width:min(300px,70vw);margin-top:1rem}.proofband ul{grid-template-columns:repeat(3,1fr)}.proofband li:nth-child(4){border-left:0}.others ul{grid-template-columns:1fr}}
@media (max-width:560px){.proofband ul{grid-template-columns:repeat(2,1fr)}.emblem .chip{display:none}.emblem{width:min(220px,60vw)}.p-hero{padding:8.5rem 1.2rem 3.5rem}.band{padding:3.5rem 1.2rem}}
@media (prefers-reduced-motion:reduce){.card-item,.feat,.toolcard,.reads a,.others a{transition:none}}

.fond-store{position:relative;overflow:hidden;background-color:#07111f;
 background-image:
  radial-gradient(55% 22% at 90% 3%,rgba(201,169,97,.16),transparent 70%),
  radial-gradient(45% 20% at 6% 30%,rgba(64,132,200,.14),transparent 70%),
  radial-gradient(55% 20% at 96% 62%,var(--pole-glow),transparent 70%),
  radial-gradient(45% 18% at 4% 88%,rgba(201,169,97,.10),transparent 70%),
  linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),
  linear-gradient(90deg,rgba(255,255,255,.035) 1px,transparent 1px);
 background-size:auto,auto,auto,auto,72px 72px,72px 72px;color:#dce7f2}
.fond-store::before,.fond-store::after{content:'';position:absolute;pointer-events:none;border-radius:50%;z-index:0}
.fond-store::before{width:1100px;height:1100px;right:-620px;top:-420px;border:1px solid transparent;border-left-color:rgba(201,169,97,.35);border-bottom-color:rgba(201,169,97,.18);transform:rotate(-12deg)}
.fond-store::after{width:900px;height:900px;left:-560px;top:38%;border:1px solid transparent;border-right-color:rgba(143,182,220,.25);border-top-color:rgba(143,182,220,.12)}
.fond-store .orbite-point{position:absolute;width:10px;height:10px;border-radius:50%;background:#c9d6e4;box-shadow:0 0 14px rgba(201,214,228,.8);z-index:1;pointer-events:none}
.fond-store>section{position:relative;z-index:1;background:transparent!important}
.fond-store>section.alt{background:rgba(255,255,255,.022)!important}
/* chiffres clés : bandeau vitré */
.fond-store .proofband{border-bottom:1px solid rgba(255,255,255,.07)}
.fond-store .proofband li{border-left-color:rgba(255,255,255,.08)}
.fond-store .proofband strong{color:#fff}
.fond-store .proofband span{color:var(--pole)}
.fond-store .proofband small{color:#c9d6e4;opacity:.75}
/* titres de section avec numéro */
.fond-store .band h2{color:#fff}
.fond-store .band h2 .num{font-size:.8rem;font-weight:700;letter-spacing:2px;color:var(--pole);border:1px solid var(--pole);border-radius:50px;padding:.2rem .6rem;margin-right:.2rem}
.fond-store .band h2 .bar{display:none}
/* cartes vitrées */
.fond-store .card-item,.fond-store .feat,.fond-store .step,.fond-store .panel,.fond-store .faq-block .faq-item,.fond-store .reads a{
  background:rgba(15,33,59,.72)!important;border:1px solid rgba(255,255,255,.08);color:#dce7f2;backdrop-filter:blur(4px)}
.fond-store .card-item:hover,.fond-store .feat:hover,.fond-store .reads a:hover{box-shadow:0 14px 30px rgba(0,0,0,.35);border-color:var(--pole)}
.fond-store .feat{border-top:3px solid var(--pole)}
.fond-store .panel{border-left:4px solid var(--pole)}
.fond-store .feat h3,.fond-store .faq-block summary,.fond-store .reads a{color:#fff}
.fond-store .feat p,.fond-store .panel p,.fond-store .step p,.fond-store .faq-block details p,.fond-store .card-item{color:#c9d6e4}
.fond-store .ico{stroke:var(--pole)}
.fond-store .ico.check,.fond-store .feat .ico{background:var(--pole-soft)}
.fond-store .step-num{background:#07111f;border:1px solid var(--pole);box-shadow:0 0 0 5px var(--pole-soft)}
.fond-store .steps::before{content:'';position:absolute;left:3%;right:3%;top:46px;height:1px;background:linear-gradient(90deg,transparent,var(--pole),transparent);opacity:.5}
.fond-store .step{z-index:1}
/* outils : cartes de même hauteur */
.toolgrid{grid-template-columns:repeat(auto-fill,minmax(260px,1fr))!important;align-items:stretch}
.toolgrid li{display:flex}
.toolcard{display:flex!important;flex-direction:column;width:100%;height:100%;background:rgba(15,33,59,.85)!important}
.toolcard span{flex:1}
.toolcard .tl{align-self:flex-start}
/* autres pôles */
.fond-store .others h2{color:#fff}
.fond-store .others a{background:rgba(15,33,59,.72);color:#fff;border:1px solid rgba(255,255,255,.08);border-top:3px solid var(--oc)}
.fond-store .others small{color:#c9d6e4}
.fond-store .others a svg{background:#07111f}
.fond-store .others{background:transparent}

.themes-assoc .chips{list-style:none;display:flex;flex-wrap:wrap;gap:.7rem}
.themes-assoc .chips a{display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.1rem;border-radius:50px;border:1px solid rgba(255,255,255,.14);background:rgba(15,33,59,.72);color:#dce7f2;text-decoration:none;font-size:.93rem;transition:border-color .25s,color .25s,translate .25s}
.themes-assoc .chips a::before{content:'';width:7px;height:7px;border-radius:50%;background:var(--pole)}
.themes-assoc .chips a:hover,.themes-assoc .chips a:focus-visible{border-color:var(--pole);color:#fff;translate:0 -2px}
.themes-assoc p.intro{color:#c9d6e4;margin:-1rem 0 1.4rem}
</style>
<style>
html,body{height:100%}
body{background:#07111f}

/* textes du header toujours visibles (burger seulement sur petit mobile) */
@media (min-width:641px){
 .nav-container{flex-wrap:wrap!important;row-gap:.6rem;padding:0 1.2rem}
 .logo{position:static!important;transform:none!important}
 .nav-toggle{display:none!important}
 .nav-links{display:flex!important;flex-direction:row!important;order:3;width:100%;justify-content:center;flex-wrap:wrap;gap:.4rem 1.4rem!important;padding:.3rem 0 0!important;border-top:0!important;overflow:visible!important}
 .nav-links a{padding:0!important;font-size:.95rem}
 .nav-linkedin{position:static!important;order:1;margin-left:auto!important}
 .nav-linkedin-text{display:inline!important}
}
@media (min-width:1150px){
 .nav-container{flex-wrap:nowrap!important}
 .logo{position:absolute!important;left:2rem;top:50%;transform:translateY(-50%)!important}
 .nav-links{width:auto;order:0;margin:0 auto!important;padding:0!important}
 .nav-linkedin{position:absolute!important;right:2rem}
}
@media (max-width:640px){.nav-linkedin-text{display:inline!important}}
</style>
<style>
  :root{
    --navy-800:#081223;
    --ice:#e9f0fa;
    --nova-blue:#7fb3ea;
    --header-h:102px;
  }
  
  html,body{height:100%}
  body{background:#07111f;overflow:hidden}

  /* ---------- Scène ---------- */
  .stage{
    position:fixed;inset:0;
    display:flex;flex-direction:column;align-items:center;
    padding-top:calc(var(--header-h) + env(safe-area-inset-top,0px));
    background:transparent;
    overflow:hidden;
  }
            
  @media (orientation:portrait){ .stage{justify-content:center;padding-bottom:8vh} }
  .club-logo{position:relative;width:clamp(170px,19vw,270px);height:auto;margin-top:clamp(4px,1.6vh,18px)}

  /* ---------- Console ---------- */
  .console-panel{
    --pw:min(600px, 92vw, calc((100vh - var(--header-h) - 150px) * 1.157));
    position:relative;width:var(--pw);aspect-ratio:950/821;
    margin-top:clamp(10px,2.5vh,28px);
    filter:drop-shadow(0 30px 40px rgba(0,0,0,.65)) drop-shadow(0 6px 10px rgba(0,0,0,.5));
  }
  .console-panel > img,.console-panel > svg{position:absolute;inset:0;width:100%;height:100%;pointer-events:none;user-select:none;-webkit-user-drag:none}
  .console-panel .glows{mix-blend-mode:screen}
  .console-panel .instr text{font-family:"Barlow Condensed","Arial Narrow",Arial,sans-serif;font-weight:600}
  .console-panel .axe{inset:auto;left:48.21%;top:31.67%;width:2.84%;height:auto}
  .lamp{opacity:0}
  #needle{transform-box:view-box;transform-origin:471px 273px;transform:rotate(-50.5deg)}
  .lever{transform-origin:49.47% 64.31%;transform:perspective(900px) rotateX(0deg);will-change:transform}
  .switch{
    position:absolute;left:35.26%;top:46.4%;width:28.42%;height:36.2%;
    border:0;background:none;cursor:grab;touch-action:none;border-radius:12px;
  }
  .switch:active{cursor:grabbing}
  .switch:focus-visible{outline:2px solid var(--nova-blue);outline-offset:4px}

  /* ---------- Verre / éclats ---------- */
  #shards{position:fixed;inset:0;z-index:45;pointer-events:none;perspective:900px}
  .shard{position:absolute;top:0;left:0;will-change:transform}
  .shard .inner{position:absolute;top:0;left:0}
  .shard > svg{position:absolute;inset:0;overflow:visible}
  .cracks-live{position:absolute;top:0;left:0;pointer-events:none;overflow:hidden}

  /* ---------- Fumée ---------- */
  #smoke{position:fixed;inset:0;width:100%;height:100%;z-index:40;pointer-events:none;opacity:0;transition:opacity .2s}
  body.running #smoke{opacity:1}
  #smoke.over{z-index:65}
  /* fond noir posé sous les vidéos : la console ne réapparaît jamais */
  #blackout{position:fixed;inset:0;z-index:48;background:#000;opacity:0;pointer-events:none}
  #blackout.on{opacity:1}

  /* ---------- Vidéos ---------- */
  #neon{
    position:fixed;inset:0;width:100%;height:100%;z-index:50;
    object-fit:contain;background:#000;
    opacity:0;transition:opacity .6s ease;pointer-events:none;
  }
  #cine{
    position:fixed;inset:0;width:100%;height:100%;z-index:60;
    object-fit:cover;background:#000;opacity:0;pointer-events:none;
  }
  /* éclair néon qui ouvre sur la vidéo d'entrée */
  #flash{
    position:fixed;inset:0;z-index:66;pointer-events:none;opacity:0;
    background:radial-gradient(circle at 50% 48%,#fff 0%,#ffe1da 12%,#ff5a45 34%,#e0142b 58%,#6d0314 82%,#1a0005 100%);
    mix-blend-mode:normal;
  }
  .seq-btn.sound{right:auto;left:max(20px,env(safe-area-inset-left,0px))}
  @media (orientation:portrait){ #cine{object-fit:contain} }
  #neon.on,#cine.on{opacity:1}
  .seq-btn{
    position:fixed;right:max(20px,env(safe-area-inset-right,0px));
    bottom:calc(20px + env(safe-area-inset-bottom,0px));
    z-index:70;padding:9px 16px;border-radius:999px;
    background:rgba(0,0,0,.45);color:rgba(255,255,255,.8);
    border:1px solid rgba(255,255,255,.25);font:inherit;font-size:13px;cursor:pointer;
    opacity:0;pointer-events:none;transition:opacity .4s;
  }
  .seq-btn.on{opacity:1;pointer-events:auto}
  .seq-btn:focus-visible{outline:2px solid var(--nova-blue);outline-offset:3px}

  /* header du site : c'est lui qui se brise (au-dessous de la fumée et des éclats) */
  header.site-header{position:fixed;top:0;left:0;right:0;z-index:30}
  header.site-header>nav{position:relative;z-index:auto}
  /* fond quadrillé du site, posé derrière la scène */
  .stage > .fond-store{position:absolute;inset:0;min-height:0;overflow:hidden;z-index:0;pointer-events:none}
  .stage > .club-logo,.stage > .console-panel{z-index:1}
</style>
<style>
/* ---------- invitation : texte + adresse e-mail sur l'image de fin ---------- */
.invitation{position:fixed;inset:0;z-index:68;display:flex;align-items:flex-end;justify-content:center;
  padding:0 1.2rem calc(clamp(2rem,8vh,5rem) + env(safe-area-inset-bottom,0px));
  background:linear-gradient(180deg,rgba(6,5,5,0) 18%,rgba(6,5,5,.6) 48%,rgba(6,5,5,.9) 100%);
  opacity:0;visibility:hidden;transition:opacity 1.6s ease,visibility 0s linear 1.6s}
.invitation.on{opacity:1;visibility:visible;transition:opacity 1.6s ease}
.inv-card{width:100%;max-width:560px;text-align:center;color:#efe8da;transform:translateY(14px);transition:transform 1.6s cubic-bezier(.2,.7,.2,1)}
.invitation.on .inv-card{transform:none}
.inv-kicker{font:500 .8rem "Barlow Condensed","Arial Narrow",sans-serif;letter-spacing:.55em;text-transform:uppercase;color:#b89a5e;margin:0 0 .9rem}
.inv-title{font:300 clamp(2.4rem,6vw,3.6rem)/1.05 "Cormorant Garamond",Georgia,serif;color:#f3ecdc;margin:0;text-shadow:0 2px 24px rgba(0,0,0,.6)}
.inv-lead{font:italic 300 clamp(1.15rem,2.6vw,1.45rem)/1.4 "Cormorant Garamond",Georgia,serif;color:#e2d9c6;margin:.7rem auto 1.8rem;max-width:30ch}
.inv-form{display:flex;gap:.6rem;align-items:stretch;max-width:470px;margin:0 auto}
.inv-form input[type=email]{flex:1;min-width:0;padding:.95rem .2rem .7rem;background:transparent;border:0;border-bottom:1px solid rgba(184,154,94,.7);
  color:#f3ecdc;font:300 1.05rem "Nunito Sans",system-ui,sans-serif;letter-spacing:.02em;border-radius:0}
.inv-form input[type=email]::placeholder{color:rgba(239,232,218,.45)}
.inv-form input[type=email]:focus{outline:none;border-bottom-color:#e9d9b0}
.inv-form button,.inv-enter{flex:none;padding:.85rem 1.4rem;background:transparent;border:1px solid #b89a5e;color:#e9d9b0;cursor:pointer;
  font:500 .9rem "Barlow Condensed","Arial Narrow",sans-serif;letter-spacing:.3em;text-transform:uppercase;text-decoration:none;transition:background .3s,color .3s}
.inv-form button:hover,.inv-enter:hover{background:#b89a5e;color:#0b0b0d}
.inv-form button:disabled{opacity:.5;cursor:wait}
.inv-form button:focus-visible,.inv-enter:focus-visible,.inv-form input:focus-visible{outline:1px solid #e9d9b0;outline-offset:4px}
.inv-hp{position:absolute!important;left:-9999px!important;width:1px;height:1px;opacity:0}
.inv-msg{min-height:1.5em;margin:1rem auto 0;max-width:44ch;font:italic 300 1.1rem/1.45 "Cormorant Garamond",Georgia,serif;color:#e9d9b0}
.inv-msg.err{color:#ffb4a8}
.inv-done{display:none}
.inv-done.on{display:block;animation:invIn 1.2s ease both}
.inv-done .inv-lead{margin-bottom:1.2rem}
.inv-sign{display:inline-flex;align-items:center;gap:.8rem;margin-top:.4rem;font:italic 300 1.15rem "Cormorant Garamond",Georgia,serif;color:#b89a5e}
.inv-sign img{width:44px;height:44px}
.inv-rgpd{margin:1.4rem auto 0;max-width:52ch;font:300 .72rem/1.5 "Nunito Sans",system-ui,sans-serif;color:rgba(239,232,218,.5)}
.inv-rgpd a{color:rgba(233,217,176,.75)}
.inv-enter{display:inline-block;padding:1rem 2.6rem}
.inv-out{display:block;margin-top:1.2rem;font:300 .8rem "Nunito Sans",system-ui,sans-serif;color:rgba(239,232,218,.55)}
@keyframes invIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
@media (max-width:520px){.inv-form{flex-direction:column;gap:.9rem}.inv-form button{padding:.95rem}}
@media (prefers-reduced-motion:reduce){.invitation,.inv-card{transition:opacity .4s ease}.inv-done.on{animation:none}}
/* --- ajustements : logo or miel, titre Art déco, champ e-mail --- */
.invitation{--miel:#c99854;--miel-clair:#efd3a0}
.invitation .inv-card{max-width:820px}
@media (min-width:720px){.inv-title{white-space:nowrap}}
.inv-logo{display:block;width:clamp(78px,7.5vw,104px);height:auto;margin:0 auto 1.1rem}
.inv-title{font-family:"Bodoni Moda","Didot","Bodoni 72",Georgia,serif;font-weight:400;text-transform:uppercase;letter-spacing:.14em;
  font-size:clamp(1.7rem,4.4vw,2.9rem);line-height:1.15;color:#f3ecdc}
.inv-orn{position:relative;width:min(260px,70%);height:14px;margin:.9rem auto 1rem}
.inv-orn::before,.inv-orn::after{content:"";position:absolute;top:50%;width:calc(50% - 16px);height:1px;background:var(--miel)}
.inv-orn::before{left:0}.inv-orn::after{right:0}
.inv-orn span{position:absolute;left:50%;top:50%;width:9px;height:9px;background:var(--miel);transform:translate(-50%,-50%) rotate(45deg)}
.inv-lead-deco{font:400 clamp(.78rem,1.5vw,.95rem)/1.7 "Barlow Condensed","Arial Narrow",sans-serif;font-style:normal;letter-spacing:.22em;text-transform:uppercase;
  color:var(--miel-clair);max-width:70ch;margin:0 auto 1.8rem}
.inv-form input[type=email]{border-bottom-color:rgba(201,152,84,.8)}
.inv-form input[type=email]:focus{border-bottom-color:var(--miel-clair)}
.inv-form button,.inv-enter{border-color:var(--miel);color:var(--miel-clair)}
.inv-form button:hover,.inv-enter:hover{background:var(--miel);color:#0b0b0d}
.inv-msg,.inv-sign{color:var(--miel-clair)}
/* saisie automatique du navigateur : pas de fond bleu, on garde le style du club */
.inv-form input[type=email]:-webkit-autofill,.inv-form input[type=email]:-webkit-autofill:hover,.inv-form input[type=email]:-webkit-autofill:focus{
  -webkit-text-fill-color:#f3ecdc;caret-color:#f3ecdc;-webkit-box-shadow:0 0 0 1000px rgba(0,0,0,0) inset;box-shadow:0 0 0 1000px rgba(0,0,0,0) inset;
  transition:background-color 100000s ease-in-out 0s,color 100000s ease-in-out 0s}
.inv-form input[type=email]:autofill{background:transparent!important;color:#f3ecdc}
</style>
</head>
<body style="--pole:#8fb6dc;--pole-dark:#3b5998;--pole-soft:rgba(143,182,220,.18);--pole-glow:rgba(64,132,200,.18)">


<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><symbol id="nova-mark" viewBox="0 0 4452 1482"><g fill="var(--nova-letter,#8fb6dc)" fill-rule="evenodd"><path d="M8 399C8 400 8 575 8 788L8 1177L10 1178C14 1179 147 1178 149 1177C150 1174 149 658 148 654C147 651 147 650 148 647C149 645 150 641 150 631C150 624 150 618 151 618C151 618 157 624 164 633C179 653 183 656 189 659C195 662 197 664 198 671C199 674 200 677 202 680C204 683 207 688 209 691C211 694 215 699 216 701C218 703 221 708 224 711C228 716 233 722 241 731C243 734 247 739 248 742C249 744 252 747 254 749C255 750 258 753 259 756C261 759 264 763 266 765C267 766 270 770 272 773C274 776 277 779 278 781C280 783 283 786 284 789C286 792 289 795 290 797C291 799 294 803 296 806C298 809 302 814 304 817C306 820 310 824 312 827C320 838 327 846 328 847C328 847 330 850 332 853C334 856 336 859 338 861C339 862 341 866 343 869C345 872 349 876 351 879C353 881 356 884 357 886C358 888 360 891 362 892C363 894 366 898 368 901C370 904 374 909 376 912C387 926 389 928 393 935C395 938 398 942 399 943C400 943 402 947 404 950C406 953 409 957 412 959C414 961 416 965 418 968C419 970 422 973 423 975C425 977 428 981 430 984C432 987 434 990 435 991C436 991 439 995 441 999C443 1002 446 1006 448 1007C449 1008 451 1012 454 1016C456 1019 459 1023 460 1025C462 1027 464 1030 466 1032C468 1037 480 1051 484 1054C485 1055 487 1058 489 1061C490 1064 493 1069 495 1072C497 1075 501 1079 503 1083C505 1086 508 1090 510 1092C513 1095 520 1105 525 1112C527 1115 530 1119 533 1121C535 1123 537 1127 538 1129C539 1130 542 1134 544 1137C546 1139 549 1143 550 1145C551 1147 554 1151 557 1154C559 1157 563 1163 565 1166C567 1169 570 1173 572 1175L576 1178L641 1178C692 1178 706 1178 707 1177C708 1176 708 1125 708 905C708 757 708 582 708 518C709 454 709 400 709 400C709 399 694 399 639 399L570 399L570 588C570 731 570 784 571 808C573 836 573 843 571 865C570 879 569 902 569 917C569 932 568 944 568 944C567 945 555 930 546 918C540 910 533 904 529 901C527 900 526 899 526 897C526 894 522 888 519 886C517 885 515 882 514 880C512 874 510 871 494 851C493 848 489 843 486 840C477 828 472 821 469 820C468 819 466 815 464 812C462 808 459 803 457 800C455 798 452 794 450 792C449 790 446 786 444 783C441 781 438 776 435 772C433 769 428 763 425 758C422 754 418 748 416 746C412 741 405 732 398 722C395 719 391 714 389 710C381 700 357 667 346 654C344 651 337 642 332 636C327 629 322 621 320 619C313 610 296 587 293 583C291 580 285 573 281 567C276 560 270 553 268 550C266 547 260 540 256 535C252 529 246 522 244 519C242 516 237 509 233 504C228 499 223 492 221 488C218 485 212 477 208 471C190 447 176 429 171 422C165 413 159 406 155 402L152 399L81 399C41 399 9 399 8 399Z"/><path d="M1473 389C1451 390 1424 394 1408 397C1396 400 1385 403 1378 405C1374 406 1369 407 1368 408C1365 408 1354 412 1340 417C1338 418 1335 419 1333 419C1325 422 1288 441 1276 449C1273 451 1270 453 1270 453C1270 453 1267 455 1264 457C1261 460 1258 462 1257 462C1257 462 1254 463 1252 465C1250 467 1247 469 1245 470C1243 471 1241 472 1240 474C1239 475 1236 477 1234 478C1230 481 1228 483 1216 494C1213 497 1206 503 1202 507C1198 511 1193 515 1192 517C1191 518 1188 522 1184 525C1181 529 1179 532 1179 532C1179 533 1176 536 1174 538C1171 541 1168 545 1166 548C1165 550 1163 553 1161 554C1160 556 1157 559 1155 562C1154 565 1151 569 1149 571C1147 574 1144 578 1144 580C1143 582 1140 587 1137 591C1134 595 1132 599 1132 600C1132 601 1130 604 1128 608C1121 620 1119 626 1118 631C1117 634 1115 639 1113 642C1111 645 1109 651 1108 654C1107 657 1104 666 1102 673C1097 690 1096 692 1094 708C1093 715 1091 728 1089 737L1086 753L1086 790C1086 821 1086 827 1088 835C1090 848 1092 858 1094 870C1096 886 1098 897 1103 912C1105 916 1107 923 1109 927C1110 931 1112 936 1114 939C1115 942 1117 947 1118 951C1120 955 1122 961 1124 963C1126 966 1128 971 1130 974C1132 981 1134 983 1137 989C1139 991 1142 996 1143 999C1146 1005 1151 1012 1159 1023C1161 1026 1165 1030 1166 1033C1168 1036 1171 1040 1173 1042C1175 1043 1178 1047 1180 1050C1182 1053 1184 1056 1185 1056C1186 1056 1188 1059 1191 1062C1195 1067 1217 1088 1229 1097C1232 1100 1237 1104 1239 1106C1241 1108 1244 1110 1244 1110C1244 1110 1247 1112 1251 1114C1254 1117 1257 1119 1258 1119C1259 1119 1261 1121 1264 1123C1267 1126 1269 1128 1270 1128C1270 1128 1273 1129 1275 1131C1278 1132 1280 1134 1280 1134C1280 1134 1283 1135 1287 1137C1300 1145 1334 1161 1345 1164C1346 1164 1352 1166 1359 1168C1371 1173 1378 1175 1387 1177C1390 1178 1396 1179 1399 1180C1431 1188 1493 1193 1529 1189C1535 1189 1544 1188 1550 1188C1587 1184 1631 1173 1671 1156C1675 1154 1688 1149 1690 1147C1691 1147 1695 1145 1699 1142C1704 1140 1708 1138 1709 1138C1709 1137 1715 1134 1721 1130C1737 1121 1759 1105 1769 1096C1791 1077 1808 1060 1820 1045C1825 1040 1830 1033 1832 1031C1835 1027 1848 1008 1856 995C1862 986 1873 965 1877 955C1879 950 1881 946 1882 945C1882 944 1883 941 1885 938C1886 935 1887 930 1888 929C1890 923 1898 897 1900 889C1907 859 1909 847 1911 821C1914 779 1912 738 1903 701C1898 680 1898 679 1889 653C1864 576 1804 499 1735 456C1717 444 1709 439 1691 430C1674 422 1639 409 1618 403C1602 399 1600 399 1593 397C1555 389 1514 386 1473 389ZM1524 518C1541 519 1556 522 1572 527C1575 528 1580 529 1583 530C1591 532 1599 535 1603 537C1606 539 1608 540 1608 540C1611 540 1648 559 1652 562C1653 564 1656 566 1658 567C1664 571 1671 576 1674 579C1676 581 1681 586 1687 591C1692 596 1698 602 1699 603C1700 605 1703 609 1705 610C1707 612 1710 616 1711 618C1712 621 1715 625 1717 627C1719 629 1722 633 1723 636C1725 639 1728 644 1730 647C1732 650 1734 655 1735 657C1737 660 1739 666 1742 670C1744 674 1747 679 1747 680C1747 682 1752 698 1755 705C1757 710 1758 715 1761 730C1761 733 1762 738 1763 740C1763 741 1764 746 1764 749C1764 752 1765 758 1766 762C1768 776 1768 801 1766 817C1761 843 1760 849 1759 857C1757 864 1757 866 1751 882C1750 886 1748 892 1747 895C1746 898 1745 902 1744 903C1744 904 1740 910 1737 917C1728 935 1722 944 1717 950C1715 953 1712 958 1711 960C1710 962 1707 965 1706 967C1704 968 1701 971 1699 974C1698 977 1695 980 1694 981C1692 982 1686 987 1680 993C1674 999 1669 1004 1668 1004C1668 1004 1665 1006 1663 1008C1660 1011 1657 1013 1657 1013C1656 1013 1654 1014 1652 1016C1649 1018 1638 1024 1620 1033C1569 1059 1502 1068 1450 1057C1423 1051 1404 1045 1391 1039C1382 1035 1368 1028 1366 1027C1341 1012 1330 1004 1310 984C1296 971 1279 949 1272 936C1270 933 1267 928 1265 926C1264 923 1263 921 1263 921C1263 921 1255 905 1253 900C1251 896 1243 874 1241 865C1239 858 1236 843 1234 830C1231 814 1231 765 1234 749C1239 718 1245 694 1254 674C1257 670 1259 664 1260 662C1267 647 1284 621 1301 602C1310 592 1346 561 1349 561C1349 561 1351 559 1354 558C1363 552 1387 540 1390 540C1390 540 1392 539 1393 538C1398 536 1417 529 1429 526C1442 523 1445 522 1450 522C1453 521 1456 520 1457 520C1460 518 1509 516 1524 518Z"/><path d="M3980 401C3979 402 3976 409 3974 416C3971 423 3967 432 3966 435C3964 439 3963 442 3963 443C3963 443 3961 446 3960 450C3955 461 3953 465 3950 474C3949 476 3946 483 3944 490C3941 496 3937 504 3936 509C3934 513 3932 519 3930 523C3928 527 3926 532 3925 535C3924 538 3921 544 3919 549C3917 554 3915 559 3915 559C3915 561 3909 576 3906 582C3904 586 3902 591 3901 595C3899 598 3897 604 3895 607C3892 613 3890 619 3888 626C3887 627 3885 632 3883 637C3880 642 3879 646 3879 646C3879 647 3872 664 3871 665C3870 666 3868 670 3867 675C3865 680 3862 688 3859 692C3857 696 3854 704 3853 710C3851 715 3848 722 3846 725C3845 728 3842 734 3841 737C3840 740 3837 747 3835 751C3832 758 3830 763 3828 770C3827 771 3825 775 3823 780C3821 784 3819 790 3817 794C3816 797 3814 803 3812 808C3810 812 3807 819 3805 823C3804 828 3801 835 3799 839C3797 844 3794 852 3791 857C3789 863 3787 869 3786 871C3785 873 3782 879 3780 885C3777 893 3775 898 3772 903C3772 904 3771 906 3771 907C3769 913 3764 924 3762 930C3760 933 3757 940 3756 945C3754 950 3751 956 3750 959C3747 964 3746 967 3742 978C3741 981 3739 987 3737 992C3734 996 3732 1002 3731 1006C3730 1009 3727 1015 3725 1020C3723 1024 3720 1030 3719 1034C3717 1037 3715 1043 3714 1046C3712 1049 3710 1055 3708 1059C3707 1063 3704 1070 3702 1073C3701 1076 3699 1079 3698 1081C3696 1087 3691 1100 3689 1104C3688 1107 3686 1113 3684 1117C3683 1121 3680 1128 3678 1132C3676 1136 3674 1141 3673 1143C3672 1145 3670 1151 3668 1156C3663 1167 3660 1174 3660 1176C3660 1178 3672 1178 3738 1178C3808 1178 3808 1178 3811 1174C3813 1169 3820 1151 3825 1138C3827 1133 3829 1126 3831 1123C3832 1120 3835 1114 3836 1110C3838 1105 3840 1098 3842 1095C3843 1092 3846 1084 3848 1079C3850 1073 3853 1065 3855 1061C3857 1057 3860 1051 3861 1047C3864 1037 3870 1026 3872 1025C3874 1024 4226 1024 4228 1025C4231 1026 4236 1036 4241 1050C4243 1056 4246 1063 4248 1066C4249 1069 4251 1074 4252 1077C4253 1080 4256 1087 4258 1092C4260 1097 4263 1104 4264 1108C4266 1111 4268 1117 4270 1122C4272 1126 4275 1132 4276 1136C4277 1140 4280 1146 4282 1150C4284 1154 4287 1161 4288 1165C4290 1170 4292 1175 4293 1176L4294 1178L4356 1178C4426 1179 4443 1178 4443 1176C4443 1174 4439 1165 4436 1159C4435 1157 4432 1150 4430 1144C4428 1138 4425 1131 4423 1127C4421 1123 4419 1117 4417 1112C4416 1108 4413 1102 4411 1098C4409 1094 4406 1087 4405 1083C4404 1079 4401 1072 4399 1068C4397 1064 4394 1058 4393 1054C4392 1050 4389 1045 4388 1042C4386 1039 4383 1033 4382 1028C4380 1022 4377 1016 4376 1014C4373 1007 4370 1001 4368 995C4367 992 4365 985 4363 981C4361 977 4358 970 4357 967C4356 964 4353 957 4351 953C4348 948 4346 941 4345 938C4343 935 4341 929 4339 926C4337 923 4334 916 4333 911C4332 907 4328 899 4326 894C4324 888 4321 882 4320 880C4319 877 4318 873 4316 870C4314 865 4312 861 4306 846C4305 843 4303 836 4301 832C4298 828 4296 823 4296 822C4296 820 4293 815 4291 811C4289 806 4286 799 4285 795C4283 790 4281 784 4278 780C4276 775 4274 769 4273 766C4269 756 4268 752 4265 747C4264 745 4262 740 4261 736C4259 732 4256 725 4254 720C4252 715 4249 709 4248 706C4246 701 4245 699 4241 692C4240 690 4238 685 4237 680C4236 676 4233 668 4230 663C4228 658 4225 652 4224 649C4223 646 4221 641 4219 637C4217 632 4214 624 4212 619C4210 613 4207 606 4206 603C4204 600 4201 593 4199 588C4197 583 4195 578 4194 578C4192 575 4191 572 4188 562C4186 557 4183 550 4182 547C4180 544 4177 538 4176 533C4174 528 4171 521 4170 518C4168 515 4166 509 4164 505C4163 501 4160 494 4158 489C4156 485 4153 479 4152 476C4150 469 4148 465 4145 461C4144 459 4142 453 4140 449C4139 445 4136 436 4134 431C4131 425 4128 417 4127 414C4124 404 4121 400 4119 399C4117 399 4086 399 4049 399L3982 399L3980 401ZM4055 594C4057 599 4060 607 4063 612C4065 617 4068 625 4070 630C4071 634 4074 641 4076 645C4078 650 4081 657 4083 663C4084 668 4087 675 4089 678C4090 681 4093 688 4095 694C4097 700 4101 709 4103 714C4105 720 4108 726 4109 729C4110 732 4112 737 4114 741C4116 746 4118 750 4119 752C4121 758 4123 763 4126 770C4128 774 4131 781 4132 785C4133 789 4136 795 4138 799C4140 803 4143 810 4144 814C4145 818 4148 826 4151 830C4153 835 4155 839 4155 839C4155 840 4159 851 4165 866C4166 867 4167 870 4168 873C4169 875 4172 881 4174 887C4180 900 4181 903 4178 905C4175 906 3927 906 3923 905C3919 904 3920 902 3928 884C3929 881 3931 877 3932 875C3933 872 3935 866 3938 860C3943 848 3944 845 3947 835C3949 830 3951 823 3953 820C3954 817 3957 812 3958 809C3959 806 3961 801 3963 798C3964 794 3966 789 3967 786C3970 780 3974 769 3976 764C3979 757 3980 756 3982 748C3983 744 3986 737 3988 733C3992 726 3994 720 3996 713C3996 710 3998 705 4000 701C4002 698 4004 692 4006 688C4007 684 4010 677 4012 673C4015 666 4022 649 4022 646C4022 646 4023 644 4024 641C4027 637 4029 630 4033 618C4033 615 4035 611 4037 608C4041 600 4046 590 4047 586C4048 581 4051 584 4055 594Z"/></g><g fill="var(--nova-mark,#4084c8)"><path d="M3232 0C3232 1 3229 3 3225 4C3217 8 3208 16 3194 30C3188 36 3132 92 3068 155C3004 219 2947 276 2941 283C2920 305 2918 306 2898 306C2883 306 2876 304 2868 296C2849 279 2843 272 2831 259C2829 258 2827 255 2825 254C2823 252 2821 250 2820 249C2819 247 2816 244 2814 242C2812 240 2806 235 2801 229C2795 224 2790 219 2789 219C2788 219 2787 217 2786 216C2785 213 2630 58 2620 49C2595 28 2564 23 2538 36C2536 37 2531 40 2529 41C2526 42 2523 45 2522 46C2521 48 2518 50 2516 52C2514 53 2509 58 2504 62C2500 67 2481 85 2463 104L2429 138L2423 150C2416 165 2415 169 2415 185C2415 200 2416 205 2423 219C2429 231 2431 233 2478 280C2499 300 2524 325 2534 335C2666 466 2713 512 2733 532C2747 545 2768 566 2780 579C2793 591 2805 602 2806 603C2808 604 2810 606 2811 607C2812 609 2816 614 2821 619C2849 647 2852 649 2863 656C2878 666 2911 668 2929 659C2932 658 2935 657 2935 657C2938 656 2972 624 3165 431C3421 176 3398 199 3402 192C3404 189 3406 184 3407 182C3416 167 3416 128 3407 116C3405 114 3403 110 3402 108C3402 106 3398 101 3395 97C3380 81 3311 12 3306 9C3300 5 3294 3 3293 3C3292 3 3291 2 3291 1C3290 -0 3233 -0 3232 0Z"/><path d="M2135 239C2135 250 2135 332 2135 420L2136 581L2151 597C2171 617 2172 618 2177 620C2180 622 2182 623 2183 627C2185 630 2186 632 2189 633C2191 634 2193 636 2194 639C2196 641 2198 644 2200 646C2202 647 2205 650 2206 652C2211 658 2221 667 2225 668C2229 669 2232 671 2232 675C2232 677 2235 682 2238 682C2239 682 2240 683 2241 685C2242 687 2246 692 2249 696C2253 699 2259 705 2262 709C2266 713 2271 717 2273 718C2276 719 2278 721 2279 723C2281 726 2282 728 2286 729C2290 731 2291 733 2292 736C2293 739 2294 741 2297 743C2300 745 2303 748 2304 750C2307 755 2319 765 2321 765C2325 765 2327 768 2328 771C2329 776 2331 778 2335 780C2338 780 2339 781 2339 784C2339 785 2342 788 2344 790C2346 792 2349 795 2350 796C2357 804 2368 815 2371 817C2373 817 2375 820 2376 822C2377 824 2380 827 2383 829C2385 831 2388 833 2388 834C2388 836 2416 864 2420 866C2421 867 2423 869 2425 871C2426 873 2428 876 2431 878C2433 879 2436 882 2437 885C2439 887 2441 890 2444 891C2446 893 2448 894 2448 895C2448 896 2466 914 2468 915C2469 915 2471 918 2473 920C2474 923 2477 925 2479 926C2482 928 2484 930 2484 931C2484 932 2488 936 2491 939C2494 943 2501 949 2505 954C2510 959 2514 963 2515 963C2515 963 2518 965 2521 967C2524 970 2528 973 2529 973C2530 974 2532 975 2533 976C2543 983 2572 988 2585 984C2612 977 2610 979 2672 917C2707 881 2709 878 2717 863C2726 843 2725 816 2715 796C2709 784 2704 778 2657 733C2611 687 2486 563 2452 529C2439 516 2400 478 2367 444C2333 411 2300 379 2294 373C2288 367 2258 338 2229 309C2199 279 2166 247 2156 237C2146 227 2137 219 2136 219C2135 219 2135 220 2135 239Z"/><path d="M3521 359C3516 361 3501 376 3393 484C3343 534 3283 594 3259 617C3235 641 3214 661 3213 663C3211 664 3204 672 3197 679C3162 712 3091 783 3032 842C2997 878 2967 908 2965 909C2964 910 2962 913 2960 915C2958 917 2954 921 2952 922C2949 926 2913 962 2906 969C2904 971 2896 979 2888 987C2865 1009 2862 1013 2860 1016C2859 1019 2857 1020 2854 1021C2852 1022 2849 1025 2848 1026C2847 1028 2844 1031 2841 1034C2838 1037 2836 1040 2835 1041C2835 1044 2833 1045 2830 1047C2828 1048 2826 1050 2826 1051C2825 1051 2824 1053 2824 1055C2823 1056 2821 1058 2819 1059C2817 1060 2814 1063 2813 1065C2811 1069 2808 1072 2805 1072C2803 1072 2788 1085 2787 1088C2787 1089 2785 1091 2782 1093C2780 1094 2777 1098 2776 1100C2774 1104 2772 1106 2769 1107C2767 1108 2765 1110 2764 1113C2762 1115 2760 1117 2758 1118C2756 1119 2749 1125 2743 1131C2737 1137 2732 1142 2731 1142C2731 1142 2729 1144 2728 1146C2726 1149 2723 1152 2721 1153C2719 1155 2716 1157 2715 1159C2714 1160 2712 1163 2710 1164C2707 1166 2687 1185 2678 1195C2674 1199 2667 1206 2663 1209C2662 1211 2656 1217 2650 1222C2645 1228 2640 1233 2638 1234C2637 1235 2635 1237 2634 1239C2632 1240 2628 1244 2625 1248C2621 1251 2618 1255 2618 1257C2617 1258 2615 1260 2613 1261C2609 1263 2596 1275 2594 1280C2593 1282 2591 1285 2589 1287C2583 1293 2582 1301 2582 1326C2582 1350 2583 1357 2588 1361C2590 1363 2592 1366 2593 1369C2594 1375 2606 1388 2613 1391C2616 1392 2617 1393 2618 1396C2618 1398 2620 1401 2624 1405C2627 1408 2636 1416 2643 1424C2667 1449 2678 1460 2686 1465C2714 1481 2735 1483 2761 1472C2779 1465 2772 1471 2925 1318C3046 1198 3411 833 3417 827C3418 826 3426 817 3436 808C3445 799 3453 791 3454 790C3454 789 3465 779 3478 766C3490 753 3501 743 3501 742C3502 741 3509 734 3513 731C3514 730 3517 727 3520 724C3523 721 3526 718 3527 717C3528 714 3528 699 3529 549C3529 387 3529 370 3526 361C3525 356 3525 356 3521 359Z"/></g></symbol></svg><a class="skip" href="#contenu">Aller au contenu</a>
<header class="site-header" id="site-header">
<nav aria-label="Navigation principale">
<div class="nav-container">
<div class="logo"><a href="../" aria-label="NOVA Serenity, page d’accueil"><span class="nova-lockup "><svg class="nl-mark" viewBox="0 0 4452 1482" aria-hidden="true" focusable="false"><use href="#nova-mark"/></svg><svg class="nl-word" viewBox="0 0 4452 860" aria-hidden="true" focusable="false"><text x="8" y="640" textLength="4435" lengthAdjust="spacing" font-size="800" font-weight="600" font-family="Segoe UI, Roboto, Inter, -apple-system, sans-serif" fill="currentColor">Serenity</text></svg></span></a></div>
<button type="button" class="nav-toggle" aria-expanded="false" aria-controls="menu-principal"><span aria-hidden="true">☰</span><span class="visually-hidden">Ouvrir le menu</span></button>
<ul class="nav-links" id="menu-principal">
<li><a href="../">Accueil</a></li>
<li><a href="../#apropos">À propos</a></li>
<li><a href="../#poles">Nos pôles</a></li>
<li><a href="../nova-store.html">Nos outils</a></li>
<li><a href="../#faq">FAQ</a></li>
<li><a href="../blog/">Blog</a></li>
<li><a href="../#contact">Contact</a></li>
</ul>
<a class="nav-linkedin" href="https://www.linkedin.com/company/nova-serenity-group/posts/" target="_blank" rel="noopener" aria-label="Suivez NOVA Serenity sur LinkedIn (nouvel onglet)"><span class="nav-linkedin-text">Suivez-nous</span><svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="26" height="26"><path fill="currentColor" d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/></svg></a>
</div>
</nav>
</header>
<main class="stage" id="stage">
<div class="fond-store" aria-hidden="true">
<span class="orbite-point" style="top:420px;right:3%"></span><span class="orbite-point" style="top:62%;left:8%"></span>
</div>
<img class="club-logo" src="assets/logo-nova-club.webp" alt="NOVA Club">

  <div class="console-panel" id="panel">
    <img src="assets/console-base.webp" alt="Console NOVA Club : voltmètre, voyants Arrêt et Marche, levier">
    <svg class="instr" viewBox="0 0 950 821" aria-hidden="true">
      <defs><filter id="age"><feGaussianBlur stdDeviation=".35"/></filter><filter id="nshadow"><feGaussianBlur stdDeviation="1.6"/></filter></defs>
      <g filter="url(#age)" fill="#2b251b" opacity=".8">
        <text x="408" y="226" text-anchor="middle" font-size="15">0</text>
        <text x="471" y="234" text-anchor="middle" font-size="13.5" letter-spacing="1.6">VOLTS</text>
      </g>
      <g id="needle">
        <path d="M468.6,289 L473.4,289 L473.2,273 L471.7,168 L471,162 L470.3,168 L468.8,273 Z" fill="rgba(0,0,0,.4)" filter="url(#nshadow)" transform="translate(3,4)"/>
        <path d="M468.6,289 L473.4,289 L473.2,273 L471.7,168 L471,162 L470.3,168 L468.8,273 Z" fill="#5b2418"/>
        <path d="M470.4,272 L471.2,168" stroke="rgba(255,190,160,.35)" stroke-width=".7"/>
      </g>
    </svg>
    <img class="axe" src="assets/console-axe.webp" alt="">
    <img class="lever" id="lever" src="assets/console-levier.webp" alt="">
    <svg class="glows" viewBox="0 0 950 821" aria-hidden="true">
      <defs>
        <radialGradient id="rCore" cx=".46" cy=".44" r=".55"><stop offset="0" stop-color="rgb(255,228,205)"/><stop offset=".28" stop-color="rgb(255,92,62)"/><stop offset=".65" stop-color="rgba(215,32,16,.65)"/><stop offset="1" stop-color="rgba(150,10,4,0)"/></radialGradient>
        <radialGradient id="rHalo"><stop offset="0" stop-color="rgba(255,64,34,.55)"/><stop offset=".35" stop-color="rgba(255,44,22,.17)"/><stop offset="1" stop-color="rgba(255,30,10,0)"/></radialGradient>
        <radialGradient id="gCore" cx=".46" cy=".44" r=".55"><stop offset="0" stop-color="rgb(226,255,212)"/><stop offset=".28" stop-color="rgb(96,255,112)"/><stop offset=".65" stop-color="rgba(22,176,52,.65)"/><stop offset="1" stop-color="rgba(8,110,28,0)"/></radialGradient>
        <radialGradient id="gHalo"><stop offset="0" stop-color="rgba(70,255,100,.48)"/><stop offset=".35" stop-color="rgba(50,240,80,.15)"/><stop offset="1" stop-color="rgba(30,220,60,0)"/></radialGradient>
      </defs>
      <g class="lamp" id="lampRed"><circle cx="182" cy="541" r="175" fill="url(#rHalo)"/><circle cx="182" cy="541" r="50" fill="url(#rCore)"/></g>
      <g class="lamp" id="lampGrn"><circle cx="760" cy="535" r="175" fill="url(#gHalo)"/><circle cx="760" cy="535" r="50" fill="url(#gCore)"/></g>
    </svg>
    <button class="switch" id="switch" aria-label="Actionner le levier pour entrer au NOVA Club"></button>
  </div>
</main>

<canvas id="smoke"></canvas>
<div id="blackout"></div>
<div id="shards" aria-hidden="true"></div>

<video id="neon" src="videos/nova-club-neon.mp4" playsinline preload="auto"></video>
<video id="cine" src="videos/nova-club-entree.mp4" playsinline preload="auto"></video>
<div id="flash"></div>
<button class="seq-btn sound" id="sound" type="button">Activer le son</button>
<button class="seq-btn" id="skip" type="button">Passer</button>

<section class="invitation" id="invitation" aria-live="polite" aria-hidden="true">
  <div class="inv-card">
    <img class="inv-logo" src="assets/nova-mark-retro.png" alt="NOVA Club" width="296" height="270">
<?php if ($me): ?>
    <h1 class="inv-title"><?= h($salut) ?></h1>
    <div class="inv-orn" aria-hidden="true"><span></span></div>
    <p class="inv-lead">Le Maître de cérémonie vous attendait.</p>
    <a class="inv-enter" href="club.php">Entrer au club</a>
    <a class="inv-out" href="deconnexion.php">Ce n'est pas vous ? Changer d'invité</a>
<?php else: ?>
    <div class="inv-ask" id="inv-ask">
      <h1 class="inv-title">Tenue correcte exigée</h1>
      <div class="inv-orn" aria-hidden="true"><span></span></div>
      <p class="inv-lead inv-lead-deco">Ici, l'élégance commence par une adresse e-mail professionnelle</p>
      <form class="inv-form" id="inv-form" action="demande.php" method="post" novalidate>
        <label class="visually-hidden" for="inv-email">Votre adresse e-mail professionnelle</label>
        <input id="inv-email" type="email" name="email" inputmode="email" autocomplete="email" placeholder="prenom.nom@entreprise.fr" required maxlength="190">
        <input class="inv-hp" type="text" name="site" tabindex="-1" autocomplete="off" aria-hidden="true">
        <button type="submit">Se présenter</button>
      </form>
      <p class="inv-msg" id="inv-msg" role="status"></p>
      <p class="inv-rgpd">Votre adresse sert uniquement à vérifier votre identité et à vous ouvrir les portes du club, pour <?= (int)cfg('access_months') ?> mois. <a href="confidentialite.php">Confidentialité</a></p>
    </div>
    <div class="inv-done" id="inv-done">
      <p class="inv-lead" id="inv-done-txt"></p>
      <p class="inv-sign"><img src="assets/maitre-ceremonie.png" alt="" width="44" height="44">Le Maître de Cérémonie</p>
    </div>
<?php endif; ?>
  </div>
</section>
<script>
(function(){
  var box=document.getElementById('invitation');
  function show(){box.classList.add('on');box.setAttribute('aria-hidden','false');var i=document.getElementById('inv-email')||box.querySelector('.inv-enter');if(i)setTimeout(function(){i.focus({preventScroll:true});},1200);}
  document.addEventListener('nova:fin',function(){setTimeout(show,700);});
  var f=document.getElementById('inv-form');if(!f)return;
  var msg=document.getElementById('inv-msg'),btn=f.querySelector('button'),email=document.getElementById('inv-email');
  f.addEventListener('submit',function(e){
    e.preventDefault();msg.classList.remove('err');
    var v=email.value.trim();
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)){msg.classList.add('err');msg.textContent="Cette adresse ne figure dans aucun annuaire connu. Vérifiez-la, je vous prie.";email.focus();return;}
    btn.disabled=true;msg.textContent='Le Maître de cérémonie prend note…';
    fetch(f.action,{method:'POST',body:new FormData(f),credentials:'same-origin'}).then(function(r){return r.json();}).then(function(j){
      if(j.ok){document.getElementById('inv-ask').style.display='none';document.getElementById('inv-done-txt').textContent=j.msg;document.getElementById('inv-done').classList.add('on');}
      else{msg.classList.add('err');msg.textContent=j.msg;btn.disabled=false;email.focus();}
    }).catch(function(){msg.classList.add('err');msg.textContent="La porte résiste un instant. Réessayez dans quelques secondes.";btn.disabled=false;});
  });
})();
</script>
<script src="assets/sons.js"></script>
<script>
(() => {
  const $ = s => document.querySelector(s);
  const header = $('#site-header'), sw = $('#switch'), lever = $('#lever');
  const neon = $('#neon'), cine = $('#cine'), skip = $('#skip');
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const rand = (a, b) => a + Math.random() * (b - a);
  const NS = 'http://www.w3.org/2000/svg';
  const svgEl = (tag, attrs, parent) => {
    const e = document.createElementNS(NS, tag);
    for (const k in attrs) e.setAttribute(k, attrs[k]);
    if (parent) parent.appendChild(e);
    return e;
  };

  /* ======================================================
     0. VOLTMÈTRE
     ====================================================== */
  const ang = v => -140.5 + v / 300 * 102.3;           // tension → angle SVG
  const needle = $('#needle');
  let needleAnim;
  function needleTo(frames, duration, loopJitter) {
    if (needleAnim) needleAnim.cancel();
    needleAnim = needle.animate(frames.map(v => ({ transform: `rotate(${ang(v) + 90}deg)` })),
      { duration, fill: 'forwards', easing: 'ease-out' });
    if (loopJitter) needleAnim.onfinish = () => {
      const base = frames[frames.length - 1];
      needleAnim = needle.animate([base, base + 3, base - 2, base + 1.5, base].map(v => ({ transform: `rotate(${ang(v) + 90}deg)` })),
        { duration: 700, iterations: Infinity });
    };
  }

  /* ======================================================
     0 ter. SONS (Web Audio) : voyant rouge, levier, moteur, alarme
     ====================================================== */
  const sound = (() => {
    const AC = window.AudioContext || window.webkitAudioContext;
    const noop = { ready: Promise.resolve(), redLevel() {}, redStrike() {}, unlock() {}, sequence() {}, stopSequence() {} };
    if (!AC || !window.NOVA_SONS) return noop;
    let ctx;
    try { ctx = new AC(); } catch (e) { return noop; }
    const buf = {};
    const toBuf = s => { const bin = atob(s), u = new Uint8Array(bin.length); for (let i = 0; i < bin.length; i++) u[i] = bin.charCodeAt(i); return u.buffer; };
    const decode = ab => new Promise((res, rej) => { const p = ctx.decodeAudioData(ab, res, rej); if (p && p.then) p.then(res, rej); });
    const ready = Promise.all(Object.keys(window.NOVA_SONS).map(k => decode(toBuf(window.NOVA_SONS[k])).then(b => { buf[k] = b; }))).catch(() => {});

    // voyant rouge : le son suit exactement la lumière du voyant
    const redGain = ctx.createGain(); redGain.gain.value = 0; redGain.connect(ctx.destination);
    let hum = null;
    const startHum = () => {
      if (hum || !buf.bourdon) return;
      hum = ctx.createBufferSource(); hum.buffer = buf.bourdon; hum.loop = true;
      hum.connect(redGain); hum.start();
    };
    // séquence levier + moteur + alarme
    const seqGain = ctx.createGain(); seqGain.connect(ctx.destination);
    let seq = null;
    return {
      ready,
      redLevel(v) { redGain.gain.setTargetAtTime(v, ctx.currentTime, .006); },
      redStrike() {
        ready.then(() => {
          startHum();
          if (ctx.state !== 'running' || !buf.amorcage) return;   // son bloqué tant que le visiteur n'a pas interagi
          const s = ctx.createBufferSource(); s.buffer = buf.amorcage; s.connect(redGain); s.start();
        });
      },
      unlock() { if (ctx.state === 'suspended') ctx.resume(); ready.then(startHum); },
      sequence() {
        ready.then(() => {
          if (!buf.sequence || seq) return;
          seq = ctx.createBufferSource(); seq.buffer = buf.sequence; seq.connect(seqGain);
          seqGain.gain.setValueAtTime(1, ctx.currentTime); seq.start();
        });
      },
      stopSequence(sec = .15) {
        if (!seq) return;
        const t = ctx.currentTime;
        seqGain.gain.cancelScheduledValues(t); seqGain.gain.setValueAtTime(seqGain.gain.value, t);
        seqGain.gain.linearRampToValueAtTime(0, t + sec);
        const s = seq; setTimeout(() => { try { s.stop(); } catch (e) {} }, sec * 1000 + 50);
        seq = null;
      }
    };
  })();
  // le son se débloque au premier geste du visiteur (règle des navigateurs)
  ['pointerdown', 'keydown', 'touchstart'].forEach(ev => document.addEventListener(ev, () => sound.unlock(), { once: true, capture: true }));

  /* ======================================================
     0 bis. VOYANTS : vieilles ampoules qui ont du mal à s'amorcer
     ====================================================== */
  function makeLamp(el, onLevel) {
    let timers = [], humT = null, on = false;
    const set = v => { el.style.opacity = v; if (onLevel) onLevel(v); };
    const later = (fn, ms) => timers.push(setTimeout(fn, ms));
    const clear = () => { timers.forEach(clearTimeout); timers = []; clearTimeout(humT); humT = null; };
    // ronronnement : légère instabilité permanente + micro-coupures
    function hum() {
      if (!on) return;
      const r = Math.random();
      if (r < .035) {                                   // coupure franche
        set(rand(0, .15));
        humT = setTimeout(() => { set(rand(.5, .8)); humT = setTimeout(() => { set(1); hum(); }, rand(30, 70)); }, rand(35, 90));
      } else if (r < .09) {                             // baisse de tension
        set(rand(.55, .75)); humT = setTimeout(hum, rand(60, 140));
      } else {
        set(rand(.86, 1)); humT = setTimeout(hum, rand(45, 120));
      }
    }
    return {
      // amorçage difficile, comme un néon fatigué
      strike(total = 1200) {
        clear(); on = true;
        let t = 0;
        const tries = 4 + Math.floor(rand(0, 4));
        for (let i = 0; i < tries; i++) {
          const k = i / tries;
          const flash = rand(25, 70) + k * 40;
          const gap = rand(70, 240) * (1 - k * .6);
          const lvl = rand(.45, 1);
          later(() => set(lvl), t); t += flash;
          later(() => set(rand(0, .12)), t); t += gap;
          if (Math.random() < .4) {                     // petit double éclair
            later(() => set(rand(.3, .7)), t); t += rand(20, 40);
            later(() => set(0), t); t += rand(40, 120);
          }
        }
        // lueur faible qui palpite avant d'accrocher
        const dimEnd = t + Math.max(160, total - t - 120);
        for (let d = t; d < dimEnd; d += rand(30, 60)) later(() => set(rand(.25, .5)), d);
        t = dimEnd;
        later(() => set(.8), t); later(() => set(.3), t + 40); later(() => { set(1); hum(); }, t + 80);
      },
      // extinction : quelques soubresauts puis le noir
      kill() {
        clear(); on = false;
        let t = 0;
        [[.3, 40], [.9, 50], [.1, 70], [.6, 35], [0, 0]].forEach(([v, d]) => { later(() => set(v), t); t += d; });
      },
      // perturbation (surtension)
      glitch() {
        if (!on) return;
        clearTimeout(humT);
        let t = 0;
        for (let i = 0; i < 5; i++) { later(() => set(rand(0, .4)), t); t += rand(25, 60); later(() => set(rand(.7, 1)), t); t += rand(25, 60); }
        later(hum, t);
      }
    };
  }
  const red = makeLamp($('#lampRed'), v => sound.redLevel(v)), grn = makeLamp($('#lampGrn'));
  setTimeout(() => { red.strike(1500); sound.redStrike(); }, 500);             // le voyant ARRÊT s'allume péniblement à l'arrivée
  needleTo([0], 10);

  /* ======================================================
     1. LEVIER (clic, glisser vers le haut, clavier)
     ====================================================== */
  let state = 'idle', angle = 0, startY = 0, moved = false;
  function setAngle(a) {
    angle = a;
    const s = Math.sin(a * Math.PI / 180);
    lever.style.transform = `perspective(900px) rotateX(${a}deg)`;
    lever.style.filter = `brightness(${1 - 0.4 * s}) drop-shadow(0 ${3 + 10 * s}px ${4 + 6 * s}px rgba(0,0,0,.6))`;
  }
  sw.addEventListener('pointerdown', e => {
    if (state !== 'idle') return;
    state = 'drag'; startY = e.clientY; moved = false;
    sw.setPointerCapture(e.pointerId);
    lever.style.transition = 'none';
  });
  sw.addEventListener('pointermove', e => {
    if (state !== 'drag') return;
    const dy = startY - e.clientY;
    if (Math.abs(dy) > 4) moved = true;
    const h = sw.getBoundingClientRect().height;
    setAngle(Math.max(0, Math.min(172, dy / (h * 0.6) * 180)));
  });
  const release = () => {
    if (state !== 'drag') return;
    if (!moved || angle > 70) return flip();
    state = 'idle';
    lever.style.transition = 'transform .5s cubic-bezier(.3,1.7,.5,1), filter .5s';
    setAngle(0);
  };
  sw.addEventListener('pointerup', release);
  sw.addEventListener('pointercancel', release);
  sw.addEventListener('keydown', e => {
    if ((e.key === 'Enter' || e.key === ' ') && state === 'idle') { e.preventDefault(); flip(); }
  });
  sw.addEventListener('click', () => { if (state === 'idle') flip(); });

  function flip() {
    state = 'on'; sw.disabled = true;
    sound.unlock();
    sound.sequence();                                // claquement du levier, moteur qui peine, puis alarme
    unlockAudio();                                   // le geste du visiteur autorise la musique plus tard
    lever.style.transition = 'transform .28s cubic-bezier(.55,0,.7,1.35), filter .28s';
    setAngle(172);
    setTimeout(() => {
      $('#panel').animate([0, 2, -1.5, 1, 0].map(y => ({ transform: `translateY(${y}px)` })), { duration: 180 });
      red.kill();
      grn.strike(1000);
      needleTo([0, 25, 10, 45, 22, 70, 45, 90], 2200, true);   // l'aiguille suit le moteur qui peine
    }, 240);
    setTimeout(startSequence, 2400);                 // le verre cède quand le moteur prend enfin son élan
  }

  /* ======================================================
     2. SÉQUENCE
     ====================================================== */
  function startSequence() {
    document.body.classList.add('running');
    if (reduced) {
      header.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 500, fill: 'forwards' });
      smoke.start(1.6);
      setTimeout(playNeon, 2000);
      return;
    }
    const glass = buildGlass();
    // à chaque impact : secousse, surtension sur le voltmètre, le voyant vert grésille
    glass.stages.forEach((d, i) => setTimeout(() => {
      $('#stage').animate([0, -4, 3, -1.5, 0].map(y => ({ transform: `translateY(${y}px)` })), { duration: 220, easing: 'ease-out' });
      needleTo([231, 296 - i * 4, 270, 288], 260 + i * 40, true);
      grn.glitch();
    }, d));
    shake(glass);
    setTimeout(() => shatter(glass), 900);
    setTimeout(() => smoke.start(2.5), 450);
    setTimeout(playNeon, 3200);
  }

  /* ======================================================
     3. LE VERRE
     Le header est découpé en éclats dès le premier impact.
     Chaque éclat se décale d'un ou deux pixels (réfraction),
     les fissures se propagent segment par segment depuis l'impact,
     s'épaississent près du point de choc, avec reflets et poudre de verre.
     ====================================================== */
  function clipHalf(poly, f) {
    const out = [];
    for (let i = 0; i < poly.length; i++) {
      const a = poly[i], b = poly[(i + 1) % poly.length], fa = f(a), fb = f(b);
      if (fa >= 0) out.push(a);
      if ((fa >= 0) !== (fb >= 0)) { const t = fa / (fa - fb); out.push([a[0] + (b[0] - a[0]) * t, a[1] + (b[1] - a[1]) * t]); }
    }
    return out;
  }
  const area = p => Math.abs(p.reduce((s, a, i) => { const b = p[(i + 1) % p.length]; return s + a[0] * b[1] - b[0] * a[1]; }, 0) / 2);
  const toD = pts => 'M' + pts.map(p => p[0].toFixed(1) + ',' + p[1].toFixed(1)).join(' L');

  function buildGlass() {
    const W = header.offsetWidth, H = header.offsetHeight;
    const box = $('#shards');
    const nz = W > 1000 ? 3 : W > 560 ? 2 : 1;
    const lines = [];
    for (let j = 1; j < nz; j++) {
      const xm = W * j / nz + rand(-50, 50), xt = xm + rand(-28, 28), xb = xm + rand(-28, 28);
      lines.push(y => xt + (xb - xt) * y / H);
    }
    const mainZone = Math.floor(nz / 2);
    const order = [mainZone, ...[...Array(nz).keys()].filter(z => z !== mainZone).sort(() => Math.random() - .5)];

    // ---- calque de fissures animées
    const svg = svgEl('svg', { class: 'cracks-live', width: W, height: H, viewBox: `0 0 ${W} ${H}` });
    const defs = svgEl('defs', {}, svg);
    const uid = Date.now().toString(36);
    // micro-irrégularités du bord de la cassure
    const rough = svgEl('filter', { id: 'rough' + uid, x: '-5%', y: '-20%', width: '110%', height: '140%' }, defs);
    svgEl('feTurbulence', { type: 'fractalNoise', baseFrequency: '0.18', numOctaves: '2', seed: '3', result: 'n' }, rough);
    svgEl('feDisplacementMap', { in: 'SourceGraphic', in2: 'n', scale: '2.4', xChannelSelector: 'R', yChannelSelector: 'G' }, rough);
    const soft = svgEl('filter', { id: 'soft' + uid, x: '-20%', y: '-50%', width: '140%', height: '200%' }, defs);
    svgEl('feGaussianBlur', { stdDeviation: '1.1' }, soft);
    const powder = svgEl('radialGradient', { id: 'pow' + uid }, defs);
    svgEl('stop', { offset: '0', 'stop-color': 'rgba(235,242,255,.55)' }, powder);
    svgEl('stop', { offset: '1', 'stop-color': 'rgba(235,242,255,0)' }, powder);

    const cells = [], stages = [];

    for (let z = 0; z < nz; z++) {
      const L = z > 0 ? lines[z - 1] : null, R = z < nz - 1 ? lines[z] : null;
      const inZone = poly => {
        let p = poly;
        p = clipHalf(p, q => q[0]); p = clipHalf(p, q => W - q[0]);
        p = clipHalf(p, q => q[1]); p = clipHalf(p, q => H - q[1]);
        if (L) p = clipHalf(p, q => q[0] - L(q[1]));
        if (R) p = clipHalf(p, q => R(q[1]) - q[0]);
        return p;
      };
      const x0 = L ? L(H / 2) : 0, x1 = R ? R(H / 2) : W;
      const ix = z === mainZone ? W / 2 + rand(-25, 25) : rand(x0 + (x1 - x0) * .3, x0 + (x1 - x0) * .7);
      const iy = z === mainZone ? H * .72 : H * rand(.3, .72);
      const delay = order.indexOf(z) * 240;
      stages.push(delay);

      const cp = svgEl('clipPath', { id: `z${z}${uid}` }, defs);
      svgEl('path', { d: toD(inZone([[-10, -10], [W + 10, -10], [W + 10, H + 10], [-10, H + 10]])) + 'Z' }, cp);
      const g = svgEl('g', { 'clip-path': `url(#z${z}${uid})` }, svg);
      const gFacets = svgEl('g', {}, g);
      const gShadow = svgEl('g', { transform: 'translate(.6,.9)', filter: `url(#rough${uid})` }, g);
      const gLight = svgEl('g', { filter: `url(#rough${uid})` }, g);
      const gGlint = svgEl('g', { filter: `url(#soft${uid})` }, g);

      // géométrie : rayons presque droits + anneaux concentriques
      const N = 11, rot = rand(0, Math.PI * 2);
      const Rs = [0, 7, 17, 31, 50, 78, 118, 175, 260, 390, 580, 860, 1280, 1900];
      const K = Rs.length - 1;
      const P = [];
      for (let i = 0; i < N; i++) {
        let a = rot + i / N * Math.PI * 2 + rand(-.16, .16);
        P[i] = Rs.map((r, k) => {
          if (k === 0) return [ix, iy];
          a += rand(-.05, .05);
          const rr = r * (1 + rand(-.13, .13));
          return [ix + Math.cos(a) * rr, iy + Math.sin(a) * rr];
        });
      }
      // éclats (cellules)
      const ks = [0, 2, 3, 5, 7, 9, 13];
      for (let i = 0; i < N; i++) {
        const j = (i + 1) % N;
        for (let m = 0; m < ks.length - 1; m++) {
          const k0 = ks[m], k1 = ks[m + 1];
          const poly = k0 === 0 ? [P[i][0], P[i][k1], P[j][k1]] : [P[i][k0], P[i][k1], P[j][k1], P[j][k0]];
          const c = inZone(poly);
          if (c.length >= 3 && area(c) > 6) cells.push({ poly: c, ix, iy, delay, k0, W, H });
        }
      }

      const seg = (a, b, w, alpha, t0, glint) => {
        const d = toD([a, b]);
        const len = Math.hypot(b[0] - a[0], b[1] - a[1]) + 1;
        const sh = svgEl('path', { d, fill: 'none', stroke: `rgba(0,0,0,${.45 + alpha * .35})`, 'stroke-width': w * 1.9, 'stroke-linecap': 'round' }, gShadow);
        const li = svgEl('path', { d, fill: 'none', stroke: `rgba(236,244,255,${alpha})`, 'stroke-width': w, 'stroke-linecap': 'round' }, gLight);
        const els = [sh, li];
        if (glint) els.push(svgEl('path', { d, fill: 'none', stroke: 'rgba(255,255,255,.9)', 'stroke-width': w * 2.2, 'stroke-linecap': 'round' }, gGlint));
        els.forEach(el => {
          el.style.strokeDasharray = len; el.style.strokeDashoffset = len;
          el.animate([{ strokeDashoffset: len }, { strokeDashoffset: 0 }], { duration: 45, delay: t0, fill: 'forwards', easing: 'linear' });
        });
      };

      // rayons : épais près de l'impact, de plus en plus fins, propagation rapide
      for (let i = 0; i < N; i++) {
        const kEnd = Math.min(K, Math.floor(rand(5, K + 1)));
        const glintK = Math.random() < .45 ? Math.floor(rand(1, 5)) : -1;
        for (let k = 0; k < kEnd; k++) {
          const w = Math.max(.35, 1.5 * Math.pow(.8, k));
          const alpha = Math.max(.32, .92 * Math.pow(.9, k));
          seg(P[i][k], P[i][k + 1], w, alpha, delay + k * 24 + rand(0, 10), k === glintK);
        }
      }
      // anneaux : arcs incomplets et légèrement bombés, surtout près de l'impact
      for (let k = 1; k <= 5; k++) {
        const prob = [0, .72, .5, .3, .12, .06][k];
        for (let i = 0; i < N; i++) {
          if (Math.random() > prob) continue;
          const a = P[i][k], b = P[(i + 1) % N][k];
          const mx = (a[0] + b[0]) / 2, my = (a[1] + b[1]) / 2;
          const bulge = rand(.08, .3), len = Math.hypot(b[0] - a[0], b[1] - a[1]);
          const nx = mx - ix, ny = my - iy, nl = Math.hypot(nx, ny) || 1;
          const c = [mx + nx / nl * len * bulge, my + ny / nl * len * bulge];
          // parfois l'arc s'arrête avant d'atteindre le rayon suivant
          const endT = Math.random() < .35 ? rand(.45, .8) : 1;
          const q = t => [(1 - t) * (1 - t) * a[0] + 2 * (1 - t) * t * c[0] + t * t * b[0], (1 - t) * (1 - t) * a[1] + 2 * (1 - t) * t * c[1] + t * t * b[1]];
          const steps = 4, pts = [];
          for (let n = 0; n <= steps; n++) pts.push(q(n / steps * endT));
          const w = Math.max(.3, 1.05 * Math.pow(.78, k)), alpha = k <= 2 ? .72 : .45;
          for (let n = 0; n < steps; n++) seg(pts[n], pts[n + 1], w, alpha, delay + k * 24 + 30 + n * 10, false);
        }
      }
      // petites ramifications qui cassent la régularité
      for (let i = 0; i < N; i++) for (let k = 1; k <= 7; k++) {
        if (Math.random() > .3) continue;
        const a = P[i][k], b = P[i][k + 1];
        const dir = Math.atan2(b[1] - a[1], b[0] - a[0]) + (Math.random() < .5 ? -1 : 1) * rand(.35, .85);
        let x = a[0], y = a[1];
        let len = rand(8, 34) * Math.pow(.9, k), d = dir, t0 = delay + k * 24 + 15;
        const w = Math.max(.28, .9 * Math.pow(.8, k));
        for (let n = 0; n < 3 && len > 2; n++) {
          const l = len * rand(.35, .6); d += rand(-.25, .25);
          const nx = x + Math.cos(d) * l, ny = y + Math.sin(d) * l;
          seg([x, y], [nx, ny], w * (1 - n * .25), .5, t0 + n * 12, false);
          x = nx; y = ny; len -= l;
        }
      }
      // facettes : quelques morceaux captent la lumière ou s'assombrissent
      for (let i = 0; i < N; i++) for (let k = 1; k <= 4; k++) {
        if (Math.random() > .42) continue;
        const j = (i + 1) % N;
        const f = svgEl('path', {
          d: toD([P[i][k], P[i][k + 1], P[j][k + 1], P[j][k]]) + 'Z',
          fill: Math.random() < .55 ? `rgba(225,238,255,${rand(.04, .12)})` : `rgba(0,0,0,${rand(.12, .28)})`
        }, gFacets);
        f.style.opacity = 0;
        f.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 80, delay: delay + 40 + k * 30, fill: 'forwards' });
      }
      // point d'impact : verre pulvérisé
      const crush = svgEl('g', {}, g);
      svgEl('circle', { cx: ix, cy: iy, r: 13, fill: `url(#pow${uid})` }, crush);
      for (let n = 0; n < 16; n++) {
        const a = rand(0, 6.28), r = rand(0, 7);
        const cx = ix + Math.cos(a) * r, cy = iy + Math.sin(a) * r, s = rand(1, 3);
        svgEl('path', { d: toD([[cx, cy - s], [cx + s, cy + s * .6], [cx - s * .8, cy + s * .4]]) + 'Z', fill: `rgba(245,250,255,${rand(.25, .75)})` }, crush);
      }
      crush.style.opacity = 0;
      crush.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 30, delay, fill: 'forwards' });
    }
    // cassures de jonction entre zones
    lines.forEach((f, idx) => {
      const pts = []; for (let y = -4; y <= H + 4; y += H / 6) pts.push([f(y) + rand(-2.5, 2.5), y]);
      const g = svgEl('g', { filter: `url(#rough${uid})` }, svg);
      for (let n = 0; n < pts.length - 1; n++) {
        const d = toD([pts[n], pts[n + 1]]);
        const a = svgEl('path', { d, stroke: 'rgba(0,0,0,.6)', 'stroke-width': 1.8, transform: 'translate(.6,.9)', fill: 'none' }, g);
        const b = svgEl('path', { d, stroke: 'rgba(236,244,255,.6)', 'stroke-width': .8, fill: 'none' }, g);
        [a, b].forEach(el => { el.style.opacity = 0; el.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 40, delay: 560 + idx * 70 + n * 25, fill: 'forwards' }); });
      }
    });

    // ---- éclats : une copie du header découpée par morceau
    const clone = header.cloneNode(true);
    clone.removeAttribute('id');
    clone.style.position = 'absolute';
    cells.forEach(c => {
      const s = document.createElement('div');
      s.className = 'shard';
      s.style.width = W + 'px'; s.style.height = H + 'px';
      s.style.clipPath = `polygon(${c.poly.map(p => `${p[0].toFixed(1)}px ${p[1].toFixed(1)}px`).join(',')})`;
      const inner = document.createElement('div');
      inner.className = 'inner';
      inner.style.width = W + 'px'; inner.style.height = H + 'px';
      inner.appendChild(clone.cloneNode(true));
      s.appendChild(inner);
      box.appendChild(s);
      c.el = s; c.inner = inner;
      // réfraction : chaque morceau se décale un peu au moment où la fissure l'atteint
      const mag = c.k0 <= 1 ? 2.4 : c.k0 <= 3 ? 1.5 : c.k0 <= 5 ? .9 : .5;
      inner.animate([{ transform: 'none' }, { transform: `translate(${rand(-mag, mag)}px,${rand(-mag, mag) * .8}px) rotate(${rand(-.3, .3)}deg)` }],
        { duration: 90, delay: c.delay + c.k0 * 24, fill: 'forwards', easing: 'cubic-bezier(.2,1.8,.4,1)' });
    });
    box.appendChild(svg);
    header.style.visibility = 'hidden';
    return { cells, stages, W, H, svg };
  }

  function shake({ stages }) {
    const T = 900, frames = [];
    for (let i = 0; i <= 54; i++) {
      const t = i / 54 * T;
      let amp = .8 + t / T * 3;
      stages.forEach(s => { if (t >= s) amp += 5 * Math.exp(-(t - s) / 60); });
      frames.push({ transform: `translate(${rand(-amp, amp)}px, ${rand(-amp, amp) * .5}px)` });
    }
    $('#shards').animate(frames, { duration: T, easing: 'linear' });
  }

  function shatter({ cells, W, H, svg }) {
    // on fige les fissures dans une image, portée par chaque éclat
    svg.querySelectorAll('path,circle,g').forEach(el => {
      el.getAnimations().forEach(a => a.cancel());
      el.style.strokeDashoffset = '0'; el.style.strokeDasharray = 'none'; el.style.opacity = '1';
    });
    const src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(new XMLSerializer().serializeToString(svg));
    svg.remove();
    cells.forEach(c => {
      const img = document.createElement('img');
      img.src = src; img.alt = '';
      img.style.cssText = `position:absolute;left:0;top:0;width:${W}px;height:${H}px;z-index:40`;
      c.inner.appendChild(img);
      const e = svgEl('svg', { viewBox: `0 0 ${W} ${H}`, width: W, height: H });
      svgEl('polygon', { points: c.poly.map(p => p.join(',')).join(' '), fill: `rgba(220,235,255,${rand(0, .06)})`, stroke: 'rgba(235,244,255,.45)', 'stroke-width': 1.2 }, e);
      e.style.opacity = 0;
      c.el.appendChild(e);

      const cx = c.poly.reduce((a, p) => a + p[0], 0) / c.poly.length;
      const cy = c.poly.reduce((a, p) => a + p[1], 0) / c.poly.length;
      c.el.style.transformOrigin = `${cx}px ${cy}px`;
      const dist = Math.hypot(cx - c.ix, cy - c.iy);
      const dx = (cx - c.ix) * rand(.2, .6) + rand(-50, 50);
      const up = innerHeight * .3 + H + rand(100, 360);
      const flyDelay = Math.min(320, dist * .8) + rand(0, 60);
      // l'arête du morceau n'apparaît qu'au moment où il se détache
      e.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 220, delay: flyDelay + 120, fill: 'forwards' });
      c.el.animate([
        { transform: 'translate(0,0) rotate(0) rotateX(0)' },
        { transform: `translate(${(cx - c.ix) * .03}px, ${rand(2, 8)}px) rotate(${rand(-3, 3)}deg)`, offset: .15 },
        { transform: `translate(${dx}px, ${-up}px) rotate(${rand(-90, 90)}deg) rotateX(${rand(-90, 90)}deg) rotateY(${rand(-60, 60)}deg)` }
      ], { duration: rand(900, 1400), delay: flyDelay, easing: 'cubic-bezier(.55,0,.85,.35)', fill: 'forwards' });
    });
    setTimeout(() => { $('#shards').innerHTML = ''; }, 2300);
  }

  /* ======================================================
     3. FUMÉE NOIRE (WebGL) — envahit l'écran puis continue
        à dériver en volutes gris foncé sur le noir
     ====================================================== */
  const smoke = (() => {
    const cv = $('#smoke');
    const gl = cv.getContext('webgl', { premultipliedAlpha: true, alpha: true });
    const SCALE = .55;
    let raf = null, t0 = 0, dur = 2.5, uRes, uTime, uProg, uOver, uFade, last = 0;
    const over = { v: 0, to: 0, rate: 1 }, fade = { v: 1, to: 1, rate: 1 };
    const resize = () => {
      cv.width = Math.ceil(innerWidth * SCALE); cv.height = Math.ceil(innerHeight * SCALE);
      if (gl) gl.viewport(0, 0, cv.width, cv.height);
    };
    addEventListener('resize', resize); resize();
    if (gl) {
      const vs = `attribute vec2 p;void main(){gl_Position=vec4(p,0.,1.);}`;
      const fs = `
        precision mediump float;
        uniform vec2 uRes; uniform float uTime; uniform float uProg; uniform float uOver; uniform float uFade;
        float hash(vec2 p){return fract(sin(dot(p,vec2(127.1,311.7)))*43758.5453);}
        float noise(vec2 p){vec2 i=floor(p),f=fract(p);vec2 u=f*f*(3.-2.*f);
          return mix(mix(hash(i),hash(i+vec2(1,0)),u.x),mix(hash(i+vec2(0,1)),hash(i+vec2(1,1)),u.x),u.y);}
        float fbm(vec2 p){float v=0.,a=.5;mat2 m=mat2(1.6,1.2,-1.2,1.6);
          for(int i=0;i<5;i++){v+=a*noise(p);p=m*p;a*=.5;}return v;}
        void main(){
          vec2 uv=gl_FragCoord.xy/uRes;
          vec2 p=uv*vec2(uRes.x/uRes.y,1.)*2.4;
          float t=uTime;
          vec2 q=vec2(fbm(p+vec2(0.,-t*.14)),fbm(p+vec2(5.2,1.3)+vec2(t*.08,0.)));
          vec2 r=vec2(fbm(p+3.*q+vec2(1.7,9.2)+t*.11),fbm(p+3.*q+vec2(8.3,2.8)-t*.09));
          float n=fbm(p+2.6*r+vec2(t*.06,-t*.2));
          // front d'invasion : part du bas et des côtés
          float d=min(uv.y*1.15,min(uv.x,1.-uv.x)*1.6);
          float front=uProg*1.45-.12;
          float c=smoothstep(-.02,.2,front-d+(n-.5)*.6);
          // volutes gris foncé qui dérivent
          float w1=smoothstep(.45,.88,n);
          float w2=smoothstep(.5,.92,fbm(p*.7+r*1.8+vec2(-t*.1,t*.05)));
          float rim=c*(1.-c)*4.;
          vec3 col=vec3(.006,.007,.011)
                  +vec3(.15,.155,.17)*(w1*.75+w2*.6)
                  +vec3(.12,.125,.14)*rim*n;
          float a=c*mix(.9+.1*n,1.,smoothstep(.75,1.,uProg));
          // mode superposition : la fumée devient translucide et dérive au-dessus de la vidéo
          float ov=clamp(w1*.55+w2*.45+smoothstep(.3,.72,n)*.3,0.,.82);
          vec3 colO=mix(vec3(.008,.009,.012),vec3(.16,.165,.18),clamp(w1*.6+w2*.5,0.,1.));
          col=mix(col,colO,uOver);
          a=mix(a,ov,uOver)*uFade;
          gl_FragColor=vec4(col*a,a);
        }`;
      const sh = (type, src) => { const s = gl.createShader(type); gl.shaderSource(s, src); gl.compileShader(s); return s; };
      const pr = gl.createProgram();
      gl.attachShader(pr, sh(gl.VERTEX_SHADER, vs)); gl.attachShader(pr, sh(gl.FRAGMENT_SHADER, fs));
      gl.linkProgram(pr); gl.useProgram(pr);
      const b = gl.createBuffer(); gl.bindBuffer(gl.ARRAY_BUFFER, b);
      gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 1, -1, -1, 1, 1, 1]), gl.STATIC_DRAW);
      const loc = gl.getAttribLocation(pr, 'p'); gl.enableVertexAttribArray(loc);
      gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);
      uRes = gl.getUniformLocation(pr, 'uRes'); uTime = gl.getUniformLocation(pr, 'uTime'); uProg = gl.getUniformLocation(pr, 'uProg');
      uOver = gl.getUniformLocation(pr, 'uOver'); uFade = gl.getUniformLocation(pr, 'uFade');
    }
    const ease = x => x < .5 ? 2 * x * x : 1 - Math.pow(-2 * x + 2, 2) / 2;
    const step = (o, dt) => { if (o.v < o.to) o.v = Math.min(o.to, o.v + o.rate * dt); else if (o.v > o.to) o.v = Math.max(o.to, o.v - o.rate * dt); };
    function loop(now) {
      const t = (now - t0) / 1000;
      const dt = Math.min(.1, (now - (last || now)) / 1000); last = now;
      step(over, dt); step(fade, dt);
      if (fade.v <= 0 && fade.to <= 0) { gl.clear(gl.COLOR_BUFFER_BIT); raf = null; return; }
      gl.uniform1f(uOver, over.v); gl.uniform1f(uFade, fade.v);
      gl.uniform2f(uRes, cv.width, cv.height);
      gl.uniform1f(uTime, t + 3.0);
      gl.uniform1f(uProg, ease(Math.min(1, t / dur)));
      gl.clearColor(0, 0, 0, 0); gl.clear(gl.COLOR_BUFFER_BIT);
      gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
      raf = requestAnimationFrame(loop);
    }
    return {
      start(seconds) {
        if (raf) return;
        dur = seconds; t0 = performance.now();
        if (!gl) { cv.style.background = '#020203'; cv.animate([{ opacity: 0 }, { opacity: 1 }], { duration: seconds * 1000, fill: 'forwards' }); return; }
        raf = requestAnimationFrame(loop);
      },
      stop() { if (raf) cancelAnimationFrame(raf); raf = null; },
      // passe en superposition translucide (au-dessus des vidéos)
      toOverlay(ms) {
        cv.classList.add('over');
        if (!gl) { cv.animate([{ opacity: 1 }, { opacity: 0 }], { duration: ms, fill: 'forwards' }); return; }
        over.to = 1; over.rate = 1000 / ms;
      },
      fadeTo(v, ms) {
        if (!gl) return;
        fade.to = v; fade.rate = Math.abs(fade.v - v) * 1000 / ms || 1;
      }
    };
  })();

  /* ======================================================
     4. VIDÉOS : néon (sous la fumée) → éclair néon → vidéo d'entrée avec musique
     ====================================================== */
  let phase = 'none';
  const flash = $('#flash'), soundBtn = $('#sound');

  // Musique : on passe par Web Audio pour pouvoir faire les fondus (y compris sur iPhone)
  let actx = null, gain = null;
  function unlockAudio() {
    try {
      const AC = window.AudioContext || window.webkitAudioContext;
      // Web Audio seulement en ligne (http/https) : ouvert en local (file://), le navigateur couperait le son
      if (AC && !actx && /^https?:/.test(location.protocol)) {
        actx = new AC();
        gain = actx.createGain(); gain.gain.value = 0;
        actx.createMediaElementSource(cine).connect(gain);
        gain.connect(actx.destination);
      }
      if (actx && actx.state === 'suspended') actx.resume();
    } catch (e) { actx = null; gain = null; }
    // Safari : "débloque" les deux vidéos pendant le geste (le son du néon est muet au début), puis les remet à zéro
    cine.volume = 0;
    [cine, neon].forEach(v => {
      v.muted = false;
      const p = v.play();
      if (p) p.then(() => { v.pause(); v.currentTime = 0; }).catch(() => {});
    });
  }
  function volumeTo(v, seconds) {
    if (gain && actx) {
      const t = actx.currentTime;
      gain.gain.cancelScheduledValues(t);
      gain.gain.setValueAtTime(gain.gain.value, t);
      gain.gain.linearRampToValueAtTime(v, t + seconds);
    } else {                                      // repli : fondu sur le volume de la vidéo
      clearInterval(volumeTo.iv);
      const from = cine.volume, t0 = performance.now(), ms = seconds * 1000;
      volumeTo.iv = setInterval(() => {
        const k = Math.min(1, (performance.now() - t0) / ms);
        cine.volume = Math.max(0, Math.min(1, from + (v - from) * k));
        if (k >= 1) clearInterval(volumeTo.iv);
      }, 30);
    }
  }

  function playNeon() {
    phase = 'neon';
    $('#smoke').classList.add('over');           // la fumée passe au premier plan…
    $('#blackout').classList.add('on');          // …et un fond noir remplace définitivement la page
    setTimeout(() => { $('#stage').style.visibility = 'hidden'; }, 100);
    neon.currentTime = 0;
    neon.classList.add('on');
    neon.muted = false; neon.volume = 1;          // grésillement du néon synchronisé à l'image
    const p = neon.play();
    if (p) p.catch(() => {
      neon.muted = true; soundBtn.classList.add('on');
      neon.play().catch(() => playEntrance());
    });
    setTimeout(() => smoke.toOverlay(1800), 650); // une fois le néon affiché, la fumée s'éclaircit et continue par-dessus
    skip.classList.add('on');
  }
  neon.addEventListener('error', () => { if (phase === 'neon') playEntrance(); });

  function startCine() {
    cine.currentTime = 0;
    if (!gain) cine.volume = 0;
    cine.muted = false;
    const p = cine.play();
    if (p) p.then(() => { if (!cine.muted) soundBtn.classList.remove('on'); });
    if (p) p.catch(() => {                        // son refusé par le navigateur : on joue sans, avec un bouton pour l'activer
      cine.muted = true;
      cine.play().catch(() => setTimeout(finish, 600));   // vidéo illisible : on passe directement à l'invitation
      soundBtn.classList.add('on');
    });
    volumeTo(1, .6);
  }
  soundBtn.addEventListener('click', () => {
    if (actx && actx.state === 'suspended') actx.resume();
    if (phase === 'neon') neon.muted = false;
    cine.muted = false; if (phase === 'cine') volumeTo(1, .3);
    soundBtn.classList.remove('on');
  });

  function playEntrance() {
    if (phase === 'cine' || phase === 'end') return;
    phase = 'cine';
    const f = (o, d) => flash.animate([{ opacity: flash.style.opacity || 0 }, { opacity: o }], { duration: d, fill: 'forwards' });
    if (reduced) {
      sound.stopSequence(.5);
      startCine();
      neon.classList.remove('on');
      cine.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 1200, fill: 'forwards' });
      smoke.fadeTo(0, 1500);
      return;
    }
    // 1. l'enseigne bégaie, comme un néon qui surchauffe
    const stutter = [[.35, 0], [0, 60], [.55, 110], [0, 150], [.25, 210], [0, 250]];
    stutter.forEach(([o, t]) => setTimeout(() => { flash.style.opacity = o; }, t));
    // la vidéo démarre sous l'éclair, pour être prête au moment de l'ouverture
    setTimeout(startCine, 150);
    // 2. l'éclair envahit tout l'écran
    setTimeout(() => {
      sound.stopSequence(.12);                    // moteur et alarme se coupent net dans l'éclair
      flash.style.opacity = 0;
      flash.animate([{ opacity: 0, transform: 'scale(.6)' }, { opacity: 1, transform: 'scale(1.4)' }],
        { duration: 110, easing: 'ease-in', fill: 'forwards' });
    }, 290);
    // 3. ouverture : l'éclair se dissipe et révèle l'entrée du club, légèrement surexposée puis nette
    setTimeout(() => {
      neon.classList.remove('on'); neon.style.transition = 'none'; neon.style.opacity = 0; neon.pause();
      cine.style.opacity = 1;
      cine.animate([
        { transform: 'scale(1.1)', filter: 'brightness(2.4) saturate(1.4)' },
        { transform: 'scale(1.02)', filter: 'brightness(1.15) saturate(1.1)', offset: .45 },
        { transform: 'scale(1)', filter: 'none' }
      ], { duration: 1800, easing: 'cubic-bezier(.2,.7,.2,1)', fill: 'forwards' });
      flash.animate([{ opacity: 1, transform: 'scale(1.4)' }, { opacity: 0, transform: 'scale(2.2)' }],
        { duration: 1200, easing: 'cubic-bezier(.3,0,.2,1)', fill: 'forwards' });
      smoke.fadeTo(.35, 200);                     // la fumée ne subsiste qu'en voile…
      setTimeout(() => smoke.fadeTo(0, 3500), 600); // …puis se dissipe
    }, 400);
  }
  neon.addEventListener('ended', playEntrance);

  // fin : dernière image figée, la musique s'estompe
  let fading = false;
  cine.addEventListener('timeupdate', () => {
    if (!fading && isFinite(cine.duration) && cine.duration - cine.currentTime < 2.6) {
      fading = true; volumeTo(0, Math.max(.3, cine.duration - cine.currentTime));
    }
  });
  const finish = () => { if (phase === 'end') return; phase = 'end'; skip.classList.remove('on'); soundBtn.classList.remove('on'); document.dispatchEvent(new Event('nova:fin')); };
  cine.addEventListener('ended', finish);
  cine.addEventListener('error', () => { if (phase === 'cine') finish(); });

  skip.addEventListener('click', () => {
    if (phase === 'neon') playEntrance();
    else if (phase === 'cine') {
      fading = true; volumeTo(0, .8);
      smoke.fadeTo(0, 600);
      setTimeout(() => {
        cine.pause();
        if (isFinite(cine.duration)) cine.currentTime = cine.duration - .05;
        finish();
      }, 800);
    }
  });
})();
</script>
<script>
(function(){var b=document.querySelector('.nav-toggle'),m=document.getElementById('menu-principal');if(!b||!m)return;
function t(o){m.classList.toggle('ouvert',o);b.setAttribute('aria-expanded',o?'true':'false');}
b.addEventListener('click',function(){t(!m.classList.contains('ouvert'));});
m.addEventListener('click',function(e){if(e.target.tagName==='A')t(false);});
document.addEventListener('keydown',function(e){if(e.key==='Escape'){t(false);}});})();
</script>
<script>
(function(){var n=document.getElementById('site-header');if(!n)return;
function h(){document.documentElement.style.setProperty('--header-h',n.offsetHeight+'px');}
h();addEventListener('resize',h);if(document.fonts)document.fonts.ready.then(h);})();
</script>
</body>
</html>
