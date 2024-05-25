<?php
session_start();
include 'db.php';

if ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'user') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_SESSION['user_role'] == 'admin') {
        $action = $_POST['action'];

        if ($action == 'create') {
            $user_id = $_POST['user_id'];
            $doctor_id = $_POST['doctor_id'];
            $date = $_POST['date'];
            $description = $_POST['description'];
            $sql = "INSERT INTO appointments (user_id, doctor_id, date, description) VALUES ('$user_id', '$doctor_id', '$date', '$description')";
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } elseif ($action == 'update') {
            $id = $_POST['id'];
            $user_id = $_POST['user_id'];
            $doctor_id = $_POST['doctor_id'];
            $date = $_POST['date'];
            $description = $_POST['description'];
            $sql = "UPDATE appointments SET user_id='$user_id', doctor_id='$doctor_id', date='$date', description='$description' WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
                echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } elseif ($action == 'delete') {
            $id = $_POST['id'];
            $sql = "DELETE FROM appointments WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
                echo "Record deleted successfully";
            } else {
                echo "Error deleting record: " . $conn->error;
            }
        }
    }
}

$sql = "SELECT * FROM appointments";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>User ID</th><th>Doctor ID</th><th>Date</th><th>Description</th><th>Actions</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['id']."</td><td>".$row['user_id']."</td><td>".$row['doctor_id']."</td><td>".$row['date']."</td><td>".$row['description']."</td>";
        if ($_SESSION['user_role'] == 'admin') {
            echo "<td><form method='POST'><input type='hidden' name='id' value='".$row['id']."'><input type='hidden' name='action' value='delete'><button type='submit'>Delete</button></form></td></tr>";
        }
    }
    echo "</table>";
} else {
    echo "0 results";
}

$conn->close();
?>
