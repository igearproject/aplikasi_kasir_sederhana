<?php
require "../utils/database.php";

$db = new Database();
$conn = $db->conn;

$data = json_decode(file_get_contents('php://input'), true);

$id_produk = $data['produkId'];
$kode_produk = $data['kode_produk'];
$nama_produk = $data['nama_produk'];
$kategori = $data['kategori'];
$harga_beli = $data['harga_beli'];
$harga_jual = $data['harga_jual'];
$stok = $data['stok'];
$satuan = $data['satuan'];

$sql = "UPDATE produk SET kode_produk = ?, nama_produk = ?, kategori = ?, harga_beli = ?, harga_jual = ?, stok = ?, satuan = ? WHERE id_produk = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssdidsi", $kode_produk, $nama_produk, $kategori, $harga_beli, $harga_jual, $stok, $satuan, $id_produk);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Produk berhasil diperbarui!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui produk.']);
}

$stmt->close();
$conn->close();
