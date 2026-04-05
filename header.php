<?php
if (!isset($page_title)) {
    $page_title = "MultiShopOrder";
}
if (!isset($base_url)) {
    $base_url = ".";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
</head>
<body class="bg-gray-100">

<?php include __DIR__ . '/navbar.php'; ?>
<div class="container py-4">
