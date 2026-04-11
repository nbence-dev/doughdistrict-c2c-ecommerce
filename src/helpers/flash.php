<?php
    function set_flash($message, $type) {
        $_SESSION['flash_message'] = [
            'message' => $message,
            'type' => $type
        ];
    }

    function get_flash() {
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $flash;
        }
        return null;
    }

?>