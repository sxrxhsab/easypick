<?php
// init.php - Initialisation commune à toutes les pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';
?>