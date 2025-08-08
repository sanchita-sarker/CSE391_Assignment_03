<?php
session_start();
require_once('dbconnection.php');


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

foreach ($_POST['slot'] as $mechanic_id => $new_slot) {
    if (!empty($new_slot) && is_numeric($new_slot)) {
        $stmt = $conn->prepare("UPDATE mechanics SET slot = ? WHERE mechanic_id = ?");
        $stmt->bind_param("ii", $new_slot, $mechanic_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: admin_home.php");
exit();
?>
