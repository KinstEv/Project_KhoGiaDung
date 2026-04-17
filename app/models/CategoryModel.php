<?php
class CategoryModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Lấy tất cả danh mục
    public function getAll() {
        // Dùng LEFT JOIN để vẫn trả về danh mục chưa có hàng hóa
    // Lấy thêm tên danh mục cha (nếu có) để hiển thị trong danh sách
    $sql = "SELECT d.*, COUNT(h.maHH) AS soLuong, p.tenDanhMuc AS tenDanhMucCha FROM danhmuc d LEFT JOIN hanghoa h ON d.maDanhMuc = h.maDanhMuc LEFT JOIN danhmuc p ON d.maDanhMucCha = p.maDanhMuc GROUP BY d.maDanhMuc ORDER BY d.tenDanhMuc ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm theo mã
    public function find($id) {
        $sql = "SELECT * FROM danhmuc WHERE maDanhMuc = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo mới
    public function create($data) {
        $sql = "INSERT INTO danhmuc (maDanhMuc, tenDanhMuc, maDanhMucCha) VALUES (:maDanhMuc, :tenDanhMuc, :maDanhMucCha)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'maDanhMuc' => $data['maDanhMuc'],
            'tenDanhMuc' => $data['tenDanhMuc']
            , 'maDanhMucCha' => isset($data['maDanhMucCha']) ? $data['maDanhMucCha'] : null
        ]);
    }

    // Cập nhật
    public function update($id, $data) {
        $sql = "UPDATE danhmuc SET tenDanhMuc = :tenDanhMuc, maDanhMucCha = :maDanhMucCha WHERE maDanhMuc = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'tenDanhMuc' => $data['tenDanhMuc'],
            'maDanhMucCha' => isset($data['maDanhMucCha']) ? $data['maDanhMucCha'] : null,
            'id' => $id
        ]);
    }

    // Xóa
    public function delete($id) {
        $sql = "DELETE FROM danhmuc WHERE maDanhMuc = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Kiểm tra xem danh mục có hàng hoá liên kết hay không
    public function hasProducts($id) {
        $sql = "SELECT COUNT(*) as cnt FROM hanghoa WHERE maDanhMuc = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row && $row['cnt'] > 0);
    }
}
?>
