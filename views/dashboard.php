<h1>Dashboard</h1>
<div class="cards">
    <div class="card"><b>User</b><strong><?= $data['users'] ?></strong></div>
    <div class="card"><b>Area</b><strong><?= $data['areas'] ?></strong></div>
    <div class="card"><b>Kendaraan</b><strong><?= $data['kendaraan'] ?></strong></div>
    <div class="card"><b>Parkir Aktif</b><strong><?= $data['parkir_aktif'] ?></strong></div>
    <div class="card"><b>Pendapatan</b><strong>Rp <?= number_format($data['pendapatan'],0,',','.') ?></strong></div>
</div>
<div class="panel"><h3>Menu sesuai role</h3><p>Gunakan navigasi di atas. Admin mengelola master data, Petugas mengelola transaksi, Owner melihat rekap transaksi.</p></div>
