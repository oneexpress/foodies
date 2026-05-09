<?php
declare(strict_types=1);
header("Content-Type: text/html; charset=UTF-8");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>ExpressVisa Check Status</title>
<style>
:root{--red:#e60023;--red2:#ff5a00;--dark:#111827;--muted:#667085;--line:#e5e7eb;--soft:#fff1f2;--green:#12b76a;--blue:#2e90fa;--amber:#f79009;--purple:#7a5af8;--gray:#98a2b3}
*{box-sizing:border-box}
body{margin:0;font-family:Arial,"Microsoft YaHei",sans-serif;background:radial-gradient(circle at top left,rgba(230,0,35,.16),transparent 34%),linear-gradient(180deg,#fff7f7,#f3f4f6);color:var(--dark);padding-bottom:138px}
.wrap{max-width:1220px;margin:auto;padding:24px 16px 48px}
.hero{display:grid;grid-template-columns:150px 1fr;gap:24px;align-items:center;background:linear-gradient(135deg,var(--red),var(--red2));border-radius:34px;padding:30px;color:#fff;box-shadow:0 28px 78px rgba(230,0,35,.26)}
.logoBox{background:#fff;border-radius:28px;padding:16px;box-shadow:0 18px 44px rgba(0,0,0,.18)}
.logoBox img{width:100%;display:block}
.hero h1{margin:0;font-size:42px;line-height:1.03;font-weight:1000;letter-spacing:-.04em}
.hero p{margin:10px 0 0;font-size:17px;font-weight:850;opacity:.96}
.heroBadges{display:flex;flex-wrap:wrap;gap:9px;margin-top:16px}
.badge{background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.22);border-radius:999px;padding:9px 12px;font-size:13px;font-weight:1000}
.shell{margin-top:20px;display:grid;grid-template-columns:minmax(0,1.15fr) 370px;gap:18px;align-items:start}
.panel{background:#fff;border:1px solid rgba(230,0,35,.12);border-radius:30px;padding:24px;box-shadow:0 26px 72px rgba(15,23,42,.10)}
.notice{display:flex;gap:12px;background:var(--soft);border:1px solid rgba(230,0,35,.18);border-radius:20px;padding:16px;margin-bottom:18px;color:#9b1020;font-weight:900;line-height:1.45}
.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:15px}
label{display:block;font-weight:1000;margin:0 0 7px;color:#1f2937}
.field{width:100%;height:56px;border:1px solid #d7dbe2;border-radius:18px;padding:0 16px;font-size:15px;background:#fff;outline:none}
.field:focus{border-color:var(--red);box-shadow:0 0 0 4px rgba(230,0,35,.10)}
.actions{margin-top:18px;display:grid;grid-template-columns:1fr 210px;gap:12px}
.submit,.back{height:60px;border-radius:999px;font-weight:1000;font-size:16px;display:flex;align-items:center;justify-content:center}
.submit{border:0;background:linear-gradient(135deg,var(--red),var(--red2));color:#fff;cursor:pointer;box-shadow:0 18px 38px rgba(230,0,35,.25)}
.back{border:1px solid rgba(230,0,35,.18);background:#fff;color:#b8001c;text-decoration:none}
.timeline{margin-top:22px;display:grid;gap:12px}
.step{display:grid;grid-template-columns:36px 1fr auto;gap:12px;align-items:center;background:#f9fafb;border:1px solid #edf0f4;border-radius:20px;padding:14px}
.led{width:18px;height:18px;border-radius:50%;box-shadow:0 0 0 6px rgba(152,162,179,.12),0 0 18px rgba(152,162,179,.45);margin:auto}
.led.submitted{background:var(--blue);box-shadow:0 0 0 6px rgba(46,144,250,.13),0 0 22px rgba(46,144,250,.55)}
.led.review{background:var(--amber);box-shadow:0 0 0 6px rgba(247,144,9,.13),0 0 22px rgba(247,144,9,.55)}
.led.required{background:#f04438;box-shadow:0 0 0 6px rgba(240,68,56,.13),0 0 22px rgba(240,68,56,.55)}
.led.confirmed{background:var(--purple);box-shadow:0 0 0 6px rgba(122,90,248,.13),0 0 22px rgba(122,90,248,.55)}
.led.completed{background:var(--green);box-shadow:0 0 0 6px rgba(18,183,106,.13),0 0 22px rgba(18,183,106,.55)}
.step strong{display:block;font-size:15px}
.step span{display:block;color:var(--muted);font-size:13px;line-height:1.35;margin-top:3px}
.pill{border-radius:999px;padding:7px 10px;font-size:12px;font-weight:1000;background:#fff;border:1px solid #edf0f4;color:#475467}
.result{margin-top:18px;border-radius:24px;background:linear-gradient(180deg,#fff,#fff7f7);border:1px solid rgba(230,0,35,.14);padding:22px;display:none}
.result h2{margin:0 0 12px;color:#b8001c}
.result p{margin:7px 0;color:#344054;font-weight:800}
.side{position:sticky;top:16px;display:grid;gap:14px}
.legend{display:grid;gap:10px}
.legendRow{display:flex;align-items:center;gap:10px;padding:12px;border-radius:16px;background:#f9fafb;border:1px solid #edf0f4;font-weight:900}
.legendRow .led{margin:0;width:14px;height:14px}
.quick{display:grid;gap:10px}
.quick a{border-radius:18px;background:#111827;color:#fff;text-decoration:none;padding:14px 16px;font-weight:1000}
@media(max-width:900px){.shell{grid-template-columns:1fr}.side{position:static}}
@media(max-width:760px){.hero{grid-template-columns:1fr;text-align:center}.logoBox{max-width:170px;margin:auto}.hero h1{font-size:32px}.grid,.actions{grid-template-columns:1fr}.panel{padding:20px;border-radius:26px}.step{grid-template-columns:30px 1fr}}
</style>
</head>
<body>
<main class="wrap">
  <section class="hero">
    <div class="logoBox"><img src="/metadata/991_visa_logo_only.png" alt="ExpressVisa One"></div>
    <div>
      <h1>ExpressVisa Check Status</h1>
      <p>Track your appointment, document review, agent action and service progress.</p>
      <div class="heroBadges">
        <span class="badge">Live Stage Preview</span>
        <span class="badge">LED Status Colors</span>
        <span class="badge">Verified Agent Workflow</span>
      </div>
    </div>
  </section>

  <section class="shell">
    <div class="panel">
      <div class="notice"><span>🔎</span><div>Enter your booking reference, passport number, or WhatsApp number to check the latest service status.</div></div>

      <form method="get" onsubmit="document.querySelector('.result').style.display='block';return false;">
        <div class="grid">
          <div>
            <label>Booking / Status Ref</label>
            <input class="field" name="ref" placeholder="EV-BOOK-...">
          </div>
          <div>
            <label>Passport No.</label>
            <input class="field" name="passport_no" placeholder="Passport No.">
          </div>
          <div>
            <label>WhatsApp</label>
            <input class="field" name="whatsapp" placeholder="+60...">
          </div>
        </div>

        <div class="actions">
          <button class="submit" type="submit">Check Status</button>
          <a class="back" href="/booking/">Book Appointment</a>
        </div>
      </form>

      <div class="timeline">
        <div class="step"><i class="led submitted"></i><div><strong>Submitted</strong><span>Request received and queued for verified agent review.</span></div><em class="pill">Blue</em></div>
        <div class="step"><i class="led review"></i><div><strong>Under Review</strong><span>Agent is checking details, passport reference and uploaded documents.</span></div><em class="pill">Amber</em></div>
        <div class="step"><i class="led required"></i><div><strong>Document Required</strong><span>Extra document or clarification is needed before confirmation.</span></div><em class="pill">Red</em></div>
        <div class="step"><i class="led confirmed"></i><div><strong>Appointment Confirmed</strong><span>Date, time and next action have been confirmed.</span></div><em class="pill">Purple</em></div>
        <div class="step"><i class="led completed"></i><div><strong>Completed</strong><span>Service flow completed or case closed.</span></div><em class="pill">Green</em></div>
      </div>

      <div class="result">
        <h2>Status Preview</h2>
        <p><strong>Current Stage:</strong> <span style="color:#f79009">Under Review</span></p>
        <p><strong>Assigned Agent:</strong> Verified Agent</p>
        <p><strong>Next Step:</strong> Wait for agent confirmation or document request.</p>
      </div>
    </div>

    <aside class="side">
      <div class="panel">
        <h2 style="margin:0 0 14px">LED Color Guide</h2>
        <div class="legend">
          <div class="legendRow"><i class="led submitted"></i> Submitted</div>
          <div class="legendRow"><i class="led review"></i> Under Review</div>
          <div class="legendRow"><i class="led required"></i> Document Required</div>
          <div class="legendRow"><i class="led confirmed"></i> Appointment Confirmed</div>
          <div class="legendRow"><i class="led completed"></i> Completed</div>
        </div>
      </div>

      <div class="panel quick">
        <a href="/booking/">📅 New Booking</a>
        <a href="/wallet/">💳 Open Wallet</a>
        <a href="/post/">📣 Free Ads / Post Jobs</a>
      </div>
    </aside>
  </section>
</main>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
