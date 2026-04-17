<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<?php require_once APP_ROOT . '/views/layouts/sidebar.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Thêm Mới Sản Phẩm</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin hàng hóa</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/product/store" method="POST">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Mã Hàng Hóa (*)</label>
                            <input type="text" name="maHH" class="form-control" placeholder="Ví dụ: HH001" required>
                            <small class="text-muted">Mã hàng không được trùng nhau.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Tên Hàng Hóa (*)</label>
                            <input type="text" name="tenHH" class="form-control" placeholder="Nhập tên sản phẩm..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thương hiệu</label>
                            <input type="text" name="thuongHieu" class="form-control" placeholder="Ví dụ: Sunhouse, Sony...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Model</label>
                            <input type="text" name="model" class="form-control" placeholder="Ví dụ: SHD-1234">
                        </div>
                        
                        <!-- heSoChiemCho removed: system now uses physical dimensions chieuDai/chieuRong/chieuCao -->
                        
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Loại quản lý hàng (*)</label>
                            <select name="loaiHang" class="form-control" required>
                                <option value="LO">Quản lý theo LÔ</option>
                                <option value="SERIAL">Quản lý theo SERIAL</option>
                            </select>
                            <small class="text-muted">Chọn cách theo dõi mặt hàng trong kho (lô hoặc serial).</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Danh Mục (*)</label>
                            <select name="maDanhMuc" class="form-control" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($data['categories'] as $cat): ?>
                                    <option value="<?php echo $cat['maDanhMuc']; ?>">
                                        <?php echo $cat['tenDanhMuc']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Thời gian Bảo hành (tháng)</label>
                            <input type="number" name="thoiGianBaoHanh" class="form-control" value="12" min="0" step="1">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Đơn Vị Tính (*)</label>
                            <select name="maDVT" class="form-control" required>
                                <option value="">-- Chọn ĐVT --</option>
                                <?php foreach ($data['units'] as $unit): ?>
                                    <option value="<?php echo $unit['maDVT']; ?>">
                                        <?php echo $unit['tenDVT']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kích thước (Dài x Rộng x Cao) (đơn vị: cm)</label>
                            <div class="d-flex" style="gap:8px;">
                                <input type="number" name="chieuDai" class="form-control" placeholder="Dài" min="0">
                                <input type="number" name="chieuRong" class="form-control" placeholder="Rộng" min="0">
                                <input type="number" name="chieuCao" class="form-control" placeholder="Cao" min="0">
                            </div>
                            <small class="text-muted">Dùng để tính diện tích sàn và chiều cao khi phân bổ vào vị trí kho.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quy tắc xoay (Rotation)</label>
                            <select name="quyTacXoay" class="form-control">
                                <option value="XOAY_NGANG">Xoay ngang (chỉ hoán đổi Dài/Rộng)</option>
                                <option value="TU_DO">Tự do (xoay tự do 6 hướng)</option>
                            </select>
                            <small class="text-muted">Quy tắc này có thể được dùng để tối ưu khi xếp hàng (hiện tại hệ thống sử dụng heuristics).</small>
                        </div>

                        <!-- Supplier field removed because hanghoa.maNCC was deleted -->
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Mô tả chi tiết</label>
                            <textarea name="moTa" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <hr>
                
                <div class="d-flex justify-content-end">
                    <a href="<?php echo BASE_URL; ?>/product" class="btn btn-secondary me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary">Lưu Sản Phẩm</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>