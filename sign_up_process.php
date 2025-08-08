<?php
    require_once('dbconnection.php');
    session_start(); 
    
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = $conn->real_escape_string($_POST['password']);
        
        $sql = "INSERT INTO user_admin (user_name, user_Email, Password) VALUES ('$name', '$email', '$password')";
    
        if ($conn->query($sql) === TRUE) {
            
            header("Location: client_home.html");
            exit(); 
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    
    $conn->close();
    ?>