<?php
$pathCommonPrefix = $pathPrefix . '/../common';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo isset($pageTitle) ? $pageTitle : 'Affiliate System'; ?></title>

    <!-- Shared White-Label Branding Head Component -->
    <?php include $pathCommonPrefix . '/includes/head_branding.php'; ?>

    <!-- Custom fonts for this template-->
    <link href="<?php echo $pathCommonPrefix; ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet"
        type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Legacy Google Font (keeping for safety until fully replaced) -->
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?php echo $pathCommonPrefix; ?>/assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?php echo $pathCommonPrefix; ?>/assets/css/dropdown.css" rel="stylesheet">
    <link href="<?php echo $pathCommonPrefix; ?>/assets/css/arrow.css" rel="stylesheet">
    <link href="<?php echo $pathCommonPrefix; ?>/assets/vendor/datatables/dataTables.bootstrap4.min.css"
        rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet">

    <link href="<?php echo $pathCommonPrefix; ?>/assets/css/tab.css" rel="stylesheet">
    <link href="<?php echo $pathCommonPrefix; ?>/assets/css/custom.css" rel="stylesheet">

    <!-- Modern Theme Overrides -->
    <link href="<?php echo $pathCommonPrefix; ?>/assets/css/modern-theme.css" rel="stylesheet">
    <!-- Offer Form Styles -->
    <link href="<?php echo $pathCommonPrefix; ?>/assets/css/offer-form.css" rel="stylesheet">

    <!-- Toast Notifications -->
    <link href="<?php echo $pathCommonPrefix; ?>/assets/css/toast.css" rel="stylesheet">

</head>