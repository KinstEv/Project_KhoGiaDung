<?php
class InventoryModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAllStock() {
        // SỬA: Lấy dữ liệu từ bảng lo_hang_vi_tri
        $sql = "SELECT 
                    lvt.soLuong as soLuongTon,
                    lvt.maLo,
                    hh.tenHH,
                    hh.maHH,
                    lh.hanBaoHanh,
                    vt.maViTri,
                        vt.daiToiDa,
                        vt.rongToiDa,
                        vt.caoToiDa,
                        vt.choPhepXepChong,
                        CONCAT(vt.day, '-', vt.ke, '-', vt.o) as viTriCuThe,
                    -- occupancy percent for that entire position (sum of quantity * size coefficient)
                    (
                        -- tổng số lượng đơn vị hiện có tại vị trí (không nhân hệ số vì DB không có heSoChiemCho)
                        SELECT COALESCE(SUM(lvt2.soLuong), 0)
                        FROM lo_hang_vi_tri lvt2
                        WHERE lvt2.maViTri = vt.maViTri
                    ) as totalAtPosition
                FROM lo_hang_vi_tri lvt
                JOIN lohang lh ON lvt.maLo = lh.maLo
                JOIN hanghoa hh ON lh.maHH = hh.maHH
                JOIN vitri vt ON lvt.maViTri = vt.maViTri
                WHERE lvt.soLuong > 0
                ORDER BY hh.tenHH ASC";

                $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>