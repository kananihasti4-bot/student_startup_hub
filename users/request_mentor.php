<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = (int) $_SESSION['user_id'];

$message = "";
$message_type = "";

$mentor_id = isset($_GET['mentor_id']) ? (int) $_GET['mentor_id'] : 0;

if ($mentor_id <= 0) {
    header("Location: mentors.php");
    exit();
}

/* Get selected mentor */
$stmt = $conn->prepare(
    "SELECT id, name, role, bio, expertise
     FROM mentors
     WHERE id = ? AND status = 'Active'"
);

$stmt->bind_param("i", $mentor_id);
$stmt->execute();

$result = $stmt->get_result();
$mentor = $result->fetch_assoc();

$stmt->close();

if (!$mentor) {
    header("Location: mentors.php");
    exit();
}


/* Submit mentor request */
if (isset($_POST['send_request'])) {

    $request_message = trim($_POST['message']);

    if ($request_message == "") {

        $message = "Please enter a message.";
        $message_type = "error";

    } else {

        /* Check if request already exists */
        $check = $conn->prepare(
            "SELECT id
             FROM mentor_requests
             WHERE mentor_id = ?
             AND student_id = ?
             AND status = 'Pending'"
        );

        $check->bind_param(
            "ii",
            $mentor_id,
            $student_id
        );

        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = "You already have a pending request for this mentor.";
            $message_type = "error";

        } else {

            $insert = $conn->prepare(
                "INSERT INTO mentor_requests
                (mentor_id, student_id, message, status, mentor_name)
                VALUES (?, ?, ?, 'Pending', ?)"
            );

            $insert->bind_param(
                "iiss",
                $mentor_id,
                $student_id,
                $request_message,
                $mentor['name']
            );

            if ($insert->execute()) {

                $message = "Mentor request sent successfully!";
                $message_type = "success";

            } else {

                $message = "Something went wrong. Please try again.";
                $message_type = "error";
            }

            $insert->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connect With Mentor | Student Startup Hub</title>

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
            color: #555;
            text-decoration: none;
            font-weight: 600;
        }

        .nav-links a:hover {
            color: #6c4df6;
        }

        /* MAIN */

        .container {
            max-width: 950px;
            margin: 0 auto;
            padding: 55px 25px 70px;
        }

        .hero-box {
            background: linear-gradient(135deg, #6c4df6, #8d70ff);
            color: white;
            padding: 38px;
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
            font-size: 38px;
        }

        .hero-box span {
            opacity: 0.9;
        }

        /* MENTOR CARD */

        .mentor-card,
        .form-card {
            background: white;
            border-radius: 22px;
            padding: 32px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
            margin-bottom: 25px;
        }

        .mentor-card {
            display: flex;
            gap: 25px;
            align-items: flex-start;
        }

        .mentor-icon {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #eee9ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
        }

        .mentor-info h2 {
            margin: 0 0 7px;
            font-size: 25px;
        }

        .mentor-role {
            color: #6c4df6;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .mentor-info p {
            margin: 0;
            color: #777;
            line-height: 1.6;
        }

        /* FORM */

        .form-card h2 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .form-card .intro {
            color: #777;
            margin-bottom: 25px;
        }

        .message {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .success {
            background: #e8f8ee;
            color: #21733f;
        }

        .error {
            background: #ffe8e8;
            color: #b42323;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
        }

        textarea {
            width: 100%;
            min-height: 160px;
            resize: vertical;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 11px;
            font-family: inherit;
            font-size: 15px;
            outline: none;
        }

        textarea:focus {
            border-color: #6c4df6;
            box-shadow: 0 0 0 3px rgba(108,77,246,0.10);
        }

        .send-btn {
            width: 100%;
            margin-top: 20px;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #6c4df6;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .send-btn:hover {
            background: #593de0;
        }

        .back-btn {
            display: inline-block;
            margin-top: 5px;
            color: #6c4df6;
            text-decoration: none;
            font-weight: 700;
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
            color: #aaa;
            margin: 0;
        }

        @media (max-width: 700px) {

            .container {
                padding: 35px 15px 50px;
            }

            .hero-box {
                padding: 30px;
            }

            .hero-box h1 {
                font-size: 30px;
            }

            .mentor-card {
                flex-direction: column;
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

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="mentors.php">
            Mentors
        </a>

        <a href="my_ideas.php">
            My Ideas
        </a>

    </div>

</header>


<main class="container">


    <section class="hero-box">

        <p>Mentorship</p>

        <h1>
            Connect With Your Mentor 🤝
        </h1>

        <span>
            Send a request and introduce yourself to your selected mentor.
        </span>

    </section>


    <?php if ($message != ""): ?>

        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <section class="mentor-card">

        <div class="mentor-icon">
            👨‍🏫
        </div>

        <div class="mentor-info">

            <h2>
                <?php echo htmlspecialchars($mentor['name']); ?>
            </h2>

            <div class="mentor-role">
                <?php echo htmlspecialchars($mentor['role']); ?>
            </div>

            <p>
                <?php echo nl2br(htmlspecialchars($mentor['bio'])); ?>
            </p>

        </div>

    </section>


    <section class="form-card">

        <h2>
            Send Mentor Request
        </h2>

        <p class="intro">
            Tell the mentor a little about yourself and what kind of guidance you need.
        </p>


        <form method="POST">

            <label for="message">
                Your Message
            </label>

            <textarea
                id="message"
                name="message"
                placeholder="Example: Hi, I am working on a student startup idea and would like your guidance on product development..."
                required
            ></textarea>


            <button
                type="submit"
                name="send_request"
                class="send-btn"
            >
                Send Mentor Request →
            </button>

        </form>


        <a href="mentors.php" class="back-btn">
            ← Back to Mentors
        </a>

    </section>


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