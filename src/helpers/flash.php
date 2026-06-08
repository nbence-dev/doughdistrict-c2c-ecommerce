<?php
    // One-time "flash" messages: set a message before a redirect, then show it
    // once on the next page. $type maps to a Bootstrap alert colour (success,
    // danger, etc.).
    function set_flash($message, $type) {
        $_SESSION['flash_message'] = [
            'message' => $message,
            'type' => $type
        ];
    }

    // Returns and immediately clears the stored message, so a refresh won't show
    // it a second time.
    function get_flash() {
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $flash;
        }
        return null;
    }

?>