<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<?php require_once APP_ROOT . '/views/layouts/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tra cứu Tồn kho</h1>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Nhập tên hàng hoặc mã lô để tìm kiếm...">
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Mã Lô</th>
                            <th>Vị trí (Dãy-Kệ-Ô)</th>
                            <th>Dung tích vị trí</th>
                            <th>Hạn bảo hành (Lô)</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['stocks'])): ?>
                            <?php foreach ($data['stocks'] as $item): ?>
                                <tr>
                                <td>
                                    <span class="fw-bold"><?php echo $item['tenHH']; ?></span><br>
                                    <small class="text-muted"><?php echo $item['maHH']; ?></small>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo $item['maLo']; ?></span></td>
                                <td>
                                    <?php 
                                        // Nếu chưa gán vị trí thì báo chưa có
                                        echo !empty($item['viTriCuThe']) ? $item['viTriCuThe'] : '<span class="text-muted fst-italic">Chưa xếp kệ</span>'; 
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        // Hiển thị dung tích vị trí theo kích thước (D x R x C)
                                        if (!empty($item['daiToiDa']) || !empty($item['rongToiDa']) || !empty($item['caoToiDa'])) {
                                            echo sprintf('%sx%sx%s', $item['daiToiDa'], $item['rongToiDa'], $item['caoToiDa']);
                                        } else {
                                            echo '<span class="text-muted">-</span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        // Format ngày tháng cho đẹp (hạn bảo hành của lô)
                                        echo $item['hanBaoHanh'] ? date('d/m/Y', strtotime($item['hanBaoHanh'])) : 'Không BH'; 
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                        // Tính phần trăm lấp đầy sơ bộ dựa trên tổng số lượng hiện có tại vị trí so với dung tích (diện tích hoặc thể tích)
                                        $percent = 0;
                                        $totalHere = isset($item['totalAtPosition']) ? (float)$item['totalAtPosition'] : 0;
                                        $d = isset($item['daiToiDa']) ? (float)$item['daiToiDa'] : 0;
                                        $r = isset($item['rongToiDa']) ? (float)$item['rongToiDa'] : 0;
                                        $c = isset($item['caoToiDa']) ? (float)$item['caoToiDa'] : 0;
                                        if (!empty($item['choPhepXepChong'])) {
                                            $cap = $d * $r * $c; // thể tích
                                        } else {
                                            $cap = $d * $r; // diện tích sàn
                                        }
                                        if ($cap > 0) {
                                            $percent = (int) min(100, round(($totalHere / $cap) * 100));
                                        }

                                        // Màu theo ngưỡng giống vitri view
                                        $badgeClass = 'bg-secondary';
                                        if ($percent == 0) $badgeClass = 'bg-primary';
                                        elseif ($percent <= 60) $badgeClass = 'bg-success';
                                        elseif ($percent <= 90) $badgeClass = 'bg-warning text-dark';
                                        else $badgeClass = 'bg-danger';

                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $percent; ?>%</span>
                                </td>
                                <td class="text-center fs-5">
                                    <strong><?php echo number_format($item['soLuongTon']); ?></strong>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Kho đang trống!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.querySelector('tbody');
        const rows = tableBody.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                // Bỏ qua dòng thông báo "Kho đang trống" nếu có
                if (row.cells.length <= 1) continue;

                const productCell = row.cells[0]; // Cột Sản phẩm
                const lotCell = row.cells[1];     // Cột Mã Lô

                if (productCell && lotCell) {
                    const productText = productCell.textContent || productCell.innerText;
                    const lotText = lotCell.textContent || lotCell.innerText;

                    if (productText.toLowerCase().indexOf(filter) > -1 || lotText.toLowerCase().indexOf(filter) > -1) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            }
        });
    });
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>