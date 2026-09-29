<?php
require "database.php";

if (!isset($_SESSION["id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if ($fullname == "" || $username == "" || $password == "") {

        $message = "Please complete all fields.";

    } elseif (strlen($password) < 4) {

        $message = "Password must be at least 4 characters.";

    } elseif ($role != "user" && $role != "admin") {

        $message = "Invalid account type.";

    } else {

        // Check for an existing username
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
                $message = "Account created successfully!";
            } else {
                $message = "Unable to create account.";
            }
        }
    }
}

// Get all registered accounts
$result = $conn->query(
    "SELECT id, fullname, username, role, `date`
     FROM dbsemis
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="admin.php">Admin</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">

    <h1>Admin Dashboard</h1>

    <h2>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["fullname"]); ?>!
    </h2>

    <p>You are logged in as an administrator.</p>

    <h2>Create Account</h2>

    <p class="success">
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

        <label>Account Type</label>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Create Account</button>

    </form>

    <h2>Registered Accounts</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Role</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>

        <tr>
            <td>
                <?php echo $row["id"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["fullname"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["username"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["role"]); ?>
            </td>
        </tr>

        <?php } ?>

    </table>

    <br>

    <a class="btn" href="logout.php">Logout</a>

</div>

</body>
</html>