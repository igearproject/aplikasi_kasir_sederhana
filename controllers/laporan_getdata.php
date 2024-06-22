<?php
require "../utils/database.php"; // Sesuaikan dengan path database.php
$db = new Database();
$conn = $db->conn;

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$start = ($page - 1) * $limit;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$order_by = $sort === 'oldest' ? 'ASC' : 'DESC';

$where_clauses = [];
if ($start_date) $where_clauses[] = "tanggal_beli >= '$start_date'";
if ($end_date) $where_clauses[] = "tanggal_beli <= '$end_date'";
$where_sql = count($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$sql_count = "SELECT COUNT(*) AS total FROM rinci_jual JOIN jual ON rinci_jual.nomor_faktur = jual.nomor_faktur $where_sql";
$result_count = $conn->query($sql_count);
$total = $result_count->fetch_assoc()['total'];
$pages = ceil($total / $limit);

$sql = "SELECT tanggal_beli, nama_produk, harga_modal, harga_jual, qty, (harga_jual * qty) AS total_harga 
        FROM rinci_jual 
        JOIN jual ON rinci_jual.nomor_faktur = jual.nomor_faktur 
        $where_sql 
        ORDER BY tanggal_beli $order_by 
        LIMIT $start, $limit";
$result = $conn->query($sql);

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = [
        'tanggal_beli' => $row['tanggal_beli'],
        'nama_produk' => $row['nama_produk'],
        'harga_modal' => $row['harga_modal'],
        'harga_jual' => $row['harga_jual'],
        'qty' => $row['qty'],
        'total_harga' => $row['total_harga']
    ];
}

echo json_encode([
    'reports' => $reports,
    'pages' => $pages
]);

$conn->close();
