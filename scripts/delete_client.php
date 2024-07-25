<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$company_id = $_GET['id'] ?? '';
$stmt = $conn->prepare("DELETE FROM clients WHERE company_id = :company_id AND created_by = :user_id ");
$stmt->bindParam(':company_id', $company_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);

if ($stmt->execute()) {
    header('Location: clients.php');
} else {
    echo "Client deletion error.";
}
?>
