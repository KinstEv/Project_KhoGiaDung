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
                    <thead class="table-light"><tr><th>Code</th><th>Dãy</th><th>Kệ</th><th>Ô</th><th>Kích thước (DxR xC)</th><th>Danh mục ưu tiên</th><th>Cho phép xếp chồng</th><th>Trạng thái</th><th>Hành động</th></tr></thead>
                    <tbody>
                        <?php if (!empty($data['rows'])): ?>
                            <?php foreach ($data['rows'] as $r): ?>
                                <tr>
                                    <td>
                                        <a href="#" class="view3d-link" data-ma="<?php echo htmlspecialchars($r['maViTri']); ?>">
                                            <?php echo htmlspecialchars($r['maViTri']); ?>
                                        </a>
                                    </td>
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
                                        $pct = isset($r['fillPercent']) ? $r['fillPercent'] : null;
                                        if ($pct === null) {
                                            $statusBadge = '<span class="text-muted">Không xác định</span>';
                                        } else {
                                            $label = ''; $cls = '';
                                            if ($pct === 0) { $cls = 'bg-primary'; $label = 'Trống'; }
                                            elseif ($pct <= 60) { $cls = 'bg-success'; $label = 'Còn trống nhiều'; }
                                            elseif ($pct <= 90) { $cls = 'bg-warning text-dark'; $label = 'Gần đầy'; }
                                            else { $cls = 'bg-danger'; $label = 'Đầy'; }
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

<div class="modal fade" id="vitri3dModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mô phỏng 3D - Vị trí <span id="vitri3d-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-8" style="height:70vh; background:#222;">
                        <div id="vitri3d-canvas" style="width:100%; height:100%;"></div>
                    </div>
                    <div class="col-md-4 p-3 bg-light">
                        <h6>Thông tin ô kệ</h6>
                        <ul class="list-unstyled small border-bottom pb-2">
                            <li>Dài: <span id="v_dai" class="fw-bold"></span></li>
                            <li>Rộng: <span id="v_rong" class="fw-bold"></span></li>
                            <li>Cao: <span id="v_cao" class="fw-bold"></span></li>
                            <li>Xếp chồng: <span id="v_stack" class="fw-bold"></span></li>
                        </ul>
                        <h6 class="mt-3">Danh sách hàng hóa</h6>
                        <div id="vitri3d-items" class="small overflow-auto" style="max-height: 40vh;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.134.0/examples/js/controls/OrbitControls.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('vitri3dModal');
    const modal = new bootstrap.Modal(modalEl);
    const canvasContainer = document.getElementById('vitri3d-canvas');
    let renderer, scene, camera, contentGroup, controls, animating = false;
    let lastVitriData = null, lastPackedData = null;

    function init3D(width, height) {
        if (renderer) {
            renderer.dispose();
            canvasContainer.innerHTML = '';
        }
        renderer = new THREE.WebGLRenderer({ antialias: true, logarithmicDepthBuffer: true });
        renderer.setSize(width, height);
        renderer.setClearColor(0x1a1a1a);
        canvasContainer.appendChild(renderer.domElement);

        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
        camera.position.set(2, 2, 2);

        const hemi = new THREE.HemisphereLight(0xffffff, 0x444444, 1.2);
        scene.add(hemi);
        const dir = new THREE.DirectionalLight(0xffffff, 0.8);
        dir.position.set(5, 10, 7.5);
        scene.add(dir);

        contentGroup = new THREE.Group();
        scene.add(contentGroup);

        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
    }

    function renderLoop() {
        if (!animating) return;
        requestAnimationFrame(renderLoop);
        if (controls) controls.update();
        renderer.render(scene, camera);
    }

    function drawVitri(vitri, packed) {
        while(contentGroup.children.length > 0){ contentGroup.remove(contentGroup.children[0]); }

        const d = parseFloat(vitri.daiToiDa) || 100;
        const r = parseFloat(vitri.rongToiDa) || 100;
        const c = parseFloat(vitri.caoToiDa) || 100;
        const s = 0.01; // scale: 1cm => 0.01 three units

        // Draw container wireframe centered so floor is at y=0
        const boxGeom = new THREE.BoxGeometry(d*s, c*s, r*s);
        const edges = new THREE.EdgesGeometry(boxGeom);
        const line = new THREE.LineSegments(edges, new THREE.LineBasicMaterial({ color: 0x888888 }));
        line.position.set(d*s/2, c*s/2, r*s/2);
        contentGroup.add(line);

        // Floor grid
        const grid = new THREE.GridHelper(Math.max(d, r)*s, 10, 0x444444, 0x333333);
        grid.position.set(d*s/2, 0, r*s/2);
        contentGroup.add(grid);

        // If we have packed data from the server (flattened array), draw that exact placement
        if (packed && Array.isArray(packed) && packed.length > 0) {
            packed.forEach((p, idx) => {
                const w = (p.w || 0) * s; // width
                const l = (p.l || 0) * s; // length/depth in DB
                const h = (p.h || 0) * s; // height

                // Map packer coordinates to three.js coordinates:
                // packer.x -> three.x, packer.y -> three.z, packer.z -> three.y
                const px = (p.x || 0) * s + w / 2;
                const py = (p.z || 0) * s + h / 2;
                const pz = (p.y || 0) * s + l / 2;

                const geom = new THREE.BoxGeometry(w, h, l);
                const mat = new THREE.MeshStandardMaterial({ color: new THREE.Color().setHSL((idx * 0.13) % 1, 0.6, 0.5), roughness: 0.45 });
                const mesh = new THREE.Mesh(geom, mat);
                mesh.position.set(px, py, pz);
                contentGroup.add(mesh);

                const iEdges = new THREE.EdgesGeometry(geom);
                const iLine = new THREE.LineSegments(iEdges, new THREE.LineBasicMaterial({ color: 0xffff66 }));
                iLine.position.copy(mesh.position);
                contentGroup.add(iLine);
            });
        } else {
            // Fallback: nothing packed, keep scene empty (or we could draw raw items list)
        }

        // Auto-fit camera
        const box3 = new THREE.Box3().setFromObject(contentGroup);
        const center = box3.getCenter(new THREE.Vector3());
        controls.target.copy(center);
        camera.position.set(center.x + 1.5, center.y + 1.5, center.z + 1.5);
    }

    modalEl.addEventListener('shown.bs.modal', function() {
        init3D(canvasContainer.clientWidth, canvasContainer.clientHeight);
        animating = true;
        renderLoop();
    if (lastVitriData) drawVitri(lastVitriData, lastPackedData);
    });

    modalEl.addEventListener('hidden.bs.modal', function() {
        animating = false;
        if (renderer) renderer.dispose();
    });

    document.querySelectorAll('.view3d-link').forEach(el => {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            const ma = this.dataset.ma;
            document.getElementById('vitri3d-title').textContent = ma;
            fetch('<?php echo BASE_URL; ?>/vitri/view3d/' + encodeURIComponent(ma))
                .then(r => r.json())
                .then(data => {
                    lastVitriData = data.vitri;
                    lastPackedData = data.packed;
                    document.getElementById('v_dai').textContent = data.vitri.daiToiDa;
                    document.getElementById('v_rong').textContent = data.vitri.rongToiDa;
                    document.getElementById('v_cao').textContent = data.vitri.caoToiDa;
                    document.getElementById('v_stack').textContent = data.vitri.choPhepXepChong ? 'Có' : 'Không';
                    
                    const list = document.getElementById('vitri3d-items');
                    list.innerHTML = (data.items || []).map(it => `
                        <div class="mb-1 p-1 border-bottom">
                            <b>${it.tenHH || it.maHH}</b><br>
                            Số lượng: ${it.quantity} | Size: ${it.chieuDai}x${it.chieuRong}x${it.chieuCao}
                        </div>
                    `).join('');
                    modal.show();
                });
        });
    });
});
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>