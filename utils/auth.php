<?php
session_start();

// Fungsi untuk memeriksa apakah pengguna sudah login
function check_login()
{
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login.php");
        exit;
    }
}
