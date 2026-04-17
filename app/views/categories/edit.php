<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<?php require_once APP_ROOT . '/views/layouts/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Cập nhật danh mục</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="<?php echo BASE_URL; ?>/category/update">
                    <div class="mb-3">
                        <label class="form-label">Danh mục cha (tùy chọn)</label>
                        <select name="maDanhMucCha" class="form-control">
                            <option value="">-- Không có --</option>
                            <?php if (!empty($parents)): ?>
                                <?php foreach ($parents as $p): ?>
                                    <option value="<?php echo $p['maDanhMuc']; ?>" <?php echo (isset($category['maDanhMucCha']) && $category['maDanhMucCha'] == $p['maDanhMuc']) ? 'selected' : ''; ?>><?php echo $p['tenDanhMuc']; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                <div class="mb-3">
                    <label class="form-label">Mã danh mục</label>
                    <input type="text" name="maDanhMuc" class="form-control" value="<?php echo $category['maDanhMuc']; ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tên danh mục</label>
                    <input type="text" name="tenDanhMuc" class="form-control" value="<?php echo $category['tenDanhMuc']; ?>" required>
                </div>

                <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Lưu</button>
                <a href="<?php echo BASE_URL; ?>/category" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</main>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
