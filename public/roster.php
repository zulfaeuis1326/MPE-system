<?php
require __DIR__ . '/config.php';
require_login();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $karyawan_id = (int)($_POST['karyawan_id'] ?? 0);
    $pola_id = (int)($_POST['pola_dinas_id'] ?? 0);
    $tanggal = $_POST['tanggal_mulai'] ?? '';
    if ($karyawan_id && $pola_id && $tanggal) {
        $stmt = $pdo->prepare('INSERT INTO penugasan_roster (karyawan_id, pola_dinas_id, tanggal_mulai_siklus, aktif) VALUES (?, ?, ?, 1)');
        $stmt->execute([$karyawan_id, $pola_id, $tanggal]);
    }
    redirect('roster.php');
}

$karyawanList = $pdo->query('SELECT id, nama FROM karyawan WHERE status = "aktif" ORDER BY nama')->fetchAll();
$polaList = $pdo->query('SELECT id, nama_pola FROM pola_dinas ORDER BY nama_pola')->fetchAll();

$penugasan = $pdo->query(
    'SELECT pr.*, k.nama AS nama_karyawan, pd.nama_pola, pd.panjang_siklus, pd.definisi_siklus
     FROM penugasan_roster pr
     JOIN karyawan k ON k.id = pr.karyawan_id
     JOIN pola_dinas pd ON pd.id = pr.pola_dinas_id
     WHERE pr.aktif = 1'
)->fetchAll();

// Bangun 14 hari ke depan dari hari ini.
$days = [];
$today = new DateTime('today');
for ($i = 0; $i < 14; $i++) {
    $d = clone $today;
    $d->modify("+$i day");
    $days[] = $d;
}

$pageTitle = 'Lihat Roster';
$activeNav = 'roster';
require __DIR__ . '/includes/header.php';
?>

<div class="board-toolbar"><h3>Tugaskan Karyawan ke Pola Dinas</h3></div>
<div class="card">
  <form method="post">
    <input type="hidden" name="action" value="add">
    <div class="form-inline">
      <div class="field">
        <label>Karyawan</label>
        <select name="karyawan_id" required>
          <?php foreach ($karyawanList as $k): ?>
            <option value="<?= (int)$k['id'] ?>"><?= e($k['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>Pola Dinas</label>
        <select name="pola_dinas_id" required>
          <?php foreach ($polaList as $p): ?>
            <option value="<?= (int)$p['id'] ?>"><?= e($p['nama_pola']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Tanggal Mulai Siklus</label><input type="date" name="tanggal_mulai" required></div>
    </div>
    <div class="form-actions"><button class="btn-primary" type="submit">+ Tugaskan</button></div>
  </form>
</div>

<div class="board-toolbar"><h3>Roster 14 Hari ke Depan</h3></div>
<div class="card" style="overflow-x:auto">
  <?php if (empty($penugasan)): ?>
    <div class="empty-state">Belum ada penugasan roster.</div>
  <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <th>Karyawan</th>
          <?php foreach ($days as $d): ?>
            <th class="mono"><?= $d->format('j/n') ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($penugasan as $p): ?>
          <?php
            $siklus = array_map('trim', explode(',', $p['definisi_siklus']));
            $panjang = $p['panjang_siklus'] ?: count($siklus);
            $mulai = new DateTime($p['tanggal_mulai_siklus']);
          ?>
          <tr>
            <td><?= e($p['nama_karyawan']) ?></td>
            <?php foreach ($days as $d): ?>
              <?php
                $selisih = (int)$mulai->diff($d)->format('%r%a');
                $kode = '-';
                if ($selisih >= 0 && $panjang > 0) {
                    $idx = $selisih % $panjang;
                    $kode = $siklus[$idx] ?? '-';
                }
                $isOff = ($kode === 'L' || strtoupper($kode) === 'OFF');
              ?>
              <td><span class="cell <?= $isOff ? 'cell-off' : 'cell-kerja' ?>"><?= e($kode) ?></span></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
