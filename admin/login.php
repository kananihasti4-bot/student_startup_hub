<?php
session_start();
include("../config/database.php");

$message = "";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email' AND password='$password' AND role='admin'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {

        $admin = mysqli_fetch_assoc($result);

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role'] = $admin['role'];

        header("Location: dashboard.php");
        exit();

    } else {
        $message = "Invalid admin email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | Student Startup Hub</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #f7f4ff, #ffffff);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 430px;
            background: white;
            padding: 42px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(50, 30, 120, 0.15);
        }

        .brand {
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 30px;
        }

        .brand span {
            color: #6c4df6;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #777;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .message {
            background: #ffecec;
            color: #c62828;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #6c4df6;
        }

        button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #6c4df6;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            background: #593de0;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 22px;
            color: #6c4df6;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>

<div class="login-card">

    <div class="brand">
        Student<span>Startup</span>Hub
    </div>

    <h1>Admin Login 🔐</h1>

    <p class="subtitle">
        Login to manage users, startup ideas and platform activities.
    </p>

    <?php if ($message != ""): ?>
        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Email Address</label>

        <input
            type="email"
            name="email"
            placeholder="Admin email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Admin password"
            required
        >

        <button type="submit" name="login">
            Login as Admin
        </button>

    </form>

    <a href="../index.php" class="back">
        ← Back to Home
    </a>

</div>

</body>
</html>