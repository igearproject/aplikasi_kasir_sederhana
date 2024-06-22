<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Login</title>
    <!-- Link ke Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .login-form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .login-form h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .form-group button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <div class="login-form">
        <h2>Login</h2>
        <?php
        session_start();

        // Konfigurasi database
        include "utils/database.php";
        $db=new Database();
        $conn=$db->conn;
        // Membuat koneksi
        // $conn = new mysqli($servername, $username, $password, $dbname);

        // Memeriksa koneksi
        if ($conn->connect_error) {
            echo("Koneksi gagal: " . $conn->connect_error);
        }

        // Memeriksa apakah form telah disubmit
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = trim($_POST["email"]);
            $password = trim($_POST["password"]);

            // Validasi input
            if (empty($email) || empty($password)) {
                echo "Email dan Password harus diisi.";
            } else {
                // Memeriksa apakah email ada di database
                $sql = "SELECT id, username, password FROM users WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                // Memeriksa apakah email ditemukan
                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($id, $username, $hashed_password);
                    $stmt->fetch();

                    // Memverifikasi password
                    if (password_verify($password, $hashed_password)) {
                        // Login berhasil, buat sesi
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;

                        // Mengarahkan ke dashboard
                        header("location: dashboard.php");
                        exit;
                    } else {
                        echo "Password salah.";
                    }
                } else {
                    echo "Email tidak ditemukan.";
                }

                $stmt->close();
            }
        }

        $conn->close();
        ?>

        <form action="login.php" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>

    <!-- Link ke Bootstrap JS dan dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>