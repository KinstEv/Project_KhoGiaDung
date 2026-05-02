<?php
class VitriController extends Controller {
    private $vitriModel;

    public function __construct() {
        $this->requireLogin();
        $this->vitriModel = $this->model('VitriModel');
    }

    public function index() {
        if (!checkPermission('Q_XEM_HANG') && !checkPermission('Q_QL_HANG')) {
            $this->requirePermission('Q_XEM_HANG');
        }
        $rows = $this->vitriModel->getAll();
        $data = ['title' => 'Quản lý Vị trí', 'rows' => $rows];
        $this->view('vitri/index', $data);
    }

    public function create() {
        $this->requirePermission('Q_QL_HANG');
        $data = ['title' => 'Thêm Vị trí mới'];
        $this->view('vitri/create', $data);
    }

    public function store() {
        $this->requirePermission('Q_QL_HANG');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $day = trim($_POST['day']);
            $ke = trim($_POST['ke']);
            $o = trim($_POST['o']);
            $ma = strtoupper(preg_replace('/\s+/', '', $day)) . '-' . strtoupper(preg_replace('/\s+/', '', $ke)) . '-' . strtoupper(preg_replace('/\s+/', '', $o));
            if ($this->vitriModel->exists($ma)) {
                die('Vị trí đã tồn tại');
            }
            $data = [
                'day' => $day,
                'ke' => $ke,
                'o' => $o,
                'daiToiDa' => isset($_POST['daiToiDa']) ? (int)$_POST['daiToiDa'] : 0,
                'rongToiDa' => isset($_POST['rongToiDa']) ? (int)$_POST['rongToiDa'] : 0,
                'caoToiDa' => isset($_POST['caoToiDa']) ? (int)$_POST['caoToiDa'] : 0,
                'choPhepXepChong' => isset($_POST['choPhepXepChong']) ? (int)$_POST['choPhepXepChong'] : 0,
                'maDanhMucUuTien' => isset($_POST['maDanhMucUuTien']) && $_POST['maDanhMucUuTien'] !== '' ? $_POST['maDanhMucUuTien'] : null,
                'trangThai' => isset($_POST['trangThai']) ? (int)$_POST['trangThai'] : 1
            ];
            if ($this->vitriModel->create($data)) {
                header('Location: ' . BASE_URL . '/vitri');
            } else {
                die('Lỗi khi tạo vị trí');
            }
        }
    }

    public function edit($id) {
        $this->requirePermission('Q_QL_HANG');
        $row = $this->vitriModel->find($id);
        if (!$row) die('Không tìm thấy vị trí');
        $data = ['title' => 'Sửa Vị trí', 'row' => $row];
        $this->view('vitri/edit', $data);
    }

    public function update() {
        $this->requirePermission('Q_QL_HANG');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['maViTri'];
            $data = [
                'day' => trim($_POST['day']),
                'ke' => trim($_POST['ke']),
                'o' => trim($_POST['o']),
                'daiToiDa' => isset($_POST['daiToiDa']) ? (int)$_POST['daiToiDa'] : 0,
                'rongToiDa' => isset($_POST['rongToiDa']) ? (int)$_POST['rongToiDa'] : 0,
                'caoToiDa' => isset($_POST['caoToiDa']) ? (int)$_POST['caoToiDa'] : 0,
                'choPhepXepChong' => isset($_POST['choPhepXepChong']) ? (int)$_POST['choPhepXepChong'] : 0,
                'maDanhMucUuTien' => isset($_POST['maDanhMucUuTien']) && $_POST['maDanhMucUuTien'] !== '' ? $_POST['maDanhMucUuTien'] : null,
                'trangThai' => isset($_POST['trangThai']) ? (int)$_POST['trangThai'] : 1
            ];
            if ($this->vitriModel->update($id, $data)) {
                header('Location: ' . BASE_URL . '/vitri');
            } else {
                die('Lỗi cập nhật vị trí');
            }
        }
    }

    public function delete($id) {
        $this->requirePermission('Q_QL_HANG');
        if ($this->vitriModel->delete($id)) {
            header('Location: ' . BASE_URL . '/vitri');
        } else {
            die('Lỗi xóa vị trí');
        }
    }

    // JSON endpoint to return vitri dimensions and contained items for 3D view
    public function view3d($id) {
        if (!checkPermission('Q_XEM_HANG') && !checkPermission('Q_QL_HANG')) {
            $this->requirePermission('Q_XEM_HANG');
        }

        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("SELECT maViTri, day, ke, o, daiToiDa, rongToiDa, caoToiDa, choPhepXepChong FROM vitri WHERE maViTri = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $vitri = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$vitri) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Vị trí không tồn tại']);
            exit;
        }

        // Fetch items currently stored at this vitri (with rotation rules)
        $sqlItems = "SELECT lvt.soLuong AS quantity, hh.maHH, hh.tenHH, hh.chieuDai, hh.chieuRong, hh.chieuCao, hh.quyTacXoay
                     FROM lo_hang_vi_tri lvt
                     JOIN lohang lh ON lvt.maLo = lh.maLo
                     JOIN hanghoa hh ON lh.maHH = hh.maHH
                     WHERE lvt.maViTri = ?";
        $stmt2 = $conn->prepare($sqlItems);
        $stmt2->execute([$id]);
        $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Try to run the exact packer server-side and return the packed coordinates so the
        // frontend only needs to draw. We reuse the WarehouseBin/WarehouseItem implementations
        // defined in ImportModel.php (they include the required interface implementations).
        $packedResult = null;
        try {
            // Ensure we have the classes available (ImportModel defines WarehouseBin/WarehouseItem and includes composer autoload)
            require_once __DIR__ . '/../models/ImportModel.php';

            $packer = new \DVDoug\BoxPacker\InfalliblePacker();

            // Determine effective height: if no-stack zone, limit box height to product height
            $isNoStackZone = (strpos($vitri['maViTri'], 'A') === 0 || strpos($vitri['maViTri'], 'B') === 0);
            $effectiveHeight = (int)$vitri['caoToiDa'];
            if ($isNoStackZone && count($items) > 0) {
                // Use the maximum item height present in this vitri (prevents stacking by limiting box height)
                $maxItemH = 0;
                foreach ($items as $it) {
                    $maxItemH = max($maxItemH, (int)($it['chieuCao'] ?? 0));
                }
                if ($maxItemH > 0) $effectiveHeight = $maxItemH;
            }

            // Create box representing the vitri inner dimensions (possibly height-limited)
            $box = new WarehouseBin(
                $vitri['maViTri'],
                (int)$vitri['daiToiDa'], // outerWidth
                (int)$vitri['rongToiDa'], // outerLength
                $effectiveHeight, // outerDepth (effective)
                0,
                (int)$vitri['daiToiDa'], // innerWidth
                (int)$vitri['rongToiDa'], // innerLength
                $effectiveHeight, // innerDepth (effective)
                1000000
            );
            $packer->addBox($box);

            // Zone rules: A/B no stacking
            $isNoStackZone = (strpos($vitri['maViTri'], 'A') === 0 || strpos($vitri['maViTri'], 'B') === 0);

            // Add each stored item individually so the packer returns exact positions
            foreach ($items as $it) {
                $qty = (int)($it['quantity'] ?? 0);
                $keepFlat = (isset($it['quyTacXoay']) && $it['quyTacXoay'] === 'xoay ngang');
                // Force TVs to remain upright
                if (!empty($it['tenHH']) && stripos($it['tenHH'], 'tivi') !== false) {
                    $keepFlat = true;
                }
                $keepFlat = (bool)$keepFlat;

                $canStack = !$isNoStackZone;
                $canStack = (bool)$canStack;

                // Use a descriptive JSON string so PackedItem->jsonSerialize includes maHH/tenHH
                $desc = json_encode(['maHH' => $it['maHH'] ?? '', 'tenHH' => $it['tenHH'] ?? '']);

                $w = (int)($it['chieuDai'] ?? 0);
                $l = (int)($it['chieuRong'] ?? 0);
                $h = (int)($it['chieuCao'] ?? 0);

                for ($i = 0; $i < $qty; $i++) {
                    $packer->addItem(new WarehouseItem(
                        (string)$desc,
                        $w,
                        $l,
                        $h,
                        0,
                        $keepFlat,
                        $canStack
                    ));
                }
            }

            $packedBoxes = $packer->pack();

            // Flatten packed boxes into simple packedData array of coords/sizes
            $packedData = [];
            foreach ($packedBoxes as $packedBox) {
                foreach ($packedBox->getItems() as $pItem) {
                    $packedData[] = [
                        'x' => $pItem->getX(),
                        'y' => $pItem->getY(),
                        'z' => $pItem->getZ(),
                        'w' => $pItem->getWidth(),
                        'l' => $pItem->getLength(),
                        'h' => $pItem->getDepth(),
                        'desc' => method_exists($pItem->getItem(), 'getDescription') ? $pItem->getItem()->getDescription() : null,
                    ];
                }
            }

            $packedResult = $packedData;
        } catch (\Throwable $e) {
            // If packing fails for any reason, fall back to returning raw items and include error info
            $packedResult = null;
            error_log('view3d packer error: ' . $e->getMessage());
        }

        header('Content-Type: application/json');
        echo json_encode(['vitri' => $vitri, 'packed' => $packedResult, 'items' => $items]);
        exit;
    }
}
