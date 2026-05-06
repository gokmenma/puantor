<?php
// Puantor Mobil - Yeni Personel Ekleme
require_once ROOT . "/Model/Persons.php";
require_once ROOT . "/Model/Projects.php";

$personsModel = new Persons();
$projectsModel = new Projects();
$firm_id = $_SESSION['firm_id'] ?? 0;

$projects = $projectsModel->getProjectsByFirm($firm_id);

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_person'])) {
    $data = [
        'firm_id' => $firm_id,
        'full_name' => $_POST['full_name'],
        'tc_no' => $_POST['tc_no'],
        'phone' => $_POST['phone'],
        'daily_wage' => $_POST['daily_wage'],
        'job_start_date' => $_POST['job_start_date'],
        'project_id' => $_POST['project_id'],
        'status' => 1
    ];
    
    // Basit bir kaydetme simülasyonu / Model kullanımı
    // Not: Model'inizdeki save metoduna göre burası güncellenmelidir.
    try {
        $personsModel->saveWithAttr($data);
        $message = "Personel başarıyla eklendi.";
    } catch (Exception $e) {
        $message = "Hata: " . $e->getMessage();
    }
}
?>

<div class="container px-0">
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="?p=persons" class="btn btn-icon btn-sm btn-outline-secondary border-0">
      <i class="ti ti-chevron-left" style="font-size: 1.5rem;"></i>
    </a>
    <h2 class="mb-0 text-semibold" style="letter-spacing: -0.5px;">Yeni Personel</h2>
  </div>

  <?php if ($message): ?>
    <div class="alert alert-info py-2 px-3 mb-3 text-sm" style="border-radius: 12px;"><?php echo $message; ?></div>
  <?php endif; ?>

  <div class="mobile-card p-3">
    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label text-muted text-xs text-uppercase font-weight-bold">Ad Soyad</label>
        <input type="text" name="full_name" class="form-control" placeholder="Örn: Ahmet Yılmaz" required>
      </div>

      <div class="mb-3">
        <label class="form-label text-muted text-xs text-uppercase font-weight-bold">T.C. Kimlik No</label>
        <input type="number" name="tc_no" class="form-control" placeholder="11 Haneli">
      </div>

      <div class="mb-3">
        <label class="form-label text-muted text-xs text-uppercase font-weight-bold">Telefon</label>
        <input type="tel" name="phone" class="form-control" placeholder="05XX XXX XX XX">
      </div>

      <div class="row g-3 mb-3">
        <div class="col-6">
          <label class="form-label text-muted text-xs text-uppercase font-weight-bold">Günlük Yevmiye</label>
          <input type="number" name="daily_wage" class="form-control" placeholder="0.00">
        </div>
        <div class="col-6">
          <label class="form-label text-muted text-xs text-uppercase font-weight-bold">Giriş Tarihi</label>
          <input type="date" name="job_start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label text-muted text-xs text-uppercase font-weight-bold">Varsayılan Proje</label>
        <select name="project_id" class="form-select">
          <option value="0">Proje Seçin</option>
          <?php foreach ($projects as $project): ?>
            <option value="<?php echo $project->id; ?>"><?php echo htmlspecialchars($project->project_name); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit" name="save_person" class="btn btn-primary w-100 py-2" style="border-radius: 12px; font-weight: 600;">
        <i class="ti ti-check me-2"></i> Personeli Kaydet
      </button>
    </form>
  </div>
</div>
