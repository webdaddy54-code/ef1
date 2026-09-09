<?php
/**
 * EnterF1.com - Admin Authentication Check
 * Include this file at the top of every admin page.
 */

session_start();

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();
