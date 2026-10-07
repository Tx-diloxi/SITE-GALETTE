<?php
/**
 * Déconnexion de l'administration
 */
declare(strict_types=1);

session_name('jalg_admin');
session_start();
session_destroy();
header('Location: /admin/login.php');
exit;
