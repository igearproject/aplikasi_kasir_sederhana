<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Link ke Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link ke Font Awesome CSS untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f4f4f4;
            height: 100vh;
        }

        .logo {
            margin-top: 20px;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .menu-container {
            margin-top: 50px;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .menu-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            margin: 10px;
            border-radius: 10px;
            background-color: #007bff;
            color: #fff;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .menu-button:hover {
            background-color: #0056b3;
        }

        .menu-icon {
            font-size: 50px;
            margin-bottom: 20px;
        }

        .menu-text {
            font-size: 20px;
        }

        /* CSS untuk toast message */
        .toast-top-center {
            position: fixed;
            top: 50px;
            /* Atur sesuai dengan jarak dari atas layar */
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            /* Pastikan z-index lebih tinggi dari modals dan elemen lainnya */
        }

        /* Animasi untuk memunculkan dan menghilangkan toast */
        @keyframes slideInRight {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        @keyframes slideOutRight {
            0% {
                transform: translateX(-50%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .toast-slide {
            animation: slideInRight 0.5s ease forwards, slideOutRight 0.5s ease 2.5s forwards;
        }
    </style>
</head>

<body>