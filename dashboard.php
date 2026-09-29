<?php
require "database.php";

if (!isset($_SESSION["id"]) || $_SESSION["role"] != "user") {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">

    <h1>User Dashboard</h1>

    <h2>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["fullname"]); ?>!
    </h2>

    <p>You have successfully logged in.</p>

    <p>
        <b>Username:</b>
        <?php echo htmlspecialchars($_SESSION["username"]); ?>
    </p>

    <p><b>Account Type:</b> User</p>

    <a class="btn" href="logout.php">Logout</a>

</div>

</body>
</html>