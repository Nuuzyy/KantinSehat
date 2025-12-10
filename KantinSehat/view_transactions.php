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

// Ambil data transaksi
$sql = "SELECT * FROM Transactions ORDER BY tanggal DESC";
$result = mysqli_query($conn, $sql);

// Hitung total keseluruhan
$total_sql = "SELECT SUM(total) as total_keseluruhan FROM Transactions";
$total_result = mysqli_query($conn, $total_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_keseluruhan = $total_row['total_keseluruhan'] ?: 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Kantin Sehat</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #28a745;
        }
        
        .summary-card {
            border-radius: 10px;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .total-transactions {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .total-revenue {
            background: linear-gradient(135deg, var(--primary-color) 0%, #20c997 100%);
        }
        
        .table-transactions {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .table-transactions thead {
            background-color: var(--primary-color);
            color: white;
        }
        
        .badge-success {
            background-color: var(--primary-color);
        }
        
        .no-data {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        
        .no-data i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-utensils me-2"></i>Kantin Sehat
            </a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-white">
                    <i class="fas fa-user me-1"></i> <?php echo $_SESSION['user']; ?>
                </span>
                <a class="nav-item nav-link" href="dashboard.php">
                    <i class="fas fa-home me-1"></i> Menu
                </a>
                <a class="nav-item nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mt-4">
        <h2 class="mb-4">
            <i class="fas fa-history me-2"></i>Riwayat Transaksi
        </h2>
        
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="summary-card total-transactions">
                    <h5><i class="fas fa-receipt me-2"></i>Total Transaksi</h5>
                    <h3><?php echo mysqli_num_rows($result); ?></h3>
                    <p class="mb-0">Jumlah transaksi yang tercatat</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="summary-card total-revenue">
                    <h5><i class="fas fa-money-bill-wave me-2"></i>Total Pendapatan</h5>
                    <h3>Rp<?php echo number_format($total_keseluruhan, 0, ',', '.'); ?></h3>
                    <p class="mb-0">Total dari semua transaksi</p>
                </div>
            </div>
        </div>
        
        <!-- Transactions Table -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Daftar Transaksi</h5>
            </div>
            <div class="card-body p-0">
                <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-transactions mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>Harga Satuan</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?>
                                </td>
                                <td><?php echo $row['nama_produk']; ?></td>
                                <td>Rp<?php echo number_format($row['harga_satuan'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="badge bg-primary rounded-pill">
                                        <?php echo $row['qty']; ?> pcs
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-success">
                                        Rp<?php echo number_format($row['total'], 0, ',', '.'); ?>
                                    </strong>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="5" class="text-end"><strong>Total Keseluruhan:</strong></td>
                                <td>
                                    <strong class="text-success">
                                        Rp<?php echo number_format($total_keseluruhan, 0, ',', '.'); ?>
                                    </strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-inbox"></i>
                    <h4>Belum ada transaksi</h4>
                    <p>Mulai transaksi pertama Anda di halaman menu produk</p>
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Buat Transaksi
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="mt-4 d-flex justify-content-between">
            <a href="dashboard.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Menu
            </a>
            <?php if (mysqli_num_rows($result) > 0): ?>
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Cetak Laporan
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="mt-5 py-3 bg-dark text-white text-center">
        <div class="container">
            <small>&copy; 2024 Kantin Sehat - CRUD Read Transaksi</small>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>