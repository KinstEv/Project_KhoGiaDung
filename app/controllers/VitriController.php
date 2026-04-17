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
}
