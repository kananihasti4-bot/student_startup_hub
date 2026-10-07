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
   FUNDING APPLICATION
   ========================= */

if (isset($_POST['apply_funding'])) {

    $funding_id = (int) $_POST['funding_id'];

    /* Check whether funding opportunity exists and is active */
    $check_funding_stmt = $conn->prepare(
        "SELECT id
         FROM funding
         WHERE id = ? AND status = 'Active'"
    );

    $check_funding_stmt->bind_param("i", $funding_id);
    $check_funding_stmt->execute();

    $check_funding_result = $check_funding_stmt->get_result();

    if ($check_funding_result->num_rows === 0) {

        $message = "This funding opportunity is no longer available.";
        $message_type = "error";

    } else {

        /* Check whether student has already applied */
        $check_application_stmt = $conn->prepare(
            "SELECT id
             FROM funding_applications
             WHERE funding_id = ? AND student_id = ?"
        );

        $check_application_stmt->bind_param(
            "ii",
            $funding_id,
            $user_id
        );

        $check_application_stmt->execute();

        $check_application_result =
            $check_application_stmt->get_result();

        if ($check_application_result->num_rows > 0) {

            $message = "You have already applied for this funding opportunity.";
            $message_type = "error";

        } else {

            /* Create funding application */
            $insert_application_stmt = $conn->prepare(
                "INSERT INTO funding_applications
                 (funding_id, student_id, status)
                 VALUES (?, ?, 'Pending')"
            );

            $insert_application_stmt->bind_param(
                "ii",
                $funding_id,
                $user_id
            );

            if ($insert_application_stmt->execute()) {

                $message =
                    "Your funding application has been submitted successfully.";

                $message_type = "success";

            } else {

                $message =
                    "Something went wrong. Please try again.";

                $message_type = "error";
            }

            $insert_application_stmt->close();
        }

        $check_application_stmt->close();
    }

    $check_funding_stmt->close();
}


/* =========================
   GET ACTIVE FUNDING
   ========================= */

$funding_result = mysqli_query(
    $conn,
    "SELECT *
     FROM funding
     WHERE status = 'Active'
     ORDER BY id DESC"
);


/* =========================
   GET STUDENT APPLICATIONS
   ========================= */

$applied_funding_ids = [];

$application_stmt = $conn->prepare(
    "SELECT funding_id
     FROM funding_applications
     WHERE student_id = ?"
);

$application_stmt->bind_param("i", $user_id);
$application_stmt->execute();

$application_result = $application_stmt->get_result();

while ($application = $application_result->fetch_assoc()) {

    $applied_funding_ids[] = (int) $application['funding_id'];

}

$application_stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Funding Opportunities | Student Startup Hub</title>

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
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
        }


        /* MESSAGE */

        .funding-message {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .funding-message.success {
            background: #e8f8ee;
            color: #21733f;
        }

        .funding-message.error {
            background: #ffe8e8;
            color: #b42323;
        }


        /* FUNDING GRID */

        .funding-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .funding-card {
            background: white;
            padding: 28px;
            border-radius: 20px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
            transition: 0.3s;
        }

        .funding-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.10);
        }

        .funding-icon {
            width: 68px;
            height: 68px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 18px;
            background: #eee9ff;
            font-size: 34px;
            margin-bottom: 18px;
        }

        .funding-card h2 {
            margin: 0 0 8px;
            font-size: 22px;
        }

        .funding-type {
            color: #6c4df6;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .funding-description {
            color: #777;
            line-height: 1.6;
            min-height: 80px;
        }

        .funding-meta {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 20px 0;
            padding: 14px;
            background: #f8f6ff;
            border-radius: 12px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .meta-label {
            color: #999;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .meta-value {
            font-weight: 700;
            color: #333;
        }

        .active-badge {
            display: inline-block;
            background: #e4f8ec;
            color: #21733f;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }


        /* APPLY BUTTON */

        .apply-button {
            width: 100%;
            margin-top: 18px;
            padding: 13px 16px;
            border: none;
            border-radius: 10px;
            background: #6c4df6;
            color: white;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .apply-button:hover {
            background: #593de0;
        }

        .applied-button {
            width: 100%;
            margin-top: 18px;
            padding: 13px 16px;
            border-radius: 10px;
            background: #e4f8ec;
            color: #21733f;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
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
            line-height: 1.6;
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

            .funding-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 650px) {

            .funding-grid {
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

        <a href="mentors.php">
            Mentors
        </a>

        <a href="events.php">
            Events
        </a>

        <a href="my_ideas.php">
            My Ideas
        </a>

        <a href="../index.php">
            Home
        </a>

    </div>

</header>


<main class="container">


    <section class="hero-box">

        <p>
            Student Startup Hub
        </p>

        <h1>
            Explore Funding Opportunities 💰
        </h1>

        <span>
            Discover grants, startup programs and funding options that can help turn your idea into reality.
        </span>

    </section>


    <?php if ($message != ""): ?>

        <div class="funding-message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <?php if ($funding_result && mysqli_num_rows($funding_result) > 0): ?>


        <section class="funding-grid">


            <?php while ($fund = mysqli_fetch_assoc($funding_result)): ?>


                <div class="funding-card">


                    <div class="funding-icon">
                        💰
                    </div>


                    <h2>

                        <?php
                        echo htmlspecialchars($fund['title']);
                        ?>

                    </h2>


                    <div class="funding-type">

                        <?php
                        echo htmlspecialchars($fund['type']);
                        ?>

                    </div>


                    <div class="funding-description">

                        <?php

                        echo nl2br(
                            htmlspecialchars($fund['description'])
                        );

                        ?>

                    </div>


                    <div class="funding-meta">


                        <div class="meta-item">

                            <span class="meta-label">
                                Funding / Prize
                            </span>

                            <span class="meta-value">

                                <?php
                                echo htmlspecialchars($fund['amount']);
                                ?>

                            </span>

                        </div>


                        <div class="meta-item">

                            <span class="meta-label">
                                Startup Stage
                            </span>

                            <span class="meta-value">

                                <?php
                                echo htmlspecialchars($fund['stage']);
                                ?>

                            </span>

                        </div>


                    </div>


                    <span class="active-badge">
                        ✓ Active
                    </span>


                    <?php if (
                        in_array(
                            (int) $fund['id'],
                            $applied_funding_ids,
                            true
                        )
                    ): ?>


                        <div class="applied-button">
                            ✓ Applied
                        </div>


                    <?php else: ?>


                        <form method="POST">

                            <input
                                type="hidden"
                                name="funding_id"
                                value="<?php echo (int) $fund['id']; ?>"
                            >

                            <button
                                type="submit"
                                name="apply_funding"
                                class="apply-button"
                            >
                                Apply Now
                            </button>

                        </form>


                    <?php endif; ?>


                </div>


            <?php endwhile; ?>


        </section>


    <?php else: ?>


        <section class="empty-card">

            <div class="empty-icon">
                💰
            </div>

            <h2>
                No Funding Opportunities Available
            </h2>

            <p>
                There are no active funding opportunities available right now.
                Please check again later.
            </p>

        </section>


    <?php endif; ?>


    <section class="info-box">

        <h2>
            A quick reminder 💜
        </h2>

        <p>
            Funding requirements can vary between programs.
            Always check eligibility rules, deadlines and funding conditions
            before applying.
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