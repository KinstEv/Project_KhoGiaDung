<?php
// Load Composer autoload. Use project root relative path (app/models -> ../../vendor)
require_once __DIR__ . '/../../vendor/autoload.php';

use DVDoug\BoxPacker\InfalliblePacker as Packer;
use DVDoug\BoxPacker\Box;
use DVDoug\BoxPacker\Item;

/**
 * Class định nghĩa Ô Kệ
 */
class WarehouseBin implements Box
{
    private $reference;
    private $outerWidth;
    private $outerLength;
    private $outerDepth;
    private $emptyWeight;
    private $innerWidth;
    private $innerLength;
    private $innerDepth;
    private $maxWeight;

    public function __construct($reference, $outerWidth, $outerLength, $outerDepth, $emptyWeight, $innerWidth, $innerLength, $innerDepth, $maxWeight)
    {
        $this->reference = $reference;
        $this->outerWidth = $outerWidth;
        $this->outerLength = $outerLength;
        $this->outerDepth = $outerDepth;
        $this->emptyWeight = $emptyWeight;
        $this->innerWidth = $innerWidth;
        $this->innerLength = $innerLength;
        $this->innerDepth = $innerDepth;
        $this->maxWeight = $maxWeight;
    }

    public function getReference(): string
    {
        return (string)$this->reference;
    }
    public function getOuterWidth(): int
    {
        return (int)$this->outerWidth;
    }
    public function getOuterLength(): int
    {
        return (int)$this->outerLength;
    }
    public function getOuterDepth(): int
    {
        return (int)$this->outerDepth;
    }
    public function getEmptyWeight(): int
    {
        return (int)$this->emptyWeight;
    }
    public function getInnerWidth(): int
    {
        return (int)$this->innerWidth;
    }
    public function getInnerLength(): int
    {
        return (int)$this->innerLength;
    }
    public function getInnerDepth(): int
    {
        return (int)$this->innerDepth;
    }
    public function getMaxWeight(): int
    {
        return (int)$this->maxWeight;
    }
}

/**
 * Class định nghĩa Hàng Hóa (Tương thích PHP 7.x)
 */
class WarehouseItem implements Item
{
    private $description;
    private $width;
    private $length;
    private $depth;
    private $weight;
    private $keepFlat;
    private $canStack;

    public function __construct($description, $width, $length, $depth, $weight, $keepFlat, $canStack = true)
    {
        $this->description = $description;
        $this->width = $width;
        $this->length = $length;
        $this->depth = $depth;
        $this->weight = $weight;
        $this->keepFlat = $keepFlat; // true = Xoay ngang, false = Xoay tự do
        $this->canStack = $canStack;
    }

    public function getDescription(): string
    {
        return (string)$this->description;
    }
    public function getWidth(): int
    {
        return (int)$this->width;
    }
    public function getLength(): int
    {
        return (int)$this->length;
    }
    public function getDepth(): int
    {
        return (int)$this->depth;
    }
    public function getWeight(): int
    {
        return (int)$this->weight;
    }

    // Thuộc tính quan trọng cho quy tắc xoay
    public function getKeepFlat(): bool
    {
        return (bool)$this->keepFlat;
    }

    // New: indicate whether the item may be rotated (library-version dependent)
    // Keeping this true allows rotation around vertical axis but getKeepFlat() prevents flipping onto a short side.
    public function canBeRotated(): bool
    {
        return true;
    }

    // Thuộc tính quan trọng cho việc xếp chồng
    public function getCanBeStackedOn(): bool
    {
        return (bool)$this->canStack;
    }
}
class ImportModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    // --- ĐÃ SỬA: Sinh mã phiếu nhập theo định dạng PN-ddMMyyyy-XXX ---
    private function generateMaPN()
    {
        // Lấy ngày format dmY (Ví dụ: 12012026)
        $date = date('dmY');

        // Cấu trúc: PN-12012026-001
        // Đếm ký tự prefix: PN- (3) + 12012026 (8) + - (1) = 12 ký tự
        // Số thứ tự bắt đầu từ ký tự thứ 13
        $sql = "SELECT MAX(CAST(SUBSTRING(maPN, 13, 3) AS UNSIGNED)) as max_stt 
                FROM phieunhap 
                WHERE maPN LIKE ?";

        // Mẫu tìm kiếm: PN-12012026-%
        $like = 'PN-' . $date . '-%';

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$like]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Nếu chưa có thì bắt đầu là 1, có rồi thì cộng thêm 1
        $stt = isset($row['max_stt']) && $row['max_stt'] ? ((int)$row['max_stt'] + 1) : 1;

        // Trả về: PN-12012026-001
        return 'PN-' . $date . '-' . str_pad($stt, 3, '0', STR_PAD_LEFT);
    }

    // Sinh tiền tố mã lô: LO + [YYYYMMDD] (Ví dụ: LO20260109)
    private function generateBaseMaLoPrefix()
    {
        return 'LO' . date('Ymd');
    }

    // ... (Giữ nguyên các hàm getSuppliers, getProducts) ...
    public function getSuppliers()
    {
        // Chỉ lấy nhà cung cấp đang hoạt động (trangThai = 1)
        $stmt = $this->conn->prepare("SELECT * FROM nhacungcap WHERE trangThai = 1 ORDER BY tenNCC ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProducts()
    {
        $stmt = $this->conn->prepare("SELECT * FROM hanghoa ORDER BY tenHH ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllImports()
    {
        $sql = "SELECT pn.*, ncc.tenNCC, nd.tenND 
                FROM phieunhap pn
                JOIN nhacungcap ncc ON pn.maNCC = ncc.maNCC
                LEFT JOIN nguoidung nd ON pn.maND = nd.maND
                ORDER BY pn.ngayNhap DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Legacy getAll() removed. If needed, re-add a safe implementation here.

    public function getImportById($maPN)
    {
        // Header
        $sql = "SELECT pn.*, ncc.tenNCC, nd.tenND
                FROM phieunhap pn
                JOIN nhacungcap ncc ON pn.maNCC = ncc.maNCC
                LEFT JOIN nguoidung nd ON pn.maND = nd.maND
                WHERE pn.maPN = :maPN LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maPN' => $maPN]);
        $header = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$header) return null;

        // Lines
        $sqlLines = "SELECT ct.*, hh.tenHH
                     FROM ct_phieunhap ct
                     LEFT JOIN hanghoa hh ON ct.maHH = hh.maHH
                     WHERE ct.maPN = :maPN";
        $stmt = $this->conn->prepare($sqlLines);
        $stmt->execute([':maPN' => $maPN]);
        $lines = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Nested Lots, Locations, Serials
        foreach ($lines as &$ln) {
            $ln['lots'] = [];
            $sqlLohang = "SELECT * FROM lohang WHERE maPN = :maPN AND maHH = :maHH";
            $stmtL = $this->conn->prepare($sqlLohang);
            $stmtL->execute([':maPN' => $maPN, ':maHH' => $ln['maHH']]);
            $lohangs = $stmtL->fetchAll(PDO::FETCH_ASSOC);

            foreach ($lohangs as $lh) {
                $lot = $lh;
                $sqlLoc = "SELECT lvt.*, v.day, v.ke, v.o FROM lo_hang_vi_tri lvt LEFT JOIN vitri v ON lvt.maViTri = v.maViTri WHERE lvt.maLo = :maLo";
                $stmtLoc = $this->conn->prepare($sqlLoc);
                $stmtLoc->execute([':maLo' => $lh['maLo']]);
                $lot['locations'] = $stmtLoc->fetchAll(PDO::FETCH_ASSOC);

                $sqlSerials = "SELECT serial, trangThai, maViTri FROM hanghoa_serial WHERE maLo = :maLo";
                $stmtS = $this->conn->prepare($sqlSerials);
                $stmtS->execute([':maLo' => $lh['maLo']]);
                $lot['serials'] = $stmtS->fetchAll(PDO::FETCH_ASSOC);

                $ln['lots'][] = $lot;
            }
        }
        return ['header' => $header, 'lines' => $lines];
    }

    // XỬ LÝ GIAO DỊCH NHẬP KHO (TRANSACTION)
    // Đảm bảo tính toàn vẹn: Tất cả thành công hoặc tất cả thất bại (Rollback)
    public function createImportTransaction($data, $products)
    {
        try {
            $this->conn->beginTransaction();

            // 1. TẠO PHIẾU NHẬP
            $maPN = !empty($data['maPN']) ? $data['maPN'] : $this->generateMaPN();
            $sqlPN = "INSERT INTO phieunhap (maPN, ngayNhap, maNCC, ghiChu, maND) 
                      VALUES (:maPN, NOW(), :maNCC, :ghiChu, :maND)";
            $stmtPN = $this->conn->prepare($sqlPN);
            $stmtPN->execute([
                ':maPN' => $maPN,
                ':maNCC' => $data['maNCC'],
                ':ghiChu' => $data['ghiChu'],
                ':maND' => $data['maND']
            ]);

            // Lấy danh sách tất cả vị trí kho
            $stmtVT = $this->conn->query("SELECT * FROM vitri");
            $vitriList = $stmtVT->fetchAll(PDO::FETCH_ASSOC);

            $prefixLo = $this->generateBaseMaLoPrefix();
            $stmtMaxLo = $this->conn->prepare("SELECT MAX(CAST(SUBSTRING(maLo, 11, 4) AS UNSIGNED)) FROM lohang WHERE maLo LIKE ?");
            $stmtMaxLo->execute([$prefixLo . '%']);
            $currentMaxStt = (int)$stmtMaxLo->fetchColumn();

            // 2. XỬ LÝ TỪNG SẢN PHẨM
            // --- Pre-sorting: đặt hàng lớn (diện tích đáy lớn) trước để tối ưu đóng gói sàn A/B ---
            if (is_array($products) && count($products) > 1) {
                $maList = array_values(array_unique(array_map(function ($p) {
                    return $p['maHH'] ?? null;
                }, $products)));
                $areaMap = [];
                if (count($maList) > 0) {
                    $placeholders = implode(',', array_fill(0, count($maList), '?'));
                    $stmtA = $this->conn->prepare("SELECT maHH, chieuDai, chieuRong FROM hanghoa WHERE maHH IN ($placeholders)");
                    $stmtA->execute($maList);
                    $rowsA = $stmtA->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($rowsA as $r) {
                        $d = (int)($r['chieuDai'] ?? 0);
                        $rwd = (int)($r['chieuRong'] ?? 0);
                        $areaMap[$r['maHH']] = $d * $rwd;
                    }
                }

                usort($products, function ($a, $b) use ($areaMap) {
                    $maA = $a['maHH'] ?? null;
                    $maB = $b['maHH'] ?? null;
                    $areaA = isset($areaMap[$maA]) ? $areaMap[$maA] : (($a['chieuDai'] ?? 0) * ($a['chieuRong'] ?? 0));
                    $areaB = isset($areaMap[$maB]) ? $areaMap[$maB] : (($b['chieuDai'] ?? 0) * ($b['chieuRong'] ?? 0));
                    return $areaB <=> $areaA; // giảm dần
                });
            }

            foreach ($products as $item) {
                $currentMaxStt++;
                $maLo = $prefixLo . str_pad($currentMaxStt, 4, '0', STR_PAD_LEFT);

                // Lấy thông tin hàng hóa bao gồm Danh mục để tìm Danh mục Cha
                $stmtHH = $this->conn->prepare("
                    SELECT h.tenHH, h.loaiHang, h.chieuDai, h.chieuRong, h.chieuCao, h.quyTacXoay, d.maDanhMucCha 
                    FROM hanghoa h
                    LEFT JOIN danhmuc d ON h.maDanhMuc = d.maDanhMuc
                    WHERE h.maHH = :maHH LIMIT 1
                ");
                $stmtHH->execute([':maHH' => $item['maHH']]);
                $rowType = $stmtHH->fetch(PDO::FETCH_ASSOC);

                $parentDM = $rowType['maDanhMucCha']; // Ví dụ: 'BIG_DTDM'
                $soLuongConLai = (int)$item['soLuong'];

                // Lưu thông tin Lô hàng
                $stmtLo = $this->conn->prepare("INSERT INTO lohang (maLo, maPN, maHH, soLuongNhap, ngayNhap) VALUES (:maLo, :maPN, :maHH, :soLuong, NOW())");
                $stmtLo->execute([':maLo' => $maLo, ':maPN' => $maPN, ':maHH' => $item['maHH'], ':soLuong' => $item['soLuong']]);

                // --- LOGIC PHÂN BỔ ƯU TIÊN MỚI ---
                // Sắp xếp vị trí theo: 1) Zoning (maDanhMucUuTien), 2) Height-fit (khít chiều cao),
                // 3) Gom hàng cùng loại, 4) Thứ tự tự nhiên mã vị trí.
                $fnSort = function ($a, $b) use ($item, $parentDM, $rowType) {
                    // 1. Ưu tiên đúng khu vực (Zoning)
                    $aMatch = ($a['maDanhMucUuTien'] === $parentDM) ? 1 : 0;
                    $bMatch = ($b['maDanhMucUuTien'] === $parentDM) ? 1 : 0;
                    if ($aMatch !== $bMatch) return $bMatch <=> $aMatch;

                    // 2. Ưu tiên độ khít chiều cao (Height Fit)
                    $prod_h = (int)$rowType['chieuCao'];
                    $aDiffH = (int)$a['caoToiDa'] - $prod_h;
                    $bDiffH = (int)$b['caoToiDa'] - $prod_h;

                    // Nếu một ô chứa vừa và ô kia không thì ưu tiên ô chứa vừa
                    if ($aDiffH >= 0 && $bDiffH < 0) return -1;
                    if ($aDiffH < 0 && $bDiffH >= 0) return 1;

                    // Nếu cả hai đều chứa vừa, ưu tiên ô có độ khít nhỏ hơn (ít lãng phí hơn)
                    if ($aDiffH >= 0 && $bDiffH >= 0 && $aDiffH !== $bDiffH) {
                        return $aDiffH <=> $bDiffH;
                    }

                    // 3. Ưu tiên ô ĐANG CÓ hàng cùng loại (Gom hàng)
                    $stmt = $this->conn->prepare("SELECT SUM(soLuong) FROM lo_hang_vi_tri lvt JOIN lohang lh ON lvt.maLo = lh.maLo WHERE lvt.maViTri = ? AND lh.maHH = ?");
                    $stmt->execute([$a['maViTri'], $item['maHH']]);
                    $cntA = (int)$stmt->fetchColumn();
                    $stmt->execute([$b['maViTri'], $item['maHH']]);
                    $cntB = (int)$stmt->fetchColumn();
                    if ($cntA !== $cntB) return $cntB <=> $cntA;

                    // 4. Thứ tự vị trí mặc định
                    return strnatcmp($a['maViTri'], $b['maViTri']);
                };

                // Sắp xếp toàn bộ danh sách vị trí theo hàm so sánh trên
                usort($vitriList, $fnSort);
                $orderedVitri = $vitriList;

                // Tiến hành xếp hàng vào danh sách vị trí đã sắp xếp
                foreach ($orderedVitri as $vitri) {
                    if ($soLuongConLai <= 0) break;

                    // Tính toán sức chứa thực tế dựa trên kích thước 3D
                    $maxCanStore = $this->calculateAvailableCapacity($vitri, $rowType);
                    if ($maxCanStore <= 0) continue;

                    $toStore = min($soLuongConLai, $maxCanStore);

                    // Insert lo_hang_vi_tri
                    $stmtTon = $this->conn->prepare("INSERT INTO lo_hang_vi_tri (maLo, maViTri, soLuong) VALUES (?, ?, ?)");
                    $stmtTon->execute([$maLo, $vitri['maViTri'], $toStore]);

                    // Xử lý Serial nếu có
                    if ($rowType['loaiHang'] === 'SERIAL' && !empty($item['serials'])) {
                        $stmtSerial = $this->conn->prepare("INSERT INTO hanghoa_serial (serial, maLo, trangThai, maViTri) VALUES (?, ?, 1, ?)");
                        $startIndex = $item['soLuong'] - $soLuongConLai;
                        $serialsToInsert = array_slice($item['serials'], $startIndex, $toStore);
                        foreach ($serialsToInsert as $s) {
                            $stmtSerial->execute([$s, $maLo, $vitri['maViTri']]);
                        }
                    }
                    $soLuongConLai -= $toStore;
                }

                if ($soLuongConLai > 0) throw new Exception("Kho không đủ chỗ cho: " . $rowType['tenHH']);

                // Lưu chi tiết phiếu nhập
                $stmtCT = $this->conn->prepare("INSERT INTO ct_phieunhap (maPN, maHH, soLuong, donGia) VALUES (?, ?, ?, ?)");
                $stmtCT->execute([$maPN, $item['maHH'], $item['soLuong'], $item['donGia']]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Hàm bổ trợ tính sức chứa dựa trên 3D và xếp chồng
    public function calculateAvailableCapacity($vitri, $hang, $maxTestCount = 200)
    {
        $canAddCount = 0;
        $maViTri = $vitri['maViTri'];

        // 1. Kiểm tra dãy A, B để cấm xếp chồng (No Stacking Zone)
        $isNoStackZone = (strpos($maViTri, 'A') === 0 || strpos($maViTri, 'B') === 0);

        // 2. Lấy danh sách hàng và quy tắc xoay của chúng
        $sql = "SELECT lvt.soLuong, h.tenHH, h.chieuDai, h.chieuRong, h.chieuCao, h.quyTacXoay
                FROM lo_hang_vi_tri lvt 
                JOIN lohang l ON lvt.maLo = l.maLo 
                JOIN hanghoa h ON l.maHH = h.maHH 
                WHERE lvt.maViTri = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maViTri]);
        $existingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $low = 1;
        $high = (int)$maxTestCount;
        $best = 0;

        while ($low <= $high) {
            $mid = intdiv($low + $high, 2);
            $packer = new Packer();

            // If stacking is forbidden in this zone, limit the effective box height to the product height
            $effectiveHeight = (int)$vitri['caoToiDa'];
            if ($isNoStackZone) {
                // Use the tested product height so the packer cannot place items above it
                $effectiveHeight = isset($hang['chieuCao']) ? (int)$hang['chieuCao'] : $effectiveHeight;
            }

            $packer->addBox(new WarehouseBin(
                $maViTri,
                (int)$vitri['daiToiDa'],
                (int)$vitri['rongToiDa'],
                $effectiveHeight,
                0,
                (int)$vitri['daiToiDa'],
                (int)$vitri['rongToiDa'],
                $effectiveHeight,
                1000000
            ));

            // 3. Nạp hàng cũ với quy tắc xoay từ DB
            foreach ($existingItems as $ei) {
                // Quy tắc xoay: 'xoay ngang' => keepFlat true. Một số mặt hàng (ví dụ Tivi) cần ép luôn đứng thẳng.
                $keepFlat = (isset($ei['quyTacXoay']) && $ei['quyTacXoay'] === 'xoay ngang');
                // Force keepFlat for TVs (product name contains 'tivi') to avoid accidental tilt
                if (!empty($ei['tenHH']) && stripos($ei['tenHH'], 'tivi') !== false) {
                    $keepFlat = true;
                }
                $keepFlat = (bool)$keepFlat;

                // Nếu vị trí thuộc dãy A/B thì cấm xếp chồng
                $canStack = !$isNoStackZone;
                $canStack = (bool)$canStack;

                for ($i = 0; $i < $ei['soLuong']; $i++) {
                    $packer->addItem(new WarehouseItem(
                        $ei['tenHH'],
                        (int)$ei['chieuDai'],
                        (int)$ei['chieuRong'],
                        (int)$ei['chieuCao'],
                        0,
                        $keepFlat,
                        $canStack
                    ));
                }
            }

            // 4. Nạp hàng mới đang thử nghiệm
            $keepFlatNew = (isset($hang['quyTacXoay']) && $hang['quyTacXoay'] === 'xoay ngang');
            if (!empty($hang['tenHH']) && stripos($hang['tenHH'], 'tivi') !== false) {
                $keepFlatNew = true;
            }
            $keepFlatNew = (bool)$keepFlatNew;
            $canStackNew = (bool)!$isNoStackZone;

            for ($j = 0; $j < $mid; $j++) {
                $packer->addItem(new WarehouseItem(
                    $hang['tenHH'] ?? 'Item',
                    (int)$hang['chieuDai'],
                    (int)$hang['chieuRong'],
                    (int)$hang['chieuCao'],
                    0,
                    $keepFlatNew,
                    $canStackNew
                ));
            }

            try {
                $packedBoxes = $packer->pack();
                if ($packedBoxes->count() === 1) {
                    $best = $mid;
                    $low = $mid + 1;
                } else {
                    $high = $mid - 1;
                }
            } catch (\Exception $e) {
                $high = $mid - 1;
            }
        }
        return $best;
    }
}
