<?php
require_once('dbconnection.php');


$name = $_POST['name'] ?? '';
$address = $_POST['address'] ?? '';
$phone = $_POST['phone'] ?? '';
$license_no = $_POST['license_no'] ?? '';
$engine_no = $_POST['engine_no'] ?? '';
$appointment_date = $_POST['appointment_date'] ?? '';
$mechanic_id = $_POST['mechanic_id'] ?? '';

if (empty($name) || empty($phone) || empty($license_no) || empty($engine_no) || empty($appointment_date) || empty($mechanic_id)) {
    die("All required fields must be filled.");
}

$check_sql = "
    SELECT m.slot, 
           (SELECT COUNT(*) FROM appointment a WHERE a.mechanic_id = m.mechanic_id AND a.appointment_date = ?) as current_appointments
    FROM mechanics m
    WHERE m.mechanic_id = ?
";

$stmt = $conn->prepare($check_sql);
$stmt->bind_param("si", $appointment_date, $mechanic_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid mechanic selected.");
}

$row = $result->fetch_assoc();
$available_slots = $row['slot'] - $row['current_appointments'];

if ($available_slots <= 0) {
    die("Selected mechanic has no available slots on that date.");
}


$insert_sql = "
    INSERT INTO appointment (client_name, client_address, client_phone, license_number, engine_number, appointment_date, mechanic_id)
    VALUES (?, ?, ?, ?, ?, ?, ?)
";

$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("ssssssi", $name, $address, $phone, $license_no, $engine_no, $appointment_date, $mechanic_id);

if ($insert_stmt->execute()) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Booking Confirmed</title>
        <script>
            setTimeout(function() {
                window.location.href = 'client_home.html';
            }, 2000); // Wait 2 seconds before redirecting
        </script>
        <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
                margin-top: 100px;
                background-color: #ffffffff;
            }
            .message {
                font-size: 24px;
                font-weight: bold;
                color: black;
            }
        </style>
    </head>
    <body>
        <div class='message'>Appointment booked successfully!</div>
        <p>Redirecting to client home page...</p>
    </body>
    </html>";
}

 else {
    echo "Error booking appointment: " . $conn->error;
}

$conn->close();
?>
