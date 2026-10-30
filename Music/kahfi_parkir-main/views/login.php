<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#2563eb">
<title>Login - Aplikasi Parkir</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-page">
<div class="login-card">
    <h1>Parkir</h1>
    <p class="muted">Silakan login untuk masuk.</p>
    <?php if ($flash): ?><div class="alert <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
    <form method="post" action="index.php?action=login">
        <label>Username</label><input name="username" required autocomplete="username">
        <label>Password</label><input type="password" name="password" required autocomplete="current-password">
        <button class="btn primary wide" type="submit">Login</button>
    </form>
    <div class="demo">Demo: admin / admin123 · petugas / petugas123 · owner / owner123</div>
</div>
</body>
</html>
