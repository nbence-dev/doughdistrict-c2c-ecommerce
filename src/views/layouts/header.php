<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="shortcut icon" href="<?= BASE_URL ?>assets/images/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Be+Vietnam+Pro:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
    </style>
    <style>
        :root {
            --dd-primary: #6f4627;
            --dd-secondary: #924c00;
            --dd-surface: #fbf9f1;
            --dd-surface-low: #f5f4ec;
            --dd-on-surface: #1b1c17;
            --dd-on-surface-var: #51443c;
            --dd-outline: #83746b;
            --dd-outline-var: #d5c3b8;
        }

        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background-color: var(--dd-surface);
            color: var(--dd-on-surface);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .font-headline {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .navbar-dd {
            background-color: #ffffff;
            border-bottom: 1px solid rgba(213, 195, 184, 0.4);
        }

        .navbar-brand {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            color: var(--dd-primary) !important;
        }

        .nav-link {
            color: var(--dd-on-surface-var) !important;
        }

        .nav-link:hover {
            color: var(--dd-primary) !important;
        }

        .btn-dd-primary {
            background: linear-gradient(135deg, var(--dd-primary) 0%, #8b5e3c 100%);
            color: #ffffff;
            border: none;
            border-radius: 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-dd-primary:hover {
            box-shadow: 0 6px 20px rgba(111, 70, 39, 0.3);
            transform: translateY(-1px);
            color: #ffffff;
        }
    </style>
</head>

<body>

    <?php $currentUser = current_user(); ?>

    <nav class="navbar navbar-expand-lg navbar-dd sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">🥐 DoughDistrict</a>
            <button class="navbar-toggler border-0 d-lg-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>browse">Browse</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <?php if ($currentUser): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <?= htmlspecialchars($currentUser['name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <?php if ($currentUser['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/dashboard">Admin Panel</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php elseif ($currentUser['role'] === 'seller'): ?>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>seller/dashboard">Seller Dashboard</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>orders">My Orders</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>cart">Cart</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>logout">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-dd-primary btn-sm px-3" href="<?= BASE_URL ?>register">Join</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php $flash = get_flash();
    if ($flash): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show py-2 small mb-0"
                role="alert">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                    style="top: 50%; transform: translateY(-50%);"></button>
            </div>
        </div>
    <?php endif; ?>