<?php
session_start();
session_unset();
session_destroy();
header("Location: ../home page/index.php");
exit();
?>
