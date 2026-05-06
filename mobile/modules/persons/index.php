<?php
// Puantor Mobil - Personel Listesi (Kasa Tasarımı Uyumlu)
require_once ROOT . "/Model/Persons.php";
require_once ROOT . "/Model/Bordro.php";
require_once ROOT . "/App/Helper/helper.php";

use App\Helper\Helper;
use App\Helper\Security;

$personModel = new Persons();
$bordroModel = new Bordro();

$firm_id = $_SESSION['firm_id'] ?? 0;
$persons = $personModel->getPersonsByFirm($firm_id);

$white_collar = 0;
$blue_collar = 0;
foreach($persons as $p) {
    if($p->wage_type == 1) $white_collar++;
    else $blue_collar++;
}
?>

<div class="container px-2">
  
  <!-- Üst Başlık Alanı -->
  <div class="mb-4 d-flex align-items-center justify-content-between pt-2 px-1">
    <div>
      <h2 class="mb-0 text-bold" style="letter-spacing: -0.8px; font-size: 1.5rem;">Personeller</h2>
      <p class="text-muted text-xs mb-0">Toplam <?php echo count($persons); ?> çalışan kayıtlı.</p>
    </div>
    <a href="person-add" class="btn btn-icon btn-primary rounded-circle shadow-sm btn-active-scale">
      <i class="ti ti-plus fs-2"></i>
    </a>
  </div>

  <!-- Kasa Tasarımı Özet Kartları -->
  <div class="row g-3 mb-4">
    <div class="col-6">
      <div class="mobile-card p-3 mb-0 border-0 shadow-sm" style="background: rgba(32, 107, 196, 0.1); color: var(--mobile-primary); border-radius: 16px;">
        <div class="text-xs text-uppercase font-weight-bold mb-1" style="font-size: 0.65rem; opacity: 0.8;">BEYAZ YAKA</div>
        <div class="text-bold h3 mb-0"><?php echo $white_collar; ?> Kişi</div>
      </div>
    </div>
    <div class="col-6">
      <div class="mobile-card p-3 mb-0 border-0 shadow-sm" style="background: rgba(47, 179, 68, 0.1); color: #2fb344; border-radius: 16px;">
        <div class="text-xs text-uppercase font-weight-bold mb-1" style="font-size: 0.65rem; opacity: 0.8;">MAVİ YAKA</div>
        <div class="text-bold h3 mb-0"><?php echo $blue_collar; ?> Kişi</div>
      </div>
    </div>
  </div>

  <!-- Arama Çubuğu -->
  <div class="search-container mb-3 px-1">
    <i class="ti ti-search search-icon"></i>
    <input type="text" id="personSearchInput" class="search-input shadow-sm" placeholder="Personel ara...">
  </div>

  <!-- Personel Listesi -->
  <div class="list-group list-group-mobile shadow-sm" id="personListContainer">
    <?php if (empty($persons)): ?>
      <div class="text-center py-5 bg-white rounded-3 border">
        <i class="ti ti-users-off text-muted mb-2" style="font-size: 2.5rem; opacity: 0.5;"></i>
        <p class="text-muted text-sm mb-0">Henüz personel eklenmemiş.</p>
      </div>
    <?php else: ?>
      <?php foreach ($persons as $person): 
        $balance = $bordroModel->getBalance($person->id);
        $color = Helper::balanceColor($balance);
        $id_encrypted = Security::encrypt($person->id);
        $initials = mb_substr($person->full_name, 0, 2, 'UTF-8');
      ?>
        <a href="person-edit?id=<?php echo $id_encrypted; ?>" 
           class="list-group-item person-card border-0 border-bottom py-3 px-3" data-name="<?php echo strtolower($person->full_name); ?>">
          <div class="d-flex align-items-center justify-content-between w-100">
            <div class="d-flex align-items-center gap-3">
              <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center border border-white shadow-sm" style="background: rgba(32, 107, 196, 0.1); color: var(--mobile-primary); width: 42px; height: 42px;">
                <span class="text-bold" style="font-size: 0.85rem;"><?php echo $initials; ?></span>
              </div>
              <div>
                <div class="text-bold text-sm text-dark"><?php echo htmlspecialchars($person->full_name); ?></div>
                <div class="text-muted text-xs mt-0.5"><?php echo $person->job; ?> • <?php echo $person->wage_type == 1 ? 'Beyaz Yaka' : 'Mavi Yaka'; ?></div>
              </div>
            </div>
            <div class="text-end">
              <div class="text-bold text-sm <?php echo $color; ?>">
                ₺ <?php echo Helper::formattedMoneyWithoutCurrency($balance); ?>
              </div>
              <div class="text-muted text-xs font-weight-bold opacity-75" style="font-size: 0.65rem;">BAKİYE</div>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#personSearchInput').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#personListContainer .person-card').filter(function() {
      $(this).toggle($(this).data('name').indexOf(value) > -1)
    });
  });
});
</script>

<style>
.text-bold { font-weight: 700 !important; }
.text-semibold { font-weight: 600 !important; }
</style>
