<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<?php require_once APP_ROOT . '/views/layouts/sidebar.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Cập nhật Hàng Hóa</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin hàng hóa</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/product/update" method="POST">
                <input type="hidden" name="maHH" value="<?php echo htmlspecialchars($data['product']['maHH']); ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Mã Hàng Hóa</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($data['product']['maHH']); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Tên Hàng Hóa (*)</label>
                            <input type="text" name="tenHH" class="form-control" value="<?php echo htmlspecialchars($data['product']['tenHH']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thương hiệu</label>
                            <input type="text" name="thuongHieu" class="form-control" value="<?php echo htmlspecialchars($data['product']['thuongHieu'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Model</label>
                            <input type="text" name="model" class="form-control" value="<?php echo htmlspecialchars($data['product']['model'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Loại quản lý hàng (*)</label>
                            <select name="loaiHang" class="form-control" required>
                                <option value="LO" <?php echo ($data['product']['loaiHang'] === 'LO') ? 'selected' : ''; ?>>Quản lý theo LÔ</option>
                                <option value="SERIAL" <?php echo ($data['product']['loaiHang'] === 'SERIAL') ? 'selected' : ''; ?>>Quản lý theo SERIAL</option>
                            </select>
                        </div>
                        
                        <!-- heSoChiemCho removed: system now uses physical dimensions chieuDai/chieuRong/chieuCao -->
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Danh Mục (*)</label>
                            <select name="maDanhMuc" class="form-control" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($data['categories'] as $cat): ?>
                                    <option value="<?php echo $cat['maDanhMuc']; ?>" <?php echo ($cat['maDanhMuc'] == $data['product']['maDanhMuc']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['tenDanhMuc']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                         <div class="mb-3">
                            <label class="form-label">Thời gian Bảo hành (tháng)</label>
                            <input type="number" name="thoiGianBaoHanh" class="form-control" value="<?php echo htmlspecialchars($data['product']['thoiGianBaoHanh'] ?? 12); ?>" min="0" step="1">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Đơn Vị Tính (*)</label>
                            <select name="maDVT" class="form-control" required>
                                <option value="">-- Chọn ĐVT --</option>
                                <?php foreach ($data['units'] as $unit): ?>
                                    <option value="<?php echo $unit['maDVT']; ?>" <?php echo ($unit['maDVT'] == $data['product']['maDVT']) ? 'selected' : ''; ?>>
                                        <?php echo $unit['tenDVT']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kích thước (Dài x Rộng x Cao) (cm)</label>
                            <div class="d-flex" style="gap:8px;">
                                <input type="number" name="chieuDai" class="form-control" placeholder="Dài" min="0" value="<?php echo htmlspecialchars($data['product']['chieuDai'] ?? 0); ?>">
                                <input type="number" name="chieuRong" class="form-control" placeholder="Rộng" min="0" value="<?php echo htmlspecialchars($data['product']['chieuRong'] ?? 0); ?>">
                                <input type="number" name="chieuCao" class="form-control" placeholder="Cao" min="0" value="<?php echo htmlspecialchars($data['product']['chieuCao'] ?? 0); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quy tắc xoay</label>
                            <select name="quyTacXoay" class="form-control">
                                <option value="XOAY_NGANG" <?php echo (($data['product']['quyTacXoay'] ?? '') === 'XOAY_NGANG') ? 'selected' : ''; ?>>Xoay ngang (chỉ hoán đổi Dài/Rộng)</option>
                                <option value="TU_DO" <?php echo (($data['product']['quyTacXoay'] ?? '') === 'TU_DO') ? 'selected' : ''; ?>>Tự do (xoay tự do)</option>
                            </select>
                        </div>

                        <!-- Supplier field removed because hanghoa.maNCC was deleted -->
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Mô tả chi tiết</label>
                            <textarea name="moTa" class="form-control" rows="3"><?php echo htmlspecialchars($data['product']['moTa'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <hr>
                
                <div class="d-flex justify-content-end">
                    <a href="<?php echo BASE_URL; ?>/product" class="btn btn-secondary me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
