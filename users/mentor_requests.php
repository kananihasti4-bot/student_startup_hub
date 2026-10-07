<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = (int) $_SESSION['user_id'];

/* Get this student's mentor requests */
$stmt = $conn->prepare(
    "SELECT id, mentor_name, message, status, created_at
     FROM mentor_requests
     WHERE student_id = ?
     ORDER BY id DESC"
);

$stmt->bind_param("i", $student_id);
$stmt->execute();

$requests_result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Mentor Requests | Student Startup Hub</title>

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
            align-items: center;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 55px 25px 70px;
        }

        /* HERO */

        .hero-box {
            background: linear-gradient(135deg, #6c4df6, #8d70ff);
            color: white;
            padding: 40px;
            border-radius: 24px;
            margin-bottom: 40px;
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

        /* REQUESTS */

        .request-grid {
            display: grid;
            gap: 22px;
        }

        .request-card {
            background: white;
            padding: 28px;
            border-radius: 20px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
        }

        .request-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .request-card h2 {
            margin: 0 0 7px;
            font-size: 23px;
        }

        .request-date {
            color: #999;
            font-size: 13px;
        }

        .request-message {
            margin: 20px 0;
            padding: 16px;
            background: #f8f6ff;
            border-radius: 12px;
            color: #666;
            line-height: 1.6;
        }

        /* STATUS */

        .status {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .pending {
            background: #fff3d6;
            color: #8a5a00;
        }

        .approved {
            background: #e4f8ec;
            color: #21733f;
        }

        .rejected {
            background: #ffe8e8;
            color: #b42323;
        }

        .status-message {
            font-size: 13px;
            color: #777;
            line-height: 1.5;
        }

        /* EMPTY */

        .empty-card {
            background: white;
            padding: 60px 30px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
        }

        .empty-icon {
            font-size: 55px;
            margin-bottom: 15px;
        }

        .empty-card h2 {
            margin: 0 0 10px;
        }

        .empty-card p {
            margin: 0 0 20px;
            color: #777;
        }

        .mentor-btn {
            display: inline-block;
            text-decoration: none;
            background: #6c4df6;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 700;
        }

        .mentor-btn:hover {
            background: #593de0;
        }

        /* BOTTOM */

        .bottom-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
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
            color: #aaa;
            margin: 0;
        }

        /* RESPONSIVE */

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

            .request-top {
                flex-direction: column;
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

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="mentors.php">
            Mentors
        </a>

        <a href="my_ideas.php">
            My Ideas
        </a>

        <a href="funding.php">
            Funding
        </a>

        <a href="events.php">
            Events
        </a>

    </div>

</header>


<main class="container">


    <section class="hero-box">

        <p>My Account</p>

        <h1>
            My Mentor Requests 🤝
        </h1>

        <span>
            Track the mentor requests you have sent and see their current status.
        </span>

    </section>


    <?php if ($requests_result && $requests_result->num_rows > 0): ?>


        <section class="request-grid">


            <?php while ($request = $requests_result->fetch_assoc()): ?>

                <?php

                $status = trim($request['status']);
                $status_lower = strtolower($status);

                if ($status_lower === 'approved') {

                    $status_class = 'approved';
                    $status_message =
                        'Your mentor request has been approved! You are now connected with this mentor.';

                } elseif ($status_lower === 'rejected') {

                    $status_class = 'rejected';
                    $status_message =
                        'This mentor request was rejected by the admin.';

                } else {

                    $status_class = 'pending';
                    $status_message =
                        'Your mentor request is waiting for admin review.';

                }

                ?>

                <article class="request-card">


                    <div class="request-top">

                        <div>

                            <h2>
                                <?php echo htmlspecialchars($request['mentor_name']); ?>
                            </h2>

                            <div class="request-date">

                                Requested on:

                                <?php
                                echo date(
                                    "d M Y, h:i A",
                                    strtotime($request['created_at'])
                                );
                                ?>

                            </div>

                        </div>


                        <span class="status <?php echo $status_class; ?>">
                            <?php echo htmlspecialchars($status); ?>
                        </span>

                    </div>


                    <div class="request-message">

                        <strong>
                            Your message:
                        </strong>

                        <br><br>

                        <?php
                        echo nl2br(
                            htmlspecialchars($request['message'])
                        );
                        ?>

                    </div>


                    <div class="status-message">

                        <?php echo htmlspecialchars($status_message); ?>

                    </div>


                </article>

            <?php endwhile; ?>


        </section>


    <?php else: ?>


        <section class="empty-card">

            <div class="empty-icon">
                🤝
            </div>

            <h2>
                No Mentor Requests Yet
            </h2>

            <p>
                You haven't sent a mentor connection request yet.
            </p>

            <a href="mentors.php" class="mentor-btn">
                Find a Mentor →
            </a>

        </section>


    <?php endif; ?>


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

<?php
$stmt->close();
?>