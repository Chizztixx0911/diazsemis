<?php
require "database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if ($fullname == "" || $username == "" || $password == "") {

        $message = "Please complete all fields.";

    } elseif (strlen($password) < 4) {

        $message = "Password must be at least 4 characters.";

    } else {

        // Check if username already exists
        $stmt = $conn->prepare(
            "SELECT id FROM dbsemis WHERE username = ?"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $message = "Username already exists.";

        } else {

            $hashed = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $role = "user";

            $stmt = $conn->prepare(
                "INSERT INTO dbsemis
                (fullname, username, password, role)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $fullname,
                $username,
                $hashed,
                $role
            );

            if ($stmt->execute()) {
                header("Location: login.php?registered=1");
                exit;
            } else {
                $message = "Registration failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="register.php">Register</a>
    <a href="login.php">Login</a>
</nav>

<div class="container">

    <h1>Create an Account</h1>

    <p class="message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST">

        <label>Full Name</label>
        <input type="text" name="fullname"
               maxlength="50" required>

        <label>Username</label>
        <input type="text" name="username"
               maxlength="50" required>

        <label>Password</label>
        <input type="password" name="password"
               minlength="4" required>

        <button type="submit">Register</button>

    </form>

    <p>
        Already have an account?
        <a href="login.php">Login here</a>
    </p>

</div>

</body>
</html>