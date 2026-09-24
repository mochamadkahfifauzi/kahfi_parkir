<h1>Struk Parkir</h1>
<?php $r = $data['receipt']; if (!$r): ?><div class="panel">Belum ada struk.</div><?php else: ?>
<div class="receipt" id="printArea"><h2>STRUK PARKIR</h2><p><b>No. Transaksi:</b> <?= $r['id_transaksi'] ?></p><p><b>No. Polisi:</b> <?= htmlspecialchars($r['no_polisi']) ?></p><p><b>Jenis:</b> <?= htmlspecialchars($r['jenis_kendaraan']) ?></p><hr><p>Masuk: <?= $r['waktu_masuk'] ?></p><p>Keluar: <?= $r['waktu_keluar'] ?></p><p>Durasi: <?= $r['durasi_jam'] ?> jam</p><p>Tarif/jam: Rp <?= number_format($r['tarif_per_jam'],0,',','.') ?></p><h2>Total: Rp <?= number_format($r['total_bayar'],0,',','.') ?></h2><p>Petugas: <?= htmlspecialchars($r['nama_petugas']) ?></p></div>
<button class="btn primary" onclick="window.print()">Cetak</button> <a class="btn" href="index.php?page=parking">Kembali</a>
<?php endif; ?>
