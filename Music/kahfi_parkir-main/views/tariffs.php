<?php $e=$data['edit']; ?>
<h1>Tarif Parkir</h1>
<div class="panel"><form method="post" action="index.php?action=tarif_save" class="grid-form">
<input type="hidden" name="id_tarif" value="<?= $e['id_tarif'] ?? '' ?>">
<label>Jenis<select name="jenis_kendaraan"><?php foreach(array('Motor','Mobil','Bus','Truk') as $jenis): ?><option value="<?= $jenis ?>" <?= (($e['jenis_kendaraan'] ?? '')===$jenis?'selected':'') ?>><?= $jenis ?></option><?php endforeach; ?></select></label><label>Tarif / Jam<input type="number" name="tarif_per_jam" min="0" value="<?= $e['tarif_per_jam'] ?? '' ?>" required></label><button class="btn primary"><?= $e ? 'Simpan Perubahan' : 'Tambah Tarif' ?></button><?php if($e): ?><a class="btn" href="index.php?page=tariffs">Batal</a><?php endif; ?>
</form></div>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Jenis</th><th>Tarif/Jam</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($data['items'] as $r): ?><tr><td><?= $r['id_tarif'] ?></td><td><?= htmlspecialchars($r['jenis_kendaraan']) ?></td><td>Rp <?= number_format($r['tarif_per_jam'],0,',','.') ?></td><td><a class="btn small" href="index.php?page=tariffs&edit=<?= $r['id_tarif'] ?>">Edit</a> <form class="inline" method="post" action="index.php?action=tarif_delete" onsubmit="return confirm('Hapus tarif?')"><input type="hidden" name="id_tarif" value="<?= $r['id_tarif'] ?>"><button class="btn danger small">Hapus</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
