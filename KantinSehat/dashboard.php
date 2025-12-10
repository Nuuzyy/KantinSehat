<?php
session_start();

// Cek login
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Koneksi database
$conn = mysqli_connect('localhost', 'root', '', 'kantin_sehat');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Ambil data produk
$sql = "SELECT * FROM Products ORDER BY jenis, nama";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Produk - Kantin Sehat</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        
        :root {
            --primary-color: #11cee7ff;
            --secondary-color: #6c757d;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .product-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(235, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .price-tag {
            color: green;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .badge-jenis {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        
        .minuman { background-color: #3e86e4ff; }
        .makanan { background-color: #f3d16aff; }
        .snack { background-color: #f3a05cff; }
        
        .btn-pilih {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 8px 20px;
            font-weight: bold;
        }
        
        .btn-pilih:hover {
            background-color: #20b462ff;
            border-color: #1e7e3bff;
        }
        
        .user-info {
            color: white;
            font-weight: bold;
        }
        
        .section-title {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-utensils me-2"></i>Kantin Sehat
            </a>
            
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link user-info">
                    <i class="fas fa-user me-1"></i> <?php echo $_SESSION['user']; ?>
                </span>
                <a class="nav-item nav-link" href="view_transactions.php">
                    <i class="fas fa-history me-1"></i> Transaksi
                </a>
                <a class="nav-item nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mt-4">
        <h2 class="section-title">
            <i class="fas fa-list-alt me-2"></i>Menu Produk Kantin
        </h2>
        
        <!-- Info Login -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Selamat datang <strong><?php echo $_SESSION['user']; ?></strong>! Pilih produk untuk memulai transaksi.
        </div>
        
        <!-- Products Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php while ($row = mysqli_fetch_assoc($result)): 
                // Tentukan warna badge berdasarkan jenis
                $badge_class = '';
                if ($row['jenis'] == 'Minuman') $badge_class = 'minuman';
                elseif ($row['jenis'] == 'Makanan') $badge_class = 'makanan';
                else $badge_class = 'snack';
            ?>
            <div class="col">
                <div class="card product-card">
                    <div class="card-header">
                        <?php echo $row['nama']; ?>
                        <span class="badge badge-jenis <?php echo $badge_class; ?>">
                            <?php echo $row['jenis']; ?>
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <div class="mb-3">
                            <h5 class="price-tag">
                                Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?>
                            </h5>
                            <p class="text-muted mb-1">
                                <i class="fas fa-weight me-1"></i> Ukuran: <?php echo $row['ukuran']; ?>
                            </p>
                            <p class="text-muted">
                                <i class="fas fa-boxes me-1"></i> Stok: <?php echo $row['stok']; ?>
                            </p>
                        </div>
                        
                        <div class="d-grid">
                            <a href="transaction.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-pilih">
                                <i class="fas fa-shopping-cart me-2"></i> PILIH PRODUK
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <!-- Informasi Tambahan -->
        <div class="row mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-question-circle me-2"></i>Petunjuk Penggunaan
                    </div>
                    <div class="card-body">
                        <ol>
                            <li>Pilih produk yang ingin dibeli</li>
                            <li>Tentukan jumlah pembelian</li>
                            <li>Sistem akan menghitung total otomatis</li>
                            <li>Simpan transaksi</li>
                            <li>Lihat riwayat transaksi di menu Transaksi</li>
                        </ol>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-clipboard-check me-2"></i>Fitur Aplikasi
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Login dengan validasi</li>
                            <li>5 produk sesuai soal ujian</li>
                            <li>Perhitungan otomatis</li>
                            <li>CRUD: Create dan Read transaksi</li>
                            <li>Desain responsif dan user-friendly</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="mt-5 py-3 bg-dark text-white text-center">
        <div class="container">
            <small>&copy; 2025 Aplikasi Kantin Sehat - Ujian Praktik RPL Kelas XI</small>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>