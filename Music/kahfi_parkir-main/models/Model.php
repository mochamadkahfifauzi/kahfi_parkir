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
        return $this->db->query('SELECT k.* FROM kendaraan k ORDER BY k.id_kendaraan DESC')->fetchAll();
    }

    public function activeTransactions(): array
    {
        return $this->db->query("SELECT t.*, k.no_polisi, k.jenis_kendaraan, u.nama AS nama_petugas, a.nama_area
            FROM transaksi_parkir t
            JOIN kendaraan k ON k.id_kendaraan=t.id_kendaraan
            JOIN users u ON u.id_user=t.id_user
            JOIN area_parkir a ON a.id_area=t.id_area
            WHERE t.status='Parkir'
            ORDER BY t.id_transaksi DESC")->fetchAll();
    }

    public function transactionHistory(?string $start = null, ?string $end = null): array
    {
        $sql = "SELECT t.*, k.no_polisi, k.jenis_kendaraan, u.nama AS nama_petugas,
                       tp.tarif_per_jam, a.nama_area
                FROM transaksi_parkir t
                JOIN kendaraan k ON k.id_kendaraan=t.id_kendaraan
                JOIN users u ON u.id_user=t.id_user
                JOIN tarif_parkir tp ON tp.id_tarif=t.id_tarif
                JOIN area_parkir a ON a.id_area=t.id_area
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

    public function findVehicleById(int $vehicleId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM kendaraan WHERE id_kendaraan=? LIMIT 1');
        $stmt->execute(array($vehicleId));
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Kendaraan terdaftar yang belum memiliki transaksi parkir aktif. */
    public function availableVehicles(): array
    {
        return $this->db->query("SELECT k.* FROM kendaraan k
            WHERE NOT EXISTS (
                SELECT 1 FROM transaksi_parkir t
                WHERE t.id_kendaraan=k.id_kendaraan AND t.status='Parkir'
            )
            ORDER BY k.no_polisi ASC")->fetchAll();
    }

    /** Area beserta jumlah kendaraan yang sedang menempatinya. */
    public function parkingAreas(): array
    {
        return $this->db->query("SELECT a.id_area, a.nama_area, a.kapasitas, a.lokasi,
                    COUNT(t.id_transaksi) AS terisi,
                    GREATEST(a.kapasitas - COUNT(t.id_transaksi), 0) AS tersedia
                FROM area_parkir a
                LEFT JOIN transaksi_parkir t
                    ON t.id_area=a.id_area AND t.status='Parkir'
                GROUP BY a.id_area, a.nama_area, a.kapasitas, a.lokasi
                ORDER BY a.nama_area ASC")->fetchAll();
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

    public function createVehicle(string $plate, string $jenis): void
    {
        $stmt = $this->db->prepare('INSERT INTO kendaraan (no_polisi, jenis_kendaraan) VALUES (?,?)');
        $stmt->execute(array(strtoupper(trim($plate)), $jenis));
    }

    public function updateVehicle(int $id, string $plate, string $jenis): void
    {
        $stmt = $this->db->prepare('UPDATE kendaraan SET no_polisi=?, jenis_kendaraan=? WHERE id_kendaraan=?');
        $stmt->execute(array(strtoupper(trim($plate)), $jenis, $id));
    }

    public function deleteVehicle(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM kendaraan WHERE id_kendaraan=?');
        $stmt->execute(array($id));
    }

    public function parkVehicle(int $vehicleId, int $areaId, int $tariffId, int $userId): void
    {
        // Kunci baris area agar dua petugas tidak mengisi slot terakhir bersamaan.
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT kapasitas FROM area_parkir WHERE id_area=? FOR UPDATE');
            $stmt->execute(array($areaId));
            $capacity = $stmt->fetchColumn();
            if ($capacity === false) {
                throw new RuntimeException('Area parkir tidak ditemukan.');
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM transaksi_parkir WHERE id_area=? AND status='Parkir'");
            $stmt->execute(array($areaId));
            if ((int)$stmt->fetchColumn() >= (int)$capacity) {
                throw new RuntimeException('Area parkir sudah penuh. Silakan pilih area lain.');
            }

            // Kunci data kendaraan juga supaya dua petugas tidak dapat
            // memasukkan kendaraan yang sama pada dua area berbeda bersamaan.
            $stmt = $this->db->prepare('SELECT id_kendaraan FROM kendaraan WHERE id_kendaraan=? FOR UPDATE');
            $stmt->execute(array($vehicleId));
            if (!$stmt->fetchColumn()) {
                throw new RuntimeException('Kendaraan tidak ditemukan.');
            }

            $stmt = $this->db->prepare("SELECT id_transaksi FROM transaksi_parkir
                WHERE id_kendaraan=? AND status='Parkir' LIMIT 1 FOR UPDATE");
            $stmt->execute(array($vehicleId));
            if ($stmt->fetch()) {
                throw new RuntimeException('Kendaraan tersebut masih tercatat sedang parkir.');
            }

            $stmt = $this->db->prepare("INSERT INTO transaksi_parkir (id_kendaraan,id_area,id_tarif,id_user,waktu_masuk,status) VALUES (?,?,?,?,NOW(),'Parkir')");
            $stmt->execute(array($vehicleId, $areaId, $tariffId, $userId));
            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function finishTransaction(int $id): array
    {
        $this->db->beginTransaction();
        try {
            // Hitung durasi di MySQL agar menggunakan jam yang sama dengan NOW()
            // saat kendaraan masuk, tanpa selisih zona waktu antara PHP dan database.
            $stmt = $this->db->prepare("SELECT t.*, tp.tarif_per_jam,
                    GREATEST(TIMESTAMPDIFF(SECOND, t.waktu_masuk, NOW()), 0) AS durasi_detik
                FROM transaksi_parkir t
                JOIN tarif_parkir tp ON tp.id_tarif=t.id_tarif
                WHERE t.id_transaksi=? AND t.status='Parkir'
                LIMIT 1 FOR UPDATE");
            $stmt->execute(array($id));
            $tx = $stmt->fetch();
            if (!$tx) {
                throw new RuntimeException('Transaksi aktif tidak ditemukan atau sudah selesai.');
            }

            $seconds = max(0, (int)$tx['durasi_detik']);
            $minutes = (int)floor($seconds / 60);
            // Tagihan minimum 1 jam; pembulatan hanya untuk tarif, bukan durasi aktual.
            $billableHours = max(1, (int)ceil($seconds / 3600));
            $total = $billableHours * (int)$tx['tarif_per_jam'];

            $stmt = $this->db->prepare("UPDATE transaksi_parkir
                SET waktu_keluar=NOW(), durasi_menit=?, durasi_jam=?, total_bayar=?, status='Selesai'
                WHERE id_transaksi=? AND status='Parkir'");
            $stmt->execute(array($minutes, $billableHours, $total, $id));

            $stmt = $this->db->prepare('SELECT t.*, k.no_polisi, k.jenis_kendaraan,
                    u.nama AS nama_petugas, tp.tarif_per_jam, a.nama_area
                FROM transaksi_parkir t
                JOIN kendaraan k ON k.id_kendaraan=t.id_kendaraan
                JOIN users u ON u.id_user=t.id_user
                JOIN tarif_parkir tp ON tp.id_tarif=t.id_tarif
                JOIN area_parkir a ON a.id_area=t.id_area
                WHERE t.id_transaksi=?');
            $stmt->execute(array($id));
            $receipt = $stmt->fetch();
            $this->db->commit();
            return $receipt;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

}
