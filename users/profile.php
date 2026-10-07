<?php

session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

$message = "";
$message_type = "";


/* =========================
   GET CURRENT USER
========================= */

$user_stmt = $conn->prepare(
    "SELECT name, email
     FROM users
     WHERE id = ?"
);

$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();

$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();

$user_stmt->close();


/* =========================
   UPDATE PROFILE
========================= */

if (isset($_POST['update_profile'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name === "" || $email === "") {

        $message = "Name and email are required.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        /* Check whether email belongs to another user */
        $check_stmt = $conn->prepare(
            "SELECT id
             FROM users
             WHERE email = ? AND id != ?"
        );

        $check_stmt->bind_param("si", $email, $user_id);
        $check_stmt->execute();

        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {

            $message = "This email address is already in use.";
            $message_type = "error";

        } else {

            $update_stmt = $conn->prepare(
                "UPDATE users
                 SET name = ?, email = ?
                 WHERE id = ?"
            );

            $update_stmt->bind_param(
                "ssi",
                $name,
                $email,
                $user_id
            );

            if ($update_stmt->execute()) {

                /* Update session values */
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;

                /* Update displayed values */
                $user_data['name'] = $name;
                $user_data['email'] = $email;

                $message = "Your profile has been updated successfully.";
                $message_type = "success";

            } else {

                $message = "Something went wrong. Please try again.";
                $message_type = "error";
            }

            $update_stmt->close();
        }

        $check_stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Profile | Student Startup Hub</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f7f5ff;
            font-family: Arial, Helvetica, sans-serif;
            color: #1d2330;
        }

        /* NAVBAR */

        .navbar {
            background: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 100;
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
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #555;
            font-weight: 600;
        }

        .nav-links a:hover {
            color: #6c4df6;
        }

        /* MAIN */

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 55px 25px 70px;
        }

        /* HERO */

        .hero-box {
            background: linear-gradient(135deg, #6c4df6, #8d70ff);
            color: white;
            padding: 40px;
            border-radius: 24px;
            margin-bottom: 30px;
            box-shadow: 0 20px 45px rgba(108,77,246,0.20);
        }

        .hero-box p {
            margin: 0 0 10px;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 13px;
        }

        .hero-box h1 {
            margin: 0 0 10px;
            font-size: 40px;
        }

        .hero-box span {
            opacity: 0.9;
            line-height: 1.6;
        }

        /* MESSAGE */

        .message {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .success {
            background: #e8f8ee;
            color: #21733f;
        }

        .error {
            background: #ffe8e8;
            color: #b42323;
        }

        /* PROFILE CARD */

        .profile-card {
            background: white;
            padding: 35px;
            border-radius: 22px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
        }

        .profile-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #eee9ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 20px;
        }

        .profile-card h2 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .profile-card .intro {
            color: #777;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 22px;
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
            box-shadow: 0 0 0 3px rgba(108,77,246,0.10);
        }

        .update-btn {
            border: none;
            background: #6c4df6;
            color: white;
            padding: 14px 25px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .update-btn:hover {
            background: #593de0;
        }

        .account-note {
            margin-top: 25px;
            padding: 16px;
            background: #f8f6ff;
            border-radius: 12px;
            color: #777;
            line-height: 1.6;
            font-size: 14px;
        }

        /* BOTTOM */

        .bottom-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 35px;
        }

        .back-btn {
            color: #6c4df6;
            text-decoration: none;
            font-weight: 700;
        }

        .logout-btn {
            background: #6c4df6;
            color: white;
            text-decoration: none;
            padding: 13px 25px;
            border-radius: 10px;
            font-weight: 700;
        }

        .logout-btn:hover {
            background: #593de0;
        }

        /* FOOTER */

        footer {
            background: #17171c;
            color: white;
            text-align: center;
            padding: 35px 7%;
        }

        footer h3 {
            margin: 0 0 10px;
        }

        footer p {
            margin: 0;
            color: #aaa;
        }

        /* RESPONSIVE */

        @media (max-width: 650px) {

            .container {
                padding: 35px 15px 50px;
            }

            .hero-box {
                padding: 30px;
            }

            .hero-box h1 {
                font-size: 30px;
            }

            .profile-card {
                padding: 25px;
            }

            .bottom-links {
                flex-direction: column;
                gap: 20px;
            }

            .nav-links a {
                display: none;
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

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="my_ideas.php">
            My Ideas
        </a>

    </div>

</header>


<main class="container">


    <section class="hero-box">

        <p>
            My Account
        </p>

        <h1>
            My Profile 👤
        </h1>

        <span>
            View and update your Student Startup Hub account details.
        </span>

    </section>


    <?php if ($message !== ""): ?>

        <div class="message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <section class="profile-card">

        <div class="profile-icon">
            👤
        </div>

        <h2>
            Account Details
        </h2>

        <p class="intro">
            Keep your name and email address up to date.
        </p>


        <form method="POST">


            <div class="form-group">

                <label>
                    Full Name
                </label>

                <input
                    type="text"
                    name="name"
                    value="<?php
                    echo htmlspecialchars($user_data['name']);
                    ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    value="<?php
                    echo htmlspecialchars($user_data['email']);
                    ?>"
                    required
                >

            </div>


            <button
                type="submit"
                name="update_profile"
                class="update-btn"
            >
                Save Changes
            </button>


        </form>


        <div class="account-note">

            Your startup ideas, mentor connections, event registrations
            and funding applications remain linked to your account.

        </div>


    </section>


    <div class="bottom-links">

        <a href="dashboard.php" class="back-btn">
            ← Back to Dashboard
        </a>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </div>


</main>


<footer>

    <h3>
        Student Startup Hub
    </h3>

    <p>
        Empowering students to innovate, connect and build.
    </p>

</footer>


</body>

</html>