<?php
session_start();
include("../config/database.php");

$message = "";
$message_type = "";

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = "user";

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {

        $message = "This email is already registered. Please use another email.";
        $message_type = "error";

    } else {

        // Insert new user
        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password, role)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {

            $message = "Registration successful! You can now login.";
            $message_type = "success";

        } else {

            $message = "Something went wrong. Please try again.";
            $message_type = "error";
        }

        $stmt->close();
    }

    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | Student Startup Hub</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #f7f4ff, #ffffff);
            color: #1d2330;
        }

        /* Navbar */

        .navbar {
            background: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

        .logo {
            font-size: 25px;
            font-weight: 800;
        }

        .logo span {
            color: #6c4df6;
        }

        .nav-links {
            display: flex;
            gap: 25px;
        }

        .nav-links a {
            text-decoration: none;
            color: #555;
            font-weight: 600;
        }

        .nav-links a:hover {
            color: #6c4df6;
        }

        /* Page */

        .page-wrapper {
            min-height: calc(100vh - 80px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px 20px;
        }

        .register-card {
            width: 450px;
            background: white;
            padding: 42px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(50, 30, 120, 0.15);
            border: 1px solid #eee;
        }

        .register-card h1 {
            margin: 0 0 10px;
            font-size: 34px;
        }

        .subtitle {
            color: #777;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Messages */

        .message {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 22px;
            font-weight: 600;
            line-height: 1.5;
        }

        .success {
            background: #e8f8ee;
            color: #21733f;
        }

        .error {
            background: #ffe8e8;
            color: #b42323;
        }

        /* Form */

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .form-group input {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #6c4df6;
            box-shadow: 0 0 0 3px rgba(108, 77, 246, 0.10);
        }

        .register-button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #6c4df6;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 5px;
        }

        .register-button:hover {
            background: #593de0;
        }

        /* Bottom */

        .bottom-text {
            text-align: center;
            color: #777;
            margin-top: 25px;
            line-height: 1.6;
        }

        .bottom-text a {
            color: #6c4df6;
            text-decoration: none;
            font-weight: 700;
        }

        .home-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #555;
            text-decoration: none;
        }

        .home-link:hover {
            color: #6c4df6;
        }

        @media (max-width: 600px) {

            .navbar {
                padding: 16px 5%;
            }

            .nav-links {
                display: none;
            }

            .register-card {
                padding: 28px 22px;
            }

            .register-card h1 {
                font-size: 29px;
            }
        }

    </style>

</head>

<body>

<header class="navbar">

    <div class="logo">
        Student<span>Startup</span>Hub
    </div>

    <div class="nav-links">

        <a href="../index.php">
            Home
        </a>

        <a href="login.php">
            Login
        </a>

    </div>

</header>


<div class="page-wrapper">

    <div class="register-card">

        <h1>Create Your Account 🚀</h1>

        <p class="subtitle">
            Join Student Startup Hub and start turning your ideas into reality.
        </p>


        <?php if ($message != ""): ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    maxlength="100"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    maxlength="100"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    minlength="6"
                    required
                >

            </div>


            <button
                type="submit"
                name="register"
                class="register-button"
            >
                Create Account
            </button>

        </form>


        <div class="bottom-text">

            Already have an account?
            <a href="login.php">
                Login here
            </a>

        </div>


        <a href="../index.php" class="home-link">
            ← Back to Home
        </a>

    </div>

</div>

</body>

</html>