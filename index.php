<?php
require_once __DIR__ . '/includes/global/auth.php';

if (isLoggedIn()) {
    header('Location: pages/dashboard/index.php');
} else {
    header('Location: pages/login/index.php');
}
exit();
