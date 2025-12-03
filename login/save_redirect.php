<?php
session_start();
if(isset($_GET['page'])){
    $_SESSION['redirect_after_login'] = $_GET['page'];
}
