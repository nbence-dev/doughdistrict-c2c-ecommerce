<?php
// Single entry point for sending mail through Resend. Returns false (instead of
// throwing) if the API key/sender aren't configured or the send fails, so a
// failed notification email never breaks the checkout or order flow that
// triggered it. Failures are logged for debugging.
require_once dirname(ROOT_PATH) . '/vendor/autoload.php';

function send_email(string $to_email, string $to_name, string $subject, string $html, ?string $from = null): bool
{
    $api_key = getenv('RESEND_API_KEY') ?: ($_ENV['RESEND_API_KEY'] ?? '');
    $from    = $from ?? (getenv('MAIL_FROM') ?: ($_ENV['MAIL_FROM'] ?? ''));

    if (!$api_key || !$from) {
        error_log('[mailer] RESEND_API_KEY or MAIL_FROM not set — email skipped');
        return false;
    }

    try {
        $resend = Resend::client($api_key);
        $resend->emails->send([
            'from'    => $from,
            'to'      => [$to_email],
            'subject' => $subject,
            'html'    => $html,
        ]);
        return true;
    } catch (\Exception $e) {
        error_log('[mailer] Resend error: ' . $e->getMessage());
        return false;
    }
}
