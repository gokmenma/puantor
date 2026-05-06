<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

define("ROOT", dirname(__DIR__));
require_once ROOT . "/Database/db.php";
require_once ROOT . "/Model/UserModel.php";
require_once ROOT . "/App/Helper/security.php";

$User = new UserModel();

if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Lütfen tüm alanları doldurun.";
    } else {
        $user = $User->getUserByEmail($email);
        if ($user && password_verify($password, $user->password)) {
            if ($user->status == 1) {
                $_SESSION['user'] = $user;
                $_SESSION['firm_id'] = $user->firm_id; // Varsayılan firmayı ata
                header("Location: index.php");
                exit();
            } else {
                $error = "Hesabınız henüz aktif değil.";
            }
        } else {
            $error = "Hatalı email adresi veya şifre.";
        }
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no" />
    <title>Puantor Mobil | Giriş Yap</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        :root { --tblr-font-sans-serif: 'Inter Var', sans-serif; }
        body { background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .login-card { width: 100%; max-width: 360px; padding: 2rem; border-radius: 24px; background: #fff; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
        .form-control { border-radius: 12px; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; }
        .btn-primary { border-radius: 12px; padding: 0.75rem; font-weight: 600; background: #206bc4; border: none; }
        .avatar-logo { width: 64px; height: 64px; background: rgba(32, 107, 196, 0.1); color: #206bc4; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="avatar-logo">
            <svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 112 112" style="width: 40px; height: 40px; fill: currentColor;">
                <circle cx="56" cy="56" r="48" fill="none" stroke="currentColor" stroke-width="4"/>
                <path d="M40 75V37h14.5c8 0 13.5 5 13.5 12s-5.5 12-13.5 12H48v14H40zm16-26c0-3.5-2.5-5.5-6.5-5.5H48v11h1.5c4 0 6.5-2 6.5-5.5z"/>
            </svg>
        </div>
        <div class="text-center mb-4">
            <h1 class="h2 mb-1">Puantor Mobil</h1>
            <p class="text-muted text-sm">Yönetici girişi yapın</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 px-3 mb-3 text-sm" style="border-radius: 12px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-muted text-xs text-uppercase font-weight-bold">Email Adresi</label>
                <input type="email" name="email" class="form-control" placeholder="ad@sirket.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted text-xs text-uppercase font-weight-bold">Şifre</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 mb-3">Giriş Yap</button>
            <div class="text-center">
                <a href="../index.php" class="text-muted text-xs text-decoration-none">Masaüstü Sürüme Dön</a>
            </div>
        </form>
    </div>
</body>
</html>
