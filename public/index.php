<?php
require __DIR__ . '/config.php';
require_login();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'add') {
        $nama = trim($_POST['nama'] ?? '');
        $nip = trim($_POST['nip'] ?? '');
        $jabatan = trim($_POST['jabatan'] ?? '');
        $status = ($_POST['status'] ?? 'aktif') === 'nonaktif' ? 'nonaktif' : 'aktif';
        if ($nama !== '') {
            $stmt = $pdo->prepare('INSERT INTO karyawan (nama, nip, jabatan, status) VALUES (?, ?, ?, ?)');
            $stmt->execute([$nama, $nip, $jabatan, $status]);
        }
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare('DELETE FROM karyawan WHERE id = ?')->execute([$id]);
    }
    redirect('index.php');
}

$karyawan = $pdo->query('SELECT * FROM karyawan ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Data Karyawan';
$activeNav = 'karyawan';
require __DIR__ . '/includes/header.php';
?>

<div class="board-toolbar"><h3>Tambah Karyawan</h3></div>
<div class="card">
  <form method="post">
    <input type="hidden" name="action" value="add">
    <div class="form-inline">
      <div class="field"><label>Nama</label><input type="text" name="nama" placeholder="Nama karyawan" required></div>
      <div class="field"><label>NIP</label><input type="text" name="nip" placeholder="NIP"></div>
      <div class="field"><label>Jabatan</label><input type="text" name="jabatan" placeholder="Jabatan"></div>
      <div class="field"><label>Status</label>
        <select name="status"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select>
      </div>
    </div>
    <div class="form-actions"><button class="btn-primary" type="submit">+ Simpan Karyawan</button></div>
  </form>
</div>

<div class="board-toolbar"><h3>Daftar Karyawan</h3></div>
<div class="card">
  <?php if (empty($karyawan)): ?>
    <div class="empty-state">Belum ada data karyawan.</div>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($karyawan as $k): ?>
        <tr>
          <td><?= e($k['nama']) ?></td>
          <td class="mono"><?= e($k['nip'] ?: '-') ?></td>
          <td><?= e($k['jabatan'] ?: '-') ?></td>
          <td><span class="tag tag-<?= $k['status'] ?>"><?= $k['status'] ?></span></td>
          <td>
            <form method="post" onsubmit="return confirm('Hapus karyawan ini?')" style="display:inline">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$k['id'] ?>">
              <button class="btn-danger" type="submit">Hapus</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
