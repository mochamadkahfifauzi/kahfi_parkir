<h1>Transaksi Parkir</h1>
<div class="panel">
    <h3>Kendaraan Masuk</h3>
    <?php if (!$data['available_vehicles']): ?>
        <p class="muted">Belum ada kendaraan terdaftar yang tersedia untuk masuk. Tambahkan kendaraan melalui menu Kendaraan atau selesaikan transaksi parkir yang masih aktif.</p>
    <?php elseif (!$data['areas']): ?>
        <p class="muted">Belum ada area parkir. Admin harus menambahkan area terlebih dahulu.</p>
    <?php else: ?>
        <form method="post" action="index.php?action=park" class="grid-form">
            <label>Kendaraan Terdaftar
                <select name="id_kendaraan" required>
                    <option value="">Pilih kendaraan</option>
                    <?php foreach ($data['available_vehicles'] as $vehicle): ?>
                        <option value="<?= (int)$vehicle['id_kendaraan'] ?>"><?= htmlspecialchars($vehicle['no_polisi']) ?> · <?= htmlspecialchars($vehicle['jenis_kendaraan']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Area Parkir
                <select name="id_area" required>
                    <option value="">Pilih area tersedia</option>
                    <?php foreach ($data['areas'] as $area): ?>
                        <option value="<?= (int)$area['id_area'] ?>" <?= ((int)$area['tersedia'] < 1 ? 'disabled' : '') ?>>
                            <?= htmlspecialchars($area['nama_area']) ?> · tersedia <?= (int)$area['tersedia'] ?>/<?= (int)$area['kapasitas'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="btn primary">Catat Masuk</button>
        </form>
    <?php endif; ?>
</div>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Plat</th><th>Jenis</th><th>Area</th><th>Masuk</th><th>Petugas</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($data['items'] as $r): ?><tr><td><?= (int)$r['id_transaksi'] ?></td><td><?= htmlspecialchars($r['no_polisi']) ?></td><td><?= htmlspecialchars($r['jenis_kendaraan']) ?></td><td><?= htmlspecialchars($r['nama_area']) ?></td><td><?= htmlspecialchars($r['waktu_masuk']) ?></td><td><?= htmlspecialchars($r['nama_petugas']) ?></td><td><form method="post" action="index.php?action=finish"><input type="hidden" name="id_transaksi" value="<?= (int)$r['id_transaksi'] ?>"><button class="btn primary small">Keluar &amp; Struk</button></form></td></tr><?php endforeach; ?>
<?php if (!$data['items']): ?><tr><td colspan="7" class="muted">Belum ada kendaraan yang sedang parkir.</td></tr><?php endif; ?>
</tbody></table></div>
