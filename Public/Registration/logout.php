<?php
include '../includes/functions.php';

session_unset();
session_destroy();

header("Location: ./Registration/log_in.php");
exit();
?>