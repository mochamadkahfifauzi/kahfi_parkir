<h1>Log Aktivitas</h1>
<div class="panel"><p class="muted">Log sesi ini disimpan di session karena database dibatasi tepat 5 tabel.</p></div>
<div class="table-wrap"><table><thead><tr><th>Waktu</th><th>User</th><th>Aktivitas</th></tr></thead><tbody><?php foreach ($data['items'] as $r): ?><tr><td><?= htmlspecialchars($r['time']) ?></td><td><?= htmlspecialchars($r['user']) ?></td><td><?= htmlspecialchars($r['text']) ?></td></tr><?php endforeach; ?></tbody></table></div>
