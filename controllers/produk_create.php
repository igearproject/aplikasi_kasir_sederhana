<?php
require "../utils/database.php";

// Membuat koneksi
$db = new Database();
$conn = $db->conn;

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Membaca input JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Ambil data dari JSON
    $kode_produk = trim($data["kode_produk"]);
    $nama_produk = trim($data["nama_produk"]);
    $kategori = trim($data["kategori"]);
    $harga_beli = trim($data["harga_beli"]);
    $harga_jual = trim($data["harga_jual"]);
    $stok = trim($data["stok"]);
    $satuan = trim($data["satuan"]);

    // Validasi input
    if (empty($kode_produk) || empty($nama_produk) || empty($kategori) || empty($harga_beli) || empty($harga_jual) || empty($stok) || empty($satuan)) {
        $response["status"] = "error";
        $response["message"] = "Semua field harus diisi.";
    } else {
        // Menyimpan data ke database
        $sql = "INSERT INTO produk (kode_produk, nama_produk, kategori, harga_beli, harga_jual, stok, satuan) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssddis", $kode_produk, $nama_produk, $kategori, $harga_beli, $harga_jual, $stok, $satuan);

        if ($stmt->execute()) {
            $response["status"] = "success";
            $response["message"] = "Produk berhasil ditambahkan!";
        } else {
            $response["status"] = "error";
            $response["message"] = "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    }
}

$conn->close();
echo json_encode($response);
