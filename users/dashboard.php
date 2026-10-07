<?php

session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


/* ==============================
   LOGGED-IN USER DETAILS
============================== */

$user_id = (int) $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];


/* ==============================
   COUNT USER'S STARTUP IDEAS
============================== */

$idea_stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM startup_ideas
     WHERE student_id = ?"
);

$idea_stmt->bind_param("i", $user_id);
$idea_stmt->execute();

$idea_result = $idea_stmt->get_result();
$idea_data = $idea_result->fetch_assoc();

$total_ideas = (int) $idea_data['total'];

$idea_stmt->close();


/* ==============================
   COUNT APPROVED MENTOR CONNECTIONS
============================== */

$mentor_count_stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM mentor_requests
     WHERE student_id = ?
     AND status = 'Approved'"
);

$mentor_count_stmt->bind_param("i", $user_id);
$mentor_count_stmt->execute();

$mentor_count_result = $mentor_count_stmt->get_result();
$mentor_count_data = $mentor_count_result->fetch_assoc();

$mentors_connected = (int) $mentor_count_data['total'];

$mentor_count_stmt->close();


/* ==============================
   COUNT EVENTS JOINED
============================== */

$event_count_stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM event_registrations
     WHERE student_id = ?"
);

$event_count_stmt->bind_param("i", $user_id);
$event_count_stmt->execute();

$event_count_result = $event_count_stmt->get_result();
$event_count_data = $event_count_result->fetch_assoc();

$events_joined = (int) $event_count_data['total'];

$event_count_stmt->close();


/* ==============================
   GET APPROVED MENTORS
============================== */

$approved_mentors_stmt = $conn->prepare(
    "SELECT mentor_name, created_at
     FROM mentor_requests
     WHERE student_id = ?
     AND status = 'Approved'
     ORDER BY id DESC"
);

$approved_mentors_stmt->bind_param("i", $user_id);
$approved_mentors_stmt->execute();

$approved_mentors_result = $approved_mentors_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>User Dashboard | Student Startup Hub</title>

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
            gap: 28px;
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

        .dashboard-container {
            max-width: 1250px;
            margin: 0 auto;
            padding: 55px 25px 70px;
        }


        /* WELCOME */

        .welcome-box {
            background: linear-gradient(135deg, #6c4df6, #8d70ff);
            color: white;
            padding: 45px 52px;
            border-radius: 25px;
            margin-bottom: 38px;
            box-shadow: 0 20px 45px rgba(108,77,246,0.20);
        }

        .welcome-box .small-title {
            margin: 0 0 10px;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.85;
        }

        .welcome-box h1 {
            margin: 0 0 12px;
            font-size: 43px;
        }

        .welcome-box p {
            margin: 0;
            font-size: 17px;
            opacity: 0.92;
        }


        /* STATS */

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
            margin-bottom: 48px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 20px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 34px;
            margin-bottom: 12px;
        }

        .stat-card h2 {
            margin: 0 0 6px;
            font-size: 34px;
            color: #6c4df6;
        }

        .stat-card p {
            margin: 0;
            color: #777;
        }


        /* SECTION */

        .section-title {
            margin-bottom: 24px;
        }

        .section-title h2 {
            margin: 0 0 8px;
            font-size: 32px;
        }

        .section-title p {
            margin: 0;
            color: #777;
        }


        /* FEATURE CARDS */

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
            transition: 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.10);
        }

        .feature-icon {
            font-size: 42px;
            margin-bottom: 16px;
        }

        .feature-card h3 {
            margin: 0 0 10px;
            font-size: 21px;
        }

        .feature-card p {
            margin: 0 0 20px;
            color: #777;
            line-height: 1.6;
            min-height: 72px;
        }

        .feature-card a {
            color: #6c4df6;
            text-decoration: none;
            font-weight: 700;
        }

        .feature-card a:hover {
            color: #4f39bf;
        }


        /* MENTOR CONNECTION */

        .mentor-section {
            margin-top: 45px;
            background: white;
            padding: 35px;
            border-radius: 22px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
        }

        .mentor-section h2 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .mentor-intro {
            color: #777;
            margin-bottom: 25px;
        }

        .mentor-list {
            display: grid;
            gap: 15px;
        }

        .mentor-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 18px;
            border-radius: 14px;
            background: #f8f6ff;
            border: 1px solid #eee9ff;
        }

        .mentor-item-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .mentor-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #eee9ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 27px;
            flex-shrink: 0;
        }

        .mentor-item h3 {
            margin: 0 0 4px;
            font-size: 18px;
        }

        .mentor-item p {
            margin: 0;
            color: #777;
            font-size: 13px;
        }

        .approved-badge {
            background: #e4f8ec;
            color: #21733f;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }


        /* ACCOUNT INFO */

        .info-section {
            margin-top: 45px;
            background: white;
            padding: 35px;
            border-radius: 22px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
        }

        .info-section h2 {
            margin-top: 0;
            font-size: 28px;
        }

        .info-section p {
            color: #666;
            line-height: 1.7;
        }

        .info-row {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 15px;
            margin: 14px 0;
        }

        .info-label {
            font-weight: 700;
        }

        .info-value {
            color: #666;
        }


        /* LOGOUT */

        .logout-section {
            text-align: center;
            margin-top: 45px;
        }

        .logout-btn {
            display: inline-block;
            background: #6c4df6;
            color: white;
            text-decoration: none;
            padding: 14px 38px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
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

        @media (max-width: 1050px) {

            .stats-grid,
            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }


        @media (max-width: 700px) {

            .navbar {
                padding: 16px 5%;
            }

            .dashboard-container {
                padding: 35px 15px 50px;
            }

            .welcome-box {
                padding: 32px 25px;
            }

            .welcome-box h1 {
                font-size: 32px;
            }

            .stats-grid,
            .feature-grid {
                grid-template-columns: 1fr;
            }

            .info-row {
                grid-template-columns: 1fr;
                gap: 5px;
            }

            .mentor-item {
                flex-direction: column;
                align-items: flex-start;
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

        <a href="submit_idea.php">
            Submit Idea
        </a>

        <a href="my_ideas.php">
            My Ideas
        </a>

        <a href="profile.php">
            Profile
        </a>

    </div>

</header>


<main class="dashboard-container">


    <!-- WELCOME -->

    <section class="welcome-box">

        <p class="small-title">
            Student Startup Hub
        </p>

        <h1>
            Welcome, <?php echo htmlspecialchars($user_name); ?>! 👋
        </h1>

        <p>
            Turn your ideas into reality and build something amazing.
        </p>

    </section>


    <!-- STATS -->

    <section class="stats-grid">


        <!-- STARTUP IDEAS -->

        <div class="stat-card">

            <div class="stat-icon">
                💡
            </div>

            <h2>
                <?php echo $total_ideas; ?>
            </h2>

            <p>
                My Startup Ideas
            </p>

        </div>


        <!-- MENTORS -->

        <div class="stat-card">

            <div class="stat-icon">
                👨‍🏫
            </div>

            <h2>
                <?php echo $mentors_connected; ?>
            </h2>

            <p>
                Mentors Connected
            </p>

        </div>


        <!-- EVENTS -->

        <div class="stat-card">

            <div class="stat-icon">
                📅
            </div>

            <h2>
                <?php echo $events_joined; ?>
            </h2>

            <p>
                Events Joined
            </p>

        </div>


        <!-- ACHIEVEMENTS -->

        <div class="stat-card">

            <div class="stat-icon">
                🏆
            </div>

            <h2>
                0
            </h2>

            <p>
                Achievements
            </p>

        </div>


    </section>


    <!-- FEATURES -->

    <section>

        <div class="section-title">

            <h2>
                Explore Student Startup Hub
            </h2>

            <p>
                Everything you need to turn your startup idea into something real.
            </p>

        </div>


        <div class="feature-grid">


            <!-- MY IDEAS -->

            <div class="feature-card">

                <div class="feature-icon">
                    💡
                </div>

                <h3>
                    My Startup Ideas
                </h3>

                <p>
                    Submit new ideas and track the approval status of your startup ideas.
                </p>

                <a href="my_ideas.php">
                    View My Ideas →
                </a>

            </div>


            <!-- MENTORS -->

            <div class="feature-card">

                <div class="feature-icon">
                    👨‍🏫
                </div>

                <h3>
                    Mentorship
                </h3>

                <p>
                    Connect with experienced mentors who can guide you through your startup journey.
                </p>

                <a href="mentors.php">
                    Find Mentor →
                </a>

            </div>


            <!-- FUNDING -->

            <div class="feature-card">

                <div class="feature-icon">
                    💰
                </div>

                <h3>
                    Funding
                </h3>

                <p>
                    Explore funding opportunities, grants and startup resources.
                </p>

                <a href="funding.php">
                    Explore Funding →
                </a>

            </div>


            <!-- EVENTS -->

            <div class="feature-card">

                <div class="feature-icon">
                    📅
                </div>

                <h3>
                    Events
                </h3>

                <p>
                    Discover workshops, startup competitions and entrepreneurship events.
                </p>

                <a href="events.php">
                    View Events →
                </a>

            </div>


        </div>

    </section>


    <!-- APPROVED MENTORS -->

    <section class="mentor-section">

        <h2>
            My Mentor Connections 🤝
        </h2>

        <p class="mentor-intro">
            These are the mentors whose connection requests have been approved.
        </p>


        <?php if (
            $approved_mentors_result &&
            $approved_mentors_result->num_rows > 0
        ): ?>


            <div class="mentor-list">


                <?php while (
                    $approved_mentor =
                    $approved_mentors_result->fetch_assoc()
                ): ?>


                    <div class="mentor-item">


                        <div class="mentor-item-left">


                            <div class="mentor-avatar">
                                👨‍🏫
                            </div>


                            <div>

                                <h3>
                                    <?php
                                    echo htmlspecialchars(
                                        $approved_mentor['mentor_name']
                                    );
                                    ?>
                                </h3>


                                <p>

                                    Connection approved

                                    •

                                    <?php

                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $approved_mentor['created_at']
                                        )
                                    );

                                    ?>

                                </p>

                            </div>


                        </div>


                        <span class="approved-badge">
                            ✓ Approved
                        </span>


                    </div>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <div class="mentor-item">


                <div class="mentor-item-left">


                    <div class="mentor-avatar">
                        👨‍🏫
                    </div>


                    <div>

                        <h3>
                            No mentor connections yet
                        </h3>

                        <p>
                            Find a mentor and send a connection request.
                        </p>

                    </div>


                </div>


                <a
                    href="mentors.php"
                    style="
                        color:#6c4df6;
                        text-decoration:none;
                        font-weight:700;
                    "
                >
                    Find Mentor →
                </a>


            </div>


        <?php endif; ?>


    </section>


    <!-- ACCOUNT INFO -->

    <section class="info-section">


        <h2>
            Your Account
        </h2>


        <p>
            You are currently logged in to your Student Startup Hub account.
        </p>


        <div class="info-row">


            <div class="info-label">
                Name
            </div>


            <div class="info-value">
                <?php echo htmlspecialchars($user_name); ?>
            </div>


        </div>


        <div class="info-row">


            <div class="info-label">
                Email
            </div>


            <div class="info-value">
                <?php echo htmlspecialchars($user_email); ?>
            </div>


        </div>


    </section>


    <!-- LOGOUT -->

    <div class="logout-section">

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

$approved_mentors_stmt->close();

?>