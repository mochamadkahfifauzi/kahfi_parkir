<?php $e=$data['edit']; ?>
<h1>Kendaraan</h1>
<div class="panel"><form method="post" action="index.php?action=vehicle_save" class="grid-form">
<input type="hidden" name="id_kendaraan" value="<?= $e['id_kendaraan'] ?? '' ?>">
<label>No Polisi<input name="no_polisi" value="<?= htmlspecialchars($e['no_polisi'] ?? '') ?>" placeholder="B 1234 ABC" required></label><label>Jenis<select name="jenis_kendaraan"><?php foreach(array('Motor','Mobil','Bus','Truk') as $jenis): ?><option value="<?= $jenis ?>" <?= (($e['jenis_kendaraan'] ?? '')===$jenis?'selected':'') ?>><?= $jenis ?></option><?php endforeach; ?></select></label><label>Area<select name="id_area" required><?php foreach ($data['areas'] as $a): ?><option value="<?= $a['id_area'] ?>" <?= ((int)($e['id_area'] ?? 0)===(int)$a['id_area']?'selected':'') ?>><?= htmlspecialchars($a['nama_area']) ?></option><?php endforeach; ?></select></label><button class="btn primary"><?= $e ? 'Simpan Perubahan' : 'Tambah Kendaraan' ?></button><?php if($e): ?><a class="btn" href="index.php?page=vehicles">Batal</a><?php endif; ?>
</form></div>
<div class="table-wrap"><table><thead><tr><th>Plat</th><th>Jenis</th><th>Area</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($data['items'] as $r): ?><tr><td><?= htmlspecialchars($r['no_polisi']) ?></td><td><?= htmlspecialchars($r['jenis_kendaraan']) ?></td><td><?= htmlspecialchars($r['nama_area']) ?></td><td><a class="btn small" href="index.php?page=vehicles&edit=<?= $r['id_kendaraan'] ?>">Edit</a> <form class="inline" method="post" action="index.php?action=vehicle_delete" onsubmit="return confirm('Hapus kendaraan?')"><input type="hidden" name="id_kendaraan" value="<?= $r['id_kendaraan'] ?>"><button class="btn danger small">Hapus</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
