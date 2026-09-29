<?php
require "database.php";

$username = "admin";
$fullname = "Administrator";
$password = password_hash("123", PASSWORD_DEFAULT);
$role = "admin";

// Check if admin already exists
$stmt = $conn->prepare(
    "SELECT id FROM dbsemis WHERE username = ?"
);

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    $stmt = $conn->prepare(
        "INSERT INTO dbsemis
        (fullname, username, password, role)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssss",
        $fullname,
        $username,
        $password,
        $role
    );

    if ($stmt->execute()) {
        echo "Admin account created successfully!";
    } else {
        echo "Error creating admin account.";
    }

} else {
    echo "Admin account already exists.";
}
?>