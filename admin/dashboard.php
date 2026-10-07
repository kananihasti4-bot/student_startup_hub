<?php

session_start();
include("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}


/* ==========================================
   DATABASE COUNTS
========================================== */

/* Total Students */
$user_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users WHERE role = 'user'"
);

$user_data = mysqli_fetch_assoc($user_result);
$total_users = (int) $user_data['total'];


/* Total Startup Ideas */
$idea_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM startup_ideas"
);

$idea_data = mysqli_fetch_assoc($idea_result);
$total_ideas = (int) $idea_data['total'];


/* Pending Startup Ideas */
$pending_idea_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM startup_ideas
     WHERE status = 'Pending'"
);

$pending_idea_data = mysqli_fetch_assoc($pending_idea_result);
$pending_ideas = (int) $pending_idea_data['total'];


/* Total Mentors */
$mentor_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM mentors"
);

$mentor_data = mysqli_fetch_assoc($mentor_result);
$total_mentors = (int) $mentor_data['total'];


/* Pending Mentor Requests */
$request_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM mentor_requests
     WHERE status = 'Pending'"
);

$request_data = mysqli_fetch_assoc($request_result);
$pending_requests = (int) $request_data['total'];


/* Total Events */
$event_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM events"
);

$event_data = mysqli_fetch_assoc($event_result);
$total_events = (int) $event_data['total'];


/* Active Funding Opportunities */
$funding_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM funding
     WHERE status = 'Active'"
);

$funding_data = mysqli_fetch_assoc($funding_result);
$active_funding = (int) $funding_data['total'];


/* Funding Applications */
$funding_application_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM funding_applications"
);

$funding_application_data = mysqli_fetch_assoc(
    $funding_application_result
);

$total_funding_applications =
    (int) $funding_application_data['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard | Student Startup Hub</title>

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

        .dashboard-container {
            max-width: 1250px;
            margin: 0 auto;
            padding: 55px 25px 70px;
        }


        /* HERO */

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


        /* CONTROL GRID */

        .control-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .control-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
            transition: 0.3s;
        }

        .control-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.10);
        }

        .control-icon {
            font-size: 42px;
            margin-bottom: 15px;
        }

        .control-card h3 {
            margin: 0 0 10px;
            font-size: 21px;
        }

        .control-card p {
            color: #777;
            line-height: 1.6;
            min-height: 72px;
            margin: 0 0 20px;
        }

        .control-btn {
            display: inline-block;
            width: 100%;
            text-align: center;
            text-decoration: none;
            background: #6c4df6;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 700;
        }

        .control-btn:hover {
            background: #593de0;
        }


        /* HIGHLIGHT CARD */

        .highlight-section {
            margin-top: 45px;
            background: white;
            padding: 32px;
            border-radius: 22px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
        }

        .highlight-section h2 {
            margin-top: 0;
            font-size: 28px;
        }

        .highlight-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 20px;
        }

        .highlight-item {
            background: #f8f6ff;
            border-radius: 15px;
            padding: 20px;
        }

        .highlight-item strong {
            display: block;
            font-size: 28px;
            color: #6c4df6;
            margin-bottom: 6px;
        }

        .highlight-item span {
            color: #777;
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

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .control-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .highlight-grid {
                grid-template-columns: 1fr;
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
            .control-grid {
                grid-template-columns: 1fr;
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

    </div>

</header>


<main class="dashboard-container">


    <!-- WELCOME -->

    <section class="welcome-box">

        <p class="small-title">
            Admin Panel
        </p>

        <h1>
            Welcome, Admin! 👋
        </h1>

        <p>
            Manage the Student Startup Hub from one place.
        </p>

    </section>


    <!-- STATS -->

    <section class="stats-grid">


        <div class="stat-card">

            <div class="stat-icon">
                👥
            </div>

            <h2>
                <?php echo $total_users; ?>
            </h2>

            <p>
                Total Students
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                💡
            </div>

            <h2>
                <?php echo $total_ideas; ?>
            </h2>

            <p>
                Total Startup Ideas
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                ⏳
            </div>

            <h2>
                <?php echo $pending_ideas; ?>
            </h2>

            <p>
                Pending Ideas
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                👨‍🏫
            </div>

            <h2>
                <?php echo $total_mentors; ?>
            </h2>

            <p>
                Total Mentors
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                🤝
            </div>

            <h2>
                <?php echo $pending_requests; ?>
            </h2>

            <p>
                Pending Mentor Requests
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                📅
            </div>

            <h2>
                <?php echo $total_events; ?>
            </h2>

            <p>
                Total Events
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                💰
            </div>

            <h2>
                <?php echo $active_funding; ?>
            </h2>

            <p>
                Active Funding
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                📋
            </div>

            <h2>
                <?php echo $total_funding_applications; ?>
            </h2>

            <p>
                Funding Applications
            </p>

        </div>


    </section>


    <!-- ADMIN CONTROLS -->

    <section>

        <div class="section-title">

            <h2>
                Admin Controls
            </h2>

            <p>
                Manage students, ideas, mentors, requests, events and funding.
            </p>

        </div>


        <div class="control-grid">


            <!-- USERS -->

            <div class="control-card">

                <div class="control-icon">
                    👥
                </div>

                <h3>
                    Manage Users
                </h3>

                <p>
                    View and manage registered students on the platform.
                </p>

                <a
                    href="manage_users.php"
                    class="control-btn"
                >
                    Manage Users →
                </a>

            </div>


            <!-- IDEAS -->

            <div class="control-card">

                <div class="control-icon">
                    💡
                </div>

                <h3>
                    Manage Startup Ideas
                </h3>

                <p>
                    Review submitted startup ideas and approve or reject them.
                </p>

                <a
                    href="manage_ideas.php"
                    class="control-btn"
                >
                    Manage Ideas →
                </a>

            </div>


            <!-- MENTORS -->

            <div class="control-card">

                <div class="control-icon">
                    👨‍🏫
                </div>

                <h3>
                    Manage Mentors
                </h3>

                <p>
                    Add and manage mentors available to students.
                </p>

                <a
                    href="manage_mentors.php"
                    class="control-btn"
                >
                    Manage Mentors →
                </a>

            </div>


            <!-- MENTOR REQUESTS -->

            <div class="control-card">

                <div class="control-icon">
                    🤝
                </div>

                <h3>
                    Mentor Requests
                </h3>

                <p>
                    Review student requests and approve or reject mentor connections.
                </p>

                <a
                    href="manage_mentor_requests.php"
                    class="control-btn"
                >
                    View Requests →
                </a>

            </div>


            <!-- EVENTS -->

            <div class="control-card">

                <div class="control-icon">
                    📅
                </div>

                <h3>
                    Manage Events
                </h3>

                <p>
                    Add and manage startup workshops, competitions and events.
                </p>

                <a
                    href="manage_events.php"
                    class="control-btn"
                >
                    Manage Events →
                </a>

            </div>


            <!-- FUNDING -->

            <div class="control-card">

                <div class="control-icon">
                    💰
                </div>

                <h3>
                    Manage Funding
                </h3>

                <p>
                    Add grants, funding programs and other startup opportunities.
                </p>

                <a
                    href="manage_funding.php"
                    class="control-btn"
                >
                    Manage Funding →
                </a>

            </div>


            <!-- FUNDING APPLICATIONS -->

            <div class="control-card">

                <div class="control-icon">
                    📋
                </div>

                <h3>
                    Funding Applications
                </h3>

                <p>
                    Review student funding applications and approve or reject them.
                </p>

                <a
                    href="manage_funding_applications.php"
                    class="control-btn"
                >
                    View Applications →
                </a>

            </div>


        </div>

    </section>


    <!-- PLATFORM OVERVIEW -->

    <section class="highlight-section">

        <h2>
            Platform Overview 📊
        </h2>


        <div class="highlight-grid">


            <div class="highlight-item">

                <strong>
                    <?php echo $total_users; ?>
                </strong>

                <span>
                    Students registered
                </span>

            </div>


            <div class="highlight-item">

                <strong>
                    <?php echo $total_mentors; ?>
                </strong>

                <span>
                    Mentors available
                </span>

            </div>


            <div class="highlight-item">

                <strong>
                    <?php echo $active_funding; ?>
                </strong>

                <span>
                    Active funding opportunities
                </span>

            </div>


        </div>

    </section>


    <!-- LOGOUT -->

    <div class="logout-section">

        <a
            href="logout.php"
            class="logout-btn"
        >
            Logout
        </a>

    </div>


</main>


<footer>

    <h3>
        Student Startup Hub - Admin Panel
    </h3>

    <p>
        Manage. Organize. Empower.
    </p>

</footer>


</body>
</html>