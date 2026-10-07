<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* Get active mentors from database */
$mentors_result = mysqli_query(
    $conn,
    "SELECT * FROM mentors
     WHERE status='Active'
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mentors | Student Startup Hub</title>

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
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
        }

        /* MENTORS */

        .mentor-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .mentor-card {
            background: white;
            padding: 28px;
            border-radius: 20px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
            transition: 0.3s;
        }

        .mentor-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.10);
        }

        .mentor-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #eee9ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            margin-bottom: 18px;
        }

        .mentor-card h2 {
            margin: 0 0 7px;
            font-size: 22px;
        }

        .mentor-role {
            color: #6c4df6;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .mentor-bio {
            color: #777;
            line-height: 1.6;
            min-height: 80px;
            margin-bottom: 20px;
        }

        .expertise-box {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .tag {
            background: #f1eeff;
            color: #5b45b9;
            padding: 7px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .connect-btn {
            display: inline-block;
            width: 100%;
            text-align: center;
            text-decoration: none;
            background: #6c4df6;
            color: white;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: 700;
        }

        .connect-btn:hover {
            background: #593de0;
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
            margin: 0;
            color: #777;
        }

        /* INFO */

        .info-box {
            margin-top: 40px;
            background: white;
            padding: 28px;
            border-radius: 20px;
            border: 1px solid #eee;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }

        .info-box h2 {
            margin-top: 0;
        }

        .info-box p {
            color: #777;
            line-height: 1.7;
            margin-bottom: 0;
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

        @media (max-width: 950px) {

            .mentor-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 650px) {

            .mentor-grid {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 35px 15px 50px;
            }

            .hero-box {
                padding: 30px;
            }

            .hero-box h1 {
                font-size: 30px;
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

        <a href="my_ideas.php">
            My Ideas
        </a>

        <a href="funding.php">
            Funding
        </a>

        <a href="events.php">
            Events
        </a>

        <a href="../index.php">
            Home
        </a>

    </div>

</header>


<main class="container">


    <section class="hero-box">

        <p>Student Startup Hub</p>

        <h1>
            Find Your Mentor 👨‍🏫
        </h1>

        <span>
            Connect with experienced professionals and get guidance for your startup journey.
        </span>

    </section>


    <?php if ($mentors_result && mysqli_num_rows($mentors_result) > 0): ?>

        <section class="mentor-grid">

            <?php while ($mentor = mysqli_fetch_assoc($mentors_result)): ?>

                <?php
                $expertise_items = array_map(
                    'trim',
                    explode(',', $mentor['expertise'])
                );
                ?>

                <div class="mentor-card">

                    <div class="mentor-icon">
                        👨‍🏫
                    </div>


                    <h2>
                        <?php echo htmlspecialchars($mentor['name']); ?>
                    </h2>


                    <div class="mentor-role">
                        <?php echo htmlspecialchars($mentor['role']); ?>
                    </div>


                    <div class="mentor-bio">
                        <?php
                        echo nl2br(
                            htmlspecialchars($mentor['bio'])
                        );
                        ?>
                    </div>


                    <div class="expertise-box">

                        <?php foreach ($expertise_items as $item): ?>

                            <?php if ($item !== ""): ?>

                                <span class="tag">
                                    <?php echo htmlspecialchars($item); ?>
                                </span>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    </div>


                    <a href="request_mentor.php?mentor_id=<?php echo (int) $mentor['id']; ?>" class="connect-btn">
                        Connect →
                    </a>

                </div>

            <?php endwhile; ?>

        </section>

    <?php else: ?>


        <section class="empty-card">

            <div class="empty-icon">
                👨‍🏫
            </div>

            <h2>
                No Mentors Available
            </h2>

            <p>
                There are no active mentors available right now.
                Please check again later.
            </p>

        </section>


    <?php endif; ?>


    <section class="info-box">

        <h2>
            Get guidance for your startup 💜
        </h2>

        <p>
            Mentors can help you with technology, business strategy,
            marketing, finance, product development and other important
            parts of your startup journey.
        </p>

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