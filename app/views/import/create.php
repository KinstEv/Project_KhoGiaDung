<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<?php require_once APP_ROOT . '/views/layouts/sidebar.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tạo Phiếu Nhập Kho Mới</h1>

    <!-- Khu vực hiển thị thông báo lỗi từ Controller -->
    <div id="importErrorBox" class="alert alert-danger <?php echo isset($data['error']) ? '' : 'd-none'; ?>" role="alert">
        <?php echo isset($data['error']) ? $data['error'] : ''; ?>
    </div>

    <form action="<?php echo BASE_URL; ?>/import/store" method="POST" id="importForm">
        <input type="hidden" name="maDH" id="selectedOrderId" value="">
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin Nhà Cung Cấp</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="font-weight-bold">Nhà cung cấp (*)</label>
                            <select name="maNCC" id="select-ncc" class="form-select" required style="width: 100%">
                                <option value="">-- Chọn NCC --</option>
                                <?php foreach ($data['suppliers'] as $ncc): ?>
                                    <option value="<?php echo $ncc['maNCC']; ?>">
                                        <?php echo $ncc['tenNCC']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="font-weight-bold">Đơn đặt hàng (tuỳ chọn)</label>
                            <select id="orderSelect" class="form-select" style="width: 100%">
                                <option value="">-- Chọn đơn đặt hàng --</option>
                            </select>
                            <div class="form-text">Chọn NCC trước, sau đó chọn đơn đặt hàng để load chi tiết.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Ghi chú nhập hàng</label>
                            <input type="text" name="ghiChu" class="form-control" placeholder="Ví dụ: Nhập hàng đợt 2...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Chi tiết hàng nhập</h6>
                <button type="button" class="btn btn-success btn-sm" id="addRow">
                    <i class="bi bi-plus-circle"></i> Thêm dòng
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="productTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%">Sản phẩm</th>
                                <th style="width: 10%">Hạn Bảo Hành (Lô)</th>
                                <th style="width: 8%">Số lượng đặt</th>
                                <th style="width: 17%">Số lượng</th>
                                <th style="width: 13%">Đơn giá nhập</th>
                                <th style="width: 20%">Thành tiền</th>
                                <th style="width: 7%">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="flex-grow: 1;">
                                            <select name="product_id[]" class="form-select form-select-sm import-product-select" required style="width: 100%">
                                                <option value="">-- Chọn hàng --</option>
                                                <?php foreach ($data['products'] as $p): ?>
                                                    <!-- Đính kèm data-loai để JS biết hàng này là LO hay SERIAL -->
                                                    <option value="<?php echo $p['maHH']; ?>" data-loai="<?php echo $p['loaiHang']; ?>">
                                                        <?php echo $p['tenHH']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <!-- Badge hiển thị loại hàng (LO hoặc SERIAL) -->
                                        <span class="badge bg-secondary type-badge"></span>
                                    </div>
                                </td>
                                <td>
                                    <input type="date" name="expiry[]" class="form-control form-control-sm" min="<?php echo date('Y-m-d'); ?>">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm ordered-qty" value="0" readonly>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center" style="gap:4px;">
                                        <input type="number" name="quantity[]" class="form-control form-control-sm qty-input" min="1" value="1" required style="max-width:80px;">
                                        
                                        <!-- Nút nhập Serial: Chỉ hiện khi chọn hàng Serial -->
                                        <button type="button" class="btn btn-outline-secondary btn-sm open-serial-modal" title="Nhập serial" style="white-space:nowrap;">Nhập serial</button>
                                    </div>
                                    <input type="hidden" name="serials[]" class="serials-hidden">
                                </td>
                                <td>
                                    <input type="number" name="price[]" class="form-control form-control-sm price-input" min="0" value="0" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control subtotal" value="0" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end font-weight-bold">TỔNG CỘNG:</td>
                                <td colspan="2" class="font-weight-bold text-primary" id="grandTotal">0 đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <a href="<?php echo BASE_URL; ?>/home" class="btn btn-secondary me-2">Hủy bỏ</a>
            <button type="submit" class="btn btn-primary btn-lg">Lưu & Nhập Kho</button>
        </div>
    </form>
</div>

<!-- Serial input modal -->
<div class="modal fade" id="serialModal" tabindex="-1" aria-labelledby="serialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serialModalLabel">Nhập Serials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>#</th><th>Serial</th><th></th></tr></thead>
                        <tbody>
                            <!-- rows injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="serialSaveBtn">Lưu Serials</button>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>

<script>
    /**
     * KHỞI TẠO CÁC DROPDOWN SELECT2
     * Giúp ô chọn trở nên thông minh hơn: Tìm kiếm được, giao diện đẹp hơn.
     */
    $(document).ready(function() {
        // Init Select2 for Supplier
        $('#select-ncc').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Chọn NCC --',
            allowClear: true
        });

        // Init Select2 for Order
        $('#orderSelect').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Chọn đơn đặt hàng --',
            allowClear: true
        });

        // Init Select2 for existing products (cột sản phẩm trong bảng)
        $('.import-product-select').each(function() {
            initSelect2Product($(this));
        });
    });

    /**
     * Hàm tái sử dụng để gán Select2 cho ô chọn sản phẩm (dùng khi thêm dòng mới)
     */
    function initSelect2Product(element) {
        element.select2({
            theme: 'bootstrap-5',
            placeholder: '-- Chọn hàng --',
            allowClear: true,
            dropdownParent: element.parent() 
        }).on('select2:select', function (e) {
            // Khi chọn xong, bắn sự kiện 'change' thuần để hàm updateEvents nhận biết
            this.dispatchEvent(new Event('change', { bubbles: true }));
        }).on('select2:unselect', function (e) {
            this.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    // 1. CHỨC NĂNG THÊM DÒNG MỚI
    document.getElementById('addRow').addEventListener('click', function() {
        var table = document.getElementById('productTable').getElementsByTagName('tbody')[0];
        // Clone first row
        var firstRow = table.rows[0];
        var newRow = null; 
        
        // Copy dòng đầu tiên (clone) nhưng cần làm sạch Select2 cũ đi
        newRow = firstRow.cloneNode(true);
        
        // Xóa class/data rác của Select2 cũ trên dòng mới
        $(newRow).find('.select2-container').remove();
        var sel = $(newRow).find('select');
        sel.removeClass('select2-hidden-accessible');
        sel.removeAttr('data-select2-id');
        sel.find('option').removeAttr('data-select2-id');
        sel.val(''); // reset value về rỗng

        // Reset giá trị các input khác (Số lượng về 1, Tiền về 0)
        var inputs = newRow.getElementsByTagName('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].value = (inputs[i].type === 'number') ? 0 : '';
            if(inputs[i].name == 'quantity[]') inputs[i].value = 1;
        }
        // Reset hidden serials value
        var sh = newRow.querySelector('.serials-hidden');
        if (sh) sh.value = '';
        
        table.appendChild(newRow);
        
        // Khởi tạo lại Select2 cho dòng mới thêm
        initSelect2Product(sel);

        updateEvents(); // Gán lại sự kiện tính tiền cho dòng mới
        
        // Kích hoạt sự kiện change để ẩn hiện nút Serial đúng logic
        newRow.querySelector('select[name="product_id[]"]').dispatchEvent(new Event('change'));
    });

    // 2. CHỨC NĂNG XÓA DÒNG
    document.getElementById('productTable').addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('removeRow')) {
            var rowCount = document.getElementById('productTable').tBodies[0].rows.length;
            if (rowCount > 1) { // Giữ lại ít nhất 1 dòng
                e.target.closest('tr').remove();
                calculateTotal(); // Tính lại tổng tiền sau khi xóa
            } else {
                alert('Phải có ít nhất 1 dòng sản phẩm!');
            }
        }
    });

    /**
     * 3. LOGIC CẬP NHẬT SỰ KIỆN TOÀN TRANG
     * Được gọi mỗi khi thêm dòng mới hoặc tải lại bảng
     * Gán sự kiện cho: Thay đổi số lượng, thay đổi đơn giá, thay đổi sản phẩm
     */
    function updateEvents() {
    var qtyInputs = document.querySelectorAll('.qty-input');
    var priceInputs = document.querySelectorAll('.price-input');
        // Lấy thêm các ô ngày hết hạn
        var dateInputs = document.querySelectorAll('input[type="date"]');

        // Khi gõ số lượng hoặc đơn giá -> Tính lại thành tiền ngay lập tức
        qtyInputs.forEach(input => { input.oninput = calculateRow; });
        priceInputs.forEach(input => { input.oninput = calculateRow; });
        // hidden serials do not drive UI directly; modal will update them
        var serialHidden = document.querySelectorAll('.serials-hidden');
        serialHidden.forEach(function(sh){ sh.onchange = calculateRow; });
        
        // SỰ KIỆN KHI CHỌN SẢN PHẨM KHÁC
        var selects = document.querySelectorAll('select[name="product_id[]"]');
        selects.forEach(sel => {
            sel.onchange = function() {
                // Kiểm tra loại hàng (LO hay SERIAL) dựa vào thuộc tính data-loai
                var loai = this.options[this.selectedIndex] ? this.options[this.selectedIndex].getAttribute('data-loai') : null;
                var tr = this.closest('tr');
                var qty = tr.querySelector('input[name="quantity[]"]');
                var serialBtn = tr.querySelector('.open-serial-modal');
                var badge = tr.querySelector('.type-badge');
                
                // Nếu là SERIAL -> Hiện nút nhập serial, đổi màu badge xanh
                if (loai === 'SERIAL') {
                    if (serialBtn) serialBtn.style.display = '';
                    if (badge) { badge.innerText = 'SERIAL'; badge.className = 'badge bg-success type-badge'; }
                } else {
                // Nếu là LO -> Ẩn nút serial, đổi màu badge xám
                    if (serialBtn) serialBtn.style.display = 'none';
                    if (badge) { badge.innerText = 'LO'; badge.className = 'badge bg-secondary type-badge'; }
                }
                // Sau khi chọn sản phẩm, cập nhật danh sách option để không cho chọn trùng
                refreshImportProductOptions();

                // Tính lại tiền
                var ev = new Event('input', { bubbles: true });
                if (qty) qty.dispatchEvent(ev);
            };
        });

        // Sự kiện mở modal nhập serial
        var serialOpenBtns = document.querySelectorAll('.open-serial-modal');
        serialOpenBtns.forEach(function(btn){ btn.onclick = openSerialModal; });
        
        // Validate ngày hết hạn không được nhỏ hơn hôm nay
        dateInputs.forEach(input => {
            input.addEventListener('change', function() {
                var today = new Date().toISOString().split('T')[0];
                if(this.value && this.value < today) {
                    alert('Hạn bảo hành không được nhỏ hơn ngày hiện tại!');
                    this.value = ''; // Xóa trắng nếu chọn sai
                }
            });
        });
        // ---------------------
    }

    /**
     * 4. HÀM TÍNH TOÁN
     * Tính Thành tiền = Số lượng * Đơn giá
     */
    function calculateRow(e) {
        var row = e.target.closest('tr');
        var price = parseFloat(row.querySelector('.price-input').value) || 0;
        var qty = parseInt(row.querySelector('.qty-input').value) || 0;

        var subtotal = qty * price;
        
        // Format tiền tệ VNĐ và gán vào ô Thành tiền
        row.querySelector('.subtotal').value = new Intl.NumberFormat('vi-VN').format(subtotal);
        calculateTotal();
    }

    // Tính Tổng cộng cả phiếu (Grand Total)
    function calculateTotal() {
        var total = 0;
        var rows = document.querySelectorAll('#productTable tbody tr');
        rows.forEach(row => {
            var price = parseFloat(row.querySelector('.price-input').value) || 0;
            var qty = parseInt(row.querySelector('.qty-input').value) || 0;
            total += (qty * price);
        });
        document.getElementById('grandTotal').innerText = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
    }

    // Khởi chạy lần đầu
    updateEvents();
    
    // Hàm ngăn chặn chọn trùng sản phẩm trên nhiều dòng
    function refreshImportProductOptions() {
        var tbody = document.querySelector('#productTable tbody');
        if (!tbody) return;
        var selectedValues = [];
        // Lấy danh sách các sản phẩm đang được chọn
        var selects = tbody.querySelectorAll('select[name="product_id[]"]');
        selects.forEach(function(sel) {
            var val = sel.value;
            if (val) selectedValues.push(val);
        });

        // Duyệt qua từng ô select và ẩn (disabled) những món đã được chọn ở dòng khác
        selects.forEach(function(sel) {
            var current = sel.value;
            var options = sel.querySelectorAll('option');
            options.forEach(function(opt) {
                if (!opt.value) return; 
                if (opt.value !== current && selectedValues.indexOf(opt.value) !== -1) {
                    opt.disabled = true;
                } else {
                    opt.disabled = false;
                }
            });
        });
    }
    // Trigger change để UI cập nhật đúng
    document.querySelectorAll('select[name="product_id[]"]').forEach(function(s){
        var ev = new Event('change'); s.dispatchEvent(ev);
    });
    refreshImportProductOptions();

    // --- CÁC HÀM XỬ LÝ MODAL SERIAL (Nâng cao) ---

    // Modal elements (we'll create modal HTML below)
    var currentRowIndex = null;

    function openSerialModal(evt) {
        var btn = (evt.currentTarget) ? evt.currentTarget : evt;
        var tr = btn.closest('tr');
        var table = document.querySelector('#productTable tbody');
        // compute index of the row within tbody
        var rows = Array.prototype.slice.call(table.querySelectorAll('tr'));
        var idx = rows.indexOf(tr);
        currentRowIndex = idx;

        // get qty
        var qty = parseInt(tr.querySelector('input[name="quantity[]"]').value) || 0;
        if (qty <= 0) qty = 1;

        // get existing serials from hidden input
        var hidden = tr.querySelector('.serials-hidden');
        var existing = [];
        if (hidden && hidden.value.trim() !== '') {
            existing = hidden.value.split(/\r\n|\r|\n/).map(function(s){ return s.trim(); }).filter(function(s){ return s !== ''; });
        }

        renderSerialModalRows(qty, existing);
        var modal = document.getElementById('serialModal');
        if (modal) {
            var bs = new bootstrap.Modal(modal);
            bs.show();
            modal._bs = bs;
        }
    }

    function renderSerialModalRows(count, existing) {
        var body = document.querySelector('#serialModal tbody');
        body.innerHTML = '';
        for (var i = 0; i < count; i++) {
            var val = existing[i] || '';
            var tr = document.createElement('tr');
            tr.innerHTML = '<td style="width:50px">' + (i+1) + '</td>' +
                '<td><div class="input-group"><input type="text" class="form-control serial-input" value="' + escapeHtml(val) + '" placeholder="Nhập serial"></div></td>' +
                '<td style="width:120px"><button type="button" class="btn btn-sm btn-outline-primary scan-serial">Quét</button></td>';
            body.appendChild(tr);
        }

        // wire scan buttons
        document.querySelectorAll('#serialModal .scan-serial').forEach(function(b){
            b.onclick = function(e){
                var row = e.currentTarget.closest('tr');
                var input = row.querySelector('.serial-input');
                if (!input) return;
                var code = 'SCAN-' + Date.now() + '-' + Math.floor(Math.random()*1000);
                input.value = code;
            };
        });
    }

    // Save serials from modal into hidden input of the row
    (function(){
        function saveHandler(){
            var modal = document.getElementById('serialModal');
            var rows = modal.querySelectorAll('tbody tr');
            var vals = [];
            rows.forEach(function(r){
                var v = r.querySelector('.serial-input').value.trim();
                if (v !== '') vals.push(v);
            });

            // find the row
            var table = document.querySelector('#productTable tbody');
            var tr = table.querySelectorAll('tr')[currentRowIndex];
            if (tr) {
                var hidden = tr.querySelector('.serials-hidden');
                if (hidden) hidden.value = vals.join('\n');
                // also update qty to match number of serials if desired
                if (vals.length > 0) {
                    var qtyInput = tr.querySelector('input[name="quantity[]"]');
                    if (qtyInput) qtyInput.value = vals.length;
                    qtyInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }

            // hide modal
            if (modal && modal._bs) modal._bs.hide();
        }

        var btn = document.getElementById('serialSaveBtn');
        if (btn) {
            btn.addEventListener('click', saveHandler);
        } else {
            // if not yet present, attach after DOM ready
            document.addEventListener('DOMContentLoaded', function(){
                var b2 = document.getElementById('serialSaveBtn');
                if (b2) b2.addEventListener('click', saveHandler);
            });
        }
    })();

    // Utility: escape HTML for insertion into value
    function escapeHtml(s) { return (s+'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>

<script>
    // Validate chi tiết hàng nhập trước khi submit
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('importForm');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            var tbodyRows = document.querySelectorAll('#productTable tbody tr');
            var hasError = false;
            var messages = [];
            var hasSerialMissing = false;
            var hasSerialCountMismatch = false;

            tbodyRows.forEach(function(row, index) {
                var lineNumber = index + 1;

                var productSelect = row.querySelector('select[name="product_id[]"]');
                var expiryInput = row.querySelector('input[name="expiry[]"]');
                var qtyInput = row.querySelector('input[name="quantity[]"]');
                var priceInput = row.querySelector('input[name="price[]"]');
                var serialHidden = row.querySelector('.serials-hidden');

                var product = productSelect ? productSelect.value : '';
                if (!product) {
                    hasError = true;
                    if (messages.indexOf('Chưa chọn đủ sản phẩm cho tất cả các dòng.') === -1) {
                        messages.push('Chưa chọn đủ sản phẩm cho tất cả các dòng.');
                    }
                }

                if (!expiryInput || !expiryInput.value) {
                    hasError = true;
                    if (messages.indexOf('Phải nhập Hạn bảo hành cho tất cả các dòng.') === -1) {
                        messages.push('Phải nhập Hạn bảo hành cho tất cả các dòng.');
                    }
                }

                var qty = qtyInput ? parseInt(qtyInput.value) || 0 : 0;

                var price = priceInput ? parseFloat(priceInput.value) || 0 : 0;
                if (price < 0) {
                    hasError = true;
                    if (messages.indexOf('Đơn giá không hợp lệ ở một số dòng.') === -1) {
                        messages.push('Đơn giá không hợp lệ ở một số dòng.');
                    }
                }

                // Nếu là hàng SERIAL thì bắt buộc serial đủ số lượng
                if (productSelect && productSelect.value) {
                    var opt = productSelect.options[productSelect.selectedIndex];
                    var loai = opt ? opt.getAttribute('data-loai') : null;
                    if (loai === 'SERIAL') {
                        var serials = [];
                        if (serialHidden && serialHidden.value.trim() !== '') {
                            serials = serialHidden.value.split(/\r\n|\r|\n/)
                                .map(function(s){ return s.trim(); })
                                .filter(function(s){ return s !== ''; });
                        }
                        if (serials.length === 0) {
                            hasError = true;
                            hasSerialMissing = true;
                        } else {
                            // Ép số lượng phải đúng bằng số serial, nếu không thì chặn
                            if (qtyInput) {
                                qty = parseInt(qtyInput.value) || 0;
                            }
                            if (serials.length !== qty) {
                                hasError = true;
                                hasSerialCountMismatch = true;
                            }
                        }
                    } else {
                        // Hàng thường: số lượng phải > 0
                        if (qty <= 0) {
                            hasError = true;
                            if (messages.indexOf('Số lượng tất cả các dòng phải > 0.') === -1) {
                                messages.push('Số lượng tất cả các dòng phải > 0.');
                            }
                        }
                    }
                } else {
                    // Chưa chọn sản phẩm nhưng vẫn nhập số lượng: vẫn check số lượng > 0 nếu cần
                    if (qty <= 0) {
                        hasError = true;
                        if (messages.indexOf('Số lượng tất cả các dòng phải > 0.') === -1) {
                            messages.push('Số lượng tất cả các dòng phải > 0.');
                        }
                    }
                }
            });

            if (hasSerialMissing) {
                messages.push('Hàng SERIAL phải nhập đủ serial cho tất cả các dòng.');
            }
            if (hasSerialCountMismatch) {
                messages.push('Số lượng serial phải đúng bằng Số lượng cho tất cả các dòng SERIAL.');
            }

            if (hasError) {
                e.preventDefault();
                var box = document.getElementById('importErrorBox');
                if (box) {
                    box.classList.remove('d-none');
                    box.innerHTML = '<strong>Vui lòng kiểm tra lại chi tiết hàng nhập:</strong>' +
                        '<ul class="mb-0">' +
                        messages.map(function(m){ return '<li>' + m + '</li>'; }).join('') +
                        '</ul>';
                    box.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    alert('Vui lòng kiểm tra lại chi tiết hàng nhập:\n\n' + messages.join('\n'));
                }
            }
        });
    });
</script>

<script>
    // --- Liên kết Đơn đặt hàng với Phiếu nhập ---
    document.addEventListener('DOMContentLoaded', function() {
        const nccSelect = document.querySelector('select[name="maNCC"]');
        const orderSelect = document.getElementById('orderSelect');
        const tbody = document.querySelector('#productTable tbody');

        if (!nccSelect || !orderSelect || !tbody) return;

        // Khi chọn NCC: load danh sách đơn đặt hàng
        nccSelect.addEventListener('change', function() {
            orderSelect.innerHTML = '<option value="">-- Chọn đơn đặt hàng --</option>';
            const maNCC = this.value;
            if (!maNCC) return;

            fetch('<?php echo BASE_URL; ?>/phieudathang/ordersBySupplier?maNCC=' + encodeURIComponent(maNCC))
                .then(r => r.json())
                .then(res => {
                    if (!res.success || !res.data) return;
                    res.data.forEach(o => {
                        const opt = document.createElement('option');
                        opt.value = o.maDH;
                        opt.textContent = o.maDH + ' - ' + (o.ngayDatHang || '');
                        orderSelect.appendChild(opt);
                    });
                })
                .catch(err => console.error('Load orders error', err));
        });
        // Khi chọn đơn đặt hàng: load chi tiết vào bảng nhập
        orderSelect.addEventListener('change', function() {
            const maDH = this.value;
            if (!maDH) return;

            // lưu lại mã đơn được chọn vào hidden để backend biết liên kết phiếu nhập với đơn đặt
            var hiddenOrder = document.getElementById('selectedOrderId');
            if (hiddenOrder) hiddenOrder.value = maDH;

            fetch('<?php echo BASE_URL; ?>/phieudathang/lines?maDH=' + encodeURIComponent(maDH))
                .then(r => r.json())
                .then(res => {
                    if (!res.success || !res.data) return;

                    const templateRow = document.querySelector('#productTable tbody tr');
                    if (!templateRow) return;

                    tbody.innerHTML = '';

                    res.data.forEach(line => {
                        const newRow = templateRow.cloneNode(true);

                        const select = newRow.querySelector('select[name="product_id[]"]');
                        // Clean up existing select inside newRow first if it was cloned with artifacts
                        // (Usually templateRow is already cleaned or pure)
                        // If clean, just set value:
                        if (select) {
                            // If select2 is active on the cloned element, we must update it via jQuery
                            // Wait, newRow is cloned. If templateRow had S2, newRow has S2 classes but not working S2.
                            
                            // Let's CLEAN it first like in addRow
                            $(newRow).find('.select2-container').remove();
                            $(select).removeClass('select2-hidden-accessible')
                                     .removeAttr('data-select2-id')
                                     .find('option').removeAttr('data-select2-id');

                            // Set value
                            select.value = line.maHH;
                        }

                        const orderedInput = newRow.querySelector('.ordered-qty');
                        if (orderedInput) {
                            orderedInput.value = line.soLuong; // số lượng đặt ban đầu
                        }

                        const qtyInput = newRow.querySelector('input[name="quantity[]"]');
                        if (qtyInput) {
                            // Nếu backend trả soLuongConLai (dựa trên soLuongDaNhap) thì dùng để nhập tiếp phần thiếu
                            const remain = (typeof line.soLuongConLai !== 'undefined' && line.soLuongConLai !== null)
                                ? line.soLuongConLai
                                : line.soLuong;
                            qtyInput.value = remain;
                        }

                        const priceInput = newRow.querySelector('input[name="price[]"]');
                        if (priceInput) {
                            priceInput.value = line.donGia || 0;
                        }

                        const sh = newRow.querySelector('.serials-hidden');
                        if (sh) sh.value = '';
                        const subtotal = newRow.querySelector('.subtotal');
                        if (subtotal) subtotal.value = '0';

                        tbody.appendChild(newRow);

                        // Init Select2
                        if (select) {
                             initSelect2Product($(select));
                        }
                    });

                    updateEvents();
                    // Sau khi gán lại handler, trigger change để badge & nút serial đúng theo loại hàng
                    document.querySelectorAll('#productTable tbody select[name="product_id[]"]').forEach(function(sel){
                        sel.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                    calculateTotal();
                })
                .catch(err => console.error('Load order lines error', err));
        });
    });
</script>

