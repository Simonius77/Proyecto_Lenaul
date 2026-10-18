<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lenaul</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; line-height: 1.5; }
        table { border-collapse: collapse; width: 100%; max-width: 640px; }
        th, td { border: 1px solid #ccc; padding: 0.5rem 0.75rem; text-align: left; }
        th { background: #f4f4f4; }
        a { margin-right: 0.75rem; }
        form { display: flex; flex-direction: column; gap: 0.75rem; max-width: 320px; }
        label { display: flex; flex-direction: column; gap: 0.25rem; }
        input, select, button { padding: 0.4rem 0.5rem; font: inherit; }
    </style>
</head>
<header>
    <?php include_once __DIR__.'/partials/navbar.php';?>
</header>
<body>
    <main>
        <section>
            <?php include_once $view; ?>
        </section>    
    </main>
    <?php include_once __DIR__.'/partials/footer.php';?>
</body>

</html>
