<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<?php require_once APP_ROOT . '/views/layouts/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Danh mục</h1>
        <a href="<?php echo BASE_URL; ?>/category/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm danh mục
        </a>
    </div>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'has_products'): ?>
        <div class="alert alert-danger">Không thể xóa: danh mục đang có hàng hóa liên kết.</div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã danh mục</th>
                            <th>Tên danh mục</th>
                            <th>Danh mục cha</th>
                            <th>Số sản phẩm</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td class="fw-bold text-primary"><?php echo $cat['maDanhMuc']; ?></td>
                                    <td><?php echo $cat['tenDanhMuc']; ?></td>
                                    <td><?php echo isset($cat['tenDanhMucCha']) ? $cat['tenDanhMucCha'] : '-'; ?></td>
                                    <td>
                                        <?php echo $cat['soLuong']; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/category/edit/<?php echo $cat['maDanhMuc']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>

                                        <a href="<?php echo BASE_URL; ?>/category/delete/<?php echo $cat['maDanhMuc']; ?>" class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này? Nếu danh mục đang có hàng hóa, hệ thống sẽ không cho phép xóa.');">
                                           <i class="bi bi-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted">Chưa có dữ liệu.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
