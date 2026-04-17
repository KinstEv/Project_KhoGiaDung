<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<?php require_once APP_ROOT . '/views/layouts/sidebar.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4">Thêm Vị trí mới</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/vitri/store" method="POST">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Dãy (day)</label>
                            <input type="text" name="day" class="form-control" required placeholder="Ví dụ: A">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Kệ (ke)</label>
                            <input type="text" name="ke" class="form-control" required placeholder="Ví dụ: 1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Ô (o)</label>
                            <input type="text" name="o" class="form-control" required placeholder="Ví dụ: 01">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Kích thước vị trí (Dài x Rộng x Cao)</label>
                            <div class="input-group">
                                <input type="number" name="daiToiDa" class="form-control" placeholder="Dài" value="0" min="0" step="1">
                                <input type="number" name="rongToiDa" class="form-control" placeholder="Rộng" value="0" min="0" step="1">
                                <input type="number" name="caoToiDa" class="form-control" placeholder="Cao" value="0" min="0" step="1">
                            </div>
                            <small class="text-muted">Đơn vị theo DB (ví dụ mm). Nhập 0 nếu không muốn dùng kích thước.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Cho phép xếp chồng</label>
                            <select name="choPhepXepChong" class="form-control">
                                <option value="0">Không</option>
                                <option value="1">Có</option>
                            </select>
                        </div>
                    </div>
                    <!-- trangThai input removed: trạng thái giờ là động (Trống/Đầy) tính từ mức chiếm -->
                </div>

                <div class="d-flex justify-content-end">
                    <a href="<?php echo BASE_URL; ?>/vitri" class="btn btn-secondary me-2">Hủy</a>
                    <button class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
