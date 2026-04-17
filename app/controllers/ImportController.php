<?php
class ImportController extends Controller {
    private $importModel;

    public function __construct() {
        $this->requireLogin();
        // Cần quyền nhập kho
        $this->requirePermission('Q_NHAP_KHO');
        $this->importModel = $this->model('ImportModel');
    }

    
    public function index() {
        // Lấy danh sách phiếu nhập
        $imports = $this->importModel->getAllImports();

        $data = [
            'title' => 'Lịch sử Nhập kho',
            'imports' => $imports
        ];

        // Gọi View danh sách
        $this->view('import/index', $data);
    }

//     public function index() {
//     $this->requirePermission('Q_NHAP_KHO');
//     $keyword = $_GET['keyword'] ?? null; // Lấy từ khóa từ URL
//     $imports = $this->importModel->getAll($keyword); // Truyền vào model
    
//     $data = [
//         'title' => 'Danh sách Phiếu Nhập',
//         'imports' => $imports
//     ];
//     $this->view('import/index', $data);
// }

    // Hiển thị form tạo phiếu
    public function create() {
        $data = [
            'title' => 'Tạo Phiếu Nhập Kho',
            'suppliers' => $this->importModel->getSuppliers(),
            'products' => $this->importModel->getProducts()
        ];
        $this->view('import/create', $data);
    }

    // Xử lý lưu dữ liệu (Hàm quan trọng nhất của quy trình Nhập kho)
    // Quy trình: Nhận dữ liệu từ Form -> Chuẩn bị cấu trúc -> Gọi Model xử lý Transaction
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. Chuẩn bị dữ liệu Header (Phiếu nhập)
            // Không truyền maPN, để model tự sinh đúng định dạng
            $headerData = [
                'maNCC' => $_POST['maNCC'],
                'ghiChu' => $_POST['ghiChu'],
                'maND' => $_SESSION['user_id'], // Lấy ID người đang đăng nhập (Tự động)
                // Mã đơn đặt hàng (nếu tạo phiếu nhập dựa trên Đơn đặt hàng đã có)
                'maDH' => isset($_POST['maDH']) && $_POST['maDH'] !== '' ? $_POST['maDH'] : null
            ];

            // 2. Chuẩn bị dữ liệu Detail (Danh sách hàng hóa chi tiết)
            // Dữ liệu từ form gửi lên dạng mảng song song: maHH[], soLuong[], donGia[]...
            $products = [];
            $count = count($_POST['product_id']);
            
            // Duyệt qua từng dòng sản phẩm user đã thêm trên giao diện
            for ($i = 0; $i < $count; $i++) {
                // Chỉ lấy dòng nào có chọn sản phẩm (tránh dòng rỗng)
                if (!empty($_POST['product_id'][$i])) {
                    
                    // Xử lý Serial Number (Nếu có)
                    // Serial được gửi lên dưới dạng chuỗi dài, phân tách bởi dòng mới hoặc dấu phẩy
                    $serialsRaw = $_POST['serials'][$i] ?? '';
                    $serials = [];
                    if (!empty(trim($serialsRaw))) {
                        // Tách chuỗi thành mảng các số serial
                        $lines = preg_split('/\r\n|\r|\n|,/', $serialsRaw);
                        foreach ($lines as $ln) {
                            $s = trim($ln);
                            if ($s !== '') $serials[] = $s;
                        }
                    }

                    // Logic tính số lượng:
                    // - Nếu có nhập Serial: Số lượng = Đếm số serial đã nhập
                    // - Nếu là hàng Lô (không serial): Lấy từ ô input số lượng
                    $qty = 0;
                    if (!empty($serials)) {
                        $qty = count($serials);
                    } else {
                        $qty = (int)($_POST['quantity'][$i] ?? 0);
                    }

                    // Gom dữ liệu vào mảng chuẩn để gửi sang Model
                    $products[] = [
                        'maHH' => $_POST['product_id'][$i],
                        'soLuong' => $qty,
                        'donGia' => $_POST['price'][$i], // Giá nhập
                        'hsd' => $_POST['expiry'][$i] ?? null, // Hạn sử dụng (quan trọng cho thực phẩm/mỹ phẩm)
                        'serials' => $serials // Mảng serial (nếu có)
                    ];
                }
            }

            // Validate cơ bản: Phải có ít nhất 1 sản phẩm mới cho lưu
            if (empty($products)) {
                die("Vui lòng chọn ít nhất 1 sản phẩm!");
            }

            // 3. Gọi Model xử lý Transaction (Tạo phiếu + Tạo lô + Cập nhật tồn kho)
            try {
                // Hàm này sẽ đảm bảo tính toàn vẹn dữ liệu (ACID)
                $this->importModel->createImportTransaction($headerData, $products);
                
                // Thành công -> Chuyển hướng người dùng về trang Tồn kho để kiểm tra kết quả ngay
                header('Location: ' . BASE_URL . '/inventory'); 
            } catch (Exception $e) {
                // NẾU CÓ LỖI (VÍ DỤ: KHO ĐẦY) -> Hiển thị lại form kèm thông báo lỗi
                // Load lại danh sách NCC và SP để form không bị lỗi data
                $data = [
                    'title' => 'Tạo Phiếu Nhập Kho',
                    'suppliers' => $this->importModel->getSuppliers(),
                    'products' => $this->importModel->getProducts(),
                    'error' => $e->getMessage() // Truyền nội dung lỗi sang View
                ];
                $this->view('import/create', $data);
            }
        }
    }

    // Hiển thị chi tiết Phiếu nhập
    public function show($maPN) {
        $data = $this->importModel->getImportById($maPN);
        if (!$data) {
            die('Không tìm thấy phiếu nhập: ' . htmlspecialchars($maPN));
        }
        $viewData = [
            'title' => 'Chi tiết Phiếu Nhập ' . $maPN,
            'import' => $data['header'],
            'lines' => $data['lines']
        ];
        $this->view('import/show', $viewData);
    }
}
?>