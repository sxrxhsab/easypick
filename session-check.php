<?php
session_start();
echo 'Valeur de la session : ' . ($_SESSION['test'] ?? 'pas de session');
