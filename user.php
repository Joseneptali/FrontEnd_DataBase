<?php
session_start();
include 'db.php';

if ($_SESSION['user_role'] != 'admin') {
    die("Access denied. Only admins can access this page.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'create') {
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $role = $_POST['role'];
        $sql = "INSERT INTO users (email, hashed_password, role) VALUES ('$email', '$password', '$role')";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif ($action == 'update') {
        $id = $_POST['id'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $sql = "UPDATE users SET email='$email', role='$role' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        $sql = "DELETE FROM users WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
}

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>Email</th><th>Role</th><th>Actions</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['id']."</td><td>".$row['email']."</td><td>".$row['role']."</td>";
        echo "<td><form method='POST'><input type='hidden' name='id' value='".$row['id']."'><input type='hidden' name='action' value='delete'><button type='submit'>Delete</button></form></td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$conn->close();
?>
