<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<?php require_once APP_ROOT . '/views/layouts/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản lý Sản phẩm</h1>
        <a href="<?php echo BASE_URL; ?>/product/create" class="btn btn-primary">
            + Thêm mới
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã Hàng</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>ĐVT</th>
                            <th class="text-center">Tồn kho</th>
                            <th class="text-center">Hạn bảo hành</th>
                            <th class="text-center">Kích thước (DxRxC)</th>
                            <th class="text-center">Quy tắc xoay</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['products'])): ?>
                            <?php foreach ($data['products'] as $item): ?>
                            <tr>
                                <td><?php echo $item['maHH']; ?></td>
                                <td class="fw-bold"><?php echo $item['tenHH']; ?></td>
                                <td><?php echo $item['tenDanhMuc']; ?></td>
                                <td><?php echo $item['tenDVT']; ?></td>
                               <td class="text-center">
                                    <?php if($item['tongTon'] == 0): ?>
                                        <span class="badge bg-danger"> (Hết hàng)</span>
                                    
                                    <?php elseif($item['tongTon'] <= 10): ?>
                                        <span class="badge bg-warning text-dark">
                                            <?php echo number_format($item['tongTon']); ?> (Sắp hết)
                                        </span>
                                    
                                    <?php else: ?>
                                        <span class="badge bg-success">
                                            <?php echo number_format($item['tongTon']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo isset($item['thoiGianBaoHanh']) ? $item['thoiGianBaoHanh'] . ' tháng' : '-'; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo (isset($item['chieuDai']) || isset($item['chieuRong']) || isset($item['chieuCao'])) ? ($item['chieuDai'] . 'x' . $item['chieuRong'] . 'x' . $item['chieuCao']) : '-'; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo isset($item['quyTacXoay']) ? $item['quyTacXoay'] : '-'; ?>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/product/edit/<?php echo $item['maHH']; ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                                    <a href="<?php echo BASE_URL; ?>/product/delete/<?php echo $item['maHH']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">Xóa</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Chưa có hàng hóa nào. Hãy thêm mới!
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