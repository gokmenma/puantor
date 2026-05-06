<?php
// Puantor Premium Mobil Giriş ve Kabuk (App Shell) Dosyası
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

define("ROOT", dirname(__DIR__));
date_default_timezone_set('Europe/Istanbul');

// Oturum kontrolü
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header("Location: sign-in.php");
    exit();
}

require_once __DIR__ . "/../Database/db.php";
require_once __DIR__ . "/../Model/UserModel.php";

$User = new UserModel();
$user = $User->find($_SESSION['user']->id) ?? null;

if (!$user) {
    header("Location: /sign-in.php");
    exit();
}

$_SESSION["user"] = $user;

// Tema ayarları
if (isset($_GET['theme'])) {
    $_SESSION['theme'] = $_GET['theme'] == 'dark' ? 'dark' : 'light';
}
$theme = $_SESSION['theme'] ?? 'light';

// Aktif sayfa tayini
$page = isset($_GET["p"]) ? $_GET["p"] : "home";
$allowed_pages = ["home", "persons", "person_add", "puantaj", "puantaj_detail", "projects", "finance", "payroll", "todos", "more"];
if (!in_array($page, $allowed_pages)) {
    $page = "home";
}

$title = "Puantor Mobil | " . ucfirst($page);

// Başlık şablonunu yükleme
include_once __DIR__ . "/inc/head.php";
?>

<body class="layout-fluid" data-bs-theme="<?php echo $theme; ?>">
    <div class="app-shell">
        
        <!-- Üst Başlık (Header) -->
        <?php include_once __DIR__ . "/inc/header.php"; ?>

        <!-- Dinamik Alt Sayfa İçeriği -->
        <main class="app-content">
            <?php 
            switch ($page) {
                case 'person_add':
                    $title = "Yeni Personel Ekle";
                    $page_file = "person_add.php";
                    break;
                case 'puantaj_detail':
                    $title = "Detaylı Puantaj";
                    $page_file = "puantaj_detail.php";
                    break;
                case 'projects':
                    $title = "Projeler";
                    $page_file = "projects.php";
                    break;
                case 'finance':
                    $title = "Kasa & Finans";
                    $page_file = "finance.php";
                    break;
                case 'payroll':
                    $title = "Bordrolar";
                    $page_file = "payroll.php";
                    break;
                case 'todos':
                    $title = "Yapılacaklar";
                    $page_file = "todos.php";
                    break;
                case 'more':
                    $title = "Daha Fazla";
                    $page_file = "more.php";
                    break;
                default:
                    $title = "Puantaj Takip";
                    $page_file = "home.php";
                    break;
            }
            $page_file = __DIR__ . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . $page_file;
            if (file_exists($page_file)) {
                include_once $page_file;
            } else {
                echo "<div class='alert alert-warning'>Sayfa bulunamadı: " . htmlspecialchars($page_file) . "</div>";
            }
            ?>
        </main>

        <!-- Alt Sabit Menü (Bottom Navigation) -->
        <?php include_once __DIR__ . "/inc/bottom-nav.php"; ?>

    </div>

    <!-- Bootstrap 5 / Tabler JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
