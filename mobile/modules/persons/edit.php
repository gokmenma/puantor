<?php
// Puantor Mobil - Personel Düzenleme
require_once ROOT . "/Model/Persons.php";
require_once ROOT . "/Model/Projects.php";
require_once ROOT . "/Model/Puantaj.php";
require_once ROOT . "/Model/CaseTransactions.php";
require_once ROOT . "/App/Helper/security.php";
require_once ROOT . "/App/Helper/helper.php";
require_once ROOT . "/App/Helper/date.php";

use App\Helper\Security;
use App\Helper\Helper;
use App\Helper\Date;

$personsModel = new Persons();
$projectsModel = new Projects();
$puantajModel = new Puantaj();
$ctModel = new CaseTransactions();

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

// Puantaj verileri (Mevcut ay için)
$year = date('Y');
$month = date('m');
$firstDayOfMonth = "$year-$month-01";
$lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));
$personPuantaj = $puantajModel->getPuantajByPersonAndDate($id, $firstDayOfMonth, $lastDayOfMonth);
$puantajMap = [];
foreach ($personPuantaj as $p) {
    $puantajMap[$p->gun] = $p;
}

// Finans verileri (Sadece bu personelin işlemleri)
$allTransactions = $ctModel->allTransactionByFirm($firm_id);
$personTransactions = array_filter($allTransactions, function($t) use ($id) {
    return $t->person_id == $id;
});

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
      <div>
        <h2 class="mb-0 text-semibold" id="page-title" style="letter-spacing: -0.5px; line-height: 1.1;">Personel Düzenle</h2>
        <span class="text-muted text-xs font-weight-bold text-uppercase" style="letter-spacing: 0.5px; opacity: 0.8;"><?php echo htmlspecialchars($person->full_name); ?></span>
      </div>
    </div>

    <!-- Üç Nokta Menü (Sekme Tetikleyiciler) -->
    <div class="dropdown">
      <button class="btn btn-icon btn-ghost-secondary rounded-circle shadow-none" type="button" id="personTabsDropdown" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="width: 40px; height: 40px;">
        <i class="ti ti-dots-vertical fs-2"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2" aria-labelledby="personTabsDropdown" style="border-radius: 16px; margin-top: 8px; min-width: 220px; z-index: 2000;">
        <li>
          <a class="dropdown-item active rounded-3 py-2 text-semibold mb-1 tab-trigger" href="#" data-tab="info" data-title="Personel Bilgileri">
            <i class="ti ti-user-circle me-2"></i> Personel Bilgileri
          </a>
        </li>
        <li>
          <a class="dropdown-item rounded-3 py-2 text-semibold mb-1 tab-trigger" href="#" data-tab="puantaj" data-title="Puantaj Cetveli">
            <i class="ti ti-calendar-event me-2"></i> Puantaj Cetveli
          </a>
        </li>
        <li>
          <a class="dropdown-item rounded-3 py-2 text-semibold mb-1 tab-trigger" href="#" data-tab="finance" data-title="Ödemeler & Finans">
            <i class="ti ti-cash-banknote me-2"></i> Ödemeler & Finans
          </a>
        </li>
        <li>
          <a class="dropdown-item rounded-3 py-2 text-semibold tab-trigger" href="#" data-tab="documents" data-title="Evraklar & Belgeler">
            <i class="ti ti-file-text me-2"></i> Evraklar & Belgeler
          </a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Tab: Personel Bilgileri -->
  <div id="tab-info" class="person-tab-content">
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

  <!-- Tab: Puantaj Cetveli -->
  <div id="tab-puantaj" class="person-tab-content d-none">
    <div class="mobile-card p-4 shadow-sm mb-4 text-center">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <button class="btn btn-icon btn-ghost-secondary rounded-circle"><i class="ti ti-chevron-left fs-2"></i></button>
        <h3 class="mb-0 font-weight-bold" style="font-size: 1.15rem;"><?php echo Date::monthName($month); ?> <?php echo $year; ?></h3>
        <button class="btn btn-icon btn-ghost-secondary rounded-circle"><i class="ti ti-chevron-right fs-2"></i></button>
      </div>

      <div class="calendar-grid">
        <div class="calendar-day-header">Pzt</div>
        <div class="calendar-day-header">Sal</div>
        <div class="calendar-day-header">Çar</div>
        <div class="calendar-day-header">Per</div>
        <div class="calendar-day-header">Cum</div>
        <div class="calendar-day-header">Cmt</div>
        <div class="calendar-day-header text-danger">Paz</div>

        <?php
        $daysInMonth = Date::daysInMonth($month, $year);
        $firstDayTimestamp = strtotime("$year-$month-01");
        $startDay = date('N', $firstDayTimestamp); // 1 (Pzt) - 7 (Paz)
        
        // Boş günler
        for ($i = 1; $i < $startDay; $i++) {
            echo '<div class="calendar-day empty"></div>';
        }

        // Ayın günleri
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDateYmd = sprintf("%s%s%02d", $year, $month, $day);
            $pData = $puantajMap[$currentDateYmd] ?? null;
            $class = "";
            $style = "";
            $displayContent = $day;
            if ($pData) {
                // Puantaj türüne göre renk
                $turu = $puantajModel->getPuantajTuruById($pData->puantaj_id);
                if ($turu) {
                    $style = "background-color: {$turu->ArkaPlanRengi}; color: {$turu->FontRengi};";
                    $class = "has-puantaj";
                    $displayContent = htmlspecialchars($turu->PuantajKod);
                }
            }
            $isSunday = (date('N', strtotime("$year-$month-$day")) == 7);
            echo '<div class="calendar-day '.$class.' '.($isSunday ? 'text-danger' : '').'" style="'.$style.'">'.$displayContent.'</div>';
        }
        ?>
      </div>

      <?php
      $totalHours = 0;
      $totalBalance = 0;
      foreach ($personPuantaj as $p) {
          $totalHours += $p->saat;
          $totalBalance += $p->tutar;
      }
      ?>

      <div class="mt-4 pt-3 border-top d-flex justify-content-around">
          <div class="text-center">
              <div class="text-xs text-muted text-uppercase font-weight-bold mb-1">Toplam Mesai</div>
              <div class="text-bold text-lg text-primary"><?php echo $totalHours; ?></div>
          </div>
          <div class="text-center">
              <div class="text-xs text-muted text-uppercase font-weight-bold mb-1">Hakediş</div>
              <div class="text-bold text-lg text-success"><?php echo Helper::formattedMoney($totalBalance); ?></div>
          </div>
      </div>
    </div>
  </div>

  <!-- Tab: Ödemeler & Finans -->
  <div id="tab-finance" class="person-tab-content d-none">
    <div class="list-group list-group-mobile shadow-sm" style="border-radius: 16px; overflow: hidden;">
      <?php if (empty($personTransactions)): ?>
        <div class="p-5 text-center text-muted bg-white">
          <i class="ti ti-receipt-off fs-1 mb-2 opacity-20"></i>
          <p class="mb-0">Henüz finansal işlem bulunamadı.</p>
        </div>
      <?php else: ?>
        <?php foreach ($personTransactions as $t): 
          $is_income = ($t->type_id == 1);
        ?>
          <div class="list-group-item d-flex align-items-center justify-content-between py-3 border-0 border-bottom">
            <div class="d-flex align-items-center gap-3">
              <div class="avatar avatar-md rounded-circle <?php echo $is_income ? 'bg-green-lt text-green' : 'bg-red-lt text-red'; ?>">
                <i class="ti <?php echo $is_income ? 'ti-arrow-up-right' : 'ti-arrow-down-left'; ?> fs-2"></i>
              </div>
              <div>
                <div class="text-bold text-sm"><?php echo htmlspecialchars($t->description ?: ($is_income ? 'Gelir' : 'Gider')); ?></div>
                <div class="text-muted text-xs"><?php echo date('d.m.Y', strtotime($t->date)); ?></div>
              </div>
            </div>
            <div class="text-end">
              <div class="text-bold <?php echo $is_income ? 'text-green' : 'text-red'; ?>">
                <?php echo $is_income ? '+' : '-'; ?> <?php echo Helper::formattedMoney($t->amount); ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Tab: Evraklar -->
  <div id="tab-documents" class="person-tab-content d-none">
     <div class="mobile-card p-5 text-center text-muted">
          <i class="ti ti-files fs-1 mb-3 opacity-20"></i>
          <h4 class="mb-1">Evrak Arşivi</h4>
          <p class="text-xs">Bu personele ait dökümanlar yakında burada listelenecek.</p>
          <button class="btn btn-outline-primary btn-sm rounded-pill mt-3">Yeni Evrak Yükle</button>
     </div>
  </div>
</div>
</div>



<script>
$(document).ready(function() {
    // Dropdown Manuel Tetikleyici
    $(document).on('click', '#personTabsDropdown', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var menu = $(this).next('.dropdown-menu');
        $('.dropdown-menu').not(menu).removeClass('show');
        menu.toggleClass('show');
    });

    // Sekme Değiştirme Mantığı
    $(document).on('click', '.tab-trigger', function(e) {
        e.preventDefault();
        var tabId = $(this).data('tab');
        var title = $(this).data('title');
        
        // Tüm sekmeleri gizle
        $('.person-tab-content').addClass('d-none');
        // Seçili sekmeyi göster
        $('#tab-' + tabId).removeClass('d-none');
        
        // Başlığı güncelle
        $('#page-title').text(title);
        
        // Dropdown'daki aktif durumu güncelle
        $('.tab-trigger').removeClass('active');
        $(this).addClass('active');
        
        // Dropdown'ı kapat
        $('.dropdown-menu').removeClass('show');
        
        // Eğer Select2 varsa ve yeni sekmede görünmüyorsa tekrar init et
        if (tabId === 'info' && $.fn.select2) {
            $('.select2-init').select2();
        }
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
.person-tab-content.d-none {
    display: none !important;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 20px;
}

.calendar-day-header {
    font-size: 0.75rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    padding-bottom: 10px;
    text-align: center;
}

.calendar-day {
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    font-weight: 600;
    border-radius: 12px;
    background-color: #f8fafc;
    color: #1d273b;
    transition: all 0.2s ease;
}

.calendar-day.has-puantaj {
    font-weight: 700;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.calendar-day.empty {
    background-color: transparent;
}

.calendar-day.text-danger {
    color: #ef4444 !important;
}

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
    right: 0 !important;
    left: auto !important;
}

.btn-check:checked + .btn-outline-primary {
    background-color: var(--tblr-primary-lt);
    color: var(--tblr-primary);
}

body[data-bs-theme="dark"] .calendar-day {
    background-color: #1e293b;
    color: #f4f6fa;
}

body[data-bs-theme="dark"] .calendar-day.empty {
    background-color: transparent;
}
</style>
