<?php


session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('dbconnection.php');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method");
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    die("Username and password are required");
}

try {

    $sql = "SELECT user_name, type FROM user_admin 
            WHERE (user_Email = ? OR user_name = ?) AND Password = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $stmt->bind_param("sss", $username, $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $_SESSION['username'] = $user['user_name']; 
        $_SESSION['user_type'] = strtolower(trim($user['type']));
        

        error_log("User type: " . $_SESSION['user_type']);
     
        if ($_SESSION['user_type'] === 'admin') {
            header("Location: admin_home.php");
        } else {
            header("Location: client_home.html");
        }
        exit();
    } else {
        throw new Exception("Invalid username or password");
    }
} catch (Exception $e) {

    error_log("Login error: " . $e->getMessage());
    

    die($e->getMessage());
}
?>