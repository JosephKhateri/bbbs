<?php

    /*
     * Redirects the user to the specified URL. If headers have already been sent, uses JavaScript to redirect.
     */
    function redirect($url) {
        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        } else {
            echo "<script>window.location.href = '$url';</script>";
            exit;
        }
    }

?>