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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, sans-serif;
            --mobile-primary: #206bc4;
        }
        body {
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
            font-family: var(--tblr-font-sans-serif);
            position: relative;
            overflow: hidden;
        }
        /* Blob Backgrounds */
        .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 1;
            opacity: 0.6;
        }
        .bg-blob-primary {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(32, 107, 196, 0.2) 0%, rgba(32, 107, 196, 0) 70%);
            top: -100px;
            left: -100px;
        }
        .bg-blob-indigo {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0) 70%);
            bottom: -100px;
            right: -100px;
        }

        /* Container */
        .login-container {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 28px;
            padding: 3rem 2.25rem 2.5rem;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.02);
            z-index: 10;
            position: relative;
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        /* Logo & Headings */
        .avatar-logo {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, rgba(32, 107, 196, 0.12) 0%, rgba(32, 107, 196, 0.04) 100%);
            color: var(--mobile-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 20px rgba(32, 107, 196, 0.08);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .avatar-logo:hover {
            transform: scale(1.08) rotate(3deg);
        }

        .login-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.5px;
        }
        .login-subtitle {
            font-size: 0.875rem;
            color: #64748b;
        }

        /* Inputs & Form */
        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 0.5rem;
        }
        .input-icon {
            position: relative;
        }
        .form-control {
            font-size: 0.95rem;
            border-radius: 14px;
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            transition: all 0.25s ease;
            color: #1e293b;
        }
        .form-control:focus {
            background-color: #ffffff;
            border-color: var(--mobile-primary);
            box-shadow: 0 0 0 4px rgba(32, 107, 196, 0.12);
        }
        .input-icon-addon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            display: flex;
            align-items: center;
            pointer-events: none;
            font-size: 1.2rem;
        }
        .input-icon-addon-right {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            display: flex;
            align-items: center;
            cursor: pointer;
            pointer-events: auto;
            font-size: 1.2rem;
            transition: color 0.2s ease;
            z-index: 10;
        }
        .input-icon-addon-right:hover {
            color: var(--mobile-primary);
        }

        /* Button */
        .btn-primary {
            border-radius: 14px;
            padding: 0.85rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            background: linear-gradient(135deg, #206bc4 0%, #1a569d 100%);
            border: none;
            box-shadow: 0 8px 24px rgba(32, 107, 196, 0.2);
            transition: all 0.25s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2475d7 0%, #1d61b3 100%);
            box-shadow: 0 10px 28px rgba(32, 107, 196, 0.3);
            transform: translateY(-1px);
        }
        .btn-primary:active {
            transform: translateY(1px) scale(0.98);
            box-shadow: 0 4px 12px rgba(32, 107, 196, 0.15);
        }

        /* Footer Link */
        .hover-underline {
            transition: color 0.2s ease;
            font-weight: 500;
        }
        .hover-underline:hover {
            color: var(--mobile-primary) !important;
            text-decoration: underline !important;
        }

        /* Mobile Responsive Adapting */
        @media (max-width: 480px) {
            body {
                background-color: #ffffff;
                padding: 0;
                align-items: stretch;
            }
            .bg-blob {
                display: none;
            }
            .login-container {
                max-width: 100%;
                height: 100vh;
                border-radius: 0;
                border: none;
                box-shadow: none;
                background: #ffffff;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 4rem 2rem 3rem;
            }
            .login-middle-form {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
                margin: 2rem 0;
            }
        }
    </style>
</head>
<body>
    <div class="bg-blob bg-blob-primary"></div>
    <div class="bg-blob bg-blob-indigo"></div>

    <div class="login-container">
        <div>
            <div class="avatar-logo">
                <svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 112 112" style="width: 40px; height: 40px; fill: currentColor;">
                    <circle cx="56" cy="56" r="48" fill="none" stroke="currentColor" stroke-width="4"/>
                    <path d="M40 75V37h14.5c8 0 13.5 5 13.5 12s-5.5 12-13.5 12H48v14H40zm16-26c0-3.5-2.5-5.5-6.5-5.5H48v11h1.5c4 0 6.5-2 6.5-5.5z"/>
                </svg>
            </div>
            <div class="text-center mb-4">
                <h1 class="login-title mb-1">Puantor Mobil</h1>
                <p class="login-subtitle">Yönetici hesabı ile giriş yapın</p>
            </div>
        </div>

        <div class="login-middle-form">
            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2.5 px-3 mb-3 text-sm animate-fade-in" style="border-radius: 14px; background-color: #fef2f2; border: 1px solid #fca5a5; color: #991b1b;">
                    <i class="ti ti-alert-triangle" style="font-size: 1.15rem; flex-shrink: 0;"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Email Adresi</label>
                    <div class="input-icon">
                        <span class="input-icon-addon-left">
                            <i class="ti ti-mail"></i>
                        </span>
                        <input type="email" name="email" class="form-control" placeholder="ad@sirket.com" required autocomplete="email">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Şifre</label>
                    <div class="input-icon">
                        <span class="input-icon-addon-left">
                            <i class="ti ti-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                        <span class="input-icon-addon-right" id="togglePasswordBtn">
                            <i class="ti ti-eye" id="togglePasswordIcon"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 mb-3">Giriş Yap</button>
            </form>
        </div>

        <div class="text-center mt-auto">
            <a href="../index.php" class="text-secondary d-inline-flex align-items-center gap-1 text-xs text-decoration-none hover-underline">
                <i class="ti ti-device-laptop" style="font-size: 1rem;"></i>
                Masaüstü Sürüme Dön
            </a>
        </div>
    </div>

    <script>
        document.getElementById('togglePasswordBtn').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            }
        });
    </script>
</body>
</html>
