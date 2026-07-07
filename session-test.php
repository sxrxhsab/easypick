<?php
session_start();
$_SESSION['test'] = 'ok';
echo 'Session test : ' . $_SESSION['test'];
echo '<br><a href="session-check.php">Vérifier</a>';