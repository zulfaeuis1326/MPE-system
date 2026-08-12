<?php
require __DIR__ . '/config.php';

if (current_user()) { redirect('index.php'); }

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nama === '' || $email === '' || $password === '') {
        $error = 'Lengkapi nama, email, dan kata sandi.';
    } elseif (strlen($password) < 6) {
        $error = 'Kata sandi minimal 6 karakter.';
    } else {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email ini sudah terdaftar. Silakan masuk.';
        } else {
            // Akun pertama yang daftar otomatis jadi super_admin (Manpower).
            $countStmt = $pdo->query('SELECT COUNT(*) AS c FROM users');
            $isFirst = ((int)$countStmt->fetch()['c']) === 0;
            $role = $isFirst ? 'super_admin' : 'admin';

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (nama, email, password_hash, role) VALUES (?, ?, ?, ?)');
            $ins->execute([$nama, $email, $hash, $role]);

            redirect('login.php?registered=1');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar — SIGAP</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{ --bg:#F4F6F8; --panel:#FFFFFF; --panel-raised:#F0F3F6; --border:#E3E8ED; --teal:#0E8074; --teal-deep:#0A5E56; --red:#E0483F; --red-dim:#FCE9E7; --text:#1C2430; --muted:#67727E; }
  *{box-sizing:border-box;margin:0;padding:0;}
  body{ font-family:'Inter',sans-serif; color:var(--text); }
  .auth-wrap{ min-height:100vh; display:flex; align-items:center; justify-content:center; background: radial-gradient(ellipse 900px 560px at 50% -8%, rgba(14,128,116,0.10), transparent 62%), var(--bg); }
  .auth-card{ width:100%; max-width:380px; background:var(--panel); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 4px 24px rgba(20,30,40,0.07); }
  .hazard{ height:4px; background:linear-gradient(90deg, var(--teal), #4FC3AE); }
  .auth-body{ padding:36px 32px 30px; }
  .brand{ font-family:'Poppins',sans-serif; font-size:25px; font-weight:700; color:var(--teal-deep); }
  .auth-sub{ color:var(--muted); font-size:12.5px; margin:6px 0 26px; line-height:1.5; }
  .field{ margin-bottom:16px; }
  .field label{ display:block; font-size:11.5px; color:var(--muted); margin-bottom:7px; font-weight:600; }
  .field input{ width:100%; background:var(--panel-raised); border:1px solid var(--border); color:var(--text); padding:11px 13px; border-radius:9px; font-size:14.5px; }
  .field input:focus{ outline:none; border-color:var(--teal); background:#fff; }
  .btn-primary{ width:100%; background:var(--teal); color:#fff; border:none; padding:12px; font-family:'Poppins',sans-serif; font-weight:600; font-size:14px; border-radius:9px; cursor:pointer; }
  .btn-primary:hover{ background:var(--teal-deep); }
  .toggle-link{ text-align:center; margin-top:16px; font-size:12.5px; color:var(--muted); }
  .msg-error{ background:var(--red-dim); color:var(--red); font-size:12.5px; padding:10px 12px; border-radius:8px; margin-bottom:14px; }
</style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="hazard"></div>
    <div class="auth-body">
      <div class="brand">SIGAP</div>
      <div class="auth-sub">Sistem Informasi Gilir &amp; Absensi Personel — Site Tambang</div>

      <?php if ($error): ?>
        <div class="msg-error"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="field"><label>Nama</label><input type="text" name="nama" value="<?= e($_POST['nama'] ?? '') ?>" required></div>
        <div class="field"><label>Email</label><input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required></div>
        <div class="field"><label>Kata Sandi</label><input type="password" name="password" required minlength="6"></div>
        <button class="btn-primary" type="submit">Daftar Akun</button>
      </form>
      <div class="toggle-link">Sudah punya akun? <a href="login.php">Masuk di sini</a></div>
    </div>
  </div>
</div>
</body>
</html>
