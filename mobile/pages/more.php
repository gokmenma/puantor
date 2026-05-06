<?php
// Puantor Mobil - Diğer Menüler ve Ayarlar
$user = $_SESSION['user'] ?? null;
?>

<div class="container px-0">
  <div class="mb-4">
    <h2 class="mb-1 text-semibold" style="letter-spacing: -0.5px;">Daha Fazla</h2>
  </div>

  <!-- Kullanıcı Profili Özeti -->
  <div class="mobile-card mb-4 p-3 d-flex align-items-center gap-3">
    <div class="avatar avatar-lg rounded-circle text-uppercase" style="background-color: rgba(32, 107, 196, 0.1); color: var(--tblr-primary); font-size: 1.2rem; font-weight: 700;">
      <?php 
        if ($user) {
          echo mb_substr($user->name ?? $user->email ?? 'U', 0, 2, 'UTF-8');
        } else {
          echo 'U';
        }
      ?>
    </div>
    <div class="flex-1">
      <h3 class="mb-0 text-bold" style="font-size: 1rem;"><?php echo htmlspecialchars($user->name ?? 'İsimsiz Kullanıcı'); ?></h3>
      <p class="text-muted text-xs mb-0 text-truncate"><?php echo htmlspecialchars($user->email ?? ''); ?></p>
    </div>
    <a href="/index.php?p=settings/manage&tab=edit-account" class="btn btn-icon btn-sm btn-outline-secondary border-0">
      <i class="ti ti-pencil"></i>
    </a>
  </div>

  <!-- Ana Modüller -->
  <h4 class="mb-2 ms-2 text-muted text-xs text-uppercase tracking-wide text-semibold">Uygulama Modülleri</h4>
  <div class="list-group list-group-mobile mb-4">
    <a href="?p=finance" class="list-group-item">
      <div class="d-flex align-items-center gap-3">
        <div class="avatar avatar-sm rounded bg-green-lt">
          <i class="ti ti-wallet text-green"></i>
        </div>
        <span class="text-semibold text-sm">Kasa & Finans</span>
      </div>
      <i class="ti ti-chevron-right text-muted" style="opacity: 0.5;"></i>
    </a>
    
    <a href="?p=payroll" class="list-group-item">
      <div class="d-flex align-items-center gap-3">
        <div class="avatar avatar-sm rounded bg-cyan-lt">
          <i class="ti ti-report-money text-cyan"></i>
        </div>
        <span class="text-semibold text-sm">Bordrolar</span>
      </div>
      <i class="ti ti-chevron-right text-muted" style="opacity: 0.5;"></i>
    </a>

    <a href="?p=projects" class="list-group-item">
      <div class="d-flex align-items-center gap-3">
        <div class="avatar avatar-sm rounded bg-blue-lt">
          <i class="ti ti-folders text-blue"></i>
        </div>
        <span class="text-semibold text-sm">Projeler</span>
      </div>
      <i class="ti ti-chevron-right text-muted" style="opacity: 0.5;"></i>
    </a>
    
    <a href="?p=todos" class="list-group-item">
      <div class="d-flex align-items-center gap-3">
        <div class="avatar avatar-sm rounded bg-orange-lt">
          <i class="ti ti-checklist text-orange"></i>
        </div>
        <span class="text-semibold text-sm">Yapılacaklar</span>
      </div>
      <i class="ti ti-chevron-right text-muted" style="opacity: 0.5;"></i>
    </a>
  </div>

  <!-- Destek ve Ayarlar -->
  <h4 class="mb-2 ms-2 text-muted text-xs text-uppercase tracking-wide text-semibold">Destek & Sistem</h4>
  <div class="list-group list-group-mobile mb-4">
    <a href="/index.php?p=settings/manage" class="list-group-item">
      <div class="d-flex align-items-center gap-3">
        <div class="avatar avatar-sm rounded bg-secondary-lt">
          <i class="ti ti-settings text-secondary"></i>
        </div>
        <span class="text-semibold text-sm">Firma Ayarları</span>
      </div>
      <i class="ti ti-chevron-right text-muted" style="opacity: 0.5;"></i>
    </a>

    <a href="/index.php?p=supports/tickets" class="list-group-item">
      <div class="d-flex align-items-center gap-3">
        <div class="avatar avatar-sm rounded bg-indigo-lt">
          <i class="ti ti-headset text-indigo"></i>
        </div>
        <span class="text-semibold text-sm">Teknik Destek</span>
      </div>
      <i class="ti ti-chevron-right text-muted" style="opacity: 0.5;"></i>
    </a>
    
    <a href="logout.php" class="list-group-item">
      <div class="d-flex align-items-center gap-3">
        <div class="avatar avatar-sm rounded bg-red-lt">
          <i class="ti ti-logout text-red"></i>
        </div>
        <span class="text-semibold text-sm text-red">Çıkış Yap</span>
      </div>
    </a>
  </div>

  <div class="text-center pb-4">
    <p class="text-muted text-xs mb-1">Puantor v2.0</p>
    <a href="/index.php?p=home&view=desktop" class="text-primary text-xs text-decoration-none">Masaüstü Görünüme Geç</a>
  </div>
</div>
