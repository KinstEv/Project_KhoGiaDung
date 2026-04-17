<?php
class VitriModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Lấy thông tin vị trí, kích thước và tổng số lượng đang chứa
        // Thêm maDanhMucUuTien để biết dãy ưu tiên (A/B/C/D)
     $sql = "SELECT v.maViTri, v.day, v.ke, v.o, v.daiToiDa, v.rongToiDa, v.caoToiDa, v.choPhepXepChong, v.trangThai, v.maDanhMucUuTien,
             d.tenDanhMuc AS tenDanhMucUuTien,
             COALESCE(SUM(lvt.soLuong), 0) AS totalAtPosition
         FROM vitri v
         LEFT JOIN lo_hang_vi_tri lvt ON lvt.maViTri = v.maViTri
         LEFT JOIN danhmuc d ON v.maDanhMucUuTien = d.maDanhMuc
         GROUP BY v.maViTri, v.day, v.ke, v.o, v.daiToiDa, v.rongToiDa, v.caoToiDa, v.choPhepXepChong, v.trangThai, v.maDanhMucUuTien, d.tenDanhMuc
         ORDER BY v.day ASC, v.ke ASC, v.o ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sort positions using natural (human) ordering so that C1-2 comes before C1-10
        usort($rows, function ($a, $b) {
            $aDay = $a['day'] ?? '';
            $bDay = $b['day'] ?? '';
            $cmp = strnatcmp($aDay, $bDay);
            if ($cmp !== 0) return $cmp;

            $aKe = $a['ke'] ?? '';
            $bKe = $b['ke'] ?? '';
            $cmp = strnatcmp($aKe, $bKe);
            if ($cmp !== 0) return $cmp;

            $aO = $a['o'] ?? '';
            $bO = $b['o'] ?? '';
            return strnatcmp($aO, $bO);
        });

        // Compute status for each position according to rules.
        foreach ($rows as &$r) {
            $r['statusComputed'] = $this->computeStatusForVitri($r);
            $r['fillPercent'] = $this->computeFillPercent($r);
        }

        return $rows;
    }

    // Compute human-friendly status for a position based on rules provided
    private function computeStatusForVitri($vitri) {
        // Basic dimension checks
        $vt_dai = isset($vitri['daiToiDa']) ? (int)$vitri['daiToiDa'] : 0;
        $vt_rong = isset($vitri['rongToiDa']) ? (int)$vitri['rongToiDa'] : 0;
        $vt_cao = isset($vitri['caoToiDa']) ? (int)$vitri['caoToiDa'] : 0;
        if ($vt_dai <= 0 || $vt_rong <= 0) return ['code' => 'no-dim', 'label' => 'Kích thước chưa cấu hình'];

        // Find smallest product in the priority category (by floor area); fallback to whole warehouse
        $cat = $vitri['maDanhMucUuTien'] ?? null;
        $stmtMin = $this->conn->prepare("SELECT chieuDai, chieuRong, chieuCao, quyTacXoay FROM hanghoa WHERE maDanhMuc = ? ORDER BY (chieuDai * chieuRong) ASC LIMIT 1");
        $stmtMin->execute([$cat]);
        $minProd = $stmtMin->fetch(PDO::FETCH_ASSOC);
        if (!$minProd) {
            $stmtMinAll = $this->conn->prepare("SELECT chieuDai, chieuRong, chieuCao, quyTacXoay FROM hanghoa ORDER BY (chieuDai * chieuRong) ASC LIMIT 1");
            $stmtMinAll->execute();
            $minProd = $stmtMinAll->fetch(PDO::FETCH_ASSOC);
        }
        if (!$minProd) return ['code' => 'no-prod', 'label' => 'Chưa có sản phẩm làm chuẩn'];

        // Build minimal product array for allocator
        $minProdArr = [
            'chieuDai' => (int)($minProd['chieuDai'] ?? 0),
            'chieuRong' => (int)($minProd['chieuRong'] ?? 0),
            'chieuCao' => (int)($minProd['chieuCao'] ?? 0),
            'quyTacXoay' => $minProd['quyTacXoay'] ?? null
        ];

        // Fast-path heuristic: use simple fill-percent to avoid running the expensive packer for every position.
        // Only call the exact allocator when the fill is ambiguous (near-full).
        try {
            $fill = $this->computeFillPercent($vitri);

            // If we cannot compute fill (bad dims) fallback to trying the allocator
            if ($fill === null) {
                if (!class_exists('ImportModel')) {
                    require_once APP_ROOT . '/models/ImportModel.php';
                }
                $importModel = new ImportModel();
                $cap = (int)$importModel->calculateAvailableCapacity($vitri, $minProdArr, 20);
                return $cap > 0 ? ['code' => 'has-space', 'label' => 'Còn chỗ'] : ['code' => 'full', 'label' => 'Đầy'];
            }

            // Quick rules based on percentage
            if ($fill >= 100) return ['code' => 'full', 'label' => 'Đầy'];
            if ($fill <= 90) return ['code' => 'has-space', 'label' => 'Còn chỗ'];

            // Ambiguous zone (91-99%) — run a limited precise check (small maxTestCount)
            if (!class_exists('ImportModel')) {
                require_once APP_ROOT . '/models/ImportModel.php';
            }
            $importModel = new ImportModel();
            $cap = (int)$importModel->calculateAvailableCapacity($vitri, $minProdArr, 20);
            return $cap > 0 ? ['code' => 'has-space', 'label' => 'Còn chỗ'] : ['code' => 'full', 'label' => 'Đầy'];
        } catch (Exception $e) {
            return ['code' => 'err', 'label' => 'Không xác định'];
        }
    }

    // Compute fill percentage for a position
    // For A/B (no stacking): use floor area percentage
    // For C/D (stacking allowed): use volume percentage
    public function computeFillPercent($vitri) {
        $vt_dai = isset($vitri['daiToiDa']) ? (int)$vitri['daiToiDa'] : 0;
        $vt_rong = isset($vitri['rongToiDa']) ? (int)$vitri['rongToiDa'] : 0;
        $vt_cao = isset($vitri['caoToiDa']) ? (int)$vitri['caoToiDa'] : 0;

        if ($vt_dai <= 0 || $vt_rong <= 0) return null;

        $allowStack = !empty($vitri['choPhepXepChong']);

        // Fetch current items in this position
        $stmt = $this->conn->prepare("SELECT lvt.soLuong, h.chieuDai, h.chieuRong, h.chieuCao FROM lo_hang_vi_tri lvt JOIN lohang l ON lvt.maLo = l.maLo JOIN hanghoa h ON l.maHH = h.maHH WHERE lvt.maViTri = ?");
        $stmt->execute([$vitri['maViTri']]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$allowStack) {
            // Area-based
            $vt_area = max(1, $vt_dai * $vt_rong);
            $occupied_area = 0;
            foreach ($items as $it) {
                $p_d = (int)($it['chieuDai'] ?? 0);
                $p_r = (int)($it['chieuRong'] ?? 0);
                $qty = (int)($it['soLuong'] ?? 0);
                $occupied_area += $qty * max(1, $p_d) * max(1, $p_r);
            }
            $percent = ($occupied_area / $vt_area) * 100.0;
            return (int)round(min(100, $percent));
        } else {
            // Volume-based for stacking
            if ($vt_cao <= 0) return null;
            $vt_vol = max(1, $vt_dai * $vt_rong * $vt_cao);
            $occupied_vol = 0;
            foreach ($items as $it) {
                $p_d = (int)($it['chieuDai'] ?? 0);
                $p_r = (int)($it['chieuRong'] ?? 0);
                $p_h = (int)($it['chieuCao'] ?? 0);
                $qty = (int)($it['soLuong'] ?? 0);
                $occupied_vol += $qty * max(1, $p_d) * max(1, $p_r) * max(1, $p_h);
            }
            $percent = ($occupied_vol / $vt_vol) * 100.0;
            return (int)round(min(100, $percent));
        }
    }

    public function find($id) {
    $sql = "SELECT maViTri, day, ke, o, daiToiDa, rongToiDa, caoToiDa, choPhepXepChong, trangThai FROM vitri WHERE maViTri = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $ma = $this->makeMaViTri($data['day'], $data['ke'], $data['o']);
        $sql = "INSERT INTO vitri (maViTri, day, ke, o, daiToiDa, rongToiDa, caoToiDa, maDanhMucUuTien, choPhepXepChong, trangThai) VALUES (:ma, :day, :ke, :o, :dai, :rong, :cao, :mdu, :chx, :tt)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':ma' => $ma,
            ':day' => $data['day'],
            ':ke' => $data['ke'],
            ':o' => $data['o'],
            ':dai' => isset($data['daiToiDa']) ? (int)$data['daiToiDa'] : 0,
            ':rong' => isset($data['rongToiDa']) ? (int)$data['rongToiDa'] : 0,
            ':cao' => isset($data['caoToiDa']) ? (int)$data['caoToiDa'] : 0,
            ':mdu' => isset($data['maDanhMucUuTien']) ? $data['maDanhMucUuTien'] : null,
            ':chx' => isset($data['choPhepXepChong']) ? (int)$data['choPhepXepChong'] : 0,
            ':tt' => isset($data['trangThai']) ? (int)$data['trangThai'] : 1
        ]);
    }

    public function update($id, $data) {
        // allow updating day/ke/o but keep maViTri consistent
        $sql = "UPDATE vitri SET day = :day, ke = :ke, o = :o, daiToiDa = :dai, rongToiDa = :rong, caoToiDa = :cao, maDanhMucUuTien = :mdu, choPhepXepChong = :chx, trangThai = :tt WHERE maViTri = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':day' => $data['day'],
            ':ke' => $data['ke'],
            ':o' => $data['o'],
            ':dai' => isset($data['daiToiDa']) ? (int)$data['daiToiDa'] : 0,
            ':rong' => isset($data['rongToiDa']) ? (int)$data['rongToiDa'] : 0,
            ':cao' => isset($data['caoToiDa']) ? (int)$data['caoToiDa'] : 0,
            ':mdu' => isset($data['maDanhMucUuTien']) ? $data['maDanhMucUuTien'] : null,
            ':chx' => isset($data['choPhepXepChong']) ? (int)$data['choPhepXepChong'] : 0,
            ':tt' => isset($data['trangThai']) ? (int)$data['trangThai'] : 1,
            ':id' => $id
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM vitri WHERE maViTri = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function exists($ma) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM vitri WHERE maViTri = :ma");
        $stmt->execute([':ma' => $ma]);
        return $stmt->fetchColumn() > 0;
    }

    private function makeMaViTri($day, $ke, $o) {
        // normalize and produce code like D1-K2-O03 or DAY1-KE2-O3 depending on input
        $d = preg_replace('/\s+/', '', strtoupper($day));
        $k = preg_replace('/\s+/', '', strtoupper($ke));
        $o = preg_replace('/\s+/', '', strtoupper($o));
        return sprintf('%s-%s-%s', $d, $k, $o);
    }
}
