<!DOCTYPE html>
<html lang=\"es\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title><?= htmlspecialchars($pageTitle ?? 'Archivo LIS') ?></title>
    <style>
        :root {
            color-scheme: light dark;
        }

        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            background-color: #f5f6fa;
            color: #222;
        }

        .site-header,
        .site-footer {
            background-color: #1b4b8c;
            color: #fff;
        }

        .site-header .container,
        .site-footer .container {
            padding: 1.5rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        .site-header a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        .site-header nav {
            margin-top: 0.5rem;
        }

        .site-content {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            border-bottom: 1px solid #d0d7de;
            padding: 0.75rem;
            text-align: left;
        }

        th {
            background-color: #f0f4f9;
        }

        tr:hover {
            background-color: #eef3fb;
        }

        .actions {
            margin-top: 1.5rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 4px;
            background-color: #1b4b8c;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .btn.secondary {
            background-color: #6c757d;
        }

        .message {
            padding: 0.8rem 1rem;
            border-radius: 4px;
            background-color: #fff3cd;
            color: #664d03;
            border: 1px solid #ffecb5;
            margin-top: 1rem;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .card {
            background: #fff;
            border-radius: 6px;
            padding: 1rem;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.15);
        }

        form {
            margin-top: 1.5rem;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        a.sort-link {
            color: #1b4b8c;
            text-decoration: none;
        }

        a.sort-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<header class=\"site-header\">
    <div class=\"container\">
        <div style=\"display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;\">
            <div>
                <a href=\"/\" title=\"Inicio\">Archivo LIS</a>
                <div style=\"font-size:0.9rem;opacity:0.8;\">Panel de consulta de eventos sísmicos</div>
            </div>
            <div>
                <!-- Espacio reservado para acciones del encabezado -->
            </div>
        </div>
    </div>
</header>
<main class=\"site-content\">
    <?= $content ?>
</main>
<footer class=\"site-footer\">
    <div class=\"container\">
        <!-- Footer personalizable -->
        <small>&copy; <?= date('Y') ?> Archivo LIS</small>
    </div>
</footer>
</body>
</html>
