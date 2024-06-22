<?php
require "../utils/database.php"; // Sesuaikan dengan path database.php
$db = new Database();
$conn = $db->conn;

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mendapatkan data produk terjual di bulan ini
$sql = "SELECT 
            DATE_FORMAT(jual.tanggal_beli, '%Y-%m') AS bulan, 
            rinci_jual.nama_produk AS barang_terlaris,
            SUM(rinci_jual.qty) AS qty_terjual,
            SUM(rinci_jual.harga_modal * rinci_jual.qty) AS total_modal,
            SUM((rinci_jual.harga_jual - rinci_jual.harga_modal) * rinci_jual.qty) AS keuntungan
        FROM rinci_jual
        JOIN jual ON rinci_jual.nomor_faktur = jual.nomor_faktur
        WHERE DATE_FORMAT(jual.tanggal_beli, '%Y-%m') = DATE_FORMAT(CURRENT_DATE(), '%Y-%m')
        GROUP BY rinci_jual.nama_produk, bulan
        ORDER BY bulan DESC, qty_terjual DESC";
$result = $conn->query($sql);

$monthlyReports = [];
while ($row = $result->fetch_assoc()) {
    $monthlyReports[] = [
        'bulan' => $row['bulan'],
        'barang_terlaris' => $row['barang_terlaris'],
        'qty_terjual' => $row['qty_terjual'],
        'keuntungan' => $row['keuntungan']
    ];
}

// Query untuk mendapatkan total keuntungan bulanan dari semua produk
$sql_total_keuntungan = "SELECT 
                            SUM((harga_jual - harga_modal) * qty) AS total_keuntungan
                         FROM rinci_jual
                         JOIN jual ON rinci_jual.nomor_faktur = jual.nomor_faktur
                         WHERE DATE_FORMAT(jual.tanggal_beli, '%Y-%m') = DATE_FORMAT(CURRENT_DATE(), '%Y-%m')";
$result_total_keuntungan = $conn->query($sql_total_keuntungan);
$total_keuntungan_bulan_ini = $result_total_keuntungan->fetch_assoc()['total_keuntungan'];

echo json_encode([
    'monthlyReports' => $monthlyReports,
    'total_keuntungan_bulan_ini' => $total_keuntungan_bulan_ini
]);

$conn->close();
