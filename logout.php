<?php
session_start();
session_destroy(); // Destroy all active login session tokens
header("Location: admin_login.php"); // Bounce the user back to login screen
exit();
?>