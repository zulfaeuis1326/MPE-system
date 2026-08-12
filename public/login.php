<?php
require __DIR__ . '/config.php';

if (current_user()) { redirect('index.php'); }

$error = null;
$success = isset($_GET['registered']) ? 'Akun berhasil dibuat. Silakan masuk.' : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Isi email dan kata sandi.';
    } else {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $u = $stmt->fetch();

        if (!$u || !password_verify($password, $u['password_hash'])) {
            $error = 'Email atau kata sandi salah.';
        } else {
            $_SESSION['user'] = ['id' => $u['id'], 'nama' => $u['nama'], 'email' => $u['email'], 'role' => $u['role']];
            redirect('index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — SIGAP</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{ --bg:#F4F6F8; --panel:#FFFFFF; --panel-raised:#F0F3F6; --border:#E3E8ED; --teal:#0E8074; --teal-deep:#0A5E56; --red:#E0483F; --red-dim:#FCE9E7; --green:#15A363; --green-dim:#E5F7ED; --text:#1C2430; --muted:#67727E; }
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
  .msg-ok{ background:var(--green-dim); color:var(--green); font-size:12.5px; padding:10px 12px; border-radius:8px; margin-bottom:14px; }
</style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="hazard"></div>
    <div class="auth-body">
      <div class="brand">SIGAP</div>
      <div class="auth-sub">Sistem Informasi Gilir &amp; Absensi Personel — Site Tambang</div>

      <?php if ($error): ?><div class="msg-error"><?= e($error) ?></div><?php endif; ?>
      <?php if ($success): ?><div class="msg-ok"><?= e($success) ?></div><?php endif; ?>

      <form method="post">
        <div class="field"><label>Email</label><input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required></div>
        <div class="field"><label>Kata Sandi</label><input type="password" name="password" required></div>
        <button class="btn-primary" type="submit">Masuk</button>
      </form>
      <div class="toggle-link">Belum punya akun? <a href="register.php">Daftar di sini</a></div>
    </div>
  </div>
</div>
</body>
</html>
