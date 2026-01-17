<?php 
session_start(); 
if(empty($_SESSION['user'])){ 
    header('Location: index.php'); 
    exit; 
}

$jabatan = strtolower(trim($_SESSION['jabatan']));
switch($jabatan) {
    case 'admin': header('Location: admin.php'); break;
    case 'kepala pabrik': 
    case 'kepala_pabrik': header('Location: kepalapabrik.php'); break;
    case 'operator': header('Location: operatortest.php'); break;
    case 'qc': header('Location: qc.php'); break;
    default: header('Location: index.php'); break;
}
exit;
?>
