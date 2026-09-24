<?php
declare(strict_types=1);

class Model
{
    private $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function login(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute(array($username));
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function dashboard(): array
    {
        $data = array();
        $data['users'] = (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $data['areas'] = (int)$this->db->query('SELECT COUNT(*) FROM area_parkir')->fetchColumn();
        $data['kendaraan'] = (int)$this->db->query('SELECT COUNT(*) FROM kendaraan')->fetchColumn();
        $data['parkir_aktif'] = (int)$this->db->query("SELECT COUNT(*) FROM transaksi_parkir WHERE status='Parkir'")->fetchColumn();
        $data['pendapatan'] = (float)$this->db->query("SELECT COALESCE(SUM(total_bayar),0) FROM transaksi_parkir WHERE status='Selesai'")->fetchColumn();
        return $data;
    }

    public function all(string $table): array
    {
        $allowed = array('users','area_parkir','tarif_parkir','kendaraan');
        if (!in_array($table, $allowed, true)) {
            throw new InvalidArgumentException('Table tidak diizinkan.');
        }
        return $this->db->query("SELECT * FROM {$table} ORDER BY 1 DESC")->fetchAll();
    }

    public function users(): array { return $this->all('users'); }
    public function areas(): array { return $this->all('area_parkir'); }
    public function tariffs(): array { return $this->all('tarif_parkir'); }

    public function vehicles(): array
    {
        return $this->db->query('SELECT k.*, a.nama_area FROM kendaraan k JOIN area_parkir a ON a.id_area = k.id_area ORDER BY k.id_kendaraan DESC')->fetchAll();
    }

    public function activeTransactions(): array
    {
        return $this->db->query("SELECT t.*, k.no_polisi, k.jenis_kendaraan, u.nama AS nama_petugas
            FROM transaksi_parkir t
            JOIN kendaraan k ON k.id_kendaraan=t.id_kendaraan
            JOIN users u ON u.id_user=t.id_user
            WHERE t.status='Parkir'
            ORDER BY t.id_transaksi DESC")->fetchAll();
    }

    public function transactionHistory(?string $start = null, ?string $end = null): array
    {
        $sql = "SELECT t.*, k.no_polisi, k.jenis_kendaraan, u.nama AS nama_petugas, tp.tarif_per_jam
                FROM transaksi_parkir t
                JOIN kendaraan k ON k.id_kendaraan=t.id_kendaraan
                JOIN users u ON u.id_user=t.id_user
                JOIN tarif_parkir tp ON tp.id_tarif=t.id_tarif
                WHERE t.status='Selesai'";
        $params = array();
        if ($start !== null && $start !== '') {
            $sql .= ' AND DATE(t.waktu_keluar) >= ?';
            $params[] = $start;
        }
        if ($end !== null && $end !== '') {
            $sql .= ' AND DATE(t.waktu_keluar) <= ?';
            $params[] = $end;
        }
        $sql .= ' ORDER BY t.id_transaksi DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findVehicleByPlate(string $plate): ?array
    {
        $stmt = $this->db->prepare('SELECT k.*, a.nama_area FROM kendaraan k JOIN area_parkir a ON a.id_area=k.id_area WHERE k.no_polisi=? LIMIT 1');
        $stmt->execute(array($plate));
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function tariffsByType(string $type): array
    {
        $stmt = $this->db->prepare('SELECT * FROM tarif_parkir WHERE jenis_kendaraan=? LIMIT 1');
        $stmt->execute(array($type));
        $row = $stmt->fetch();
        return $row ?: array();
    }

    public function createUser(string $nama, string $username, string $password, string $role): void
    {
        $stmt = $this->db->prepare('INSERT INTO users (nama, username, password, role) VALUES (?,?,?,?)');
        $stmt->execute(array($nama, $username, password_hash($password, PASSWORD_DEFAULT), $role));
    }

    public function updateUser(int $id, string $nama, string $username, string $role, ?string $password): void
    {
        if ($password !== null && $password !== '') {
            $stmt = $this->db->prepare('UPDATE users SET nama=?, username=?, role=?, password=? WHERE id_user=?');
            $stmt->execute(array($nama, $username, $role, password_hash($password, PASSWORD_DEFAULT), $id));
        } else {
            $stmt = $this->db->prepare('UPDATE users SET nama=?, username=?, role=? WHERE id_user=?');
            $stmt->execute(array($nama, $username, $role, $id));
        }
    }

    public function deleteUser(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id_user=?');
        $stmt->execute(array($id));
    }

    public function createArea(string $nama, int $kapasitas, string $lokasi): void
    {
        $stmt = $this->db->prepare('INSERT INTO area_parkir (nama_area, kapasitas, lokasi) VALUES (?,?,?)');
        $stmt->execute(array($nama, $kapasitas, $lokasi));
    }

    public function updateArea(int $id, string $nama, int $kapasitas, string $lokasi): void
    {
        $stmt = $this->db->prepare('UPDATE area_parkir SET nama_area=?, kapasitas=?, lokasi=? WHERE id_area=?');
        $stmt->execute(array($nama, $kapasitas, $lokasi, $id));
    }

    public function deleteArea(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM area_parkir WHERE id_area=?');
        $stmt->execute(array($id));
    }

    public function createTariff(string $jenis, int $tarif): void
    {
        $stmt = $this->db->prepare('INSERT INTO tarif_parkir (jenis_kendaraan, tarif_per_jam) VALUES (?,?)');
        $stmt->execute(array($jenis, $tarif));
    }

    public function updateTariff(int $id, string $jenis, int $tarif): void
    {
        $stmt = $this->db->prepare('UPDATE tarif_parkir SET jenis_kendaraan=?, tarif_per_jam=? WHERE id_tarif=?');
        $stmt->execute(array($jenis, $tarif, $id));
    }

    public function deleteTariff(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM tarif_parkir WHERE id_tarif=?');
        $stmt->execute(array($id));
    }

    public function createVehicle(string $plate, string $jenis, int $area): void
    {
        $stmt = $this->db->prepare('INSERT INTO kendaraan (no_polisi, jenis_kendaraan, id_area) VALUES (?,?,?)');
        $stmt->execute(array(strtoupper($plate), $jenis, $area));
    }

    public function updateVehicle(int $id, string $plate, string $jenis, int $area): void
    {
        $stmt = $this->db->prepare('UPDATE kendaraan SET no_polisi=?, jenis_kendaraan=?, id_area=? WHERE id_kendaraan=?');
        $stmt->execute(array(strtoupper($plate), $jenis, $area, $id));
    }

    public function deleteVehicle(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM kendaraan WHERE id_kendaraan=?');
        $stmt->execute(array($id));
    }

    public function parkVehicle(int $vehicleId, int $tariffId, int $userId): void
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM transaksi_parkir WHERE id_kendaraan=? AND status='Parkir'");
        $stmt->execute(array($vehicleId));
        if ((int)$stmt->fetchColumn() > 0) {
            throw new RuntimeException('Kendaraan tersebut masih tercatat sedang parkir.');
        }
        $stmt = $this->db->prepare("INSERT INTO transaksi_parkir (id_kendaraan,id_tarif,id_user,waktu_masuk,status) VALUES (?,?,?,NOW(),'Parkir')");
        $stmt->execute(array($vehicleId, $tariffId, $userId));
    }

    public function finishTransaction(int $id): array
    {
        $stmt = $this->db->prepare("SELECT t.*, tp.tarif_per_jam FROM transaksi_parkir t JOIN tarif_parkir tp ON tp.id_tarif=t.id_tarif WHERE t.id_transaksi=? AND t.status='Parkir' LIMIT 1");
        $stmt->execute(array($id));
        $tx = $stmt->fetch();
        if (!$tx) {
            throw new RuntimeException('Transaksi aktif tidak ditemukan.');
        }
        $start = new DateTime($tx['waktu_masuk']);
        $end = new DateTime('now');
        $seconds = max(0, $end->getTimestamp() - $start->getTimestamp());
        $hours = max(1, (int)ceil($seconds / 3600));
        $total = $hours * (int)$tx['tarif_per_jam'];
        $stmt = $this->db->prepare("UPDATE transaksi_parkir SET waktu_keluar=NOW(), durasi_jam=?, total_bayar=?, status='Selesai' WHERE id_transaksi=?");
        $stmt->execute(array($hours, $total, $id));
        $stmt = $this->db->prepare('SELECT t.*, k.no_polisi, k.jenis_kendaraan, u.nama AS nama_petugas, tp.tarif_per_jam FROM transaksi_parkir t JOIN kendaraan k ON k.id_kendaraan=t.id_kendaraan JOIN users u ON u.id_user=t.id_user JOIN tarif_parkir tp ON tp.id_tarif=t.id_tarif WHERE t.id_transaksi=?');
        $stmt->execute(array($id));
        return $stmt->fetch();
    }
}
