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
   EVENT REGISTRATION
   ========================= */

if (isset($_POST['register_event'])) {

    $event_id = (int) $_POST['event_id'];

    /* Check whether this student is already registered */
    $check_stmt = $conn->prepare(
        "SELECT id
         FROM event_registrations
         WHERE event_id = ? AND student_id = ?"
    );

    $check_stmt->bind_param("ii", $event_id, $user_id);
    $check_stmt->execute();

    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {

        $message = "You are already registered for this event.";
        $message_type = "error";

    } else {

        /* Insert new registration */
        $insert_stmt = $conn->prepare(
            "INSERT INTO event_registrations (event_id, student_id)
             VALUES (?, ?)"
        );

        $insert_stmt->bind_param("ii", $event_id, $user_id);

        if ($insert_stmt->execute()) {

            $message = "You have successfully registered for the event.";
            $message_type = "success";

        } else {

            $message = "Something went wrong. Please try again.";
            $message_type = "error";
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
}


/* =========================
   GET ALL EVENTS
   ========================= */

$events_result = mysqli_query(
    $conn,
    "SELECT * FROM events
     ORDER BY event_date ASC, id DESC"
);


/* =========================
   GET STUDENT REGISTRATIONS
   ========================= */

$registered_event_ids = [];

$reg_stmt = $conn->prepare(
    "SELECT event_id
     FROM event_registrations
     WHERE student_id = ?"
);

$reg_stmt->bind_param("i", $user_id);
$reg_stmt->execute();

$reg_result = $reg_stmt->get_result();

while ($reg_row = $reg_result->fetch_assoc()) {

    $registered_event_ids[] = (int) $reg_row['event_id'];

}

$reg_stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Startup Events | Student Startup Hub</title>

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

        .registration-message {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .registration-message.success {
            background: #e8f8ee;
            color: #21733f;
        }

        .registration-message.error {
            background: #ffe8e8;
            color: #b42323;
        }


        /* EVENTS */

        .event-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .event-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
            transition: 0.3s;
        }

        .event-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.10);
        }

        .event-top {
            background: linear-gradient(135deg, #eee9ff, #f6f3ff);
            padding: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .event-icon {
            font-size: 42px;
        }

        .event-date {
            text-align: center;
            background: white;
            border-radius: 12px;
            padding: 8px 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .event-date strong {
            display: block;
            color: #6c4df6;
            font-size: 20px;
        }

        .event-date span {
            color: #777;
            font-size: 11px;
            text-transform: uppercase;
        }

        .event-content {
            padding: 25px;
        }

        .event-category {
            display: inline-block;
            padding: 7px 11px;
            background: #eee9ff;
            color: #5b45b9;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .event-content h2 {
            margin: 0 0 10px;
            font-size: 22px;
        }

        .event-description {
            color: #777;
            line-height: 1.6;
            min-height: 75px;
        }

        .event-info {
            border-top: 1px solid #eee;
            margin-top: 18px;
            padding-top: 18px;
            display: grid;
            gap: 8px;
        }

        .event-info div {
            color: #666;
            font-size: 14px;
        }


        /* STATUS */

        .status {
            display: inline-block;
            margin-top: 18px;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .upcoming {
            background: #e4f8ec;
            color: #21733f;
        }

        .closed {
            background: #ffe8e8;
            color: #b42323;
        }


        /* REGISTRATION BUTTON */

        .register-button {
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

        .register-button:hover {
            background: #593de0;
        }

        .registered-button {
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

        .registration-closed {
            width: 100%;
            margin-top: 18px;
            padding: 13px 16px;
            border-radius: 10px;
            background: #ffe8e8;
            color: #b42323;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
        }


        /* EMPTY */

        .empty-card {
            background: white;
            padding: 55px 30px;
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
            margin: 0;
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

            .event-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 650px) {

            .event-grid {
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

        <a href="../index.php">
            Home
        </a>

    </div>

</header>



<main class="container">


    <section class="hero-box">

        <p>Student Startup Hub</p>

        <h1>
            Discover Startup Events 📅
        </h1>

        <span>
            Join workshops, competitions and networking events that can help you grow your startup journey.
        </span>

    </section>



    <?php if ($message != ""): ?>

        <div class="registration-message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>



    <?php if ($events_result && mysqli_num_rows($events_result) > 0): ?>


        <section class="event-grid">


            <?php while ($event = mysqli_fetch_assoc($events_result)): ?>


                <?php

                $status_class =
                    strtolower($event['status']) === 'closed'
                    ? 'closed'
                    : 'upcoming';

                $date_timestamp = strtotime($event['event_date']);

                $day = date("d", $date_timestamp);
                $month = date("M", $date_timestamp);

                ?>


                <div class="event-card">


                    <div class="event-top">


                        <div class="event-icon">
                            📅
                        </div>


                        <div class="event-date">

                            <strong>
                                <?php echo $day; ?>
                            </strong>

                            <span>
                                <?php echo $month; ?>
                            </span>

                        </div>


                    </div>



                    <div class="event-content">


                        <span class="event-category">

                            <?php
                            echo htmlspecialchars($event['category']);
                            ?>

                        </span>



                        <h2>

                            <?php
                            echo htmlspecialchars($event['title']);
                            ?>

                        </h2>



                        <div class="event-description">

                            <?php

                            echo nl2br(
                                htmlspecialchars($event['description'])
                            );

                            ?>

                        </div>



                        <div class="event-info">


                            <div>

                                📍

                                <?php
                                echo htmlspecialchars($event['location']);
                                ?>

                            </div>


                            <div>

                                📅

                                <?php
                                echo date(
                                    "d M Y",
                                    $date_timestamp
                                );
                                ?>

                            </div>


                        </div>



                        <span class="status <?php echo $status_class; ?>">

                            <?php
                            echo htmlspecialchars($event['status']);
                            ?>

                        </span>



                        <?php if (strtolower($event['status']) === 'closed'): ?>


                            <div class="registration-closed">

                                Registration Closed

                            </div>


                        <?php elseif (
                            in_array(
                                (int) $event['id'],
                                $registered_event_ids,
                                true
                            )
                        ): ?>


                            <div class="registered-button">

                                ✓ Registered

                            </div>


                        <?php else: ?>


                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="event_id"
                                    value="<?php echo (int) $event['id']; ?>"
                                >

                                <button
                                    type="submit"
                                    name="register_event"
                                    class="register-button"
                                >

                                    Register for Event

                                </button>

                            </form>


                        <?php endif; ?>


                    </div>


                </div>


            <?php endwhile; ?>


        </section>


    <?php else: ?>


        <section class="empty-card">


            <div class="empty-icon">
                📅
            </div>


            <h2>
                No Events Available
            </h2>


            <p>
                There are no startup events available right now.
                Please check again later.
            </p>


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