<?php
declare(strict_types=1);

require __DIR__ . '/config/database.php';
require __DIR__ . '/config/auth.php';
require __DIR__ . '/models/Model.php';
require __DIR__ . '/controllers/AppController.php';

$model = new Model($pdo);
$app = new AppController($model);
$app->handle();

$page = $_GET['page'] ?? (isLoggedIn() ? 'dashboard' : 'login');
$public = array('login');
if (!in_array($page, $public, true)) requireLogin();

$allowedPages = array('login','dashboard','users','areas','tariffs','vehicles','parking','report','receipt','activity');
if (!in_array($page, $allowedPages, true)) $page = isLoggedIn() ? 'dashboard' : 'login';

if ($page === 'users') requireRole(array('admin'));
if ($page === 'areas') requireRole(array('admin'));
if ($page === 'tariffs') requireRole(array('admin'));
if ($page === 'vehicles') requireRole(array('admin'));
if ($page === 'parking') requireRole(array('petugas','admin'));
if ($page === 'report') requireRole(array('owner','admin'));
if ($page === 'activity') requireRole(array('admin'));

$data = $app->data($page);
$flash = getFlash();
$view = __DIR__ . '/views/' . $page . '.php';
if (!file_exists($view)) die('View tidak ditemukan.');

if ($page !== 'login') require __DIR__ . '/views/header.php';
require $view;
if ($page !== 'login') require __DIR__ . '/views/footer.php';
