<?php
session_start();
session_destroy();
header("Location: pageconnexion.php");
exit();
?>