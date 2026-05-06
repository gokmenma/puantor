<?php
// Mobil Sabit Alt Menü (Sticky Bottom Nav)
$active_page = $page ?? 'home';
?>
<nav class="app-nav">
  <a href="?p=home" class="nav-item <?php echo ($active_page == 'home') ? 'active' : ''; ?>">
    <i class="ti ti-smart-home"></i>
    <span>Ana Sayfa</span>
  </a>

  <a href="?p=persons" class="nav-item <?php echo ($active_page == 'persons') ? 'active' : ''; ?>">
    <i class="ti ti-users"></i>
    <span>Personel</span>
  </a>

    <a href="?p=puantaj" class="nav-item <?php echo ($page == 'puantaj') ? 'active' : ''; ?>">
      <i class="ti ti-calendar-event"></i>
      <span>Puantaj</span>
    </a>

  <a href="?p=more" class="nav-item <?php echo ($active_page == 'more') ? 'active' : ''; ?>">
    <i class="ti ti-grid-pattern"></i>
    <span>Menü</span>
  </a>
</nav>
