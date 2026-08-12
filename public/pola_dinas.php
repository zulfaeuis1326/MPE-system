<?php
require __DIR__ . '/config.php';
require_login();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'add') {
        $nama = trim($_POST['nama_pola'] ?? '');
        $panjang = (int)($_POST['panjang_siklus'] ?? 0);
        $def = trim($_POST['definisi_siklus'] ?? '');
        if ($nama !== '' && $panjang > 0 && $def !== '') {
            $stmt = $pdo->prepare('INSERT INTO pola_dinas (nama_pola, panjang_siklus, definisi_siklus) VALUES (?, ?, ?)');
            $stmt->execute([$nama, $panjang, $def]);
        }
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare('DELETE FROM pola_dinas WHERE id = ?')->execute([$id]);
    }
    redirect('pola_dinas.php');
}

$pola = $pdo->query('SELECT * FROM pola_dinas ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Pola Dinas';
$activeNav = 'poladinas';
require __DIR__ . '/includes/header.php';
?>

<div class="board-toolbar"><h3>Tambah Pola Dinas</h3></div>
<div class="card">
  <form method="post">
    <input type="hidden" name="action" value="add">
    <div class="form-inline">
      <div class="field"><label>Nama Pola</label><input type="text" name="nama_pola" placeholder="mis. 13 Kerja 1 Off" required></div>
      <div class="field"><label>Panjang Siklus (hari)</label><input type="number" name="panjang_siklus" placeholder="70" required></div>
    </div>
    <div class="field" style="padding:0 18px;">
      <label>Definisi Siklus (pisahkan koma)</label>
      <textarea name="definisi_siklus" rows="2" placeholder="P,P,P,P,P,P,P,P,P,P,P,P,P,L" required></textarea>
    </div>
    <div class="form-actions"><button class="btn-primary" type="submit">+ Simpan Pola Dinas</button></div>
  </form>
</div>

<div class="board-toolbar"><h3>Daftar Pola Dinas</h3></div>
<div class="card">
  <?php if (empty($pola)): ?>
    <div class="empty-state">Belum ada pola dinas.</div>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Nama Pola</th><th>Siklus (hari)</th><th>Pola</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($pola as $p): ?>
        <tr>
          <td><?= e($p['nama_pola']) ?></td>
          <td class="mono"><?= (int)$p['panjang_siklus'] ?></td>
          <td class="mono"><?= e(str_replace(',', ' ', $p['definisi_siklus'])) ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Hapus pola dinas ini?')" style="display:inline">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
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
