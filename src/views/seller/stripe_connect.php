<?php
$pageTitle = 'Stripe Payments';
include __DIR__ . '/layout.php';

$flash     = get_flash();
$connected = !empty($sellerProfile['stripe_account_id']);
$accountId = $sellerProfile['stripe_account_id'] ?? null;
?>

<!-- Top bar -->
<header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md flex items-center gap-3 px-8 py-4 border-b border-outline-variant/10">
  <div>
    <h2 class="font-headline font-bold text-2xl text-on-surface tracking-tight">Stripe Payments</h2>
    <p class="text-sm text-on-surface-variant">Receive payments directly into your bank account</p>
  </div>
</header>

<div class="p-8 max-w-5xl">

  <!-- Flash -->
  <?php if ($flash): ?>
  <?php $err = in_array($flash['type'], ['danger','error']); ?>
  <div class="mb-6 px-5 py-4 rounded-2xl flex items-center gap-3 <?= $err ? 'bg-error-container text-error' : 'bg-tertiary-fixed text-on-tertiary-fixed-variant' ?>">
    <span class="material-symbols-outlined"><?= $err ? 'error' : 'check_circle' ?></span>
    <p class="text-sm font-medium"><?= htmlspecialchars($flash['message']) ?></p>
  </div>
  <?php endif; ?>

  <div class="grid grid-cols-12 gap-8">

    <!-- Left: connection state -->
    <div class="col-span-12 lg:col-span-7 space-y-6">

      <?php if ($connected): ?>
      <!-- Connected state -->
      <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm ring-1 ring-outline-variant/10">
        <div class="flex items-center gap-4 mb-6">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:#635BFF;">
            <svg viewBox="0 0 24 24" fill="white" class="w-6 h-6"><path d="M13.479 9.883c-1.626-.604-2.512-1.067-2.512-1.803 0-.622.518-.979 1.442-.979 1.69 0 3.41.641 4.599 1.197l.677-4.157C16.088 3.49 14.214 3 12.5 3 8.906 3 6.375 4.917 6.375 8.089c0 3.038 2.104 4.259 4.37 5.069 1.784.632 2.388 1.124 2.388 1.87 0 .722-.596 1.104-1.698 1.104-1.525 0-3.647-.751-4.957-1.666l-.72 4.237C7.027 19.5 9.146 20 11.296 20c3.741 0 6.204-1.836 6.204-5.149-.001-3.119-2.001-4.392-4.021-4.968z"/></svg>
          </div>
          <div>
            <h3 class="font-headline font-bold text-on-surface text-lg">Stripe Connected</h3>
            <p class="text-sm text-outline">Payments are active on your account</p>
          </div>
          <span class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-tertiary-container/20 text-tertiary text-[10px] font-bold uppercase tracking-widest border border-tertiary/10">
            <span class="w-1.5 h-1.5 rounded-full bg-current"></span> Active
          </span>
        </div>

        <div class="bg-surface-container-low rounded-xl p-5 space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-outline uppercase tracking-wider">Account ID</span>
            <code class="text-xs font-mono text-on-surface bg-surface-container px-2 py-1 rounded-lg">
              <?= htmlspecialchars($accountId) ?>
            </code>
          </div>
          <div class="h-px bg-outline-variant/10"></div>
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-outline uppercase tracking-wider">Payouts</span>
            <span class="text-xs font-medium text-tertiary">Enabled</span>
          </div>
          <div class="h-px bg-outline-variant/10"></div>
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-outline uppercase tracking-wider">Currency</span>
            <span class="text-xs font-medium text-on-surface">ZAR — South African Rand</span>
          </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
          <a href="https://dashboard.stripe.com" target="_blank" rel="noopener"
             class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-headline font-bold text-sm text-on-surface border-2 border-outline-variant/30 hover:bg-surface-container-low transition-all">
            <span class="material-symbols-outlined text-lg">open_in_new</span>
            Open Stripe Dashboard
          </a>
          <a href="<?= BASE_URL ?>seller/stripe/connect"
             class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-headline font-bold text-sm text-outline hover:text-on-surface hover:bg-surface-container-low transition-all">
            <span class="material-symbols-outlined text-lg">refresh</span>
            Re-link Account
          </a>
        </div>
      </div>

      <?php else: ?>
      <!-- Not connected state -->
      <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm ring-1 ring-outline-variant/10">
        <div class="flex items-center gap-4 mb-6">
          <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined text-outline text-3xl">payments</span>
          </div>
          <div>
            <h3 class="font-headline font-bold text-on-surface text-lg">No Payment Account</h3>
            <p class="text-sm text-outline">Connect Stripe to start receiving payments</p>
          </div>
        </div>

        <div class="bg-surface-container-low rounded-xl p-5 mb-6">
          <p class="text-sm text-on-surface-variant leading-relaxed">
            DoughDistrict uses <strong class="text-on-surface">Stripe Connect</strong> to route payments directly
            to your bank account. You'll be redirected to Stripe to set up your account — it takes about 5 minutes.
          </p>
        </div>

        <a href="<?= BASE_URL ?>seller/stripe/connect"
           class="w-full flex items-center justify-center gap-3 px-8 py-4 rounded-xl font-headline font-extrabold text-white text-sm hover:opacity-90 transition-all shadow-md active:scale-[0.98]"
           style="background: #635BFF;">
          <svg viewBox="0 0 24 24" fill="white" class="w-5 h-5 flex-shrink-0"><path d="M13.479 9.883c-1.626-.604-2.512-1.067-2.512-1.803 0-.622.518-.979 1.442-.979 1.69 0 3.41.641 4.599 1.197l.677-4.157C16.088 3.49 14.214 3 12.5 3 8.906 3 6.375 4.917 6.375 8.089c0 3.038 2.104 4.259 4.37 5.069 1.784.632 2.388 1.124 2.388 1.87 0 .722-.596 1.104-1.698 1.104-1.525 0-3.647-.751-4.957-1.666l-.72 4.237C7.027 19.5 9.146 20 11.296 20c3.741 0 6.204-1.836 6.204-5.149-.001-3.119-2.001-4.392-4.021-4.968z"/></svg>
          Connect with Stripe
        </a>

        <p class="text-center text-[10px] text-outline mt-3">
          You'll be redirected to Stripe's secure onboarding flow.
        </p>
      </div>
      <?php endif; ?>

    </div>

    <!-- Right: info panel -->
    <div class="col-span-12 lg:col-span-5 space-y-5">

      <!-- Why Stripe Connect -->
      <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm ring-1 ring-outline-variant/10">
        <h4 class="font-headline font-bold text-on-surface mb-4">Why Stripe Connect?</h4>
        <ul class="space-y-4">
          <?php
          $benefits = [
            ['account_balance', 'Direct payouts',      'Funds go straight into your bank — no middleman holds your money.'],
            ['security',     'Secure & compliant',  'Stripe handles PCI compliance, fraud detection, and encryption.'],
            ['schedule',     'Flexible payouts',    'Set your own payout schedule — daily, weekly, or monthly.'],
            ['public',       'Trusted by millions', 'Stripe powers payments for millions of businesses worldwide.'],
          ];
          foreach ($benefits as [$icon, $title, $desc]): ?>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-primary mt-0.5 flex-shrink-0" style="font-variation-settings: 'FILL' 1; font-size: 20px;"><?= $icon ?></span>
            <div>
              <p class="text-sm font-bold text-on-surface"><?= $title ?></p>
              <p class="text-xs text-outline mt-0.5 leading-relaxed"><?= $desc ?></p>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Help card -->
      <div class="bg-tertiary-container/10 rounded-2xl p-5 border border-tertiary/10 flex items-start gap-3">
        <span class="material-symbols-outlined text-tertiary flex-shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">help</span>
        <div>
          <h4 class="text-sm font-bold text-tertiary mb-1">Need help?</h4>
          <p class="text-xs text-on-tertiary-fixed-variant leading-relaxed">
            If you run into issues during Stripe onboarding, make sure your South African bank account
            and ID number are on hand. Contact support if the problem persists.
          </p>
        </div>
      </div>

    </div>

  </div><!-- /grid -->

</div><!-- /p-8 -->

</main>
</body>
</html>
