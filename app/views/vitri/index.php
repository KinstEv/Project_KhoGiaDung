<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<?php require_once APP_ROOT . '/views/layouts/sidebar.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Quản lý Vị trí</h1>
        <a href="<?php echo BASE_URL; ?>/vitri/create" class="btn btn-primary">+ Thêm vị trí</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light"><tr><th>Code</th><th>Dãy</th><th>Kệ</th><th>Ô</th><th>Kích thước (DxR xC)</th><th>Danh mục ưu tiên</th><th>Cho phép xếp chồng</th><th>Trạng thái</th><th>Hành động</th></thead>
                    <tbody>
                        <?php if (!empty($data['rows'])): ?>
                            <?php foreach ($data['rows'] as $r): ?>
                                <tr>
                                    <td><?php echo $r['maViTri']; ?></td>
                                    <td><?php echo htmlspecialchars($r['day']); ?></td>
                                    <td><?php echo htmlspecialchars($r['ke']); ?></td>
                                    <td><?php echo htmlspecialchars($r['o']); ?></td>
                                    <td>
                                        <?php
                                            $d = isset($r['daiToiDa']) ? intval($r['daiToiDa']) : 0;
                                            $width = isset($r['rongToiDa']) ? intval($r['rongToiDa']) : 0;
                                            $h = isset($r['caoToiDa']) ? intval($r['caoToiDa']) : 0;
                                            echo ($d || $width || $h) ? ($d . 'x' . $width . 'x' . $h) : '<span class="text-muted">Chưa cấu hình</span>';
                                        ?>
                                    </td>
                                    <td><?php echo !empty($r['tenDanhMucUuTien']) ? htmlspecialchars($r['tenDanhMucUuTien']) : (!empty($r['maDanhMucUuTien']) ? htmlspecialchars($r['maDanhMucUuTien']) : '<span class="text-muted">-</span>'); ?></td>
                                    <td>
                                        <?php echo isset($r['choPhepXepChong']) && $r['choPhepXepChong'] ? '<span class="badge bg-info">Có</span>' : '<span class="text-muted">Không</span>'; ?>
                                    </td>
                                    <?php
                                        // Display fill percentage and color according to rules:
                                        // A/B (no stacking): area% ; C/D (stacking): volume%
                                        $pct = isset($r['fillPercent']) ? $r['fillPercent'] : null;
                                        if ($pct === null) {
                                            $statusBadge = '<span class="text-muted">Không xác định</span>';
                                        } else {
                                            $label = '';
                                            $cls = '';
                                            if ($pct === 0) {
                                                $cls = 'bg-primary';
                                                $label = 'Trống';
                                            } elseif ($pct <= 60) {
                                                $cls = 'bg-success';
                                                $label = 'Còn trống nhiều';
                                            } elseif ($pct <= 90) {
                                                $cls = 'bg-warning text-dark';
                                                $label = 'Gần đầy';
                                            } else {
                                                $cls = 'bg-danger';
                                                $label = 'Đầy';
                                            }
                                            $statusBadge = '<span class="badge ' . $cls . '">' . htmlspecialchars($pct) . '% - ' . htmlspecialchars($label) . '</span>';
                                        }
                                    ?>
                                    <td><?php echo $statusBadge; ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/vitri/edit/<?php echo $r['maViTri']; ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                                        <a href="<?php echo BASE_URL; ?>/vitri/delete/<?php echo $r['maViTri']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa vị trí?');">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="9" class="text-center text-muted">Chưa có vị trí nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>
