<?php

session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}


/* ==========================================
   GET CURRENT USER FROM DATABASE
========================================== */

$user_email = $_SESSION['user_email'];

$user_stmt = $conn->prepare(
    "SELECT id, name, email
     FROM users
     WHERE email = ?
     AND role = 'user'
     LIMIT 1"
);

$user_stmt->bind_param("s", $user_email);
$user_stmt->execute();

$user_result = $user_stmt->get_result();
$current_user = $user_result->fetch_assoc();

$user_stmt->close();


/* ==========================================
   GET CURRENT USER'S STARTUP IDEAS
========================================== */

$ideas_result = false;

if ($current_user) {

    $user_id = (int) $current_user['id'];

    /* Keep session data updated */
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $current_user['name'];
    $_SESSION['user_email'] = $current_user['email'];


    $idea_stmt = $conn->prepare(
        "SELECT
            id,
            title,
            description,
            category,
            status,
            created_at
         FROM startup_ideas
         WHERE student_id = ?
         ORDER BY id DESC"
    );

    $idea_stmt->bind_param("i", $user_id);
    $idea_stmt->execute();

    $ideas_result = $idea_stmt->get_result();

} else {

    header("Location: logout.php");
    exit();
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

    <title>My Startup Ideas | Student Startup Hub</title>

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


        /* IDEAS GRID */

        .ideas-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .idea-card {
            background: white;
            padding: 28px;
            border-radius: 20px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
            transition: 0.3s;
        }

        .idea-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.09);
        }

        .idea-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
        }

        .idea-card h2 {
            margin: 0;
            font-size: 24px;
        }


        /* CATEGORY */

        .category {
            display: inline-block;
            background: #eee9ff;
            color: #5b45b9;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 15px;
        }


        /* DESCRIPTION */

        .description {
            color: #777;
            line-height: 1.7;
            margin-bottom: 18px;
        }


        /* DATE */

        .idea-date {
            color: #999;
            font-size: 13px;
            margin-bottom: 16px;
        }


        /* STATUS */

        .status {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
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


        /* STATUS MESSAGE */

        .status-text {
            margin-top: 12px;
            color: #777;
            font-size: 13px;
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
            color: #777;
            margin: 0 0 20px;
        }

        .submit-btn {
            display: inline-block;
            text-decoration: none;
            background: #6c4df6;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 700;
        }

        .submit-btn:hover {
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
        }


        /* RESPONSIVE */

        @media (max-width: 800px) {

            .ideas-grid {
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

        <a href="submit_idea.php">
            Submit Idea
        </a>

        <a href="mentors.php">
            Mentors
        </a>

        <a href="funding.php">
            Funding
        </a>

        <a href="events.php">
            Events
        </a>

        <a href="profile.php">
            Profile
        </a>

    </div>

</header>


<main class="container">


    <section class="hero-box">

        <p>
            My Account
        </p>

        <h1>
            My Startup Ideas 💡
        </h1>

        <span>
            Track your submitted ideas and see their current approval status.
        </span>

    </section>


    <?php if ($ideas_result && $ideas_result->num_rows > 0): ?>


        <section class="ideas-grid">


            <?php while ($idea = $ideas_result->fetch_assoc()): ?>


                <?php

                $status = trim($idea['status']);

                if (strtolower($status) === 'approved') {

                    $status_class = 'approved';

                    $status_message =
                        'Your startup idea has been approved by the admin.';

                } elseif (strtolower($status) === 'rejected') {

                    $status_class = 'rejected';

                    $status_message =
                        'This startup idea was rejected by the admin.';

                } else {

                    $status_class = 'pending';

                    $status_message =
                        'Your startup idea is waiting for admin review.';

                }

                ?>


                <article class="idea-card">


                    <div class="idea-top">


                        <h2>
                            <?php
                            echo htmlspecialchars(
                                $idea['title']
                            );
                            ?>
                        </h2>


                        <span
                            class="status <?php echo $status_class; ?>"
                        >

                            <?php
                            echo htmlspecialchars($status);
                            ?>

                        </span>


                    </div>


                    <span class="category">

                        <?php
                        echo htmlspecialchars(
                            $idea['category']
                        );
                        ?>

                    </span>


                    <div class="description">

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $idea['description']
                            )
                        );

                        ?>

                    </div>


                    <div class="idea-date">

                        Submitted on:

                        <?php

                        echo date(
                            "d M Y",
                            strtotime(
                                $idea['created_at']
                            )
                        );

                        ?>

                    </div>


                    <div class="status-text">

                        <?php
                        echo htmlspecialchars(
                            $status_message
                        );
                        ?>

                    </div>


                </article>


            <?php endwhile; ?>


        </section>


    <?php else: ?>


        <section class="empty-card">


            <div class="empty-icon">
                💡
            </div>


            <h2>
                No Startup Ideas Yet
            </h2>


            <p>
                You haven't submitted a startup idea yet.
            </p>


            <a
                href="submit_idea.php"
                class="submit-btn"
            >
                Submit Your First Idea →
            </a>


        </section>


    <?php endif; ?>


    <div class="bottom-links">


        <a
            href="dashboard.php"
            class="back-btn"
        >
            ← Back to Dashboard
        </a>


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
        Student Startup Hub
    </h3>

    <p>
        Empowering students to innovate, connect and build.
    </p>

</footer>


</body>

</html>

<?php

if (isset($idea_stmt)) {
    $idea_stmt->close();
}

?>