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
            $name = $_POST['name'];
            $description = $_POST['description'];
            $sql = "INSERT INTO specialties (name, description) VALUES ('$name', '$description')";
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } elseif ($action == 'update') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $sql = "UPDATE specialties SET name='$name', description='$description' WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
                echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } elseif ($action == 'delete') {
            $id = $_POST['id'];
            $sql = "DELETE FROM specialties WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
                echo "Record deleted successfully";
            } else {
                echo "Error deleting record: " . $conn->error;
            }
        }
    }
}

$sql = "SELECT * FROM specialties";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>Name</th><th>Description</th><th>Actions</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['id']."</td><td>".$row['name']."</td><td>".$row['description']."</td>";
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
