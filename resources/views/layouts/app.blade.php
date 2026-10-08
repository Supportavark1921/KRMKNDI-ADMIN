<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'ARK Jyotish' }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <style>
        :root { --ink:#15233d; --muted:#69758b; --line:#e6e9f0; --saffron:#f39a31; --gold:#f6c453; --night:#12213b; --violet:#5c4bb7; --paper:#ffffff; }
        * { box-sizing:border-box; } body { margin:0; min-height:100vh; color:var(--ink); font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif; background:#f7f7fb; }
        .auth-page { min-height:100vh; display:grid; grid-template-columns:1fr 1fr; }
        .auth-art { position:relative; overflow:hidden; min-height:100%; padding:56px; color:#fff; background:radial-gradient(circle at 20% 25%, #835de6 0, transparent 32%), radial-gradient(circle at 72% 75%, #ee9c35 0, transparent 28%), linear-gradient(145deg,#101f3a 0%,#233f6e 52%,#5b3f91 100%); }
        .auth-art:before,.auth-art:after { content:""; position:absolute; border:1px solid rgba(255,255,255,.17); border-radius:50%; } .auth-art:before { width:560px; height:560px; right:-180px; top:-110px; } .auth-art:after { width:340px; height:340px; left:-125px; bottom:-120px; }
        .brand { position:relative; display:flex; align-items:center; gap:11px; font-size:18px; font-weight:750; letter-spacing:.02em; } .brand-mark { display:grid; place-items:center; width:35px; height:35px; border-radius:12px; background:linear-gradient(135deg,#ffd56c,#f49039); color:#432766; font-size:20px; }
        .sidebar-logo-img { display:block; height:38px; width:auto; object-fit:contain; }
        .art-copy { position:relative; z-index:1; max-width:490px; margin-top:18vh; } .eyebrow { color:#ffd779; font-size:12px; letter-spacing:.18em; text-transform:uppercase; font-weight:700; } h1 { margin:15px 0; font-size:clamp(38px,4vw,60px); line-height:1.04; letter-spacing:-.045em; } .art-copy p { max-width:405px; color:#dbe4f8; font-size:17px; line-height:1.6; }
        .zodiac { position:relative; z-index:1; display:grid; grid-template-columns:repeat(4,1fr); gap:10px; max-width:390px; margin-top:42px; } .zodiac span { padding:14px 10px; border:1px solid rgba(255,255,255,.18); border-radius:14px; background:rgba(255,255,255,.08); text-align:center; color:#ffe0a1; font-size:23px; }
        .auth-panel { display:flex; align-items:center; justify-content:center; padding:36px; background:var(--paper); } .form-wrap { width:min(100%,440px); } .form-wrap h2 { margin:0 0 8px; font-size:31px; letter-spacing:-.035em; } .subtext { margin:0 0 29px; color:var(--muted); }
        label { display:block; margin:17px 0 7px; font-size:14px; font-weight:650; } input { width:100%; padding:13px 14px; border:1px solid var(--line); border-radius:11px; outline:none; color:var(--ink); font:inherit; transition:.2s; } input:focus { border-color:#8b78df; box-shadow:0 0 0 4px #eeeaff; } .grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .roles { display:grid; grid-template-columns:1fr 1fr; gap:10px; } .role input { position:absolute; opacity:0; pointer-events:none; } .role span { display:block; border:1px solid var(--line); border-radius:12px; padding:13px; cursor:pointer; transition:.2s; } .role input:checked + span { border-color:#6f5acb; background:#f1eeff; color:#47358d; box-shadow:0 0 0 3px #eeeaff; } .role strong,.role small { display:block; } .role small { margin-top:3px; color:var(--muted); font-size:12px; }
        button { width:100%; margin-top:24px; padding:14px; border:0; border-radius:11px; cursor:pointer; color:#fff; font:700 15px inherit; background:linear-gradient(100deg,#5c4bb7,#7666d4); box-shadow:0 10px 20px #5c4bb735; transition:transform .2s,box-shadow .2s; } button:hover { transform:translateY(-1px); box-shadow:0 13px 25px #5c4bb745; } .form-foot { margin-top:24px; color:var(--muted); text-align:center; font-size:14px; } a { color:#5542ad; font-weight:700; text-decoration:none; }
        .notice { padding:12px 14px; border-radius:10px; margin-bottom:18px; font-size:14px; } .notice.success { color:#1d6a45; background:#e9f8ef; } .notice.error { color:#a43d43; background:#fff0f0; } .check { display:flex; gap:8px; align-items:center; margin-top:17px; font-size:14px; color:var(--muted); } .check input { width:auto; }
        .dashboard { min-height:100vh; padding:28px clamp(22px,6vw,90px); background:linear-gradient(145deg,#f9f7f2,#eef0fb); } .topbar { display:flex; justify-content:space-between; align-items:center; } .topbar .brand { color:var(--ink); } .logout { width:auto; margin:0; padding:10px 15px; box-shadow:none; } .hero { max-width:850px; margin:12vh auto 0; padding:52px; border-radius:25px; background:#fff; box-shadow:0 20px 60px #18284412; } .pill { display:inline-block; padding:7px 11px; border-radius:30px; color:#5d419f; background:#f0ecff; font-size:13px; font-weight:750; text-transform:capitalize; } .hero h1 { color:var(--night); font-size:48px; } .hero p { color:var(--muted); font-size:17px; line-height:1.7; } .cards { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-top:30px; } .card { padding:20px; border-radius:16px; background:#f7f6fb; } .card b { display:block; margin-bottom:6px; } .card span { color:var(--muted); font-size:14px; }
        .app-shell { min-height:100vh; display:flex; background:#f5f6fb; } .sidebar { position:sticky; top:0; display:flex; flex:0 0 265px; height:100vh; flex-direction:column; padding:28px 17px 20px; color:#e7e9ff; background:linear-gradient(180deg,#172443,#111a33); overflow-y:auto; scrollbar-width:thin; scrollbar-color:#ffffff18 transparent; } .sidebar::-webkit-scrollbar { width:4px; } .sidebar::-webkit-scrollbar-track { background:transparent; } .sidebar::-webkit-scrollbar-thumb { border-radius:4px; background:#ffffff22; } .sidebar-brand { display:flex; align-items:center; gap:10px; padding:0 10px 35px; color:#fff; font-size:19px; font-weight:500; } .sidebar-brand b { color:#ffd575; } .sidebar-label { padding:0 12px 10px; color:#8290b3; font-size:11px; font-weight:800; letter-spacing:.13em; text-transform:uppercase; } .sidebar-nav { display:grid; gap:5px; } .sidebar-nav a { display:flex; align-items:center; gap:13px; padding:12px; border-radius:10px; color:#bfc9e2; font-size:14px; font-weight:600; } .sidebar-nav a span { display:grid; width:18px; place-items:center; color:#93a3ca; font-size:19px; } .sidebar-nav a:hover,.sidebar-nav a.active { color:#fff; background:#ffffff14; } .sidebar-nav a.active { box-shadow:inset 3px 0 #f3b44c; } .sidebar-nav a.active span { color:#ffd575; } .sidebar-coming { margin:32px 6px auto; padding:17px; border:1px solid #ffffff16; border-radius:14px; background:#ffffff09; } .sidebar-coming span { color:#ffd575; font-size:12px; font-weight:750; } .sidebar-coming p { margin:7px 0 0; color:#aeb9d5; font-size:12px; line-height:1.5; } .sidebar-user { display:flex; align-items:center; gap:10px; margin:18px 6px 0; padding:14px 0 0; border-top:1px solid #ffffff17; } .sidebar-user b,.sidebar-user small { display:block; } .sidebar-user b { color:#f5f6ff; font-size:13px; } .sidebar-user small { margin-top:2px; color:#9facc9; font-size:11px; } .avatar { display:grid; width:33px; height:33px; place-items:center; border-radius:50%; color:#513a14; background:linear-gradient(135deg,#ffda77,#ed9d47); font-size:13px; font-weight:800; } .app-main { min-width:0; flex:1; } .app-main .dashboard { min-height:100vh; padding:28px clamp(25px,5vw,80px); background:radial-gradient(circle at 95% 2%,#e8e1ff 0,transparent 24%),#f7f8fc; } .app-main .topbar { min-height:42px; padding-bottom:20px; border-bottom:1px solid #e8eaf2; } .app-main .topbar .brand { display:none; } .app-main .topbar .logout { padding:9px 14px; font-size:13px; } .app-main .topbar .nav-link { margin-left:auto; margin-right:12px; } .app-main .hero { max-width:1000px; margin:54px auto 0; border:1px solid #ececf3; box-shadow:0 24px 70px #23325d10; } .app-main .hero h1 { max-width:650px; } .dashboard-booking-link { max-width:1000px; margin:0 auto; } .booking-card,.appointments-wrap { max-width:930px; margin:38px auto; padding:42px; border:1px solid #ececf3; border-radius:25px; background:#fff; box-shadow:0 20px 60px #18284412; } .booking-card h1,.appointments-wrap h1 { font-size:42px; color:var(--night); } .booking-card p,.appointments-wrap p { color:var(--muted); line-height:1.6; } .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:0 14px; } select,textarea { width:100%; padding:13px 14px; border:1px solid var(--line); border-radius:11px; color:var(--ink); background:#fff; font:inherit; } textarea { resize:vertical; } em { color:var(--muted); font-weight:400; font-style:normal; } .nav-link { color:var(--violet); } .primary-link,.secondary-link { display:inline-block; margin-top:20px; padding:12px 16px; border-radius:11px; } .primary-link { color:#fff; background:var(--violet); box-shadow:0 8px 17px #5c4bb72b; } .secondary-link { margin-left:8px; background:#f1eeff; } .section-heading { display:flex; justify-content:space-between; gap:20px; } .section-heading .primary-link { margin:0; height:fit-content; white-space:nowrap; } .appointment-list { display:grid; gap:12px; } .appointment-row { display:flex; align-items:center; gap:18px; padding:18px; border:1px solid var(--line); border-radius:16px; transition:box-shadow .2s; } .appointment-row:hover { box-shadow:0 9px 25px #24315a0c; } .appointment-date { display:grid; place-items:center; min-width:55px; padding:8px; border-radius:12px; color:#54419c; background:#f2effc; } .appointment-date strong { font-size:22px; } .appointment-date span { font-size:12px; font-weight:700; } .appointment-info { flex:1; } .appointment-info h3 { margin:0 0 4px; } .appointment-info p { margin:2px 0; font-size:14px; } .appointment-action { min-width:110px; text-align:right; } .appointment-action select { margin-top:8px; padding:8px; font-size:13px; } .status { display:inline-block; padding:6px 9px; border-radius:20px; font-size:12px; font-weight:750; } .status-pending { color:#8a5a11; background:#fff3d7; } .status-confirmed { color:#276946; background:#e4f6ea; } .status-completed { color:#3a538a; background:#e9effe; } .status-cancelled { color:#8d4343; background:#fdeaea; } .empty-state { padding:40px; text-align:center; border:1px dashed #d8d4e8; border-radius:16px; }
        .page-heading span,.page-heading small { display:block; } .page-heading span { color:var(--ink); font-size:18px; font-weight:800; } .page-heading small { margin-top:3px; color:var(--muted); font-size:12px; } .dashboard-welcome { display:flex; align-items:center; justify-content:space-between; gap:30px; max-width:1100px; margin:35px auto 28px; padding:42px 46px; overflow:hidden; border-radius:24px; color:#fff; background:radial-gradient(circle at 88% 5%,#9d80ef 0,transparent 31%),linear-gradient(120deg,#172b4d,#3c377d); box-shadow:0 18px 42px #1c295530; } .dashboard-welcome h1 { margin:10px 0; font-size:42px; color:#fff; } .dashboard-welcome p { max-width:600px; margin:0; color:#dce4fb; line-height:1.6; } .dashboard-welcome .pill { color:#fff0bd; background:#ffffff19; } .large-action { flex:0 0 auto; margin:0; color:#372657; background:linear-gradient(120deg,#ffdd7c,#f0ab4d); box-shadow:none; } .large-action span { margin-left:8px; font-size:19px; } .dashboard-grid { display:grid; grid-template-columns:minmax(0,1.5fr) minmax(270px,.8fr); gap:20px; max-width:1100px; margin:0 auto 38px; } .next-appointment,.help-card { padding:28px; border:1px solid #e6e8f0; border-radius:19px; background:#fff; } .card-heading,.section-title { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; } .card-heading h2,.help-card h2,.services-section h2 { margin:5px 0 0; color:var(--night); font-size:22px; } .card-heading a,.section-title > a { font-size:13px; white-space:nowrap; } .section-kicker { color:#8268c3; font-size:11px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; } .next-details { display:flex; align-items:center; gap:15px; margin-top:23px; } .date-tile { display:grid; min-width:62px; padding:10px; place-items:center; border-radius:13px; color:#5a419c; background:#f1edfc; } .date-tile strong { font-size:25px; } .date-tile span { font-size:12px; font-weight:800; text-transform:uppercase; } .next-details b { font-size:15px; } .next-details p,.empty-copy { margin:4px 0 10px; color:var(--muted); font-size:14px; } .help-card { background:linear-gradient(145deg,#fffdf7,#fff4d6); border-color:#f6e5bd; } .help-icon { display:grid; width:39px; height:39px; place-items:center; border-radius:12px; color:#805018; background:#ffe5a2; font-size:20px; } .help-card p { color:#735f44; font-size:14px; line-height:1.6; } .help-card a { font-size:13px; } .services-section { max-width:1100px; margin:0 auto; } .service-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:18px; } .service-card { display:block; padding:23px; border:1px solid #e6e8f0; border-radius:18px; color:var(--ink); background:#fff; transition:transform .2s,box-shadow .2s; } .service-card:hover { transform:translateY(-3px); box-shadow:0 15px 28px #26365b14; } .service-icon { display:grid; width:39px; height:39px; place-items:center; border-radius:12px; color:#5b43a0; background:#f0ecff; font-size:21px; } .service-card h3 { margin:17px 0 7px; } .service-card p { min-height:43px; margin:0 0 15px; color:var(--muted); font-size:13px; line-height:1.55; } .service-card > span:last-child { color:#654eb1; font-size:13px; font-weight:750; } .metric-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; max-width:1100px; margin:0 auto; } .metric-grid article { padding:25px; border:1px solid #e6e8f0; border-radius:18px; background:#fff; } .metric-grid span,.metric-grid small { display:block; color:var(--muted); font-size:13px; } .metric-grid strong { display:block; margin:9px 0 5px; color:var(--night); font-size:34px; }
        .appointments-page { padding-bottom:65px !important; } .topbar-link { padding:9px 13px; border-radius:9px; background:#efedfa; font-size:13px; } .appointments-hero { display:flex; align-items:center; justify-content:space-between; gap:30px; max-width:1200px; margin:35px auto 20px; padding:40px 45px; overflow:hidden; border-radius:22px; color:#fff; background:radial-gradient(circle at 88% 15%,#c89967 0,transparent 24%),radial-gradient(circle at 76% 90%,#6450af 0,transparent 32%),linear-gradient(118deg,#142444,#333268); } .hero-overline { color:#ffdc87; font-size:11px; font-weight:800; letter-spacing:.13em; text-transform:uppercase; } .appointments-hero h1 { margin:9px 0; font-size:38px; color:#fff; } .appointments-hero p { max-width:630px; margin:0; color:#dfe5f8; line-height:1.6; } .hero-book-button { display:flex; flex:0 0 auto; align-items:center; gap:8px; padding:14px 18px; border-radius:12px; color:#3d2c5c; background:#ffdc7d; font-size:14px; font-weight:800; box-shadow:0 11px 25px #08142f2e; } .hero-book-button span { font-size:19px; } .appointment-summary { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; max-width:1200px; margin:0 auto 22px; } .appointment-summary article { display:flex; align-items:center; gap:12px; padding:17px 20px; border:1px solid #e6e8ef; border-radius:15px; background:#fff; } .summary-icon { display:grid; width:36px; height:36px; place-items:center; border-radius:10px; font-weight:800; } .summary-icon.all { color:#5b49aa; background:#efedfd; } .summary-icon.pending { color:#9a671a; background:#fff1d5; } .summary-icon.confirmed { color:#28724d; background:#e4f6e9; } .appointment-summary strong,.appointment-summary small { display:block; } .appointment-summary strong { color:var(--night); font-size:20px; } .appointment-summary small { color:var(--muted); font-size:12px; } .appointment-board { max-width:1200px; margin:0 auto; padding:28px; border:1px solid #e4e7f0; border-radius:20px; background:#fff; box-shadow:0 18px 45px #23325c0b; } .board-header { display:flex; align-items:flex-start; justify-content:space-between; gap:15px; padding:0 2px 22px; border-bottom:1px solid #eceef4; } .board-header h2 { margin:0; font-size:21px; } .board-header p { margin:5px 0 0; color:var(--muted); font-size:13px; } .board-badge { padding:6px 9px; border-radius:20px; color:#6654a7; background:#f1effa; font-size:11px; font-weight:750; } .appointment-board .appointment-list { margin-top:14px; } .appointment-board .appointment-row { display:grid; grid-template-columns:70px minmax(0,1fr) 145px; gap:19px; padding:20px 10px; border:0; border-bottom:1px solid #edf0f5; border-radius:0; } .appointment-board .appointment-row:last-child { border-bottom:0; } .appointment-board .appointment-row:hover { border-radius:13px; background:#fafaff; box-shadow:none; } .appointment-board .appointment-date { min-width:0; padding:7px; background:#f3f0fd; } .appointment-date small { color:#8d84a9; font-size:10px; font-weight:750; text-transform:uppercase; } .service-label { color:#755ab9; font-size:11px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; } .appointment-board .appointment-info h3 { margin:3px 0 5px; font-size:16px; } .appointment-board .appointment-info p { display:flex; align-items:center; gap:7px; margin:0; color:#536078; font-size:13px; } .appointment-board .appointment-info small { display:block; margin-top:4px; color:#8a95a9; font-size:12px; } .time-dot { width:6px; height:6px; border-radius:50%; background:#eeac4f; } .appointment-info i { width:3px; height:3px; border-radius:50%; background:#aab1bf; } .appointment-board .note { margin-top:8px !important; color:#7e7591 !important; font-style:italic; } .appointment-board .appointment-action { display:flex; min-width:0; flex-direction:column; align-items:flex-end; gap:9px; } .appointment-board .appointment-action form { width:100%; } .appointment-board .appointment-action select { margin:0; } .appointment-board .appointment-action a { color:#6b55b1; font-size:12px; font-weight:750; } .visually-hidden { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; } .appointment-empty { margin-top:18px; border:0; background:#faf9ff; } .appointment-empty > span { display:grid; width:46px; height:46px; margin:0 auto 12px; place-items:center; border-radius:14px; color:#765ab9; background:#eeeaff; font-size:24px; }
        .admin-appointments { background:radial-gradient(circle at 84% 3%,#dcefe9 0,transparent 23%),#f5f8f7 !important; } .admin-appointments .topbar { border-color:#dfe8e5; } .admin-appointments .topbar-link { color:#146650; background:#e2f3ec; } .admin-appointments .appointments-hero { position:relative; border:1px solid #244d48; background:radial-gradient(circle at 63% 0%,#32645c 0,transparent 31%),radial-gradient(circle at 96% 95%,#ba8740 0,transparent 27%),linear-gradient(120deg,#0d2829,#164442); } .admin-appointments .hero-overline { color:#f8ca73; } .admin-appointments .admin-hero-panel { display:grid; min-width:180px; gap:3px; padding:16px 18px; border:1px solid #ffffff29; border-radius:15px; background:#ffffff12; backdrop-filter:blur(6px); } .admin-hero-panel span,.admin-hero-panel small { color:#d5e7e1; font-size:11px; } .admin-hero-panel strong { color:#ffcf78; font-size:25px; } .admin-hero-panel a { margin-top:6px; color:#fff; font-size:12px; font-weight:800; } .admin-appointments .appointment-summary article { border-color:#dce8e2; background:#fbfefd; } .admin-appointments .summary-icon.all { color:#16644f; background:#dff2e9; } .admin-appointments .summary-icon.pending { color:#9b6613; background:#fff0d3; } .admin-appointments .summary-icon.confirmed { color:#fff; background:#278467; } .admin-appointments .appointment-board { border-color:#dce6e2; box-shadow:0 18px 45px #17463c0d; } .admin-appointments .board-header { padding:4px 4px 23px; border-color:#e1ebe7; } .admin-appointments .board-badge { color:#1d745b; background:#e5f5ed; } .admin-appointments .appointment-board .appointment-row { padding:21px 12px; } .admin-appointments .appointment-board .appointment-row:hover { background:#f6fbf8; } .admin-appointments .appointment-board .appointment-date { color:#17644f; background:#e4f4ec; } .admin-appointments .appointment-date small { color:#629c8a; } .admin-appointments .service-label { color:#297a65; } .admin-appointments .time-dot { background:#d89934; } .admin-appointments .appointment-action select { border-color:#c8ded5; color:#205c4e; background:#f7fcf9; font-weight:650; } .admin-appointments .status-pending { color:#905b0b; background:#fff0d1; } .admin-appointments .status-confirmed { color:#17664d; background:#dff3e8; } .admin-appointments .status-completed { color:#225e85; background:#e1f1fb; } .admin-appointments .status-cancelled { color:#984546; background:#fce9e9; }
        .booking-meta { display:flex; flex-wrap:wrap; gap:7px; margin-top:10px; } .booking-meta span { padding:4px 7px; border-radius:6px; color:#738097; background:#f3f5f9; font-size:10px; font-weight:700; } .client-detail { display:flex; align-items:center; gap:9px; margin-top:12px; } .client-avatar { display:grid; width:28px; height:28px; place-items:center; border-radius:50%; color:#fff; background:#367e6b; font-size:11px; font-weight:800; } .client-detail b,.client-detail small { display:block; } .client-detail b { color:#2b3b4f; font-size:13px; } .client-detail small { margin-top:2px; color:#7b8798; font-size:11px; } .member-detail { display:flex; gap:8px; margin-top:12px; font-size:12px; } .member-detail span { color:#8993a4; } .member-detail b { color:#48566d; } .appointment-board .appointment-row { align-items:start; } .appointment-board .appointment-action { min-width:150px; } .status-form { display:grid; width:100%; gap:6px; padding:11px; border:1px solid #e1e7e4; border-radius:10px; background:#f9fcfa; text-align:left; } .status-form label { margin:0; color:#63736e; font-size:10px; font-weight:800; letter-spacing:.04em; text-transform:uppercase; } .status-form select { padding:8px 9px; } .status-save { width:100%; margin:0; padding:8px 9px; border-radius:8px; color:#fff; background:#237861; box-shadow:none; font-size:11px; } .status-save:hover { box-shadow:none; } .book-again { margin-top:4px; color:#6954af !important; font-size:12px; font-weight:750; } .member-appointments .appointment-board .appointment-action { min-width:135px; }
        .field-help { display:block; margin-top:6px; color:#788398; font-size:11px; } .availability-page { background:radial-gradient(circle at 82% 0,#dcefe9 0,transparent 25%),#f6f9f8 !important; } .availability-hero { display:flex; align-items:center; justify-content:space-between; gap:30px; max-width:1100px; margin:35px auto 22px; padding:37px 42px; border-radius:22px; color:#fff; background:radial-gradient(circle at 89% 5%,#458879 0,transparent 29%),linear-gradient(120deg,#12312f,#23554c); } .availability-hero h1 { margin:8px 0; color:#fff; font-size:38px; } .availability-hero p { max-width:650px; margin:0; color:#d8ece5; line-height:1.6; } .availability-legend { display:grid; gap:9px; min-width:185px; padding:15px; border:1px solid #ffffff22; border-radius:12px; background:#ffffff0e; color:#edf9f5; font-size:12px; } .availability-legend i { display:inline-block; width:8px; height:8px; margin-right:7px; border-radius:50%; } .availability-legend .open { background:#7ee1a9; } .availability-legend .closed { background:#d3a26c; } .availability-board { max-width:1100px; margin:0 auto; padding:27px; border:1px solid #dde8e3; border-radius:20px; background:#fff; box-shadow:0 18px 46px #173f350c; } .availability-board .board-header { align-items:center; } .availability-save { width:auto; margin:0; padding:11px 16px; border-radius:9px; background:#237a61; box-shadow:none; font-size:13px; } .schedule-head,.schedule-row { display:grid; grid-template-columns:1.1fr 1.15fr 2fr 1fr; gap:16px; align-items:center; } .schedule-head { margin-top:22px; padding:11px 14px; color:#788780; background:#f4f8f6; font-size:10px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; } .schedule-row { padding:15px 14px; border-bottom:1px solid #edf1ef; } .schedule-row:last-of-type { border-bottom:0; } .day-name { display:flex; align-items:center; gap:10px; } .day-round { display:grid; width:30px; height:30px; place-items:center; border-radius:9px; color:#267259; background:#e4f4ec; font-size:12px; font-weight:800; } .day-name b { font-size:14px; } .switch { display:flex; align-items:center; gap:8px; margin:0; cursor:pointer; } .switch input { position:absolute; opacity:0; } .switch > span { position:relative; width:34px; height:19px; border-radius:12px; background:#ccd5d1; transition:.2s; } .switch > span:after { position:absolute; top:3px; left:3px; width:13px; height:13px; border-radius:50%; background:#fff; content:""; transition:.2s; } .switch input:checked + span { background:#2c9a73; } .switch input:checked + span:after { transform:translateX(15px); } .switch em { color:#718078; font-size:12px; font-style:normal; } .hours { display:flex; align-items:center; gap:7px; } .hours input { padding:9px; } .hours span { color:#88958f; font-size:12px; } .schedule-row select { padding:10px; font-size:13px; }
        .notification-nav { position:relative; } .notification-nav b { display:grid; min-width:17px; height:17px; margin-left:auto; place-items:center; border-radius:10px; color:#4b2f0f; background:#ffd16e; font-size:10px; } .notifications-hero { display:flex; align-items:center; justify-content:space-between; max-width:900px; margin:35px auto 20px; padding:38px 42px; border-radius:22px; color:#fff; background:radial-gradient(circle at 91% 12%,#af91ec 0,transparent 24%),linear-gradient(120deg,#1c3159,#51418f); } .notifications-hero h1 { margin:8px 0; color:#fff; font-size:38px; } .notifications-hero p { max-width:570px; margin:0; color:#dce4fc; line-height:1.6; } .bell-mark { display:grid; width:73px; height:73px; place-items:center; border:1px solid #ffffff2b; border-radius:22px; color:#ffe192; background:#ffffff13; font-size:35px; } .notifications-board { max-width:900px; margin:0 auto; padding:26px; border:1px solid #e5e7f0; border-radius:20px; background:#fff; box-shadow:0 17px 40px #2b33670e; } .read-all { width:auto; margin:0; padding:9px 12px; border-radius:8px; color:#5b48a3; background:#f0edfc; box-shadow:none; font-size:12px; } .notification-list { margin-top:14px; } .notification-item { margin:0; border-bottom:1px solid #edf0f5; } .notification-item:last-child { border:0; } .notification-item button { display:flex; width:100%; align-items:center; gap:13px; margin:0; padding:17px 10px; border-radius:10px; color:var(--ink); background:transparent; box-shadow:none; text-align:left; } .notification-item button:hover { transform:none; background:#f8f7fd; box-shadow:none; } .notification-item.is-unread button { background:#faf9ff; } .notification-icon { display:grid; width:37px; height:37px; flex:0 0 auto; place-items:center; border-radius:11px; color:#614cac; background:#ece8ff; font-size:17px; } .notification-icon.confirmed { color:#1e704d; background:#e0f3e8; } .notification-icon.cancelled { color:#9a4848; background:#fceaea; } .notification-copy { display:grid; gap:3px; } .notification-copy b { font-size:14px; } .notification-copy span { color:#606d83; font-size:13px; font-weight:400; line-height:1.45; } .notification-copy small { color:#929bac; font-size:11px; font-weight:400; } .notification-item i { width:7px; height:7px; margin-left:auto; border-radius:50%; background:#7658cc; }
        /* ── Admin / Store UI ─────────────────────────────────────────────── */
        .store-header { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:22px; flex-wrap:wrap; }
        .store-title { margin:0 0 4px; font-size:26px; font-weight:800; color:var(--night); letter-spacing:-.03em; }
        .store-sub { margin:0; color:var(--muted); font-size:14px; }
        .store-card { padding:24px; border:1px solid #e6e9f0; border-radius:18px; background:#fff; box-shadow:0 4px 18px #1b2c5508; margin-bottom:20px; }
        .store-card + .store-card { margin-top:0; }
        .detail-list { display:grid; grid-template-columns:140px 1fr; gap:10px 16px; margin:0; font-size:13px; } .detail-list dt { color:#8290b3; font-weight:700; padding-top:2px; } .detail-list dd { margin:0; color:var(--ink); }
        .store-table { width:100%; border-collapse:collapse; font-size:14px; }
        .store-table thead tr { border-bottom:2px solid #ebedf5; }
        .store-table th { padding:10px 12px; color:#8290b3; font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; text-align:left; white-space:nowrap; }
        .store-table td { padding:13px 12px; border-bottom:1px solid #f0f2f8; color:var(--ink); vertical-align:middle; }
        .store-table tbody tr:last-child td { border-bottom:0; }
        .store-table tbody tr:hover td { background:#fafbff; }
        /* Buttons */
        .btn-primary,.btn-secondary,.btn-danger,.btn-success,.btn-warning { display:inline-flex; align-items:center; gap:6px; padding:10px 18px; border:0; border-radius:9px; cursor:pointer; font:700 13px inherit; text-decoration:none; transition:opacity .15s,transform .1s; white-space:nowrap; }
        .btn-primary:hover,.btn-secondary:hover,.btn-danger:hover,.btn-success:hover,.btn-warning:hover { opacity:.88; transform:translateY(-1px); }
        .btn-primary { color:#fff; background:linear-gradient(100deg,#5c4bb7,#7666d4); box-shadow:0 6px 16px #5c4bb72a; }
        .btn-secondary { color:var(--ink); background:#eef0f8; box-shadow:none; }
        .btn-danger { color:#fff; background:#dc3545; box-shadow:none; }
        .btn-success { color:#fff; background:#28a745; box-shadow:none; }
        .btn-warning { color:#2d1f00; background:#ffc107; box-shadow:none; }
        .btn-sm { display:inline-flex; align-items:center; gap:4px; padding:6px 10px; border:0; border-radius:7px; cursor:pointer; font:700 12px inherit; text-decoration:none; color:var(--ink); background:#eef0f8; transition:opacity .15s; white-space:nowrap; }
        .btn-sm:hover { opacity:.8; }
        .btn-sm.btn-danger { color:#fff; background:#dc3545; }
        .btn-sm.btn-success { color:#fff; background:#28a745; }
        .btn-sm.btn-warning { color:#2d1f00; background:#ffc107; }
        /* Badges */
        .badge { display:inline-block; padding:4px 9px; border-radius:20px; font-size:11px; font-weight:750; }
        .badge-active,.badge-admin,.badge-confirmed,.badge-paid,.badge-delivered { color:#1a6b43; background:#dcf5e8; }
        .badge-inactive,.badge-cancelled,.badge-suspended { color:#962a2a; background:#fce8e8; }
        .badge-pending,.badge-draft { color:#7a5a10; background:#fff3d2; }
        .badge-manager { color:#2c5f9e; background:#ddeeff; }
        .badge-support { color:#5a3a8e; background:#ede9ff; }
        .badge-guruji { color:#7a4205; background:#fff0de; }
        .badge-vendor { color:#1f6b5a; background:#d7f5ec; }
        .badge-user { color:#444f6a; background:#eef0f8; }
        /* Form elements */
        .form-group { display:flex; flex-direction:column; gap:5px; }
        .form-label { font-size:13px; font-weight:700; color:var(--ink); }
        .form-hint { margin:3px 0 0; color:var(--muted); font-size:12px; }
        .form-error { margin:3px 0 0; color:#c93434; font-size:12px; }
        .form-input { width:100%; padding:11px 13px; border:1px solid var(--line); border-radius:9px; color:var(--ink); background:#fff; font:14px/1.4 inherit; transition:border-color .2s,box-shadow .2s; }
        .form-input:focus { outline:none; border-color:#8b78df; box-shadow:0 0 0 3px #eeeaff; }
        .form-input.is-invalid { border-color:#dc3545; }
        select.form-input { appearance:auto; }
        textarea.form-input { resize:vertical; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        /* Alerts */
        .alert-success { padding:12px 16px; border-radius:10px; color:#1a6b43; background:#dcf5e8; border:1px solid #b6eacb; margin-bottom:14px; font-size:14px; }
        .alert-error { padding:12px 16px; border-radius:10px; color:#962a2a; background:#fce8e8; border:1px solid #f5baba; margin-bottom:14px; font-size:14px; }
        /* Pagination override */
        nav[aria-label="pagination"] { font-size:13px; }
        /* Opacity util */
        .opacity-50 { opacity:.5; }
        @media (max-width:800px) { .form-grid { grid-template-columns:1fr; } .store-table { font-size:12px; } .store-table th,.store-table td { padding:9px 8px; } }
        /* ── End Admin / Store UI ─────────────────────────────────────────── */
        @media (max-width:800px) { .auth-page { grid-template-columns:1fr; } .auth-art { min-height:270px; padding:30px; } .art-copy { margin-top:48px; } .art-copy h1,.art-copy p,.zodiac { display:none; } .auth-panel { padding:42px 24px; } .sidebar { flex-basis:72px; padding:22px 10px; align-items:center; } .sidebar-brand { padding:0 0 30px; } .sidebar-brand > span:not(.brand-mark),.sidebar-label,.sidebar-coming,.sidebar-user { display:none; } .sidebar-nav a { justify-content:center; padding:12px; } .sidebar-nav a span { font-size:21px; } .sidebar-nav a:not(.active) { font-size:0; } .sidebar-nav a.active { font-size:0; } .notification-nav b { position:absolute; top:4px; right:2px; } .app-main .dashboard { padding:20px; } .hero,.booking-card,.appointments-wrap { padding:30px; margin-top:35px; } .cards,.form-grid,.metric-grid,.appointment-summary { grid-template-columns:1fr; } .section-heading,.appointment-row,.dashboard-welcome,.appointments-hero,.availability-hero { flex-direction:column; align-items:flex-start; } .dashboard-welcome,.appointments-hero,.availability-hero { padding:30px; } .dashboard-grid,.service-grid { grid-template-columns:1fr; } .appointment-action { text-align:left; } .appointment-board { padding:20px; } .appointment-board .appointment-row { display:flex; } .appointment-board .appointment-action { align-items:flex-start; } .schedule-head { display:none; } .schedule-row { grid-template-columns:1fr; gap:10px; } .availability-board { padding:20px; } .notifications-hero { padding:30px; } .bell-mark { display:none; } } @media (max-width:420px) { .grid,.roles { grid-template-columns:1fr; } .booking-card,.appointments-wrap { padding:22px; } .booking-card h1,.appointments-wrap h1,.hero h1,.dashboard-welcome h1,.appointments-hero h1,.availability-hero h1,.notifications-hero h1 { font-size:34px; } }
    </style>
</head>
<body>
    @auth
        <div class="app-shell">
            <aside class="sidebar">
                <a class="sidebar-brand" href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/krmknd-brand-logo.png') }}" alt="krmknd" class="sidebar-logo-img">
                </a>
                <span class="sidebar-label">Workspace</span>
                <nav class="sidebar-nav">
                    <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span>⌂</span> Dashboard</a>
                    <a class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}"><span>◷</span> Appointments</a>
                    <a class="notification-nav {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}"><span>♢</span> Notifications @if(auth()->user()->unreadNotifications()->count())<b>{{ auth()->user()->unreadNotifications()->count() }}</b>@endif</a>
                    @if(auth()->user()->role === 'admin')
                    <a class="{{ request()->routeIs('push-notifications.*') ? 'active' : '' }}" href="{{ route('push-notifications.create') }}"><span>🔔</span> Send Push</a>
                    @endif
                    @if(auth()->user()->role !== 'admin')
                        <a class="{{ request()->routeIs('appointments.create') ? 'active' : '' }}" href="{{ route('appointments.create') }}"><span>＋</span> Book a session</a>
                        <a class="{{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}"><span>◉</span> My Profile</a>
                    @else
                        <a class="{{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}"><span>◉</span> Clients</a>
                    @endif
                </nav>
                @php $role = auth()->user()->role; @endphp

                {{-- People & Access (admin-level permissions) --}}
                @canany(['users.view','roles.view','audit-log.view'])
                <span class="sidebar-label" style="margin-top:18px">People</span>
                <nav class="sidebar-nav">
                    @can('users.view')
                    <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><span>👤</span> Users</a>
                    @endcan
                    @can('roles.view')
                    <a class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}"><span>🔑</span> Roles &amp; Permissions</a>
                    @endcan
                    @can('audit-log.view')
                    <a class="{{ request()->routeIs('admin.audit.*') ? 'active' : '' }}" href="{{ route('admin.audit.index') }}"><span>📋</span> Audit Log</a>
                    @endcan
                </nav>
                @endcanany

                {{-- Operations --}}
                @canany(['availability.view','services.view','gurus.view','donation-categories.view','donations.view','locations.view','panchang.view','api-docs.view'])
                <span class="sidebar-label" style="margin-top:18px">Operations</span>
                <nav class="sidebar-nav">
                    @can('availability.view')
                    <a class="{{ request()->routeIs('availability.*') ? 'active' : '' }}" href="{{ route('availability.index') }}"><span>▦</span> Availability</a>
                    @endcan
                    @can('services.view')
                    <a class="{{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}"><span>✦</span> Services</a>
                    @endcan
                    @can('gurus.view')
                    <a class="{{ request()->routeIs('gurus.*') ? 'active' : '' }}" href="{{ route('gurus.index') }}"><span>🕉</span> Gurujis</a>
                    @endcan
                    @can('donation-categories.view')
                    <a class="{{ request()->routeIs('donation-categories.*') ? 'active' : '' }}" href="{{ route('donation-categories.index') }}"><span>❧</span> Don. Categories</a>
                    @endcan
                    @can('donations.view')
                    <a class="{{ request()->routeIs('donations.*') ? 'active' : '' }}" href="{{ route('donations.index') }}"><span>₹</span> Donations</a>
                    @endcan
                    @can('locations.view')
                    <a class="{{ request()->routeIs('admin.location.*') ? 'active' : '' }}" href="{{ route('admin.location.sync') }}"><span>📍</span> Location Data</a>
                    @endcan
                    @can('panchang.view')
                    <a class="{{ request()->routeIs('admin.panchang.monitor') ? 'active' : '' }}" href="{{ route('admin.panchang.monitor') }}"><span>🌙</span> Panchang Monitor</a>
                    <a class="{{ request()->routeIs('admin.panchang.test') ? 'active' : '' }}" href="{{ route('admin.panchang.test') }}"><span>🧪</span> Panchang Test</a>
                    @endcan
                    @can('api-docs.view')
                    <a class="{{ request()->routeIs('api.docs') ? 'active' : '' }}" href="{{ route('api.docs') }}"><span>⎇</span> API Docs</a>
                    @endcan
                </nav>
                @endcanany

                {{-- App Content --}}
                @canany(['promotions.view','articles.view'])
                <span class="sidebar-label" style="margin-top:18px">App Content</span>
                <nav class="sidebar-nav">
                    @can('promotions.view')
                    <a class="{{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}" href="{{ route('admin.promotions.index') }}"><span>📣</span> Promotions</a>
                    @endcan
                    @can('articles.view')
                    <a class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}"><span>📰</span> Articles</a>
                    @endcan
                </nav>
                @endcanany

                {{-- Vastra Store --}}
                @can('products.view')
                <span class="sidebar-label" style="margin-top:18px">🛍 Vastra Store</span>
                <nav class="sidebar-nav">
                    @can('matajis.view')
                    <a class="{{ request()->routeIs('admin.store.matajis.*') ? 'active' : '' }}" href="{{ route('admin.store.matajis.index') }}"><span>🕉</span> Matajis</a>
                    @endcan
                    @can('categories.view')
                    <a class="{{ request()->routeIs('admin.store.categories.*') ? 'active' : '' }}" href="{{ route('admin.store.categories.index') }}"><span>📦</span> Categories</a>
                    @endcan
                    @can('vendors.view')
                    <a class="{{ request()->routeIs('admin.store.vendors.*') ? 'active' : '' }}" href="{{ route('admin.store.vendors.index') }}"><span>🏪</span> Vendors</a>
                    @endcan
                    <a class="{{ request()->routeIs('admin.store.products.*') ? 'active' : '' }}" href="{{ route('admin.store.products.index') }}"><span>🛒</span> Products</a>
                    @can('inventory.view')
                    <a class="{{ request()->routeIs('admin.store.inventory.*') ? 'active' : '' }}" href="{{ route('admin.store.inventory.index') }}"><span>📊</span> Inventory</a>
                    @endcan
                    @can('samagri-orders.view')
                    <a class="{{ request()->routeIs('admin.samagri-orders.*') ? 'active' : '' }}" href="{{ route('admin.samagri-orders.index') }}"><span>🧾</span> Samagri Orders</a>
                    @endcan
                </nav>
                @endcan

                {{-- Guruji section --}}
                @can('mataji-orders.view')
                @if($role === 'guruji')
                <span class="sidebar-label" style="margin-top:18px">Guruji</span>
                <nav class="sidebar-nav">
                    <a class="{{ request()->routeIs('donations.*') ? 'active' : '' }}" href="{{ route('donations.index') }}"><span>₹</span> My Donations</a>
                    <a class="{{ request()->routeIs('admin.mataji-orders.*') ? 'active' : '' }}" href="{{ route('admin.mataji-orders.index') }}"><span>🧵</span> Mataji Orders</a>
                </nav>
                @endif
                @endcan

                @php
                    $roleLabel = match($role) {
                        'admin'    => 'Administrator',
                        'manager'  => 'Manager',
                        'support'  => 'Support',
                        'guruji'   => 'Guruji',
                        'vendor'   => 'Vendor',
                        default    => 'Member',
                    };
                @endphp
                <div class="sidebar-user"><div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><div><b>{{ auth()->user()->name }}</b><small>{{ $roleLabel }}</small></div></div>
            </aside>
            <div class="app-main">@yield('content')</div>
        </div>
    @else
        @yield('content')
    @endauth
</body>
</html>
