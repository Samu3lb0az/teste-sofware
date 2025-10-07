<?php
session_start();

$_SESSION = array();

session_destroy();

header("location: src/login.php");
exit;
?>