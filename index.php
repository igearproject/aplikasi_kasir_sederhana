<?php

// Routing berdasarkan path
include_once "/utils/database.php";
include "config.php";
$request = $_SERVER['REQUEST_URI'];
$request = trim($request, '/');
switch ($request) {
    case '/':
        include 'dashboard.php';
        break;
    case '':
        include 'dashboard.php';
        break;
    case '/index.php':
        include 'dashboard.php';
        break;
    case '/login':
        include 'login.php';
        break;
    case '/kasir':
        include 'kasir.php';
        break;
    case '/fetch_laporan':
        include 'fetch_laporan.php';
        break;
    case '/fetch_monthly_report':
        include 'fetch_monthly_report.php';
        break;
    case '/proses_jual':
        include 'proses_jual.php';
        break;
    default:
        http_response_code(404);
        include '404.php';
        break;
}
