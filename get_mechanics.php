<?php

ob_start();
header('Content-Type: application/json');
require_once('dbconnection.php');


$date = $_GET['date'] ?? '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid date format']));
}

try {
    $sql = "SELECT m.mechanic_id, m.mechanic_name, m.slot,
            (SELECT COUNT(*) FROM appointment a
             WHERE a.mechanic_id = m.mechanic_id 
             AND a.appointment_date = ?) as current_appointments
            FROM mechanics m
";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("SQL prepare error: " . $conn->error);
    }
    
    $stmt->bind_param("s", $date);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $available = [];
    
    while ($row = $result->fetch_assoc()) {
        $free_slots = $row['slot'] - $row['current_appointments'];
        if ($free_slots > 0) {
            $available[] = [
                'mechanic_id' => $row['mechanic_id'],
                'mechanic_name' => $row['mechanic_name'],
                'free_slots' => $free_slots
            ];
        }
    }
    

    ob_end_clean();
    echo json_encode($available);
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>