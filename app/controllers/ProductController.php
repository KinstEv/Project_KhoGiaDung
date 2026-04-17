<?php
class ProductController extends Controller {
    private $productModel;

    public function __construct() {
        $this->requireLogin();
        $this->productModel = $this->model('ProductModel');
    }

    // Trang danh sách sản phẩm
    public function index() {
        // Cần quyền Xem hàng hoặc Quản lý hàng
        if (!checkPermission('Q_XEM_HANG') && !checkPermission('Q_QL_HANG')) {
             // Gọi requirePermission với 1 quyền để kích hoạt thông báo lỗi
             $this->requirePermission('Q_XEM_HANG');
        }

        $products = $this->productModel->getAll();
        
        $data = [
            'title' => 'Danh sách Hàng hóa',
            'products' => $products
        ];
        
        $this->view('products/index', $data);
    }

    public function create() {
        // Chỉ quản lý hàng được thêm
        $this->requirePermission('Q_QL_HANG');

        // 1. Lấy dữ liệu phụ trợ cho các ô Select box
        // Lưu ý: Bạn cần viết thêm các hàm này trong ProductModel hoặc tạo CategoryModel/UnitModel riêng
        // Ở đây mình ví dụ gọi trực tiếp query đơn giản hoặc giả định Model đã có hàm
        $db = Database::getInstance()->getConnection();

    // Lấy danh mục
    $stmt = $db->query("SELECT * FROM danhmuc ORDER BY tenDanhMuc ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy đơn vị tính
        $stmt = $db->query("SELECT * FROM DONVITINH");
        $units = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Thêm hàng hóa mới',
            'categories' => $categories,
            'units' => $units
        ];

        $this->view('products/create', $data);
    }

    public function edit($id) {
        $this->requirePermission('Q_QL_HANG');
        $db = Database::getInstance()->getConnection();

    // Lấy danh mục
    $stmt = $db->query("SELECT * FROM danhmuc ORDER BY tenDanhMuc ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy đơn vị tính
        $stmt = $db->query("SELECT * FROM DONVITINH");
        $units = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $product = $this->productModel->find($id);
        if (!$product) { die('Không tìm thấy sản phẩm'); }

        $data = [
            'title' => 'Cập nhật Hàng hóa',
            'product' => $product,
            'categories' => $categories,
            'units' => $units
        ];

        $this->view('products/edit', $data);
    }

    public function update() {
        $this->requirePermission('Q_QL_HANG');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['maHH'];
            $data = [
                'tenHH' => $_POST['tenHH'],
                'loaiHang' => $_POST['loaiHang'] ?? 'LO',
                'maDanhMuc' => $_POST['maDanhMuc'],
                // supplier removed: maNCC
                'maDVT' => $_POST['maDVT'],
                'model' => $_POST['model'],
                'thuongHieu' => $_POST['thuongHieu'],
                'moTa' => $_POST['moTa'],
                'thoiGianBaoHanh' => $_POST['thoiGianBaoHanh'],
                'chieuDai' => isset($_POST['chieuDai']) ? (int)$_POST['chieuDai'] : 0,
                'chieuRong' => isset($_POST['chieuRong']) ? (int)$_POST['chieuRong'] : 0,
                'chieuCao' => isset($_POST['chieuCao']) ? (int)$_POST['chieuCao'] : 0,
                'quyTacXoay' => isset($_POST['quyTacXoay']) ? $_POST['quyTacXoay'] : 'XOAY_NGANG'
            ];

            if ($this->productModel->update($id, $data)) {
                header('Location: ' . BASE_URL . '/product');
            } else {
                die('Lỗi cập nhật sản phẩm');
            }
        }
    }

    public function delete($id) {
        $this->requirePermission('Q_QL_HANG');
        // Try to delete product; model will prevent deleting if there are lots
        if ($this->productModel->delete($id)) {
            header('Location: ' . BASE_URL . '/product');
        } else {
            // If delete failed due to dependencies, redirect with error
            header('Location: ' . BASE_URL . '/product?error=has_lots');
        }
    }

    // API: trả về danh sách lô cho 1 sản phẩm (dùng AJAX)
    public function lots() {
        // lấy maHH từ query string hoặc param
    $maHH = isset($_GET['maHH']) ? $_GET['maHH'] : null;
        header('Content-Type: application/json');
        if (!$maHH) {
            echo json_encode(['success' => false, 'message' => 'Thiếu mã hàng']);
            return;
        }

        $lots = $this->productModel->getLots($maHH);
        echo json_encode(['success' => true, 'lots' => $lots]);
    }

    // API: trả về vị trí cho 1 lô (maLo)
    public function locations() {
        $maLo = isset($_GET['maLo']) ? $_GET['maLo'] : null;
        header('Content-Type: application/json');
        if (!$maLo) {
            echo json_encode(['success' => false, 'message' => 'Thiếu mã lô']);
            return;
        }
        $locs = $this->productModel->getLocationsByLo($maLo);
        echo json_encode(['success' => true, 'locations' => $locs]);
    }

    // API: trả về serials khả dụng cho một sản phẩm
    public function serials() {
        $maHH = isset($_GET['maHH']) ? $_GET['maHH'] : null;
        header('Content-Type: application/json');
        if (!$maHH) {
            echo json_encode(['success' => false, 'message' => 'Thiếu mã hàng']);
            return;
        }
        $serials = $this->productModel->getAvailableSerials($maHH);
        echo json_encode(['success' => true, 'serials' => $serials]);
    }

    // Xử lý lưu sản phẩm vào CSDL
    public function store() {
        $this->requirePermission('Q_QL_HANG');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy dữ liệu từ form
            $data = [
                'maHH' => $_POST['maHH'],
                'tenHH' => $_POST['tenHH'],
                'loaiHang' => $_POST['loaiHang'],
                'maDanhMuc' => $_POST['maDanhMuc'],
                'maDVT' => $_POST['maDVT'],
                'model' => $_POST['model'],
                'thuongHieu' => $_POST['thuongHieu'],
                'moTa' => $_POST['moTa'],
                'thoiGianBaoHanh' => $_POST['thoiGianBaoHanh'],
                'chieuDai' => isset($_POST['chieuDai']) ? (int)$_POST['chieuDai'] : 0,
                'chieuRong' => isset($_POST['chieuRong']) ? (int)$_POST['chieuRong'] : 0,
                'chieuCao' => isset($_POST['chieuCao']) ? (int)$_POST['chieuCao'] : 0,
                'quyTacXoay' => isset($_POST['quyTacXoay']) ? $_POST['quyTacXoay'] : 'XOAY_NGANG'
            ];

            // Gọi Model để insert
            if ($this->productModel->create($data)) {
                // Thành công -> Về trang danh sách
                header('Location: ' . BASE_URL . '/product');
            } else {
                // Thất bại -> Báo lỗi (Tạm thời die ra màn hình)
                die("Lỗi: Mã hàng đã tồn tại hoặc dữ liệu không hợp lệ.");
            }
        }
    }
}
?>