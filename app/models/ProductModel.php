<?php
class ProductModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAll() {
        // Tính tổng tồn:
        // - Với hàng SERIAL: đếm số serial còn khả dụng (trangThai = 1) thông qua bảng Lô Hàng
        // - Với hàng thường (LO): lấy tổng số lượng tồn từ lo_hang_vi_tri
        $sql = "SELECT h.*, d.tenDanhMuc, dv.tenDVT,
                CASE 
                    WHEN h.loaiHang = 'SERIAL' THEN (
                        SELECT COALESCE(COUNT(*), 0)
                        FROM hanghoa_serial hs
                        INNER JOIN lohang lh ON hs.maLo = lh.maLo -- SỬA LỖI: Join bảng lô hàng
                        WHERE lh.maHH = h.maHH AND hs.trangThai = 1
                    )
                    ELSE (
                        SELECT COALESCE(SUM(lvt.soLuong), 0)
                        FROM lo_hang_vi_tri lvt
                        JOIN lohang lh ON lvt.maLo = lh.maLo
                        WHERE lh.maHH = h.maHH
                    )
                END as tongTon,
                -- Lấy giá nhập gần nhất từ chi tiết phiếu nhập (theo ngày nhập gần nhất)
                (
                    SELECT ct.donGia
                    FROM ct_phieunhap ct
                    JOIN phieunhap pn ON ct.maPN = pn.maPN
                    WHERE ct.maHH = h.maHH
                    ORDER BY pn.ngayNhap DESC, ct.maPN DESC
                    LIMIT 1
                ) as donGiaNhap
                FROM hanghoa h
                LEFT JOIN danhmuc d ON h.maDanhMuc = d.maDanhMuc
                LEFT JOIN donvitinh dv ON h.maDVT = dv.maDVT
                ORDER BY h.tenHH ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find($id) {
        $sql = "SELECT * FROM hanghoa WHERE maHH = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy các lô hiện có cho một sản phẩm (maHH)
    public function getLots($maHH) {
        $sql = "SELECT lh.maLo, lh.maPN, lh.soLuongNhap, lh.ngayNhap,
                    COALESCE((SELECT SUM(lvt.soLuong) FROM lo_hang_vi_tri lvt WHERE lvt.maLo = lh.maLo), 0) AS soLuongCon,
                    (
                        SELECT ct.donGia FROM ct_phieunhap ct WHERE ct.maPN = lh.maPN AND ct.maHH = lh.maHH LIMIT 1
                    ) AS donGia
                FROM lohang lh
                WHERE lh.maHH = :maHH
                ORDER BY lh.ngayNhap ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maHH' => $maHH]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy các vị trí chứa một lô (maLo)
    public function getLocationsByLo($maLo) {
        $sql = "SELECT lvt.maViTri, lvt.soLuong, v.day, v.ke, v.o
                FROM lo_hang_vi_tri lvt
                LEFT JOIN vitri v ON lvt.maViTri = v.maViTri
                WHERE lvt.maLo = :maLo";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maLo' => $maLo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách serial khả dụng cho một mã hàng (chưa xuất, trangThai = 1)
    public function getAvailableSerials($maHH) {
        // SỬA LỖI: Luôn JOIN với bảng lohang vì hanghoa_serial không có cột maHH
        $sql = "SELECT hs.serial, hs.maLo, hs.maViTri
                FROM hanghoa_serial hs
                JOIN lohang lh ON hs.maLo = lh.maLo
                WHERE lh.maHH = :maHH AND hs.trangThai = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maHH' => $maHH]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        // Insert includes dimensions and rotation rule
    $sql = "INSERT INTO hanghoa (maHH, tenHH, loaiHang, maDanhMuc, maDVT, model, thuongHieu, moTa, thoiGianBaoHanh, chieuDai, chieuRong, chieuCao, quyTacXoay) 
        VALUES (:maHH, :tenHH, :loaiHang, :maDanhMuc, :maDVT, :model, :thuongHieu, :moTa, :bh, :dai, :rong, :cao, :quyTac)";
        $stmt = $this->conn->prepare($sql);
        // Ensure array has loaiHang key
        $params = [
            ':maHH' => $data['maHH'],
            ':tenHH' => $data['tenHH'],
            ':loaiHang' => $data['loaiHang'] ?? 'LO',
            ':maDanhMuc' => $data['maDanhMuc'],
            ':maDVT' => $data['maDVT'],
            ':model' => $data['model'],
            ':thuongHieu' => $data['thuongHieu'],
            ':moTa' => $data['moTa'],
            ':bh' => isset($data['thoiGianBaoHanh']) ? (int)$data['thoiGianBaoHanh'] : 12,
            ':dai' => isset($data['chieuDai']) ? (int)$data['chieuDai'] : 0,
            ':rong' => isset($data['chieuRong']) ? (int)$data['chieuRong'] : 0,
            ':cao' => isset($data['chieuCao']) ? (int)$data['chieuCao'] : 0,
            ':quyTac' => isset($data['quyTacXoay']) ? $data['quyTacXoay'] : 'XOAY_NGANG'
        ];
        return $stmt->execute($params);
    }

    public function update($id, $data) {
    $sql = "UPDATE hanghoa SET tenHH = :tenHH, loaiHang = :loaiHang, maDanhMuc = :maDanhMuc,
        maDVT = :maDVT, model = :model, thuongHieu = :thuongHieu, moTa = :moTa, thoiGianBaoHanh = :bh,
        chieuDai = :dai, chieuRong = :rong, chieuCao = :cao, quyTacXoay = :quyTac
        WHERE maHH = :maHH";
        $stmt = $this->conn->prepare($sql);
        $params = [
            ':tenHH' => $data['tenHH'],
            ':loaiHang' => $data['loaiHang'] ?? 'LO',
            ':maDanhMuc' => $data['maDanhMuc'],
            ':maDVT' => $data['maDVT'],
            ':model' => $data['model'],
            ':thuongHieu' => $data['thuongHieu'],
            ':moTa' => $data['moTa'],
            ':bh' => isset($data['thoiGianBaoHanh']) ? (int)$data['thoiGianBaoHanh'] : 12,
            ':dai' => isset($data['chieuDai']) ? (int)$data['chieuDai'] : 0,
            ':rong' => isset($data['chieuRong']) ? (int)$data['chieuRong'] : 0,
            ':cao' => isset($data['chieuCao']) ? (int)$data['chieuCao'] : 0,
            ':quyTac' => isset($data['quyTacXoay']) ? $data['quyTacXoay'] : 'XOAY_NGANG',
            ':maHH' => $id
        ];
        return $stmt->execute($params);
    }

    public function delete($id) {
        // Prevent delete if there are lots referencing this product
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM lohang WHERE maHH = :id");
        $stmt->execute(['id' => $id]);
        $cnt = $stmt->fetchColumn();
        if ($cnt > 0) {
            return false;
        }

        $sql = "DELETE FROM hanghoa WHERE maHH = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
?>