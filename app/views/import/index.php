<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<?php require_once APP_ROOT . '/views/layouts/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản lý Nhập Kho</h1>
        <a href="<?php echo BASE_URL; ?>/import/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tạo phiếu nhập mới
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <!-- Tìm kiếm
                <form method="GET" action="" class="row g-3 mb-3">
                    <div class="col-auto">
                        <input type="text" name="keyword" class="form-control" placeholder="Nhập mã phiếu hoặc tên NCC..." value="<?php echo $_GET['keyword'] ?? ''; ?>">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    </div>
                </form> -->
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã Phiếu</th>
                            <th>Ngày Nhập</th>
                            <th>Nhà Cung Cấp</th>
                            <th>Nhân viên thực hiện</th>
                            <th>Ghi Chú</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['imports'])): ?>
                            <?php foreach ($data['imports'] as $item): ?>
                                <tr>
                                    <td class="fw-bold text-primary"><?php echo $item['maPN']; ?></td>
                                    <td>
                                        <?php echo date('d/m/Y H:i', strtotime($item['ngayNhap'])); ?>
                                    </td>
                                    <td><?php echo $item['tenNCC']; ?></td>
                                    <td><?php echo $item['tenND']; ?></td>
                                    <td><?php echo $item['ghiChu']; ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/import/show/<?php echo urlencode($item['maPN']); ?>" class="btn btn-sm btn-outline-info">Chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Chưa có phiếu nhập nào.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>