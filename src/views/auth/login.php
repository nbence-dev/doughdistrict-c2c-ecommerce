<?php $flash = get_flash(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — DoughDistrict</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Be+Vietnam+Pro:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --dd-primary: #6f4627;
            --dd-secondary: #924c00;
            --dd-surface: #fbf9f1;
            --dd-surface-low: #f5f4ec;
            --dd-on-surface: #1b1c17;
            --dd-on-surface-var: #51443c;
        }

        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background-color: var(--dd-surface);
        }

        .font-headline {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Brand panel */
        .brand-panel {
            background: linear-gradient(160deg, #6f4627 0%, #4a2c15 100%);
            min-height: 100vh;
        }

        @media (max-width: 991.98px) {
            .brand-panel {
                min-height: 160px;
            }
        }

        /* Form panel */
        .form-panel {
            background-color: #ffffff;
            min-height: 100vh;
        }

        /* Inputs */
        .input-carved {
            background-color: var(--dd-surface-low);
            border: 1px solid transparent;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-family: 'Be Vietnam Pro', sans-serif;
            color: var(--dd-on-surface);
            transition: all 0.2s ease;
        }

        .input-carved:focus {
            background-color: #ffffff;
            border-color: rgba(111, 70, 39, 0.4);
            box-shadow: none;
            outline: none;
        }

        /* CTA button */
        .btn-golden {
            background: linear-gradient(135deg, #6f4627 0%, #8b5e3c 100%);
            color: #ffffff;
            border: none;
            border-radius: 2rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-family: 'Be Vietnam Pro', sans-serif;
            transition: all 0.3s ease;
        }

        .btn-golden:hover {
            box-shadow: 0 8px 32px rgba(111, 70, 39, 0.3);
            transform: translateY(-1px);
            color: #ffffff;
        }

        .link-warm {
            color: var(--dd-secondary);
            text-decoration: none;
        }

        .link-warm:hover {
            color: var(--dd-primary);
            text-decoration: underline;
        }

        .pw-wrapper {
            position: relative;
        }

        .btn-pw-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            color: var(--dd-on-surface-var);
            cursor: pointer;
            line-height: 1;
            opacity: 0.6;
        }

        .btn-pw-toggle:hover { opacity: 1; }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">

            <!-- Brand Panel -->
            <div
                class="col-lg-6 brand-panel d-flex flex-column justify-content-center align-items-center text-white p-5">
                <div class="text-center">
                    <div class="mb-3" style="font-size: 3.5rem;">🥐</div>
                    <h1 class="font-headline fw-bold display-5 text-white mb-2">DoughDistrict</h1>
                    <p class="fs-5 fw-light mb-0" style="opacity: 0.75;">South Africa's Artisanal Hearth</p>
                    <hr class="border-white my-4"
                        style="opacity: 0.2; width: 60px; margin-left: auto; margin-right: auto;">
                    <p class="small fst-italic" style="opacity: 0.5;">"Baked with heart, sold with pride."</p>
                </div>
            </div>

            <!-- Form Panel -->
            <div class="col-lg-6 form-panel d-flex align-items-center justify-content-center p-4 p-md-5">
                <div class="w-100" style="max-width: 420px;">

                    <h2 class="font-headline fw-bold mb-1" style="color: var(--dd-on-surface);">Welcome back.</h2>
                    <p class="mb-4" style="color: var(--dd-on-surface-var);">Sign in to your account to continue.</p>

                    <?php if ($flash): ?>
                        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> py-2 small" role="alert">
                            <?= htmlspecialchars($flash['message']) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>login" novalidate data-validate>
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-semibold"
                                style="color: var(--dd-on-surface-var);">
                                Email Address
                            </label>
                            <input type="email" id="email" name="email" class="form-control input-carved"
                                placeholder="you@example.com" autocomplete="email" required>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label small fw-semibold"
                                style="color: var(--dd-on-surface-var);">
                                Password
                            </label>
                            <div class="pw-wrapper">
                                <input type="password" id="password" name="password" class="form-control input-carved pe-5"
                                    placeholder="••••••••" autocomplete="current-password" required>
                                <button type="button" class="btn-pw-toggle" onclick="togglePw('password', this)" aria-label="Toggle password visibility">
                                    <svg id="eye-password" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" name="login" class="btn btn-golden">
                                Sign In
                            </button>
                        </div>

                        <p class="text-center small mb-0" style="color: var(--dd-on-surface-var);">
                            Don't have an account?
                            <a href="<?= BASE_URL ?>register" class="link-warm fw-semibold">Join the District</a>
                        </p>
                    </form>

                </div>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/validation.js') ?>"></script>
    <script>
        const eyeSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>';
        const eyeSlashSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709z"/><path d="M13.646 14.354l-12-12 .708-.708 12 12-.707.707z"/></svg>';

        function togglePw(inputId, btn) {
            const input = document.getElementById(inputId);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.innerHTML = isHidden ? eyeSlashSvg : eyeSvg;
        }
    </script>
</body>

</html>