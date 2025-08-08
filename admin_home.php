
<?php
session_start();
require_once('dbconnection.php');


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}


$appointments = $conn->query("SELECT a.appointment_id,  a.client_name, a.client_address, a.client_phone, a.license_number, a.engine_number, a.appointment_date, m.mechanic_id, m.mechanic_name 
    FROM appointment a 
    JOIN mechanics m ON a.mechanic_id = m.mechanic_id");

$mechanics = $conn->query("SELECT mechanic_id, mechanic_name FROM mechanics");
$mechanicOptions = "";
while ($row = $mechanics->fetch_assoc()) {
    $mechanicOptions .= "<option value='{$row['mechanic_id']}'>{$row['mechanic_name']}</option>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Appointments</title>
    <link rel="stylesheet" href="css/admin.css" />

</head>
<body>
<h2 style="text-align: center;">Appointment List</h2>
<form method="post" action="update_appointments.php">
    <table border="1">
        <tr>
            <th>Client Name</th>
            <th>Phone</th>
            <th>Car Reg. No</th>
            <th>Appointment Date</th>
            <th>Mechanic</th>
            <th>Change Date</th>
            <th>Change Mechanic</th>
        </tr>

        <?php while ($row = $appointments->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['client_name'] ?></td>
                <td><?= $row['client_phone'] ?></td>
                <td><?= $row['license_number'] ?></td>
                <td><?= $row['appointment_date'] ?></td>
                <td><?= $row['mechanic_name'] ?></td>


                <td><input type="date" name="new_date[<?= $row['appointment_id'] ?>]"></td>
                <td>
                    <select name="new_mechanic[<?= $row['appointment_id'] ?>]">
                        <option value="">-- Keep Same --</option>
                        <?= $mechanicOptions ?>
                    </select>
                </td>
            </tr>
        <?php } ?>
    </table>

    <br>
    <input type="submit" value="Update Appointments">
</form>

<h3 style= "text-align:center;">Update Mechanic Slot Count</h3>
<form method="post" action="update_slots.php">
    <table border="1">
        <tr>
            <th>Mechanic</th>
            <th>Current Slots</th>
            <th>New Slot Count</th>
        </tr>
        <?php
        $mechanics2 = $conn->query("SELECT mechanic_id, mechanic_name, slot FROM mechanics");
        while ($m = $mechanics2->fetch_assoc()) { ?>
            <tr>
                <td><?= $m['mechanic_name'] ?></td>
                <td><?= $m['slot'] ?></td>
                <td>
                    <input type="number" name="slot[<?= $m['mechanic_id'] ?>]" min="1">
                </td>
            </tr>
        <?php } ?>
    </table>
    <br>
    <input type="submit" value="Update Slots">
</form>

</body>
</html>

