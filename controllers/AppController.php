<?php
declare(strict_types=1);

class AppController
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function handle(): void
    {
        $action = $_GET['action'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->post($action);
        }
        if ($action === 'logout') {
            logoutUser();
            header('Location: index.php?page=login');
            exit;
        }
    }

    private function post(string $action): void
    {
        try {
            switch ($action) {
                case 'login':
                    $username = trim($_POST['username'] ?? '');
                    $password = $_POST['password'] ?? '';
                    $user = $this->model->login($username);
                    if (!$user || !password_verify($password, $user['password'])) {
                        throw new RuntimeException('Username atau password salah.');
                    }
                    loginUser($user);
                    addActivity('Login ke sistem');
                    header('Location: index.php');
                    exit;

                case 'user_save':
                    requireRole(array('admin'));
                    $id = (int)($_POST['id_user'] ?? 0);
                    if ($id > 0) {
                        $this->model->updateUser($id, trim($_POST['nama']), trim($_POST['username']), $_POST['role'], trim($_POST['password'] ?? ''));
                        addActivity('Mengubah data user');
                    } else {
                        $this->model->createUser(trim($_POST['nama']), trim($_POST['username']), $_POST['password'], $_POST['role']);
                        addActivity('Menambah user');
                    }
                    flash('success', 'Data user berhasil disimpan.');
                    break;

                case 'user_delete':
                    requireRole(array('admin'));
                    $this->model->deleteUser((int)$_POST['id_user']);
                    addActivity('Menghapus user');
                    flash('success', 'User berhasil dihapus.');
                    break;

                case 'area_save':
                    requireRole(array('admin'));
                    $id = (int)($_POST['id_area'] ?? 0);
                    if ($id > 0) $this->model->updateArea($id, trim($_POST['nama_area']), (int)$_POST['kapasitas'], trim($_POST['lokasi']));
                    else $this->model->createArea(trim($_POST['nama_area']), (int)$_POST['kapasitas'], trim($_POST['lokasi']));
                    addActivity($id > 0 ? 'Mengubah area parkir' : 'Menambah area parkir');
                    flash('success', 'Area parkir berhasil disimpan.');
                    break;

                case 'area_delete':
                    requireRole(array('admin'));
                    $this->model->deleteArea((int)$_POST['id_area']);
                    addActivity('Menghapus area parkir');
                    flash('success', 'Area parkir berhasil dihapus.');
                    break;

                case 'tarif_save':
                    requireRole(array('admin'));
                    $id = (int)($_POST['id_tarif'] ?? 0);
                    if ($id > 0) $this->model->updateTariff($id, $_POST['jenis_kendaraan'], (int)$_POST['tarif_per_jam']);
                    else $this->model->createTariff($_POST['jenis_kendaraan'], (int)$_POST['tarif_per_jam']);
                    addActivity($id > 0 ? 'Mengubah tarif parkir' : 'Menambah tarif parkir');
                    flash('success', 'Tarif berhasil disimpan.');
                    break;

                case 'tarif_delete':
                    requireRole(array('admin'));
                    $this->model->deleteTariff((int)$_POST['id_tarif']);
                    addActivity('Menghapus tarif parkir');
                    flash('success', 'Tarif berhasil dihapus.');
                    break;

                case 'vehicle_save':
                    requireRole(array('admin'));
                    $id = (int)($_POST['id_kendaraan'] ?? 0);
                    if ($id > 0) $this->model->updateVehicle($id, trim($_POST['no_polisi']), $_POST['jenis_kendaraan'], (int)$_POST['id_area']);
                    else $this->model->createVehicle(trim($_POST['no_polisi']), $_POST['jenis_kendaraan'], (int)$_POST['id_area']);
                    addActivity($id > 0 ? 'Mengubah kendaraan' : 'Menambah kendaraan');
                    flash('success', 'Data kendaraan berhasil disimpan.');
                    break;

                case 'vehicle_delete':
                    requireRole(array('admin'));
                    $this->model->deleteVehicle((int)$_POST['id_kendaraan']);
                    addActivity('Menghapus kendaraan');
                    flash('success', 'Kendaraan berhasil dihapus.');
                    break;

                case 'park':
                    requireRole(array('petugas','admin'));
                    $plate = strtoupper(trim($_POST['no_polisi'] ?? ''));
                    $vehicle = $this->model->findVehicleByPlate($plate);
                    if (!$vehicle) throw new RuntimeException('Nomor polisi belum terdaftar. Admin harus menambah kendaraan terlebih dahulu.');
                    $tariff = $this->model->tariffsByType($vehicle['jenis_kendaraan']);
                    if (!$tariff) throw new RuntimeException('Tarif untuk jenis kendaraan belum tersedia.');
                    $this->model->parkVehicle((int)$vehicle['id_kendaraan'], (int)$tariff['id_tarif'], (int)currentUser()['id_user']);
                    addActivity('Mencatat kendaraan masuk: ' . $plate);
                    flash('success', 'Kendaraan berhasil dicatat masuk.');
                    break;

                case 'finish':
                    requireRole(array('petugas','admin'));
                    $receipt = $this->model->finishTransaction((int)$_POST['id_transaksi']);
                    $_SESSION['receipt'] = $receipt;
                    addActivity('Menyelesaikan transaksi #' . (int)$_POST['id_transaksi']);
                    header('Location: index.php?page=receipt');
                    exit;

                default:
                    break;
            }
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
    }

    public function data(string $page): array
    {
        $data = array();
        switch ($page) {
            case 'dashboard': $data = $this->model->dashboard(); break;
            case 'users':
                $data['items'] = $this->model->users();
                $editId = (int)($_GET['edit'] ?? 0); $data['edit'] = null; foreach ($data['items'] as $row) if ((int)$row['id_user'] === $editId) { $data['edit'] = $row; break; }
                break;
            case 'areas':
                $data['items'] = $this->model->areas();
                $editId = (int)($_GET['edit'] ?? 0); $data['edit'] = null; foreach ($data['items'] as $row) if ((int)$row['id_area'] === $editId) { $data['edit'] = $row; break; }
                break;
            case 'tariffs':
                $data['items'] = $this->model->tariffs();
                $editId = (int)($_GET['edit'] ?? 0); $data['edit'] = null; foreach ($data['items'] as $row) if ((int)$row['id_tarif'] === $editId) { $data['edit'] = $row; break; }
                break;
            case 'vehicles':
                $data['items'] = $this->model->vehicles(); $data['areas'] = $this->model->areas();
                $editId = (int)($_GET['edit'] ?? 0); $data['edit'] = null; foreach ($data['items'] as $row) if ((int)$row['id_kendaraan'] === $editId) { $data['edit'] = $row; break; }
                break;
            case 'parking': $data['items'] = $this->model->activeTransactions(); break;
            case 'report':
                $data['start'] = $_GET['start'] ?? date('Y-m-01');
                $data['end'] = $_GET['end'] ?? date('Y-m-d');
                $data['items'] = $this->model->transactionHistory($data['start'], $data['end']);
                $data['total'] = 0;
                foreach ($data['items'] as $row) $data['total'] += (int)$row['total_bayar'];
                break;
            case 'receipt': $data['receipt'] = $_SESSION['receipt'] ?? null; break;
            case 'activity': $data['items'] = $_SESSION['activity_log'] ?? array(); break;
        }
        return $data;
    }
}
