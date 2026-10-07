<?php
session_start();
include("../config/database.php");

$message = "";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email' AND password='$password' AND role='user'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit();

    } else {
        $message = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Login | Student Startup Hub</title>

    <link rel="stylesheet" href="../style.css">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #f7f4ff, #ffffff);
            font-family: Arial, Helvetica, sans-serif;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .login-card {
            width: 420px;
            background: white;
            padding: 42px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(50, 30, 120, 0.15);
        }

        .login-card h1 {
            margin-bottom: 10px;
            font-size: 34px;
            color: #17171c;
        }

        .login-card .subtitle {
            color: #777;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .message {
            background: #ffecec;
            color: #c62828;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #6c4df6;
            box-shadow: 0 0 0 3px rgba(108, 77, 246, 0.1);
        }

        .login-button {
            width: 100%;
            padding: 15px;
            background: #6c4df6;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .login-button:hover {
            background: #5b3fe0;
        }

        .bottom-text {
            text-align: center;
            margin-top: 25px;
            color: #777;
        }

        .bottom-text a {
            color: #6c4df6;
            font-weight: 700;
            text-decoration: none;
        }

        .home-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #555;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <h1>Welcome Back 👋</h1>

        <p class="subtitle">
            Login to your Student Startup Hub account and continue your startup journey.
        </p>

        <?php if ($message != ""): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Email Address</label>
                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <button type="submit" name="login" class="login-button">
                Login to Account
            </button>

        </form>

        <div class="bottom-text">
            Don't have an account?
            <a href="register.php">Create Account</a>
        </div>

        <a href="../index.php" class="home-link">
            ← Back to Home
        </a>

    </div>

</div>

</body>
</html>