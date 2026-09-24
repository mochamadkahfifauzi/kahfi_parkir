<?php $user = currentUser(); ?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#2563eb">
<title>Aplikasi Parkir</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div><strong>APLIKASI PARKIR</strong><small><?= htmlspecialchars($user['nama']) ?> · <?= htmlspecialchars(ucfirst($user['role'])) ?></small></div>
    <a class="btn danger" href="index.php?action=logout">Logout</a>
</header>
<nav class="nav">
    <a href="index.php">Dashboard</a>
    <?php if ($user['role'] === 'admin'): ?>
        <a href="index.php?page=users">User</a>
        <a href="index.php?page=tariffs">Tarif</a>
        <a href="index.php?page=areas">Area</a>
        <a href="index.php?page=vehicles">Kendaraan</a>
        <a href="index.php?page=activity">Log</a>
    <?php endif; ?>
    <?php if (in_array($user['role'], array('petugas','admin'), true)): ?><a href="index.php?page=parking">Transaksi</a><?php endif; ?>
    <?php if (in_array($user['role'], array('owner','admin'), true)): ?><a href="index.php?page=report">Rekap</a><?php endif; ?>
</nav>
<main class="container">
<?php if ($flash): ?><div class="alert <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
