<?php
require "../utils/database.php";

$db = new Database();
$conn = $db->conn;

$id = $_GET['id'];

$sql = "SELECT * FROM produk WHERE id_produk = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

echo json_encode($product);

$stmt->close();
$conn->close();
