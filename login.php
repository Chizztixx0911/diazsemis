<?php
require "database.php";

$message = "";

if (isset($_GET["registered"])) {
    $message = "Registration successful! You can now log in.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT id, fullname, username, password, role
         FROM dbsemis
         WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $account = $result->fetch_assoc();

        if (password_verify($password, $account["password"])) {

            session_regenerate_id(true);

            $_SESSION["id"] = $account["id"];
            $_SESSION["fullname"] = $account["fullname"];
            $_SESSION["username"] = $account["username"];
            $_SESSION["role"] = $account["role"];

            if ($account["role"] == "admin") {
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php");
            }

            exit;

        } else {
            $message = "Invalid username or password.";
        }

    } else {
        $message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="register.php">Register</a>
</nav>

<div class="container">

    <h1>Login</h1>

    <p class="message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST">

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>

    </form>

    <p>
        Don't have an account?
        <a href="register.php">Register here</a>
    </p>

</div>

</body>
</html>