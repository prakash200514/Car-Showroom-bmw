<?php
/* admin/partials/sidebar.php */
// Determine active page
$currentPage = basename($_SERVER['PHP_SELF']);
function sidebarLink($file, $icon, $label, $current) {
    $active = ($current === $file) ? ' active' : '';
    echo "<a href=\"/showroom/admin/{$file}\" class=\"{$active}\"><i class=\"fas fa-{$icon}\"></i> {$label}</a>";
}
?>
<aside class="adm-sidebar">
    <!-- Brand -->
    <a href="/showroom/admin/dashboard.php" class="adm-sidebar__brand">
        <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/BMW.svg" alt="BMW">
        <div class="adm-sidebar__brand-text">
            <strong>BMW Showroom</strong>
            <span>Admin Panel</span>
        </div>
    </a>

    <nav class="adm-nav">
        <!-- Overview -->
        <div class="adm-nav__section">Overview</div>
        <?php sidebarLink('dashboard.php', 'chart-line',    'Dashboard',       $currentPage); ?>

        <!-- Inventory -->
        <div class="adm-nav__section">Inventory</div>
        <?php sidebarLink('cars.php',      'car',           'All Cars',        $currentPage); ?>
        <?php sidebarLink('car-add.php',   'plus-circle',   'Add New Car',     $currentPage); ?>
        <?php sidebarLink('car-images.php','images',        'Manage Images',   $currentPage); ?>

        <!-- Spares -->
        <div class="adm-nav__section">Spares</div>
        <?php sidebarLink('spares.php',    'cogs',          'All Spares',      $currentPage); ?>
        <?php sidebarLink('spare-add.php', 'plus-circle',   'Add New Spare',   $currentPage); ?>
        <?php sidebarLink('spare-categories.php', 'tags',   'Categories',      $currentPage); ?>

        <!-- Website -->
        <div class="adm-nav__section">Website</div>
        <?php sidebarLink('banners.php',   'image',         'Homepage Banners',$currentPage); ?>

        <!-- CRM -->
        <div class="adm-nav__section">CRM</div>
        <?php sidebarLink('bookings.php',         'calendar-check','Test Drive Bookings', $currentPage); ?>
        <?php sidebarLink('service-requests.php', 'tools',         'Service Requests',    $currentPage); ?>
        <?php sidebarLink('enquiries.php',         'envelope',      'Enquiries',           $currentPage); ?>
        <?php sidebarLink('users.php',             'users',         'Users',               $currentPage); ?>

        <!-- Divider -->
        <div style="height:1px;background:rgba(255,255,255,0.07);margin:12px 0;"></div>
        <a href="/showroom/index.php" target="_blank">
            <i class="fas fa-external-link-alt"></i> View Website
        </a>
        <a href="/showroom/logout.php" class="adm-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>

    <div class="adm-sidebar__footer">
        &copy; <?= date('Y') ?> BMW Showroom
    </div>
</aside>
