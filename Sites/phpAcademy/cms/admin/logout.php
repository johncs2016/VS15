<?php
include_once '../includes/bootstrap.php';

session_destroy();

header('Location: index.php');
exit('You have been successfully logged out.');
?>