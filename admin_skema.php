<?php
require_once 'config.php';
require_once 'skema_functions.php';

$skemaManager = new SkemaManager($conn);
$skema_list = $skemaManager->getAllSkema();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_skema':
                $result = $skemaManager->createSkemaComplete($_POST);
                if ($result) {
                    header("Location: admin_skema.php?success=Skema berhasil ditambahkan");
                } else {
                    $error = "Gagal menambahkan skema";
                }
                break;
                
            case 'update_skema':
                $result = $skemaManager->updateSkemaComplete($_POST);
                if ($result) {
                    header("Location: admin_skema.php?success=Skema berhasil diperbarui");
                } else {
                    $error = "Gagal memperbarui skema";
                }
                break;
                
            case 'delete_skema':
                $result = $skemaManager->deleteSkema($_POST['skema_id']);
                if ($result) {
                    header("Location: admin_skema.php?success=Skema berhasil dihapus");
                } else {
                    $error = "Gagal menghapus skema";
                }
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Skema - Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css" />
    <!-- Blok <style> akan dikosongkan atau dihapus karena style dipindahkan ke admin.css -->
    <style>
        /* Style yang sangat spesifik untuk admin_skema.php yang tidak umum bisa tetap di sini,
           atau idealnya diminimalkan. Untuk contoh ini, kita akan mengasumsikan sebagian besar
           telah dipindahkan ke admin.css atau akan ditangani olehnya. */

        /* Contoh style yang mungkin spesifik dan tidak ada di admin_blog.php sebelumnya */
        .schema-card img {
            transition: transform 0.3s ease;
            max-width: 100%; /* Pastikan gambar responsif di dalam card */
            height: auto;
            margin-bottom: 10px;
        }

        .schema-card img:hover {
            transform: scale(1.05);
            cursor: pointer;
        }

        #current_image_preview img,
        #add_image_preview img {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 200px;
            max-height: 150px;
            display: block;
            margin-top: 10px;
        }

        /* Style untuk tab, dynamic field, dll. yang spesifik untuk form skema
           Jika tidak digunakan di admin_blog.php, bisa tetap di sini atau dipindahkan
           ke admin.css dengan selector yang lebih spesifik jika ada potensi penggunaan ulang.
           Untuk saat ini, kita biarkan beberapa di sini untuk menunjukkan pemisahan.
        */
        .tab-container {
            margin-top: 20px;
        }

        .tab-buttons {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 8px 16px;
            background-color: #ecf0f1;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 12px;
        }

        .tab-btn.active {
            background-color: #3498db;
            color: white;
        }

        .tab-content {
            display: none;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fafafa;
        }

        .tab-content.active {
            display: block;
        }

        .dynamic-field {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        .dynamic-field input,
        .dynamic-field select { /* Termasuk select di sini */
            flex-grow: 1; /* Bertanggung jawab untuk mengambil sisa ruang */
            flex-shrink: 1; /* Izinkan menyusut jika perlu */
            flex-basis: 0; /* Mulai dari basis 0 agar flex-grow bekerja proporsional */
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 100px; /* Tambahkan min-width agar tidak terlalu sempit */
            /* font-size: 14px; /* Dihapus agar mewarisi dari .form-group input */
        }

        /* Input nomor di Unit Kompetensi memiliki style inline flex: 0 0 60px; yang akan diutamakan */
        /* Jika ingin override dari CSS, gunakan !important atau selector yang lebih spesifik */
        /* Untuk saat ini, kita biarkan style inline tersebut */
        /* .dynamic-field input[type="number"] {
             flex: 0 0 70px;
        } */

        .remove-btn { /* Style tombol hapus field dinamis */
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px; /* Sedikit lebih besar dari btn-small */
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
         .remove-btn:hover {
            background-color: #c0392b;
        }

        .add-btn { /* Style tombol tambah field dinamis */
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 8px 16px; /* Sedikit lebih besar dari btn-small */
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 10px;
        }
        .add-btn:hover {
            background-color: #219a52;
        }

        .checkbox-field {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .checkbox-field input[type="checkbox"] {
            width: auto; /* Checkbox tidak perlu full width */
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <?php require_once 'includes/sidebar.php'; ?>

    <div class="main-content">
        <header>
            <div class="header-content">
                <h1>Manajemen Skema</h1>
                <!-- Tombol Sign Out dihapus dari header -->
            </div>
        </header>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <section class="form-section">
            <h2>Tambah Skema Baru</h2>
            <button type="button" class="btn" onclick="openModal('addSkemaModal')">Tambah Skema</button>
        </section>

        <section class="schema-section">
        <h2>Daftar Skema</h2>
<div class="schema-container">
    <?php foreach ($skema_list as $skema): ?>
        <div class="schema-card">
            <?php if (!empty($skema['gambar'])): ?>
                <div class="schema-image">
                    <img src="dksassets/img/<?php echo htmlspecialchars($skema['gambar']); ?>" 
                         alt="<?php echo htmlspecialchars($skema['nama']); ?>">
                </div>
            <?php endif; ?>
            
            <h3><?php echo htmlspecialchars($skema['nama']); ?></h3>
            <div class="schema-info">
                <strong>Kode:</strong> <?php echo htmlspecialchars($skema['kode']); ?><br>
                <strong>Jenis:</strong> <?php echo htmlspecialchars($skema['jenis']); ?><br>
                <strong>Harga:</strong> Rp <?php echo htmlspecialchars($skema['harga'],); ?><br>
                <strong>Unit Kompetensi:</strong> <?php echo $skema['unit_kompetensi']; ?>
            </div>
            <p><?php echo htmlspecialchars(substr($skema['ringkasan'], 0, 150)) . '...'; ?></p>
            <div class="card-actions">
                <button class="btn btn-small" onclick="editSkema(<?php echo $skema['id']; ?>)">Edit</button>
                <button class="btn btn-danger btn-small" onclick="deleteSkema(<?php echo $skema['id']; ?>)">Hapus</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</section>
    </div>

    <!-- Add Skema Modal -->
    <div id="addSkemaModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addSkemaModal')">&times;</span>
            <h2>Tambah Skema Baru</h2>
            <form method="POST" id="addSkemaForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create_skema">
                
                <!-- Basic Info -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama">Nama Skema *</label>
                        <input type="text" id="nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="kode">Kode Skema *</label>
                        <input type="text" id="kode" name="kode" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="jenis">Jenis *</label>
                        <select id="jenis" name="jenis" required>
                            <option value="">Pilih Jenis</option>
                            <option value="Klaster">Klaster</option>
                            <option value="Okupasi">Okupasi</option>
                            <option value="Mandiri">Mandiri</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga *</label>
                        <input type="number" id="harga" name="harga" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="unit_kompetensi">Jumlah Unit Kompetensi *</label>
                        <input type="number" id="unit_kompetensi" name="unit_kompetensi" required>
                    </div>
                    <div class="form-group">
                        <label for="masa_berlaku">Masa Berlaku Sertifikat (Tahun)</label>
                        <input type="number" id="masa_berlaku" name="masa_berlaku" placeholder="Contoh: 3">
                    </div>
                </div>

                <div class="form-group">
                    <label for="ringkasan">Ringkasan *</label>
                    <textarea id="ringkasan" name="ringkasan" required placeholder="Masukkan ringkasan skema..."></textarea>
                </div>

                <div class="form-group">
    <label for="gambar">Gambar Skema</label>
    <input type="file" id="gambar" name="gambar" accept="image/*">
    <small class="form-text text-muted">Format: JPG, PNG, maksimal 2MB</small>
</div>

                <!-- Tab Container -->
                <div class="tab-container">
                    <div class="tab-buttons">
                        <button type="button" class="tab-btn active" onclick="showAddTab('unit')">Unit Kompetensi</button>
                        <button type="button" class="tab-btn" onclick="showAddTab('persyaratan')">Persyaratan</button>
                        <button type="button" class="tab-btn" onclick="showAddTab('dokumen')">Dokumen Persyaratan</button>
                        <button type="button" class="tab-btn" onclick="showAddTab('asesmen')">Metode Asesmen</button>
                        <button type="button" class="tab-btn" onclick="showAddTab('pemeliharaan')">Pemeliharaan</button>
                        <button type="button" class="tab-btn" onclick="showAddTab('metode_pengujian')">Metode Pengujian</button>
                    </div>

                    <!-- Unit Kompetensi Tab -->
                    <div id="add-tab-unit" class="tab-content active">
                        <h4>Unit Kompetensi</h4>
                        <div id="unit-fields">
                            <div class="dynamic-field">
                                <input type="number" name="unit_no[]" placeholder="No" style="flex: 0 0 60px;">
                                <input type="text" name="kode_unit[]" placeholder="Kode Unit" required>
                                <input type="text" name="unit_judul[]" placeholder="Judul Unit" required>
                                <input type="text" name="unit_standar[]" placeholder="Standar Kompetensi">
                                <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
                            </div>
                        </div>
                        <button type="button" class="add-btn" onclick="addUnitField()">+ Tambah Unit</button>
                    </div>

                    <!-- Persyaratan Tab -->
                    <div id="add-tab-persyaratan" class="tab-content">
                        <h4>Persyaratan</h4>
                        <div id="persyaratan-fields">
                            <div class="dynamic-field">
                                <textarea name="persyaratan[]" placeholder="Deskripsi persyaratan" required style="flex: 1; height: 60px;"></textarea>
                                <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
                            </div>
                        </div>
                        <button type="button" class="add-btn" onclick="addPersyaratanField()">+ Tambah Persyaratan</button>
                    </div>

                    <!-- Dokumen Tab -->
                    <div id="add-tab-dokumen" class="tab-content">
                        <h4>Dokumen Persyaratan</h4>
                        <div id="dokumen-fields">
                            <div class="dynamic-field">
                                <input type="text" name="dokumen_nama[]" placeholder="Nama Dokumen" required>
                                <div class="checkbox-field">
                                    <input type="checkbox" name="dokumen_wajib[0]" value="1" checked>
                                    <label>Wajib</label>
                                </div>
                                <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
                            </div>
                        </div>
                        <button type="button" class="add-btn" onclick="addDokumenField()">+ Tambah Dokumen</button>
                    </div>

                    <!-- Metode Asesmen Tab -->
                    <div id="add-tab-asesmen" class="tab-content">
                        <h4>Metode Asesmen</h4>
                        <div id="asesmen-fields">
                            <div class="dynamic-field">
                                <select name="asesmen_jenis[]" required>
                                    <option value="">Pilih Jenis Peserta</option>
                                    <option value="Berpengalaman">Berpengalaman</option>
                                    <option value="Belum Berpengalaman">Belum Berpengalaman</option>
                                </select>
                                <input type="text" name="asesmen_metode[]" placeholder="Metode Asesmen" required>
                                <textarea name="asesmen_deskripsi[]" placeholder="Deskripsi" style="height: 60px;"></textarea>
                                <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
                            </div>
                        </div>
                        <button type="button" class="add-btn" onclick="addAsesmenField()">+ Tambah Metode</button>
                    </div>

                    <!-- Pemeliharaan Tab -->
                    <div id="add-tab-pemeliharaan" class="tab-content">
                        <h4>Pemeliharaan Sertifikasi</h4>
                        <div class="form-group">
                            <textarea name="pemeliharaan" placeholder="Masukkan deskripsi pemeliharaan sertifikasi..." style="width: 100%; height: 100px;"></textarea>
                        </div>
                    </div>

                    <!-- Metode Pengujian Tab -->
                    <div id="add-tab-metode_pengujian" class="tab-content">
                        <h4>Metode Pengujian Skema</h4>
                        <div id="add-metode-pengujian-fields">
                            <!-- Field dinamis akan ditambahkan di sini oleh JavaScript -->
                            <div class="dynamic-field">
                                <select name="metode_pengujian_skema[]" class="form-control">
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="Sertifikasi Jarak Jauh (SJJ)">Sertifikasi Jarak Jauh (SJJ)</option>
                                    <option value="Metode Paperless (non-kertas)">Metode Paperless (non-kertas)</option>
                                    <option value="Paper-based (berbasis kertas)">Paper-based (berbasis kertas)</option>
                                </select>
                                <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
                            </div>
                        </div>
                        <button type="button" class="add-btn" onclick="addMetodePengujianField()">+ Tambah Metode</button>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn" style="background-color: #95a5a6; margin-right: 10px;" onclick="closeModal('addSkemaModal')">Batal</button>
                    <button type="submit" class="btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Skema Modal -->
    <div id="editSkemaModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editSkemaModal')">&times;</span>
            <h2>Edit Skema</h2>
            <form method="POST" id="editSkemaForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_skema">
                <input type="hidden" name="skema_id" id="edit_skema_id">
                
                <!-- Same structure as add form but with edit_ prefixes -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_nama">Nama Skema *</label>
                        <input type="text" id="edit_nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_kode">Kode Skema *</label>
                        <input type="text" id="edit_kode" name="kode" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_jenis">Jenis *</label>
                        <select id="edit_jenis" name="jenis" required>
                            <option value="">Pilih Jenis</option>
                            <option value="Klaster">Klaster</option>
                            <option value="Okupasi">Okupasi</option>
                            <option value="Mandiri">Mandiri</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_harga">Harga *</label>
                        <input type="number" id="edit_harga" name="harga" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_unit_kompetensi">Jumlah Unit Kompetensi *</label>
                        <input type="number" id="edit_unit_kompetensi" name="unit_kompetensi" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_masa_berlaku">Masa Berlaku Sertifikat (Tahun)</label>
                        <input type="number" id="edit_masa_berlaku" name="masa_berlaku" placeholder="Contoh: 3">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_ringkasan">Ringkasan *</label>
                    <textarea id="edit_ringkasan" name="ringkasan" required></textarea>
                </div>

                <div class="form-group">
    <label for="edit_gambar">Gambar Skema</label>
    <input type="file" id="edit_gambar" name="gambar" accept="image/*">
    <small class="form-text text-muted">Format: JPG, PNG, maksimal 2MB</small>
    <input type="hidden" name="existing_gambar" id="edit_existing_gambar">
    <div id="current-image-preview" style="margin-top: 10px;"></div>
</div>
                <!-- Tab Container for Edit -->
                <div class="tab-container">
                    <div class="tab-buttons">
                        <button type="button" class="tab-btn active" onclick="showEditTab('unit')">Unit Kompetensi</button>
                        <button type="button" class="tab-btn" onclick="showEditTab('persyaratan')">Persyaratan</button>
                        <button type="button" class="tab-btn" onclick="showEditTab('dokumen')">Dokumen Persyaratan</button>
                        <button type="button" class="tab-btn" onclick="showEditTab('asesmen')">Metode Asesmen</button>
                        <button type="button" class="tab-btn" onclick="showEditTab('pemeliharaan')">Pemeliharaan</button>
                        <button type="button" class="tab-btn" onclick="showEditTab('metode_pengujian')">Metode Pengujian</button>
                    </div>

                    <div id="edit-tab-unit" class="tab-content active">
                        <h4>Unit Kompetensi</h4>
                        <div id="edit-unit-fields"></div>
                        <button type="button" class="add-btn" onclick="addEditUnitField()">+ Tambah Unit</button>
                    </div>

                    <div id="edit-tab-persyaratan" class="tab-content">
                        <h4>Persyaratan</h4>
                        <div id="edit-persyaratan-fields"></div>
                        <button type="button" class="add-btn" onclick="addEditPersyaratanField()">+ Tambah Persyaratan</button>
                    </div>

                    <div id="edit-tab-dokumen" class="tab-content">
                        <h4>Dokumen Persyaratan</h4>
                        <div id="edit-dokumen-fields"></div>
                        <button type="button" class="add-btn" onclick="addEditDokumenField()">+ Tambah Dokumen</button>
                    </div>

                    <div id="edit-tab-asesmen" class="tab-content">
                        <h4>Metode Asesmen</h4>
                        <div id="edit-asesmen-fields"></div>
                        <button type="button" class="add-btn" onclick="addEditAsesmenField()">+ Tambah Metode</button>
                    </div>

                    <!-- Pemeliharaan Tab for Edit -->
                    <div id="edit-tab-pemeliharaan" class="tab-content">
                        <h4>Pemeliharaan Sertifikasi</h4>
                        <div class="form-group">
                            <textarea id="edit_pemeliharaan" name="pemeliharaan" placeholder="Masukkan deskripsi pemeliharaan sertifikasi..." style="width: 100%; height: 100px;"></textarea>
                        </div>
                    </div>

                    <!-- Metode Pengujian Tab for Edit -->
                    <div id="edit-tab-metode_pengujian" class="tab-content">
                        <h4>Metode Pengujian Skema</h4>
                        <div id="edit-metode-pengujian-fields">
                            <!-- Field dinamis akan diisi di sini oleh JavaScript saat edit -->
                        </div>
                        <button type="button" class="add-btn" onclick="addEditMetodePengujianField()">+ Tambah Metode</button>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn" style="background-color: #95a5a6; margin-right: 10px;" onclick="closeModal('editSkemaModal')">Batal</button>
                    <button type="submit" class="btn">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteSkemaModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteSkemaModal')">&times;</span>
            <h2>Konfirmasi Hapus</h2>
            <p>Apakah Anda yakin ingin menghapus skema ini? Tindakan ini tidak dapat dibatalkan.</p>
            <form method="POST" id="deleteSkemaForm">
                <input type="hidden" name="action" value="delete_skema">
                <input type="hidden" name="skema_id" id="delete_skema_id">
                
                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn" style="background-color: #95a5a6; margin-right: 10px;" onclick="closeModal('deleteSkemaModal')">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>

<script>
    // Fixed JavaScript code for admin_skema.php

let dokumenCounter = 0;

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
}

// Tab functions for Add Modal
function showAddTab(tabName) {
    try {
        // Hide all tab contents
        document.querySelectorAll('#addSkemaModal .tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('#addSkemaModal .tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Show selected tab content
        const targetTab = document.getElementById(`add-tab-${tabName}`);
        if (targetTab) {
            targetTab.classList.add('active');
        }
        
        // Add active class to clicked button
        if (event && event.target) {
            event.target.classList.add('active');
        } else {
            // Fallback: find button based on onclick attribute
            const buttons = document.querySelectorAll('#addSkemaModal .tab-btn');
            buttons.forEach(btn => {
                const onclickAttr = btn.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes(`'${tabName}'`)) {
                    btn.classList.add('active');
                }
            });
        }
    } catch (error) {
        console.error('Error in showAddTab:', error);
    }
}

// Tab functions for Edit Modal
function showEditTab(tabName) {
    try {
        // Hide all tab contents
        document.querySelectorAll('#editSkemaModal .tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('#editSkemaModal .tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Show selected tab content
        const targetTab = document.getElementById(`edit-tab-${tabName}`);
        if (targetTab) {
            targetTab.classList.add('active');
        }
        
        // Add active class to clicked button
        if (event && event.target) {
            event.target.classList.add('active');
        } else {
            // Fallback: find button based on onclick attribute
            const buttons = document.querySelectorAll('#editSkemaModal .tab-btn');
            buttons.forEach(btn => {
                const onclickAttr = btn.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes(`'${tabName}'`)) {
                    btn.classList.add('active');
                }
            });
        }
    } catch (error) {
        console.error('Error in showEditTab:', error);
    }
}

// ===== FUNCTIONS FOR ADD MODAL =====

// Add Unit field for ADD modal
function addUnitField() {
    const container = document.getElementById('unit-fields');
    if (!container) {
        console.error('Container unit-fields not found');
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'dynamic-field';
    div.innerHTML = `
        <input type="number" name="unit_no[]" placeholder="No" style="flex: 0 0 60px;">
        <input type="text" name="kode_unit[]" placeholder="Kode Unit" required>
        <input type="text" name="unit_judul[]" placeholder="Judul Unit" required>
        <input type="text" name="unit_standar[]" placeholder="Standar Kompetensi">
        <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
    `;
    container.appendChild(div);
    console.log('Added unit field to ADD modal');
}

// Add Persyaratan field for ADD modal
function addPersyaratanField() {
    const container = document.getElementById('persyaratan-fields');
    if (!container) {
        console.error('Container persyaratan-fields not found');
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'dynamic-field';
    div.innerHTML = `
        <textarea name="persyaratan[]" placeholder="Deskripsi persyaratan" required style="flex: 1; height: 60px;"></textarea>
        <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
    `;
    container.appendChild(div);
    console.log('Added persyaratan field to ADD modal');
}

// Add Dokumen field for ADD modal
function addDokumenField() {
    dokumenCounter++;
    const container = document.getElementById('dokumen-fields');
    if (!container) {
        console.error('Container dokumen-fields not found');
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'dynamic-field';
    div.innerHTML = `
        <input type="text" name="dokumen_nama[]" placeholder="Nama Dokumen" required>
        <div class="checkbox-field">
            <input type="checkbox" name="dokumen_wajib[${dokumenCounter}]" value="1" checked>
            <label>Wajib</label>
        </div>
        <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
    `;
    container.appendChild(div);
    console.log('Added dokumen field to ADD modal');
}

// Add Asesmen field for ADD modal
function addAsesmenField() {
    const container = document.getElementById('asesmen-fields');
    if (!container) {
        console.error('Container asesmen-fields not found');
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'dynamic-field';
    div.innerHTML = `
        <select name="asesmen_jenis[]" required>
            <option value="">Pilih Jenis Peserta</option>
            <option value="Berpengalaman">Berpengalaman</option>
            <option value="Belum Berpengalaman">Belum Berpengalaman</option>
        </select>
        <input type="text" name="asesmen_metode[]" placeholder="Metode Asesmen" required>
        <textarea name="asesmen_deskripsi[]" placeholder="Deskripsi" style="height: 60px;"></textarea>
        <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
    `;
    container.appendChild(div);
    console.log('Added asesmen field to ADD modal');
}

// Add Metode Pengujian field for ADD modal
function addMetodePengujianField() {
    const container = document.getElementById('add-metode-pengujian-fields');
    if (!container) {
        console.error('Container add-metode-pengujian-fields not found');
        return;
    }

    const div = document.createElement('div');
    div.className = 'dynamic-field';
    div.innerHTML = `
        <select name="metode_pengujian_skema[]" class="form-control">
            <option value="">-- Pilih Metode --</option>
            <option value="Sertifikasi Jarak Jauh (SJJ)">Sertifikasi Jarak Jauh (SJJ)</option>
            <option value="Metode Paperless (non-kertas)">Metode Paperless (non-kertas)</option>
            <option value="Paper-based (berbasis kertas)">Paper-based (berbasis kertas)</option>
        </select>
        <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
    `;
    container.appendChild(div);
    console.log('Added metode pengujian field to ADD modal');
}


// ===== FUNCTIONS FOR EDIT MODAL =====

// Add Unit field for EDIT modal
function addEditUnitField() {
    const container = document.getElementById('edit-unit-fields');
    if (!container) {
        console.error('Container edit-unit-fields not found');
        return;
    }
    
    const count = container.querySelectorAll('.dynamic-field').length;
    
    const div = document.createElement('div');
    div.className = 'dynamic-field';
    div.innerHTML = `
        <input type="number" name="unit_no[]" value="${count + 1}" style="flex: 0 0 60px;">
        <input type="text" name="kode_unit[]" placeholder="Kode Unit" required>
        <input type="text" name="unit_judul[]" placeholder="Judul Unit" required>
        <input type="text" name="unit_standar[]" placeholder="Standar Kompetensi">
        <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
    `;
    container.appendChild(div);
    console.log('Added unit field to EDIT modal');
}

// Add Persyaratan field for EDIT modal
function addEditPersyaratanField() {
    const container = document.getElementById('edit-persyaratan-fields');
    if (!container) {
        console.error('Container edit-persyaratan-fields not found');
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'dynamic-field';
    div.innerHTML = `
        <textarea name="persyaratan[]" placeholder="Deskripsi persyaratan" required style="flex: 1; height: 60px;"></textarea>
        <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
    `;
    container.appendChild(div);
    console.log('Added persyaratan field to EDIT modal');
}

// Add Dokumen field for EDIT modal
function addEditDokumenField() {
    dokumenCounter++;
    const container = document.getElementById('edit-dokumen-fields');
    if (!container) {
        console.error('Container edit-dokumen-fields not found');
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'dynamic-field';
    div.innerHTML = `
        <input type="text" name="dokumen_nama[]" placeholder="Nama Dokumen" required>
        <div class="checkbox-field">
            <input type="checkbox" name="dokumen_wajib[${dokumenCounter}]" value="1" checked>
            <label>Wajib</label>
        </div>
        <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
    `;
    container.appendChild(div);
    console.log('Added dokumen field to EDIT modal');
}

// Add Asesmen field for EDIT modal
function addEditAsesmenField() {
    const container = document.getElementById('edit-asesmen-fields');
    if (!container) {
        console.error('Container edit-asesmen-fields not found');
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'dynamic-field';
    div.innerHTML = `
        <select name="asesmen_jenis[]" required>
            <option value="">Pilih Jenis Peserta</option>
            <option value="Berpengalaman">Berpengalaman</option>
            <option value="Belum Berpengalaman">Belum Berpengalaman</option>
        </select>
        <input type="text" name="asesmen_metode[]" placeholder="Metode Asesmen" required>
        <textarea name="asesmen_deskripsi[]" placeholder="Deskripsi" style="height: 60px;"></textarea>
        <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
    `;
    container.appendChild(div);
    console.log('Added asesmen field to EDIT modal');
}

// Add Metode Pengujian field for EDIT modal
function addEditMetodePengujianField(selectedValue = "") {
    const container = document.getElementById('edit-metode-pengujian-fields');
    if (!container) {
        console.error('Container edit-metode-pengujian-fields not found');
        return;
    }

    const div = document.createElement('div');
    div.className = 'dynamic-field';
    div.innerHTML = `
        <select name="metode_pengujian_skema[]" class="form-control">
            <option value="">-- Pilih Metode --</option>
            <option value="Sertifikasi Jarak Jauh (SJJ)" ${selectedValue === "Sertifikasi Jarak Jauh (SJJ)" ? "selected" : ""}>Sertifikasi Jarak Jauh (SJJ)</option>
            <option value="Metode Paperless (non-kertas)" ${selectedValue === "Metode Paperless (non-kertas)" ? "selected" : ""}>Metode Paperless (non-kertas)</option>
            <option value="Paper-based (berbasis kertas)" ${selectedValue === "Paper-based (berbasis kertas)" ? "selected" : ""}>Paper-based (berbasis kertas)</option>
        </select>
        <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
    `;
    container.appendChild(div);
    // Jika ada selectedValue, kita set setelah append untuk memastikan elemen ada di DOM
    if (selectedValue) {
        const newSelect = div.querySelector('select');
        newSelect.value = selectedValue;
    }
    console.log('Added metode pengujian field to EDIT modal');
}


// Remove field function (universal)
function removeField(button) {
    if (button && button.parentElement) {
        button.parentElement.remove();
        console.log('Field removed');
    }
}

// ===== MAIN EDIT SKEMA FUNCTION =====

function editSkema(skemaId) {
    console.log('Editing skema with ID:', skemaId);
    
    // Validate input
    if (!skemaId || skemaId === '' || skemaId === 'undefined') {
        console.error('Invalid skema ID provided:', skemaId);
        alert('ID skema tidak valid');
        return;
    }
    
    // Show loading state
    const editButtons = document.querySelectorAll(`button[onclick="editSkema(${skemaId})"]`);
    editButtons.forEach(btn => {
        btn.disabled = true;
        btn.textContent = 'Loading...';
    });
    
    // Create AbortController for request timeout
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
    
    // Build URL with cache busting
    const url = `get_skema.php?id=${encodeURIComponent(skemaId)}&_t=${Date.now()}`;
    console.log('Fetching URL:', url);
    
    // Fetch skema data with improved error handling
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Cache-Control': 'no-cache'
        },
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.text(); // Get as text first for debugging
    })
    .then(text => {
        console.log('Raw response length:', text.length);
        console.log('Raw response (first 500 chars):', text.substring(0, 500));
        
        if (!text.trim()) {
            throw new Error('Empty response received from server');
        }
        
        try {
            const data = JSON.parse(text);
            return data;
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            console.error('Response text:', text);
            throw new Error(`Invalid JSON response: ${parseError.message}`);
        }
    })
    .then(data => {
        console.log('Parsed data:', data);
        
        if (!data) {
            throw new Error('No data received from server');
        }
        
        if (data.success === false) {
            throw new Error(data.message || 'Server returned error without message');
        }
        
        // Process the data
        if (data.skema || data.id) {
            populateEditForm(data);
            resetEditModalTabs();
            openModal('editSkemaModal');
        } else {
            throw new Error('Invalid data structure received from server');
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.error('Error in editSkema:', error);
        
        let errorMessage = 'Terjadi kesalahan saat mengambil data';
        
        if (error.name === 'AbortError') {
            errorMessage = 'Request timeout - server tidak merespons dalam waktu yang ditentukan';
        } else if (error.message.includes('Failed to fetch')) {
            errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet dan coba lagi.';
        } else if (error.message.includes('HTTP')) {
            errorMessage = `Server error: ${error.message}`;
        } else {
            errorMessage = `Error: ${error.message}`;
        }
        
        alert(errorMessage);
    })
    .finally(() => {
        // Re-enable buttons
        editButtons.forEach(btn => {
            btn.disabled = false;
            btn.textContent = 'Edit';
        });
    });
}

// ===== POPULATE FUNCTIONS FOR EDIT MODAL =====

// Populate edit form with data (FIXED VERSION - NO DUPLICATE)
function populateEditForm(data) {
    console.log('Populating edit form with data:', data);
    
    try {
        const skema = data.skema || data;
        
        // Fill basic info with null checks
        safeSetValue('edit_skema_id', skema.id);
        safeSetValue('edit_nama', skema.nama);
        safeSetValue('edit_kode', skema.kode);
        safeSetValue('edit_jenis', skema.jenis);
        safeSetValue('edit_harga', skema.harga);
        safeSetValue('edit_unit_kompetensi', skema.unit_kompetensi);
        safeSetValue('edit_masa_berlaku', skema.masa_berlaku || '');
        safeSetValue('edit_ringkasan', skema.ringkasan);
        
        // Handle gambar
        const gambarField = document.getElementById('edit_existing_gambar');
        const previewDiv = document.getElementById('current-image-preview');
        
        if (gambarField && previewDiv) {
            if (skema.gambar) {
                gambarField.value = skema.gambar;
                previewDiv.innerHTML = `
                    <p>Gambar saat ini:</p>
                    <img src="dksassets/img/${skema.gambar}" alt="Gambar Skema" 
                        style="max-width: 200px; max-height: 150px; display: block; margin-bottom: 10px;">
                    <button type="button" class="btn btn-small btn-danger" 
                            onclick="removeCurrentImage()" style="padding: 5px 10px; font-size: 12px;">
                        Hapus Gambar
                    </button>
                `;
            } else {
                gambarField.value = '';
                previewDiv.innerHTML = '<p>Tidak ada gambar</p>';
            }
        }

        // Handle pemeliharaan with multiple fallbacks
        const pemeliharaanField = document.getElementById('edit_pemeliharaan');
        if (pemeliharaanField) {
            let pemeliharaanValue = '';
            
            if (skema.pemeliharaan) {
                pemeliharaanValue = skema.pemeliharaan;
            } else if (data.pemeliharaan) {
                if (Array.isArray(data.pemeliharaan) && data.pemeliharaan.length > 0) {
                    const firstItem = data.pemeliharaan[0];
                    pemeliharaanValue = firstItem.deskripsi || firstItem.pemeliharaan || firstItem;
                } else if (typeof data.pemeliharaan === 'string') {
                    pemeliharaanValue = data.pemeliharaan;
                }
            }
            
            pemeliharaanField.value = pemeliharaanValue;
            console.log('Pemeliharaan value set to:', pemeliharaanValue);
        }

        // Clear all containers first
        clearEditContainers();

        // Populate related data with improved error handling
        populateEditUnits(data.units || data.unit_kompetensi_data || []);
        populateEditPersyaratan(data.persyaratan || []);
        populateEditDokumen(data.dokumen || data.dokumen_persyaratan || []);
        populateEditAsesmen(data.asesmen || data.metode_asesmen || []);
        
        // Populate Metode Pengujian (sekarang array)
        const metodePengujianContainer = document.getElementById('edit-metode-pengujian-fields');
        if (metodePengujianContainer) {
            metodePengujianContainer.innerHTML = ''; // Kosongkan dulu
            const metodePengujianData = data.metode_pengujian_skema || (data.skema ? data.skema.metode_pengujian : []);
            if (Array.isArray(metodePengujianData) && metodePengujianData.length > 0) {
                metodePengujianData.forEach(metode => {
                    if (typeof metode === 'string') { // Jika array of strings
                        addEditMetodePengujianField(metode);
                    } else if (typeof metode === 'object' && metode.metode_pengujian) { // Jika array of objects
                        addEditMetodePengujianField(metode.metode_pengujian);
                    }
                });
            } else {
                 addEditMetodePengujianField(); // Tambahkan satu field kosong jika tidak ada data
            }
        }

        console.log('Edit form populated successfully');
        
    } catch (error) {
        console.error('Error populating edit form:', error);
        throw new Error('Failed to populate form: ' + error.message);
    }
}

// Function to populate unit kompetensi fields in edit form (FIXED VERSION)
function populateEditUnits(units) {
    console.log('Populating edit units:', units);
    
    const unitContainer = document.getElementById('edit-unit-fields');
    if (!unitContainer) {
        console.error('Edit unit container not found');
        return;
    }
    
    unitContainer.innerHTML = ''; // Clear existing content
    
    if (units && Array.isArray(units) && units.length > 0) {
        units.forEach((unit, index) => {
            const div = document.createElement('div');
            div.className = 'dynamic-field';
            
            // Handle different possible data structures
            const unitNo = unit.no_urut || unit.no || unit.unit_no || (index + 1);
            const kodeUnit = unit.kode_unit || unit.kode || '';
            const judulUnit = unit.judul_unit || unit.judul || unit.nama || '';
            const standarKompetensi = unit.standar_kompetensi || unit.standar || '';
            
            div.innerHTML = `
                <input type="number" name="unit_no[]" value="${unitNo}" style="flex: 0 0 60px;">
                <input type="text" name="kode_unit[]" value="${kodeUnit}" placeholder="Kode Unit" required>
                <input type="text" name="unit_judul[]" value="${judulUnit}" placeholder="Judul Unit" required>
                <input type="text" name="unit_standar[]" value="${standarKompetensi}" placeholder="Standar Kompetensi">
                <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
            `;
            unitContainer.appendChild(div);
        });
        console.log(`Added ${units.length} unit fields to edit form`);
    } else {
        // Add at least one empty field if no units exist
        console.log('No units found, adding empty field');
        addEditUnitField();
    }
}

// Function to populate persyaratan fields in edit form (FIXED VERSION)
function populateEditPersyaratan(persyaratan) {
    console.log('Populating edit persyaratan:', persyaratan);
    
    const container = document.getElementById('edit-persyaratan-fields');
    if (!container) {
        console.error('Edit persyaratan container not found');
        return;
    }
    
    container.innerHTML = ''; // Clear existing content
    
    if (persyaratan && Array.isArray(persyaratan) && persyaratan.length > 0) {
        persyaratan.forEach(req => {
            const div = document.createElement('div');
            div.className = 'dynamic-field';
            
            // Handle different possible data structures
            const deskripsi = req.deskripsi || req.persyaratan || req.nama || req;
            
            div.innerHTML = `
                <textarea name="persyaratan[]" required style="flex: 1; height: 60px;">${deskripsi}</textarea>
                <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
            `;
            container.appendChild(div);
        });
        console.log(`Added ${persyaratan.length} persyaratan fields to edit form`);
    } else {
        // Always ensure at least one field exists
        console.log('No persyaratan found, adding empty field');
        addEditPersyaratanField();
    }
}

// Function to populate dokumen fields in edit form (FIXED VERSION)
function populateEditDokumen(dokumen) {
    console.log('Populating edit dokumen:', dokumen);
    
    const container = document.getElementById('edit-dokumen-fields');
    if (!container) {
        console.error('Edit dokumen container not found');
        return;
    }
    
    container.innerHTML = ''; // Clear existing content
    
    if (dokumen && Array.isArray(dokumen) && dokumen.length > 0) {
        dokumen.forEach((doc, index) => {
            const div = document.createElement('div');
            div.className = 'dynamic-field';
            
            // Handle different possible data structures
            const nama = doc.nama || doc.nama_dokumen || doc.dokumen || '';
            const wajib = doc.wajib == 1 || doc.wajib === true || doc.wajib === '1';
            
            div.innerHTML = `
                <input type="text" name="dokumen_nama[]" value="${nama}" placeholder="Nama Dokumen" required>
                <div class="checkbox-field">
                    <input type="checkbox" name="dokumen_wajib[${index}]" value="1" ${wajib ? 'checked' : ''}>
                    <label>Wajib</label>
                </div>
                <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
            `;
            container.appendChild(div);
        });
        console.log(`Added ${dokumen.length} dokumen fields to edit form`);
    }
}

// Function to populate asesmen fields in edit form (FIXED VERSION)
function populateEditAsesmen(asesmen) {
    console.log('Populating edit asesmen:', asesmen);
    
    const container = document.getElementById('edit-asesmen-fields');
    if (!container) {
        console.error('Edit asesmen container not found');
        return;
    }
    
    container.innerHTML = ''; // Clear existing content
    
    if (asesmen && Array.isArray(asesmen) && asesmen.length > 0) {
        asesmen.forEach(asesmenItem => {
            const div = document.createElement('div');
            div.className = 'dynamic-field';
            
            // Handle different possible data structures
            const jenisPeserta = asesmenItem.jenis_peserta || asesmenItem.jenis || '';
            const metode = asesmenItem.metode || asesmenItem.metode_asesmen || '';
            const deskripsi = asesmenItem.deskripsi || asesmenItem.keterangan || '';
            
            div.innerHTML = `
                <select name="asesmen_jenis[]" required>
                    <option value="">Pilih Jenis Peserta</option>
                    <option value="Berpengalaman" ${jenisPeserta === 'Berpengalaman' ? 'selected' : ''}>Berpengalaman</option>
                    <option value="Belum Berpengalaman" ${jenisPeserta === 'Belum Berpengalaman' ? 'selected' : ''}>Belum Berpengalaman</option>
                </select>
                <input type="text" name="asesmen_metode[]" value="${metode}" placeholder="Metode Asesmen" required>
                <textarea name="asesmen_deskripsi[]" placeholder="Deskripsi" style="height: 60px;">${deskripsi}</textarea>
                <button type="button" class="remove-btn" onclick="removeField(this)">×</button>
            `;
            container.appendChild(div);
        });
        console.log(`Added ${asesmen.length} asesmen fields to edit form`);
    }
}

// ===== UTILITY FUNCTIONS =====

// Safely set form field value
function safeSetValue(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.value = value || '';
        console.log(`Set ${elementId} to:`, value);
    } else {
        console.warn(`Element with ID '${elementId}' not found`);
    }
}

// Clear all edit containers
function clearEditContainers() {
    const containers = [
        'edit-unit-fields',
        'edit-persyaratan-fields', 
        'edit-dokumen-fields',
        'edit-asesmen-fields'
    ];
    
    containers.forEach(id => {
        const container = document.getElementById(id);
        if (container) {
            container.innerHTML = '';
            console.log(`Cleared container: ${id}`);
        }
    });
}

// Function to remove current image preview
function removeCurrentImage() {
    const existingGambarField = document.getElementById('edit_existing_gambar');
    const previewDiv = document.getElementById('current-image-preview');
    
    if (existingGambarField) {
        existingGambarField.value = '';
    }
    
    if (previewDiv) {
        previewDiv.innerHTML = '<p>Gambar akan dihapus saat disimpan</p>';
    }
    
    // Also clear the file input
    const fileInput = document.getElementById('edit_gambar');
    if (fileInput) {
        fileInput.value = '';
    }
    
    console.log('Current image removed');
}

// Reset edit modal tabs to initial state
function resetEditModalTabs() {
    try {
        // Reset all tab content
        document.querySelectorAll('#editSkemaModal .tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Reset all tab buttons
        document.querySelectorAll('#editSkemaModal .tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Activate first tab (unit kompetensi)
        const firstTab = document.getElementById('edit-tab-unit');
        const firstButton = document.querySelector('#editSkemaModal .tab-btn');
        
        if (firstTab) firstTab.classList.add('active');
        if (firstButton) firstButton.classList.add('active');
        
        console.log('Edit modal tabs reset successfully');
        
    } catch (error) {
        console.error('Error resetting edit modal tabs:', error);
    }
}

// Delete skema function
function deleteSkema(skemaId) {
    const deleteIdField = document.getElementById('delete_skema_id');
    if (deleteIdField) {
        deleteIdField.value = skemaId;
        openModal('deleteSkemaModal');
    } else {
        console.error('Delete skema ID field not found');
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
}

// ===== ADDITIONAL UTILITY FUNCTIONS =====

// Function to validate form data
function validateFormData(formType) {
    const prefix = formType === 'add' ? '' : 'edit-';
    const requiredFields = [
        { id: `${prefix}nama`, name: 'Nama Skema' },
        { id: `${prefix}kode`, name: 'Kode Skema' },
        { id: `${prefix}jenis`, name: 'Jenis Skema' },
        { id: `${prefix}harga`, name: 'Harga' }
    ];
    
    const errors = [];
    
    // Validate required fields
    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (element && (!element.value || element.value.trim() === '')) {
            errors.push(`${field.name} harus diisi`);
        }
    });
    
    // Validate unit kompetensi
    const unitFields = document.querySelectorAll(`#${prefix}unit-fields .dynamic-field`);
    if (unitFields.length === 0) {
        errors.push('Minimal harus ada satu unit kompetensi');
    }
    
    // Validate persyaratan
    const persyaratanFields = document.querySelectorAll(`#${prefix}persyaratan-fields .dynamic-field`);
    if (persyaratanFields.length === 0) {
        errors.push('Minimal harus ada satu persyaratan');
    }
    
    return errors;
}

// Function to show validation errors
function showValidationErrors(errors) {
    if (errors.length > 0) {
        const errorMessage = 'Terdapat kesalahan dalam form:\n\n' + errors.join('\n');
        alert(errorMessage);
        return false;
    }
    return true;
}

// Function to prepare form data before submission
function prepareFormData(formType) {
    try {
        const errors = validateFormData(formType);
        if (!showValidationErrors(errors)) {
            return false;
        }
        
        console.log(`Form ${formType} validation passed`);
        return true;
    } catch (error) {
        console.error('Error preparing form data:', error);
        alert('Terjadi kesalahan saat memproses data form');
        return false;
    }
}

// Function to handle form submission
function handleFormSubmission(event, formType) {
    if (!prepareFormData(formType)) {
        event.preventDefault();
        return false;
    }
    
    // Show loading state
    const submitButton = event.target.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan...';
    }
    
    return true;
}

// Function to reset form
function resetForm(formType) {
    const modalId = formType === 'add' ? 'addSkemaModal' : 'editSkemaModal';
    const form = document.querySelector(`#${modalId} form`);
    
    if (form) {
        form.reset();
        
        // Clear dynamic fields
        const prefix = formType === 'add' ? '' : 'edit-';
        const containers = [
            `${prefix}unit-fields`,
            `${prefix}persyaratan-fields`,
            `${prefix}dokumen-fields`,
            `${prefix}asesmen-fields`,
            `${prefix}metode-pengujian-fields` // Tambahkan kontainer metode pengujian
        ];
        
        containers.forEach(containerId => {
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = '';
            }
        });
        
        // Reset tabs to first tab
        if (formType === 'add') {
            showAddTab('unit');
            const addMetodeContainer = document.getElementById('add-metode-pengujian-fields');
            if (addMetodeContainer) {
                addMetodeContainer.innerHTML = ''; // Kosongkan
                addMetodePengujianField(); // Tambah satu field default
            }
        } else { // edit
            showEditTab('unit');
            const editMetodeContainer = document.getElementById('edit-metode-pengujian-fields');
            if (editMetodeContainer) {
                editMetodeContainer.innerHTML = ''; // Kosongkan
                 addEditMetodePengujianField(); // Tambah satu field default
            }
        }
        
        console.log(`${formType} form reset successfully`);
    }
}

// Function to handle image preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview" 
                     style="max-width: 200px; max-height: 150px; display: block; margin-top: 10px;">
            `;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '';
    }
}

// Function to format currency input
function formatCurrency(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
    }
    input.value = value;
}

// Function to handle number validation
function validateNumber(input, min = 0, max = null) {
    let value = parseInt(input.value);
    
    if (isNaN(value) || value < min) {
        input.value = min;
    } else if (max !== null && value > max) {
        input.value = max;
    }
}

// Function to auto-generate kode skema
function generateKodeSkema() {
    const namaInput = document.getElementById('nama') || document.getElementById('edit_nama');
    const kodeInput = document.getElementById('kode') || document.getElementById('edit_kode');
    
    if (namaInput && kodeInput && namaInput.value) {
        const nama = namaInput.value.trim();
        const words = nama.split(' ');
        let kode = '';
        
        // Take first letter of each word, max 5 characters
        for (let i = 0; i < Math.min(words.length, 5); i++) {
            if (words[i]) {
                kode += words[i][0].toUpperCase();
            }
        }
        
        // Add timestamp to make it unique
        const timestamp = Date.now().toString().slice(-4);
        kode += timestamp;
        
        kodeInput.value = kode;
    }
}

// Function to copy data from one field to another
function copyFieldValue(sourceId, targetId) {
    const source = document.getElementById(sourceId);
    const target = document.getElementById(targetId);
    
    if (source && target) {
        target.value = source.value;
    }
}

// Function to toggle field visibility
function toggleFieldVisibility(fieldId, show = true) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.style.display = show ? 'block' : 'none';
    }
}

// Function to count dynamic fields
function countDynamicFields(containerId) {
    const container = document.getElementById(containerId);
    return container ? container.querySelectorAll('.dynamic-field').length : 0;
}

// Function to export skema data (optional feature)
function exportSkemaData(skemaId) {
    if (!skemaId) {
        alert('ID skema tidak valid');
        return;
    }
    
    const url = `export_skema.php?id=${encodeURIComponent(skemaId)}`;
    window.open(url, '_blank');
}

// Function to print skema details
function printSkemaDetails(skemaId) {
    if (!skemaId) {
        alert('ID skema tidak valid');
        return;
    }
    
    const url = `print_skema.php?id=${encodeURIComponent(skemaId)}`;
    const printWindow = window.open(url, 'printWindow', 'width=800,height=600');
    
    if (printWindow) {
        printWindow.onload = function() {
            printWindow.print();
        };
    }
}

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Skema JavaScript loaded successfully');
    
    // Add event listeners for form submissions
    const addForm = document.querySelector('#addSkemaModal form');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            return handleFormSubmission(e, 'add');
        });
    }
    
    const editForm = document.querySelector('#editSkemaModal form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            return handleFormSubmission(e, 'edit');
        });
    }
    
    // Add event listeners for number validation
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateNumber(this);
        });
    });
    
    // Add event listeners for image preview
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.id.replace('_', '_preview_');
            previewImage(this, previewId);
        });
    });
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Escape key to close modals
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal[style*="block"]');
            openModals.forEach(modal => {
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            });
        }
        
        // Ctrl+S to save (prevent default browser save)
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            const activeModal = document.querySelector('.modal[style*="block"]');
            if (activeModal) {
                const submitButton = activeModal.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.click();
                }
            }
        }
    });
    
    console.log('Event listeners initialized');
});

// Export functions for global access
window.adminSkemaJS = {
    openModal,
    closeModal,
    showAddTab,
    showEditTab,
    editSkema,
    deleteSkema,
    addUnitField,
    addPersyaratanField,
    addDokumenField,
    addAsesmenField,
    addEditUnitField,
    addEditPersyaratanField,
    addEditDokumenField,
    addEditAsesmenField,
    addMetodePengujianField, // Tambahkan fungsi baru
    addEditMetodePengujianField, // Tambahkan fungsi baru
    removeField,
    resetForm,
    generateKodeSkema,
    exportSkemaData,
    printSkemaDetails
};

console.log('Admin Skema JavaScript module loaded');
</script>

</body>
</html>