<?php
require_once 'config.php';

// Check session
checkSession();

// Role-based routing
$jabatan = $_SESSION['jabatan'];

// Redirect ke dashboard sesuai role
switch ($jabatan) {
    case ROLE_ADMIN:
        header('Location: admin/dashboard.php');
        break;
    case ROLE_KEPALA_PABRIK:
        header('Location: kepalapabrik/beranda.php');
        break;
    case ROLE_OPERATOR:
        header('Location: operatorproduksi/beranda.php');
        break;
    case ROLE_QC:
        header('Location: qualitycontrol/beranda.php');
        break;
    default:
        header('Location: index.php');
}
exit;
?>
