<?php
class CategoryController extends Controller {
    private $categoryModel;

    public function __construct() {
        $this->requireLogin();
        // Chỉ Quản lý hàng (hoặc Admin) mới được quản lý danh mục
        $this->requirePermission('Q_QL_HANG');
        $this->categoryModel = $this->model('CategoryModel');
    }

    public function index() {
        $categories = $this->categoryModel->getAll();
        $data = [
            'title' => 'Danh sách Danh mục',
            'categories' => $categories
        ];
        $this->view('categories/index', $data);
    }

    public function create() {
        // Lấy danh sách danh mục hiện có để người dùng có thể chọn danh mục cha (tùy chọn)
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM danhmuc ORDER BY tenDanhMuc ASC");
        $parents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = ['title' => 'Thêm Danh mục', 'parents' => $parents];
        $this->view('categories/create', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'maDanhMuc' => $_POST['maDanhMuc'],
                'tenDanhMuc' => $_POST['tenDanhMuc'],
                'maDanhMucCha' => isset($_POST['maDanhMucCha']) && $_POST['maDanhMucCha'] !== '' ? $_POST['maDanhMucCha'] : null
            ];

            // Kiểm tra trùng mã
            $exists = $this->categoryModel->find($data['maDanhMuc']);
            if ($exists) { die('Mã danh mục đã tồn tại!'); }

            if ($this->categoryModel->create($data)) {
                header('Location: ' . BASE_URL . '/category');
            } else {
                die('Lỗi khi tạo danh mục');
            }
        }
    }

    public function edit($id) {
        $category = $this->categoryModel->find($id);
        if (!$category) { die('Không tìm thấy danh mục'); }
        // Lấy danh sách danh mục để chọn danh mục cha (không bao gồm chính nó)
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM danhmuc WHERE maDanhMuc != :id ORDER BY tenDanhMuc ASC");
        $stmt->execute(['id' => $id]);
        $parents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = ['title' => 'Cập nhật Danh mục', 'category' => $category, 'parents' => $parents];
        $this->view('categories/edit', $data);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['maDanhMuc'];
            $data = [
                'tenDanhMuc' => $_POST['tenDanhMuc'],
                'maDanhMucCha' => isset($_POST['maDanhMucCha']) && $_POST['maDanhMucCha'] !== '' ? $_POST['maDanhMucCha'] : null
            ];
            if ($this->categoryModel->update($id, $data)) {
                header('Location: ' . BASE_URL . '/category');
            } else {
                die('Lỗi cập nhật danh mục');
            }
        }
    }

    public function delete($id) {
        // Nếu có hàng hóa liên kết, không cho xóa
        if ($this->categoryModel->hasProducts($id)) {
            // redirect back with error message
            header('Location: ' . BASE_URL . '/category?error=has_products');
            return;
        }

        if ($this->categoryModel->delete($id)) {
            header('Location: ' . BASE_URL . '/category');
        } else {
            die('Lỗi xóa danh mục');
        }
    }
}
?>
