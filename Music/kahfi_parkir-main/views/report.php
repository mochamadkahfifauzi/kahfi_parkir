<h1>Rekap Transaksi</h1>
<div class="panel"><form method="get" class="grid-form"><input type="hidden" name="page" value="report"><label>Dari<input type="date" name="start" value="<?= htmlspecialchars($data['start']) ?>"></label><label>Sampai<input type="date" name="end" value="<?= htmlspecialchars($data['end']) ?>"></label><button class="btn primary">Tampilkan</button></form></div>
<div class="panel total"><b>Total Pendapatan</b><strong>Rp <?= number_format($data['total'],0,',','.') ?></strong></div>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Plat</th><th>Jenis</th><th>Area</th><th>Masuk</th><th>Keluar</th><th>Durasi Aktual</th><th>Tagihan</th></tr></thead><tbody>
<?php foreach ($data['items'] as $r): ?><?php if ($r['durasi_menit'] === null): $durationText = 'Data lama: ' . (int)$r['durasi_jam'] . ' jam tersimpan'; else: $minutes = (int)$r['durasi_menit']; $durationText = $minutes < 60 ? $minutes . ' menit' : (int)floor($minutes / 60) . ' jam' . (($minutes % 60) > 0 ? ' ' . ($minutes % 60) . ' menit' : ''); endif; ?>
<tr><td><?= (int)$r['id_transaksi'] ?></td><td><?= htmlspecialchars($r['no_polisi']) ?></td><td><?= htmlspecialchars($r['jenis_kendaraan']) ?></td><td><?= htmlspecialchars($r['nama_area']) ?></td><td><?= htmlspecialchars($r['waktu_masuk']) ?></td><td><?= htmlspecialchars($r['waktu_keluar']) ?></td><td><?= htmlspecialchars($durationText) ?></td><td><?= (int)$r['durasi_jam'] ?> jam · Rp <?= number_format($r['total_bayar'],0,',','.') ?></td></tr>
<?php endforeach; ?>
<?php if (!$data['items']): ?><tr><td colspan="8" class="muted">Belum ada transaksi pada rentang tanggal ini.</td></tr><?php endif; ?>
</tbody></table></div>
