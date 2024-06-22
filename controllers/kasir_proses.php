<?php
require "../utils/database.php";

$db = new Database();
$conn = $db->conn;

$data = json_decode(file_get_contents('php://input'), true);

$nomor_faktur = $data['nomor_faktur'];
$tanggal_beli = $data['tanggal_beli'];
$total_belanja = $data['total_belanja'];
$total_bayar = $data['total_bayar'];
$kembalian = $data['kembalian'];
$cart = $data['cart'];

$conn->begin_transaction();

try {
    // Insert ke tabel jual
    $sql = "INSERT INTO jual (nomor_faktur, tanggal_beli, total_belanja, total_bayar, kembalian) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssddd", $nomor_faktur, $tanggal_beli, $total_belanja, $total_bayar, $kembalian);
    $stmt->execute();

    // Insert ke tabel rinci_jual dan update stok produk
    foreach ($cart as $item) {
        // Periksa apakah `kode_produk` ada dan tidak null
        if (!isset($item['kode_produk']) || empty($item['kode_produk'])) {
            throw new Exception('Kode produk cannot be null');
        }

        // Insert ke tabel rinci_jual
        $sql = "INSERT INTO rinci_jual (nomor_faktur, kode_produk, nama_produk, harga_modal, harga_jual, qty, total_harga, untung) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $total_harga = $item['harga'] * $item['qty'];
        $untung = ($item['harga'] - $item['harga_beli']) * $item['qty'];
        $stmt->bind_param("ssssdiid", $nomor_faktur, $item['kode_produk'], $item['nama'], $item['harga_beli'], $item['harga'], $item['qty'], $total_harga, $untung);
        $stmt->execute();

        // Update stok produk menggunakan id_produk
        $sql = "UPDATE produk SET stok = stok - ? WHERE kode_produk = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $item['qty'], $item['kode_produk']);
        $stmt->execute();
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil!']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Gagal melakukan transaksi: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
