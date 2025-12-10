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

// Ambil data produk berdasarkan ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$sql = "SELECT * FROM Products WHERE id = $product_id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Produk tidak ditemukan!");
}

// Proses simpan transaksi
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $qty = intval($_POST['qty']);
    $total = $product['harga'] * $qty;
    
    // Simpan ke database
    $insert_sql = "INSERT INTO Transactions (product_id, nama_produk, harga_satuan, qty, total) 
                   VALUES ($product_id, '{$product['nama']}', {$product['harga']}, $qty, $total)";
    
    if (mysqli_query($conn, $insert_sql)) {
        $message = 'success';
        // Kurangi stok
        $update_stok = "UPDATE Products SET stok = stok - $qty WHERE id = $product_id";
        mysqli_query($conn, $update_stok);
    } else {
        $message = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Kantin Sehat</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #11cee7ff;
        }
        
        .transaction-card {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border: none;
        }
        
        .product-detail {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        
        .total-box {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px;
            padding: 15px;
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .btn-simpan {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: bold;
        }
        
        .btn-kembali {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .input-qty {
            max-width: 150px;
            font-size: 1.2rem;
            text-align: center;
        }
        
        .stepper {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .step {
            display: flex;
            align-items: center;
            color: #7d6c6cff;
        }
        
        .step.active {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }
        
        .step.active .step-number {
            background-color: var(--primary-color);
            color: white;
        }
        
        .step-line {
            flex-grow: 1;
            height: 2px;
            background-color: #e9ecef;
            margin: 0 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Menu
            </a>
            <span class="navbar-text text-white">
                <i class="fas fa-user me-1"></i> <?php echo $_SESSION['user']; ?>
            </span>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Stepper -->
        <div class="stepper">
            <div class="step active">
                <div class="step-number">1</div>
                <div>Pilih Produk</div>
            </div>
            <div class="step-line"></div>
            <div class="step active">
                <div class="step-number">2</div>
                <div>Input Transaksi</div>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <div class="step-number">3</div>
                <div>Selesai</div>
            </div>
        </div>
        
        <!-- Success Message -->
        <?php if ($message == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            Transaksi berhasil disimpan! Total: Rp<?php echo number_format($total, 0, ',', '.'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php elseif ($message == 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            Gagal menyimpan transaksi!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card transaction-card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-cash-register me-2"></i>Transaksi Pembelian</h4>
                    </div>
                    
                    <div class="card-body">
                        <!-- Product Info -->
                        <div class="product-detail mb-4">
                            <h5><?php echo $product['nama']; ?></h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><i class="fas fa-tag me-2"></i> Jenis: <?php echo $product['jenis']; ?></p>
                                    <p class="mb-1"><i class="fas fa-weight me-2"></i> Ukuran: <?php echo $product['ukuran']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><i class="fas fa-boxes me-2"></i> Stok: <?php echo $product['stok']; ?></p>
                                    <p class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Harga Satuan: 
                                        <strong class="text-primary">Rp<?php echo number_format($product['harga'], 0, ',', '.'); ?></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Transaction Form -->
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="qty" class="form-label h5">
                                    <i class="fas fa-calculator me-2"></i>Jumlah (Qty)
                                </label>
                                <div class="d-flex align-items-center">
                                    <input type="number" 
                                           class="form-control form-control-lg input-qty" 
                                           id="qty" 
                                           name="qty" 
                                           min="1" 
                                           max="<?php echo $product['stok']; ?>"
                                           value="1" 
                                           required>
                                    <span class="ms-3">pcs</span>
                                </div>
                                <div class="form-text">
                                    Maksimal: <?php echo $product['stok']; ?> pcs (sesuai stok)
                                </div>
                            </div>
                            
                            <!-- Total Display -->
                            <div class="total-box text-center mb-4">
                                <div class="mb-1">TOTAL HARGA</div>
                                <div id="totalDisplay">Rp<?php echo number_format($product['harga'], 0, ',', '.'); ?></div>
                            </div>
                            
                            <!-- Hidden Fields -->
                            <input type="hidden" id="hargaSatuan" value="<?php echo $product['harga']; ?>">
                            
                            <!-- Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="dashboard.php" class="btn btn-kembali btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-simpan btn-lg">
                                    <i class="fas fa-save me-2"></i>Simpan Transaksi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Information -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h6><i class="fas fa-info-circle me-2 text-primary"></i>Informasi:</h6>
                        <ul class="mb-0">
                            <li>Total harga akan otomatis terhitung berdasarkan jumlah (qty)</li>
                            <li>Transaksi yang disimpan dapat dilihat di menu "Transaksi"</li>
                            <li>Stok akan otomatis berkurang setelah transaksi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="mt-5 py-3 bg-dark text-white text-center">
        <div class="container">
            <small>Sistem Kantin Sehat &copy; 2024</small>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Auto Calculation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hargaSatuan = parseInt(document.getElementById('hargaSatuan').value);
            const qtyInput = document.getElementById('qty');
            const totalDisplay = document.getElementById('totalDisplay');
            
            function calculateTotal() {
                const qty = parseInt(qtyInput.value) || 0;
                const total = hargaSatuan * qty;
                totalDisplay.textContent = 'Rp' + total.toLocaleString('id-ID');
            }
            
            // Initial calculation
            calculateTotal();
            
            // Recalculate on input change
            qtyInput.addEventListener('input', calculateTotal);
            
            // Prevent negative values
            qtyInput.addEventListener('change', function() {
                if (this.value < 1) this.value = 1;
                if (this.value > <?php echo $product['stok']; ?>) this.value = <?php echo $product['stok']; ?>;
                calculateTotal();
            });
        });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>