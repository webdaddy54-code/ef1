<?php
/**
 * EnterF1.com - Admin Logout
 */

session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;
