<?php
session_start();
require_once('dbconnection.php');

// Check admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Update appointments
foreach ($_POST['new_date'] as $appointment_id => $new_date) {
    $new_mechanic_id = $_POST['new_mechanic'][$appointment_id] ?? '';

    // Build query dynamically
    $queryParts = [];
    $params = [];
    $types = '';

    if (!empty($new_date)) {
        $queryParts[] = "appointment_date = ?";
        $params[] = $new_date;
        $types .= 's';
    }

    if (!empty($new_mechanic_id)) {
        $queryParts[] = "mechanic_id = ?";
        $params[] = $new_mechanic_id;
        $types .= 'i';
    }

    if (!empty($queryParts)) {
        $sql = "UPDATE appointment SET " . implode(', ', $queryParts) . " WHERE appointment_id = ?";
        $params[] = $appointment_id;
        $types .= 'i';

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: admin_home.php");
exit();
?>
