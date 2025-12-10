<?php
session_start();

// Koneksi database
$conn = mysqli_connect('localhost', 'root', '', 'kantin_sehat');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit();
}

// Variabel error
$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Query cek user
    $sql = "SELECT * FROM Users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        // Login berhasil
        $_SESSION['user'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        // Login gagal
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kantin Sehat</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1dc7ddff;
            --secondary-color: #3d81a1ff;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, #cb7dffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .login-header {
            background-color: var(--primary-color);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(216, 19, 206, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #3b4ef8ff;
            border-color: #fafafaff;
        }
        
        .login-info {
            font-size: 0.9rem;
            color: var(--secondary-color);
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h2>KANTIN SEHAT</h2>
                    <p class="mb-0">Silakan login</p>
                </div>
                
                <div class="login-body">
                    <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Masukkan username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Masukkan password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">LOGIN</button>
                    </form>
                    
                    <div class="login-info">
                        <strong>Login untuk testing:</strong><br>
                        Username: <code>admin</code><br>
                        Password: <code>admin</code>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3 text-white">
                <small>&copy; 2025 Kantin Sehat - Ujian Praktik RPL</small>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-fill untuk testing
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').value = 'admin';
            document.getElementById('password').value = 'admin';
        });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>