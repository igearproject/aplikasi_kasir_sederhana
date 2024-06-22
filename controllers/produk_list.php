<?php
require "../utils/database.php";

$db = new Database();
$conn = $db->conn;

$limit = 10; // jumlah produk per halaman
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$sql_count = "SELECT COUNT(id_produk) AS id FROM produk";
$result_count = $conn->query($sql_count);
$total = $result_count->fetch_assoc()['id'];
$pages = ceil($total / $limit);

$sql = "SELECT * FROM produk LIMIT $start, $limit";
$result = $conn->query($sql);

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$response = [
    'products' => $products,
    'pages' => $pages
];

echo json_encode($response);
$conn->close();
