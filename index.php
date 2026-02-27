<?php
// Inisialisasi session
session_start();
include('config.php'); // Menghubungkan ke database

// Variabel untuk menyimpan pesan error (jika ada)
$error = '';

// Cek apakah pengguna sudah login (dengan session)
if (isset($_SESSION['admin_logged_in'])) {
    // Jika sudah login, langsung redirect ke beranda
    header('Location: beranda.php');
    exit();
}

// Jika form dikirimkan (login di-submit)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']); // Cek apakah Remember Me dicentang

    // Cek login dengan data database
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika email ditemukan
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session untuk pengguna yang login
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin'] = $user['email'];

            // Jika Remember Me dicentang, simpan email dan password di cookie
            if ($remember) {
                setcookie('email', $email, time() + (86400 * 30), "/"); // Simpan selama 30 hari
                setcookie('password', $password, time() + (86400 * 30), "/"); // Simpan password dalam cookie
            } else {
                // Jika tidak dicentang, hapus cookie
                setcookie('email', '', time() - 3600, "/");
                setcookie('password', '', time() - 3600, "/");
            }

            // Redirect ke halaman beranda setelah login
            header("Location: beranda.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}

// Cek apakah ada cookie dan isi email dan password secara otomatis
$email_cookie = isset($_COOKIE['email']) ? $_COOKIE['email'] : '';
$password_cookie = isset($_COOKIE['password']) ? $_COOKIE['password'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="login-background">
    <div class="login-container">
        <div class="login-box">
            <img src="img/logoo.png" alt="Laporanku Logo" class="logo" />
            <h1 class="welcome-text">Welcome Back!!</h1>

            <!-- Pesan error jika login gagal -->
            <?php if (!empty($error)) { ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php } ?>

            <!-- Form login -->
            <form action="" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <img src="img/email.png" alt="Email Icon" class="input-icon">
                        <input type="email" name="email" placeholder="Enter your email" value="<?php echo $email_cookie; ?>" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <img src="img/pass.png" alt="Password Icon" class="input-icon">
                        <input type="password" name="password" placeholder="Enter your password" value="<?php echo $password_cookie; ?>" required>
                    </div>
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" <?php if ($email_cookie) { echo "checked"; } ?>>
                        <label for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="login-button">Login</button>
            </form>
        </div>
    </div>

    <!-- Tambahkan script untuk toggle password visibility -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // Toggle tipe input antara password dan text
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle ikon atau teks tombol
            this.textContent = this.textContent === '👁' ? '🙈' : '👁';
        });
    </script>
</body>
</html>
