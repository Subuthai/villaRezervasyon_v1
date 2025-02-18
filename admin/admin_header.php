<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="/public/style.css">
    <style>
        header.admin-header {
            background: #4A4A4A;
            color: #FFD700;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        header.admin-header .logo h1 {
            margin: 0;
            font-size: 2rem;
        }
        header.admin-header nav {
            margin-top: 0.5rem;
        }
        header.admin-header nav a {
            color: #FFD700;
            text-decoration: none;
            margin: 0 0.5rem;
            font-size: 1rem;
            transition: color 0.3s;
        }
        header.admin-header nav a:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="logo">
            <h1>Admin Paneli</h1>
        </div>
        <nav>
            <a href="/admin/dashboard.php">Dashboard</a>
            <a href="/public/index.php">Siteye Dön</a>
            <a href="/admin/logout.php">Çıkış Yap</a>
        </nav>
    </header>
