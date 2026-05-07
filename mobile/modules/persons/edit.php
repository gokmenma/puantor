<?php
// Puantor Mobil - Personel Düzenleme
require_once ROOT . "/Model/Persons.php";
require_once ROOT . "/Model/Projects.php";
require_once ROOT . "/App/Helper/security.php";
require_once ROOT . "/App/Helper/helper.php";

use App\Helper\Security;
use App\Helper\Helper;

$personsModel = new Persons();
$projectsModel = new Projects();
$firm_id = $_SESSION['firm_id'] ?? 0;

$id_encrypted = $_GET['id'] ?? '';
$id = Security::decrypt($id_encrypted);

if (!$id) {
    header("Location: persons");
    exit();
}

$person = $personsModel->find($id);

if (!$person || $person->firm_id != $firm_id) {
    header("Location: persons");
    exit();
}

$projects = $projectsModel->getProjectsByFirm($firm_id);

$message = "";
$status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_person'])) {
    $tc_no = trim($_POST['tc_no'] ?? '');
    if (strlen($tc_no) > 11) {
        $tc_no = substr($tc_no, 0, 11);
    }
    
    $data = [
        'id' => $id,
        'firm_id' => $firm_id,
        'full_name' => $_POST['full_name'],
        'kimlik_no' => Security::encrypt($tc_no),
        'phone' => $_POST['phone'],
        'email' => $_POST['email'],
        'daily_wages' => $_POST['daily_wage'],
        'wage_type' => $_POST['wage_type'],
        'job_start_date' => $_POST['job_start_date'],
        'job_end_date' => $_POST['job_end_date'],
        'job' => $_POST['job'],
        'project_id' => $_POST['project_id'],
        'address' => $_POST['address']
    ];

    // Şifre değişikliği varsa ekle
    if (!empty($_POST['password'])) {
        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    
    try {
        $personsModel->saveWithAttr($data);
        $message = "Personel başarıyla güncellendi.";
        $status = "success";
        // Güncel veriyi tekrar çek
        $person = $personsModel->find($id);
    } catch (Exception $e) {
        $message = "Hata: " . $e->getMessage();
        $status = "danger";
    }
}

// Personel Silme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_person'])) {
    try {
        $personsModel->softDelete($id_encrypted); // softDelete genellikle şifreli ID bekliyor projede
        header("Location: persons");
        exit();
    } catch (Exception $e) {
        $message = "Silme hatası: " . $e->getMessage();
        $status = "danger";
    }
}
?>

<div class="container px-0 pb-5">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
      <a href="persons" class="btn btn-icon btn-sm btn-outline-secondary border-0 bg-secondary-lt rounded-circle">
        <i class="ti ti-chevron-left" style="font-size: 1.2rem;"></i>
      </a>
      <h2 class="mb-0 text-semibold" style="letter-spacing: -0.5px;">Personel Düzenle</h2>
    </div>

    <!-- Üç Nokta Menü (Sekmeler) -->
    <div class="dropdown">
      <button class="btn btn-icon btn-ghost-secondary rounded-circle shadow-none" type="button" id="personTabsDropdown" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="width: 40px; height: 40px;">
        <i class="ti ti-dots-vertical fs-2"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2" aria-labelledby="personTabsDropdown" style="border-radius: 16px; margin-top: 8px; min-width: 220px; z-index: 2000;">
        <li>
          <a class="dropdown-item active rounded-3 py-2 text-semibold mb-1" href="#">
            <i class="ti ti-user-circle me-2"></i> Personel Bilgileri
          </a>
        </li>
        <li>
          <a class="dropdown-item rounded-3 py-2 text-semibold mb-1" href="puantaj-detail?person_id=<?php echo $id_encrypted; ?>">
            <i class="ti ti-calendar-event me-2"></i> Puantaj Cetveli
          </a>
        </li>
        <li>
          <a class="dropdown-item rounded-3 py-2 text-semibold mb-1" href="finance?person_id=<?php echo $id_encrypted; ?>">
            <i class="ti ti-cash-banknote me-2"></i> Ödemeler & Finans
          </a>
        </li>
        <li>
          <a class="dropdown-item rounded-3 py-2 text-semibold" href="documents?person_id=<?php echo $id_encrypted; ?>">
            <i class="ti ti-file-text me-2"></i> Evraklar & Belgeler
          </a>
        </li>
      </ul>
    </div>
  </div>

  <?php if ($message): ?>
    <div class="alert alert-<?php echo $status; ?> d-flex align-items-center mb-3" role="alert" style="border-radius: 14px;">
      <div class="alert-icon me-3">
        <?php if ($status == 'success'): ?>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon"><path d="M5 12l5 5l10 -10"></path></svg>
        <?php else: ?>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>
        <?php endif; ?>
      </div>
      <div class="text-sm"><?php echo $message; ?></div>
    </div>
  <?php endif; ?>

  <div class="mobile-card p-3 shadow-sm mb-4">
    <form method="POST" action="">
      <div class="row g-3">
        <!-- Temel Bilgiler Grubu -->
        <div class="col-12">
          <label class="form-label text-muted text-xs text-uppercase font-weight-bold mb-2">Genel Bilgiler</label>
          <div class="form-floating mb-3">
            <input type="text" name="full_name" class="form-control" id="floatingFullName" placeholder="Ad Soyad" value="<?php echo htmlspecialchars($person->full_name); ?>" required>
            <label for="floatingFullName">Ad Soyad</label>
          </div>
          
          <div class="form-floating mb-3">
            <input type="text" name="tc_no" class="form-control" id="floatingTcNo" placeholder="T.C. Kimlik No" value="<?php echo Security::safeDecrypt($person->kimlik_no ?? ''); ?>" inputmode="numeric" pattern="[0-9]*" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11);">
            <label for="floatingTcNo">T.C. Kimlik No</label>
          </div>
        </div>

        <!-- İletişim Grubu -->
        <div class="col-12">
          <label class="form-label text-muted text-xs text-uppercase font-weight-bold mb-2">İletişim & Erişim</label>
          <div class="form-floating mb-3">
            <input type="tel" name="phone" class="form-control" id="floatingPhone" placeholder="Telefon" value="<?php echo htmlspecialchars($person->phone ?? ''); ?>">
            <label for="floatingPhone">Telefon</label>
          </div>
          <div class="form-floating mb-3">
            <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="E-posta" value="<?php echo htmlspecialchars($person->email ?? ''); ?>">
            <label for="floatingEmail">E-posta</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="PWA Giriş Şifresi">
            <label for="floatingPassword">Yeni PWA Giriş Şifresi</label>
          </div>
        </div>

        <!-- Ücret & Çalışma Grubu -->
        <div class="col-12">
          <label class="form-label text-muted text-xs text-uppercase font-weight-bold mb-2">Çalışma & Ücret</label>
          
          <div class="d-flex gap-2 mb-3">
            <input type="radio" class="btn-check" name="wage_type" id="wage_mavi" value="2" <?php echo ($person->wage_type == 2) ? 'checked' : ''; ?>>
            <label class="btn btn-outline-primary w-50 py-2 border-2" for="wage_mavi" style="border-radius: 10px;">Mavi Yaka</label>

            <input type="radio" class="btn-check" name="wage_type" id="wage_beyaz" value="1" <?php echo ($person->wage_type == 1) ? 'checked' : ''; ?>>
            <label class="btn btn-outline-primary w-50 py-2 border-2" for="wage_beyaz" style="border-radius: 10px;">Beyaz Yaka</label>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <div class="form-floating">
                <input type="number" step="0.01" name="daily_wage" class="form-control" id="floatingDailyWage" placeholder="0.00" value="<?php echo (float)$person->daily_wages; ?>">
                <label for="floatingDailyWage">Yevmiye / Maaş</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating">
                <input type="text" name="job" class="form-control" id="floatingJob" placeholder="Görevi" value="<?php echo htmlspecialchars($person->job ?? ''); ?>">
                <label for="floatingJob">Görevi</label>
              </div>
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <div class="form-floating">
                <input type="date" name="job_start_date" class="form-control" id="floatingStartDate" value="<?php echo $person->job_start_date; ?>" placeholder="İşe Giriş">
                <label for="floatingStartDate">İşe Giriş</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating">
                <input type="date" name="job_end_date" class="form-control" id="floatingEndDate" value="<?php echo $person->job_end_date; ?>" placeholder="İşten Çıkış">
                <label for="floatingEndDate">İşten Çıkış</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Proje & Adres -->
        <div class="col-12">
          <label class="form-label text-muted text-xs text-uppercase font-weight-bold mb-2">Detaylar</label>
          <div class="form-floating mb-3">
            <select name="project_id" id="floatingProject" class="form-select select2-init">
              <option value="0">Varsayılan Proje Seçin</option>
              <?php foreach ($projects as $project): ?>
                <option value="<?php echo $project->id; ?>" <?php echo ($person->project_id == $project->id) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($project->project_name); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <label for="floatingProject">Varsayılan Proje</label>
          </div>

          <div class="form-floating mb-3">
            <textarea name="address" id="floatingAddress" class="form-control" placeholder="Adres Bilgisi" style="height: 100px;"><?php echo htmlspecialchars($person->address ?? ''); ?></textarea>
            <label for="floatingAddress">Adres Bilgisi</label>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <button type="submit" name="save_person" class="btn btn-primary w-100 py-3 shadow-sm btn-active-scale" style="border-radius: 14px; font-weight: 700; letter-spacing: 0.5px;">
          <i class="ti ti-device-floppy me-2" style="font-size: 1.2rem;"></i> DEĞİŞİKLİKLERİ KAYDET
        </button>
      </div>
    </form>
  </div>
</div>



<script>
$(document).ready(function() {
    // Dropdown Manuel Tetikleyici (Bootstrap Popper sorunlarını aşmak için)
    $(document).on('click', '#personTabsDropdown', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var menu = $(this).next('.dropdown-menu');
        $('.dropdown-menu').not(menu).removeClass('show'); // Diğerlerini kapat
        menu.toggleClass('show');
    });

    // Dışarı tıklayınca kapatma
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
});
</script>

<style>
.dropdown-menu.show {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) !important;
}
.dropdown-menu {
    transition: all 0.2s ease-in-out;
    transform: translateY(10px);
    display: none;
    right: 0 !important; /* Sağa hizala ki sola doğru açılsın */
    left: auto !important;
}
.form-control-modern, .form-select-modern {
    border-radius: 12px;
    padding: 0.65rem 1rem;
    border: 1px solid rgba(0,0,0,0.08);
    background-color: rgba(0,0,0,0.01);
    transition: all 0.2s ease;
}
.form-control-modern:focus, .form-select-modern:focus {
    background-color: #fff;
    box-shadow: 0 0 0 0.25rem rgba(var(--tblr-primary-rgb), 0.1);
    border-color: var(--tblr-primary);
}
.input-group-flat {
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 12px;
    background-color: rgba(0,0,0,0.01);
    overflow: hidden;
}
.input-group-flat .input-group-text {
    background: transparent;
    border: none;
    padding-left: 1rem;
}
.input-group-flat .form-control {
    background: transparent;
    border: none;
    padding: 0.65rem 1rem 0.65rem 0.5rem;
}
.btn-check:checked + .btn-outline-primary {
    background-color: var(--tblr-primary-lt);
    color: var(--tblr-primary);
}
</style>
