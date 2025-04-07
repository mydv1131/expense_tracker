<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST["type"];
    $amount = $_POST["amount"];
    $description = $_POST["description"];
    $user_id = $_SESSION["user_id"];

    $sql = "INSERT INTO transactions (user_id, type, amount, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isds", $user_id, $type, $amount, $description);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
