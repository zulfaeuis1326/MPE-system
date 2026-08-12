<?php
// Dipanggil dari tiap halaman setelah require_login(). $activeNav diset di halaman pemanggil.
$user = current_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SIGAP — <?= e($pageTitle ?? 'Dashboard') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#F4F6F8; --panel:#FFFFFF; --panel-raised:#F0F3F6; --border:#E3E8ED;
    --teal:#0E8074; --teal-dim:#E2F3F0; --teal-deep:#0A5E56;
    --amber:#D97706; --amber-dim:#FDF1DF;
    --green:#15A363; --green-dim:#E5F7ED;
    --red:#E0483F; --red-dim:#FCE9E7;
    --text:#1C2430; --muted:#67727E; --faint:#9AA4AF;
    --shadow:0 1px 2px rgba(20,30,40,0.04), 0 1px 12px rgba(20,30,40,0.04);
  }
  *{box-sizing:border-box; margin:0; padding:0;}
  body{ background:var(--bg); color:var(--text); font-family:'Inter',sans-serif; min-height:100vh; }
  h1,h2,h3{ font-family:'Poppins',sans-serif; }
  .mono{ font-family:'IBM Plex Mono',monospace; }
  a{ color:var(--teal-deep); }
  .field{ margin-bottom:16px; }
  .field label{ display:block; font-size:11.5px; color:var(--muted); margin-bottom:7px; font-weight:600; }
  .field input, .field select, .field textarea{
    width:100%; background:var(--panel-raised); border:1px solid var(--border);
    color:var(--text); padding:11px 13px; border-radius:9px; font-size:14.5px; font-family:'Inter',sans-serif;
  }
  .field input:focus, .field select:focus, .field textarea:focus{ outline:none; border-color:var(--teal); background:#fff; }
  .btn-primary{ background:var(--teal); color:#fff; border:none; padding:11px 20px; font-family:'Poppins',sans-serif; font-weight:600; font-size:13.5px; border-radius:9px; cursor:pointer; }
  .btn-primary:hover{ background:var(--teal-deep); }
  .btn-ghost{ background:var(--panel); border:1px solid var(--border); color:var(--text); padding:7px 14px; font-size:12px; border-radius:8px; cursor:pointer; font-weight:500; text-decoration:none; display:inline-block; }
  .btn-ghost:hover{ border-color:var(--teal); color:var(--teal-deep); }
  .btn-danger{ background:#fff; border:1px solid var(--red); color:var(--red); padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; }
  .btn-danger:hover{ background:var(--red-dim); }
  .msg-box{ font-size:12.5px; padding:10px 12px; border-radius:8px; margin-bottom:16px; }
  .msg-error{ background:var(--red-dim); color:var(--red); }
  .msg-ok{ background:var(--green-dim); color:var(--green); }

  .shell{ display:flex; min-height:100vh; }
  .sidebar{ width:225px; background:var(--panel); border-right:1px solid var(--border); display:flex; flex-direction:column; flex-shrink:0; }
  .sidebar-brand{ padding:22px 20px 18px; border-bottom:1px solid var(--border); }
  .sidebar-brand .brand{ font-family:'Poppins',sans-serif; font-size:18px; font-weight:700; color:var(--teal-deep); }
  .role-tag{ display:inline-block; margin-top:8px; font-size:10.5px; background:var(--teal-dim); color:var(--teal-deep); padding:3px 9px; border-radius:20px; font-weight:600; }
  .nav{ padding:14px 10px; flex:1; }
  .nav-item{ display:block; padding:10px 12px; border-radius:9px; color:var(--muted); font-size:13.5px; font-weight:500; text-decoration:none; margin-bottom:2px; }
  .nav-item:hover{ background:var(--panel-raised); color:var(--text); }
  .nav-item.active{ background:var(--teal-dim); color:var(--teal-deep); font-weight:600; }
  .sidebar-foot{ padding:16px 20px; border-top:1px solid var(--border); font-size:12px; color:var(--muted); }
  .main{ flex:1; min-width:0; }
  .topbar{ height:58px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 26px; background:var(--panel); }
  .topbar .page-title{ font-size:15px; font-weight:600; }
  .content{ padding:24px; max-width:1100px; }

  .board-toolbar{ display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:10px; }
  .board-toolbar h3{ font-size:13px; color:var(--muted); font-weight:600; }
  .card{ background:var(--panel); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); margin-bottom:20px; }
  table.data-table{ width:100%; border-collapse:collapse; font-size:13px; }
  table.data-table th{ text-align:left; padding:11px 16px; border-bottom:1px solid var(--border); color:var(--faint); font-size:10.5px; text-transform:uppercase; letter-spacing:0.02em; font-weight:600; }
  table.data-table td{ padding:11px 16px; border-bottom:1px solid var(--border); }
  table.data-table tr:last-child td{ border-bottom:none; }
  .tag{ font-size:10.5px; padding:3px 9px; border-radius:20px; font-weight:600; }
  .tag-aktif{ background:var(--green-dim); color:var(--green); }
  .tag-nonaktif{ background:var(--red-dim); color:var(--red); }
  .form-inline{ display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px; padding:18px 18px 4px; }
  .form-actions{ padding:0 18px 18px; }
  .empty-state{ padding:36px 18px; text-align:center; color:var(--faint); font-size:13px; }
  .cell{ width:24px; height:24px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; font-family:'IBM Plex Mono',monospace; font-size:10px; font-weight:600; margin:1px; }
  .cell-kerja{ background:var(--teal-dim); color:var(--teal-deep); }
  .cell-off{ background:transparent; color:var(--faint); border:1px dashed var(--border); }

  .auth-wrap{ min-height:100vh; display:flex; align-items:center; justify-content:center; background: radial-gradient(ellipse 900px 560px at 50% -8%, rgba(14,128,116,0.10), transparent 62%), var(--bg); }
  .auth-card{ width:100%; max-width:380px; background:var(--panel); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 4px 24px rgba(20,30,40,0.07); }
  .hazard{ height:4px; background:linear-gradient(90deg, var(--teal), #4FC3AE); }
  .auth-body{ padding:36px 32px 30px; }
  .auth-body .brand{ font-family:'Poppins',sans-serif; font-size:25px; font-weight:700; color:var(--teal-deep); }
  .auth-sub{ color:var(--muted); font-size:12.5px; margin:6px 0 26px; line-height:1.5; }
  .toggle-link{ text-align:center; margin-top:16px; font-size:12.5px; color:var(--muted); }

  @media (max-width:820px){
    .sidebar{ position:fixed; left:-225px; z-index:20; height:100vh; transition:left .2s; }
    .sidebar.open{ left:0; }
    .content{ padding:16px; }
  }
</style>
</head>
<body>
<div class="shell">
  <div class="sidebar">
    <div class="sidebar-brand">
      <div class="brand">SIGAP</div>
      <span class="role-tag"><?= $user['role'] === 'super_admin' ? 'Manpower · Super Admin' : 'Admin' ?></span>
    </div>
    <div class="nav">
      <a class="nav-item <?= ($activeNav ?? '') === 'karyawan' ? 'active' : '' ?>" href="index.php">▣ Data Karyawan</a>
      <a class="nav-item <?= ($activeNav ?? '') === 'poladinas' ? 'active' : '' ?>" href="pola_dinas.php">◆ Pola Dinas</a>
      <a class="nav-item <?= ($activeNav ?? '') === 'roster' ? 'active' : '' ?>" href="roster.php">▤ Lihat Roster</a>
    </div>
    <div class="sidebar-foot">
      <div style="margin-bottom:10px"><?= e($user['email']) ?></div>
      <a class="btn-ghost" style="width:100%;text-align:center" href="logout.php">Keluar</a>
    </div>
  </div>
  <div class="main">
    <div class="topbar"><div class="page-title"><?= e($pageTitle ?? '') ?></div></div>
    <div class="content">
