<?php
include "utils/auth.php";
check_login();
include "layout/header.php";
include "layout/navbar.php";
?>

<div class="container">
    <div class="row menu-container">
        <a href="produk.php" class="col-md-4 menu-button">
            <i class="fas fa-box-open menu-icon"></i>
            <div class="menu-text">Produk</div>
        </a>
        <a href="kasir.php" class="col-md-4 menu-button">
            <i class="fas fa-cash-register menu-icon"></i>
            <div class="menu-text">Kasir</div>
        </a>
        <a href="laporan.php" class="col-md-4 menu-button">
            <i class="fas fa-file-alt menu-icon"></i>
            <div class="menu-text">Laporan</div>
        </a>
    </div>
</div>

<?php
include "layout/footer.php";
?>