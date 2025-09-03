<?php
require_once 'config.php';
require_once 'skema_functions.php';

$skemaManager = new SkemaManager($conn);

// Get skema ID from URL parameter
$skema_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$skema_id) {
    header('Location: sertifikasi.php');
    exit;
}

// Get skema details
$skema = $skemaManager->getSkemaById($skema_id);

if (!$skema) {
    header('Location: sertifikasi.php');
    exit;
}

// Get unit kompetensi for this skema
$unit_kompetensi = $skemaManager->getUnitKompetensiBySkemaId($skema_id);

// Get persyaratan
$persyaratan = $skemaManager->getPersyaratanBySkemaId($skema_id);

// Get dokumen persyaratan
$dokumen_persyaratan = $skemaManager->getDokumenPersyaratanBySkemaId($skema_id);

// Get metode asesmen
$metode_asesmen = $skemaManager->getMetodeAsesmenBySkemaId($skema_id);

// Get metode pengujian (dari tabel skema_metode_pengujian)
$metode_pengujian_list = $skemaManager->getMetodePengujianBySkemaId($skema_id);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($skema['nama']); ?> - LSP DKS</title>
    <link rel="stylesheet" href="css/styles.css" />
    <style>
        .tab-active {
            background-color: #007BFF;
            color: white;
        }

        .tab-content-box {
            display: none;
        }

        .tab-content-box.active {
            display: block;
        }

        .tab {
            padding: 10px 15px;
            background: #eee;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
        }

        .tab:hover {
            background-color: #ddd;
        }

        .skema-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .skema-title {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            border-bottom: 3px solid #3498db;
            padding-bottom: 0.5rem;
        }

        .skema-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .skema-info-table td {
            padding: 0.8rem 0;
            border-bottom: 1px solid #ecf0f1;
        }

        .skema-info-table td:first-child {
            font-weight: bold;
            width: 20%;
            color: #2c3e50;
        }

        .price {
            color: #e74c3c;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .tab-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }

        .unit-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .unit-table th,
        .unit-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .unit-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }

        .unit-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .unit-table tr:hover {
            background-color: #e8f4fd;
        }

        .requirement-list {
            list-style-type: none;
            padding: 0;
        }

        .requirement-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #ecf0f1;
            position: relative;
            padding-left: 1.5rem;
        }

        .requirement-list li:before {
            content: "✓";
            color: #27ae60;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .method-section {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }

        .method-section h4 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .back-button {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 1rem;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }

        .register-button {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(45deg, #3498db, #2c3e50);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 1rem;
            font-weight: bold;
            transition: all 0.3s;
        }

        .register-button:hover {
            background: linear-gradient(45deg, #2980b9, #34495e);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .skema-info-table td:first-child {
                width: 30%;
            }
            
            .tab-scroll {
                overflow-x: auto;
            }
            
            .tabs {
                min-width: max-content;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#page-top">lsp-dks</a>
            <button class="navbar-toggler text-uppercase font-weight-bold bg-primary text-white rounded" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive"
                aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item mx-0 mx-lg-1">
                        <a class="nav-link py-3 px-0 px-lg-3 rounded" href="index.php">Home</a>
                    </li>
                    <li class="nav-item mx-0 mx-lg-1">
                        <a class="nav-link py-3 px-0 px-lg-3 rounded" href="sertifikasi.php">Sertifikasi</a>
                    </li>
                    <li class="nav-item mx-0 mx-lg-1">
                        <a class="nav-link py-3 px-0 px-lg-3 rounded" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item mx-0 mx-lg-1">
                        <a class="nav-link py-3 px-0 px-lg-3 rounded" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item mx-0 mx-lg-1">
                        <a class="btn btn-outline-light ms-3" href="#register">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 120px; padding: 20px;">
        <a href="sertifikasi.php" class="back-button">← Kembali ke Daftar Sertifikasi</a>
        
        <!-- Section Skema -->
        <section class="skema-section">
            <div class="text-center mb-4">
                <img src="<?php echo $skemaManager->getGambarPath($skema['gambar']); ?>" alt="<?php echo htmlspecialchars($skema['nama']); ?>" class="img-fluid rounded" style="max-height: 300px;">
            </div>
            <h2 class="skema-title"><?php echo htmlspecialchars($skema['nama']); ?></h2>
            <table class="skema-info-table">
                <tr>
                    <td>Nama</td>
                    <td>: <?php echo htmlspecialchars($skema['nama']); ?></td>
                </tr>
                <tr>
                    <td>Kode</td>
                    <td>: <?php echo htmlspecialchars($skema['kode']); ?></td>
                </tr>
                <tr>
                    <td>Jenis</td>
                    <td>: <?php echo htmlspecialchars($skema['jenis']); ?></td>
                </tr>
                <tr>
                    <td>Harga</td>
                    <td>: <span class="price">Rp <?php echo number_format($skema['harga'], 0, ',', '.'); ?></span></td>
                </tr>
                <tr>
                    <td>Unit Kompetensi</td>
                    <td>: <?php echo $skema['unit_kompetensi']; ?> Unit</td>
                </tr>
                <tr>
                    <td>Masa Berlaku</td>
                    <td>: <?php echo $skema['masa_berlaku']; ?></td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>Ringkasan</td>
                    <td>: <?php echo nl2br(htmlspecialchars($skema['ringkasan'])); ?></td>
                </tr>
            </table>
            
            <a href="#register" class="register-button">Daftar Sekarang</a>
        </section>

        <!-- Section Tabs -->
        <section class="tab-section">
            <div class="tab-scroll" style="display: flex; gap: 10px; overflow-x: auto;">
                <div class="tabs" style="display: flex; gap: 5px;">
                    <div class="tab tab-active" onclick="showTab(event, 'unit')">Unit Kompetensi</div>
                    <div class="tab" onclick="showTab(event, 'persyaratan')">Persyaratan</div>
                    <div class="tab" onclick="showTab(event, 'dokumen')">Dokumen Persyaratan</div>
                    <div class="tab" onclick="showTab(event, 'asesmen')">Metode Asesmen</div>
                    <div class="tab" onclick="showTab(event, 'metode_pengujian')">Metode Pengujian</div>
                    <div class="tab" onclick="showTab(event, 'pemeliharaan')">Pemeliharaan</div>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <!-- Unit Kompetensi -->
                <div class="tab-content-box active" id="tab-unit">
                    <?php if (!empty($unit_kompetensi)): ?>
                        <table class="unit-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Unit</th>
                                    <th>Judul Unit</th>
                                    <th>Standar Kompetensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($unit_kompetensi as $index => $unit): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($unit['kode_unit']); ?></td>
                                    <td><?php echo htmlspecialchars($unit['judul_unit']); ?></td>
                                    <td><?php echo htmlspecialchars($unit['standar_kompetensi'] ?? '-'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Informasi unit kompetensi belum tersedia.</p>
                    <?php endif; ?>
                </div>

                <!-- Persyaratan -->
                <div class="tab-content-box" id="tab-persyaratan">
                    <?php if (!empty($persyaratan)): ?>
                        <ul class="requirement-list">
                            <?php foreach ($persyaratan as $syarat): ?>
                            <li><?php echo htmlspecialchars($syarat['deskripsi']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <ul class="requirement-list">
                            <li>Minimal Lulusan SMA/SMK Sederajat</li>
                            <li>Berpengalaman kerja atau pelatihan bidang terkait</li>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Dokumen Persyaratan -->
                <div class="tab-content-box" id="tab-dokumen">
                    <?php if (!empty($dokumen_persyaratan)): ?>
                        <ul class="requirement-list">
                            <?php foreach ($dokumen_persyaratan as $dokumen): ?>
                            <li><?php echo htmlspecialchars($dokumen['nama_dokumen']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <ul class="requirement-list">
                            <li>Fotokopi Ijazah terakhir</li>
                            <li>Fotokopi KTP</li>
                            <li>Pas Foto 3x4 (2 lembar)</li>
                            <li>Portofolio (jika ada pengalaman)</li>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Metode Asesmen -->
                <div class="tab-content-box" id="tab-asesmen">
                    <?php if (!empty($metode_asesmen)): ?>
                        <?php 
                        $grouped_methods = [];
                        foreach ($metode_asesmen as $metode) {
                            $grouped_methods[$metode['jenis_peserta']][] = $metode;
                        }
                        ?>
                        <?php foreach ($grouped_methods as $kategori => $methods): ?>
                        <div class="method-section">
                            <h4><?php echo htmlspecialchars($kategori); ?></h4>
                            <?php foreach ($methods as $method): ?>
                            <p><strong>Metode:</strong> <?php echo htmlspecialchars($method['metode']); ?></p>
                            <?php if ($method['deskripsi']): ?>
                            <p><?php echo nl2br(htmlspecialchars($method['deskripsi'])); ?></p>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="method-section">
                            <h4>Berpengalaman</h4>
                            <p><strong>Metode:</strong> Asesmen Portofolio dan Wawancara</p>
                        </div>
                        <div class="method-section">
                            <h4>Belum Berpengalaman</h4>
                            <p><strong>Metode:</strong> Observasi Demonstrasi dan Tes Lisan</p>
                        </div>
                    <?php endif; ?>
                </div>

               <!-- Pemeliharaan - Versi Dinamis -->
<div class="tab-content-box" id="tab-pemeliharaan">
    <?php 
    // Ambil data pemeliharaan dari database
    $pemeliharaan = $skemaManager->getPemeliharaanBySkemaId($skema_id);
    
    if (!empty($pemeliharaan)): ?>
        <?php foreach ($pemeliharaan as $item): ?>
        <div class="method-section">
            <?php echo nl2br(htmlspecialchars($item['deskripsi'])); ?>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Default jika tidak ada data di database -->
        <div class="method-section">
            <h4>Pemeliharaan Sertifikasi</h4>
            <p>Untuk mempertahankan sertifikasi, pemegang sertifikat dapat mengikuti kegiatan asesmen ulang (re-asesmen) sebelum masa berlaku habis.</p>
            <p><strong>Masa Berlaku:</strong> <?php echo htmlspecialchars($skema['masa_berlaku']); ?> sejak tanggal diterbitkan.</p>
            <p><strong>Proses Pemeliharaan:</strong></p>
            <ul>
                <li>Pengajuan permohonan re-asesmen 3 bulan sebelum masa berlaku habis</li>
                <li>Penyerahan bukti pengembangan kompetensi berkelanjutan</li>
                <li>Mengikuti asesmen pemeliharaan sesuai dengan metode yang ditentukan</li>
            </ul>
        </div>
    <?php endif; ?>
</div>

                <!-- Metode Pengujian Tab Content -->
                <div class="tab-content-box" id="tab-metode_pengujian">
                    <h3>Metode Pengujian yang Dapat Dipilih</h3>
                    <?php if (!empty($metode_pengujian_list)): ?>
                        <ul class="requirement-list">
                            <?php foreach ($metode_pengujian_list as $metode_uji): ?>
                            <li><?php echo htmlspecialchars($metode_uji); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Informasi metode pengujian belum ditentukan untuk skema ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <script>
        function showTab(event, tabName) {
            // Reset all tab buttons
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('tab-active'));

            // Reset all tab content
            document.querySelectorAll('.tab-content-box').forEach(content => content.classList.remove('active'));

            // Activate selected
            document.querySelector(`#tab-${tabName}`).classList.add('active');
            event.target.classList.add('tab-active');
        }
    </script>

<?php include 'includes/footer.php'; ?>