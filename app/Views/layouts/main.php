<?php
/**
 * @var string $title
 * @var string $content
 * @var array $app
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? $app['name']) ?> | <?= e($app['tagline']) ?></title>
    <meta name="description" content="Experience bespoke hair styling, luxury facials, nail art, and holistic wellness at Deluxe Salon & Spa, Mumbai.">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Deluxe Salon CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navigation Bar -->
    <?php \Core\View::partial('navbar'); ?>

    <!-- Flash Alerts -->
    <?php \Core\View::partial('flash'); ?>

    <!-- Main Page Content -->
    <main class="flex-grow-1">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <?php \Core\View::partial('footer'); ?>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- Core App JS -->
    <script src="/assets/js/app.js"></script>
</body>
</html>
