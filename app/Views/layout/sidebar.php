<?php
$currentSlug = uri_string();

$menus = [
    [
        'name' => 'Kualitas Udara',
        'icon' => 'fi fi-rr-clouds',
        'url'  => 'dashboard',
    ],
    [
        'name' => 'Riwayat',
        'icon' => 'fi fi-rr-time-past',
        'url'  => 'riwayat-data',
    ],
    [
        'name' => 'Informasi',
        'icon' => 'fi fi-rr-info',
        'url'  => 'info-aqi',
    ],
];
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner">
        <ul class="sidebar-menu">
            <?php foreach ($menus as $menu): ?>
                <?php $isActive = $currentSlug === $menu['url']; ?>
                <li class="menu-item">
                    <a href="<?= base_url($menu['url']) ?>"
                       class="menu-toggle <?= $isActive ? 'active' : '' ?>">
                        <div class="menu-toggle-left">
                            <i class="<?= esc($menu['icon']) ?> menu-icon"></i>
                            <span class="menu-label"><?= esc($menu['name']) ?></span>
                        </div>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
</div>

<script>
// ===== HELPER FUNCTIONS =====
function isSplitMode() {
    return window.innerWidth <= 992;
}

function toggleSidebar() {
    if (isSplitMode()) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            if (overlay) overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        } else {
            sidebar.classList.add('active');
            if (overlay) overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }
}

function closeSidebar() {
    if (isSplitMode()) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// ===== EVENT LISTENERS =====
document.addEventListener('DOMContentLoaded', function() {
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            closeSidebar();
        });
    }
    
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }
});

// ===== CLICK OUTSIDE HANDLER =====
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    
    if (isSplitMode() && sidebar && sidebar.classList.contains('active')) {
        if (!sidebar.contains(e.target) && 
            (!sidebarToggle || !sidebarToggle.contains(e.target))) {
            closeSidebar();
        }
    }
});

// ===== WINDOW RESIZE HANDLER =====
window.addEventListener('resize', function() {
    if (!isSplitMode()) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});
</script>