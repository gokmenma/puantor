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

  <!-- Üst Özet Alanı (Animated Button Style) -->
  <div class="row g-2 mb-3 px-1">
    <div class="col-6">
      <a href="#" class="btn btn-animate-icon w-100 p-3 border-0 shadow-sm d-flex flex-column align-items-start gap-1" style="background: rgba(32, 107, 196, 0.1); color: var(--mobile-primary); border-radius: 16px; text-align: left;">
        <div class="d-flex align-items-center justify-content-between w-100">
           <span class="text-bold h3 mb-0"><?php echo $white_collar; ?> <span class="text-xs text-uppercase opacity-75">Kişi</span></span>
           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-animate-rotate" style="opacity: 0.6;">
              <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
              <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1"></path>
              <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
              <path d="M17 10h2a2 2 0 0 1 2 2v1"></path>
              <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
              <path d="M3 13v-1a2 2 0 0 1 2 -2h2"></path>
           </svg>
        </div>
        <div class="text-xs text-uppercase font-weight-bold" style="font-size: 0.6rem; opacity: 0.8;">BEYAZ YAKA</div>
      </a>
    </div>
    <div class="col-6">
      <a href="#" class="btn btn-animate-icon w-100 p-3 border-0 shadow-sm d-flex flex-column align-items-start gap-1" style="background: rgba(47, 179, 68, 0.1); color: #2fb344; border-radius: 16px; text-align: left;">
        <div class="d-flex align-items-center justify-content-between w-100">
           <span class="text-bold h3 mb-0"><?php echo $blue_collar; ?> <span class="text-xs text-uppercase opacity-75">Kişi</span></span>
           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-animate-pulse" style="opacity: 0.6;">
              <path d="M7 5m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
              <path d="M12 11l0 .01"></path>
              <path d="M12 15l0 .01"></path>
              <path d="M12 7l0 .01"></path>
           </svg>
        </div>
        <div class="text-xs text-uppercase font-weight-bold" style="font-size: 0.6rem; opacity: 0.8;">MAVİ YAKA</div>
      </a>
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
        <div class="swipe-container transaction-item-wrapper" data-name="<?php echo strtolower($person->full_name); ?>">
          <div class="swipe-actions">
            <button class="btn-swipe-action btn-delete-person" data-id="<?php echo $id_encrypted; ?>" data-name="<?php echo htmlspecialchars($person->full_name); ?>">
              <i class="ti ti-trash"></i>
              <span>Sil</span>
            </button>
          </div>
          <div class="swipe-content transaction-item-content">
            <a href="person-edit?id=<?php echo $id_encrypted; ?>" 
               class="list-group-item person-card border-0 py-3 px-3 w-100 bg-transparent" style="text-decoration: none; color: inherit;">
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
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>


<script>
$(document).ready(function() {
  // Arama fonksiyonu
  $('#personSearchInput').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#personListContainer .swipe-container').filter(function() {
      $(this).toggle($(this).find('.person-card').data('name').indexOf(value) > -1)
    });
  });

  // Personel Silme
  $(document).on('click', '.btn-delete-person', function(e) {
    e.preventDefault();
    const btn = $(this);
    const id = btn.data('id');
    const name = btn.data('name');
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Emin misiniz?',
            text: name + " isimli personeli silmek istediğinize emin misiniz?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Evet, Sil',
            cancelButtonText: 'Vazgeç',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('/api/persons/person.php', {
                    action: 'deletePerson',
                    id: id
                }, function(res) {
                    if (res.status === 'success') {
                        btn.closest('.swipe-container').fadeOut(300, function() {
                            $(this).remove();
                        });
                        Swal.fire({
                            title: 'Silindi!',
                            text: res.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Hata!', res.message, 'error');
                    }
                }, 'json');
            }
        });
    }
  });
});
</script>

<style>
.text-bold { font-weight: 700 !important; }
.text-semibold { font-weight: 600 !important; }

/* Unified List Style (Finance Style) */
.list-group-mobile {
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.06);
    background: #fff;
    margin-bottom: 2rem;
}
body[data-bs-theme="dark"] .list-group-mobile {
    background: #1e293b;
    border-color: rgba(255,255,255,0.05);
}
.transaction-item-wrapper {
    position: relative;
    overflow: hidden;
    background: #fff;
    border-radius: 0 !important; /* Kartların kendi radiusu olmasın */
}
body[data-bs-theme="dark"] .transaction-item-wrapper {
    background: #1e293b;
}
.transaction-item-wrapper {
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.transaction-item-wrapper:last-child {
    border-bottom: none;
}
.swipe-actions {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
    display: flex;
    border-radius: 0 !important; /* Sil butonu arkasında taşma olmasın */
}
.swipe-content {
    position: relative;
    z-index: 2;
    background: #fff;
    transition: transform 0.2s ease-out;
    border-radius: 0 !important; /* İçerik tam kare olmalı ki arkayı kapatsın */
}
body[data-bs-theme="dark"] .swipe-content {
    background: #1e293b;
}
.btn-swipe-action {
    border-radius: 0 !important;
}

/* Animated Buttons (Üst Özet) */
.btn-animate-icon {
    position: relative;
    transition: all 0.2s ease;
    border-radius: 16px;
    text-decoration: none !important;
    overflow: hidden;
}
.btn-animate-icon:active {
    transform: scale(0.96);
}
.icon-animate-pulse {
    animation: pulse-subtle 2s infinite ease-in-out;
}
@keyframes pulse-subtle {
    0% { transform: scale(1); opacity: 0.6; }
    50% { transform: scale(1.15); opacity: 1; }
    100% { transform: scale(1); opacity: 0.6; }
}
@keyframes rotate-subtle {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.btn-animate-icon:hover .icon-animate-rotate {
    animation: rotate-subtle 2s infinite linear;
}

body[data-bs-theme="dark"] .text-dark { color: #f4f6fa !important; }
</style>






