<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<main class="container py-5 mt-5 text-center">
    <div class="py-5">
        <p class="mb-2" style="font-size: 6rem; line-height: 1;">🥐</p>
        <h1 class="display-4 fw-bold mb-2" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--dd-primary);">
            404
        </h1>
        <h2 class="h4 fw-semibold mb-3" style="color:var(--dd-on-surface);">
            This page got left in the oven.
        </h2>
        <p class="mb-5" style="color:var(--dd-on-surface-var); max-width:400px; margin:0 auto 2.5rem;">
            The page you're looking for doesn't exist or may have been moved.
        </p>
        <a href="<?= BASE_URL ?>browse" class="btn btn-dd-primary px-5 py-3 fw-bold">
            Back to the Bakery
        </a>
    </div>
</main>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
