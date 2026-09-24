<?php $e=$data['edit']; ?>
<h1>Kelola User</h1>
<div class="panel"><form method="post" action="index.php?action=user_save" class="grid-form">
<input type="hidden" name="id_user" value="<?= $e['id_user'] ?? '' ?>">
<label>Nama<input name="nama" value="<?= htmlspecialchars($e['nama'] ?? '') ?>" required></label>
<label>Username<input name="username" value="<?= htmlspecialchars($e['username'] ?? '') ?>" required></label>
<label>Password<input type="password" name="password" <?= $e ? '' : 'required' ?> placeholder="<?= $e ? 'Kosongkan jika tidak diubah' : '' ?>"></label>
<label>Role<select name="role"><?php foreach(array('admin','petugas','owner') as $role): ?><option value="<?= $role ?>" <?= (($e['role'] ?? '')===$role?'selected':'') ?>><?= $role ?></option><?php endforeach; ?></select></label>
<button class="btn primary"><?= $e ? 'Simpan Perubahan' : 'Tambah User' ?></button><?php if($e): ?><a class="btn" href="index.php?page=users">Batal</a><?php endif; ?>
</form></div>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Nama</th><th>Username</th><th>Role</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($data['items'] as $r): ?><tr><td><?= $r['id_user'] ?></td><td><?= htmlspecialchars($r['nama']) ?></td><td><?= htmlspecialchars($r['username']) ?></td><td><?= htmlspecialchars($r['role']) ?></td><td><a class="btn small" href="index.php?page=users&edit=<?= $r['id_user'] ?>">Edit</a> <form class="inline" method="post" action="index.php?action=user_delete" onsubmit="return confirm('Hapus user?')"><input type="hidden" name="id_user" value="<?= $r['id_user'] ?>"><button class="btn danger small">Hapus</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
