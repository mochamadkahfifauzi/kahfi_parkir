<?php $e=$data['edit']; ?>
<h1>Area Parkir</h1>
<div class="panel"><form method="post" action="index.php?action=area_save" class="grid-form">
<input type="hidden" name="id_area" value="<?= $e['id_area'] ?? '' ?>">
<label>Nama Area<input name="nama_area" value="<?= htmlspecialchars($e['nama_area'] ?? '') ?>" required></label><label>Kapasitas<input type="number" name="kapasitas" min="1" value="<?= $e['kapasitas'] ?? '' ?>" required></label><label>Lokasi<input name="lokasi" value="<?= htmlspecialchars($e['lokasi'] ?? '') ?>" required></label><button class="btn primary"><?= $e ? 'Simpan Perubahan' : 'Tambah Area' ?></button><?php if($e): ?><a class="btn" href="index.php?page=areas">Batal</a><?php endif; ?>
</form><p class="muted">Area ditentukan saat petugas mencatat kendaraan masuk, bukan saat kendaraan didaftarkan.</p></div>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Area</th><th>Kapasitas</th><th>Lokasi</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($data['items'] as $r): ?><tr><td><?= $r['id_area'] ?></td><td><?= htmlspecialchars($r['nama_area']) ?></td><td><?= $r['kapasitas'] ?></td><td><?= htmlspecialchars($r['lokasi']) ?></td><td><a class="btn small" href="index.php?page=areas&edit=<?= $r['id_area'] ?>">Edit</a> <form class="inline" method="post" action="index.php?action=area_delete" onsubmit="return confirm('Hapus area?')"><input type="hidden" name="id_area" value="<?= $r['id_area'] ?>"><button class="btn danger small">Hapus</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
