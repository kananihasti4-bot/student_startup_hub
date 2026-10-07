<?php

session_start();
include("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";


/* ==========================================
   APPROVE / REJECT FUNDING APPLICATION
========================================== */

if (isset($_POST['update_application'])) {

    $application_id = (int) $_POST['application_id'];
    $new_status = $_POST['status'];

    if (
        $new_status === "Approved" ||
        $new_status === "Rejected"
    ) {

        $update_stmt = $conn->prepare(
            "UPDATE funding_applications
             SET status = ?
             WHERE id = ?"
        );

        $update_stmt->bind_param(
            "si",
            $new_status,
            $application_id
        );

        if ($update_stmt->execute()) {

            $message = "Funding application status updated successfully.";
            $message_type = "success";

        } else {

            $message = "Something went wrong. Please try again.";
            $message_type = "error";
        }

        $update_stmt->close();
    }
}


/* ==========================================
   GET ALL FUNDING APPLICATIONS
========================================== */

$applications_result = mysqli_query(
    $conn,
    "SELECT
        fa.id,
        fa.status,
        fa.applied_at,
        u.name AS student_name,
        u.email AS student_email,
        f.title AS funding_title,
        f.amount
     FROM funding_applications fa
     LEFT JOIN users u
        ON fa.student_id = u.id
     LEFT JOIN funding f
        ON fa.funding_id = f.id
     ORDER BY fa.id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Funding Applications | Student Startup Hub</title>

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
            max-width: 1250px;
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
            padding: 14px 18px;
            border-radius: 10px;
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

        /* TABLE CARD */

        .table-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th {
            background: #f8f6ff;
            color: #333;
            text-align: left;
            padding: 18px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        td {
            padding: 18px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
            vertical-align: middle;
        }

        td strong {
            color: #222;
        }

        /* STATUS */

        .status {
            display: inline-block;
            padding: 7px 12px;
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

        /* ACTIONS */

        .action-form {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            border: none;
            padding: 9px 13px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
        }

        .approve-btn {
            background: #e4f8ec;
            color: #21733f;
        }

        .approve-btn:hover {
            background: #d5f2df;
        }

        .reject-btn {
            background: #ffe8e8;
            color: #b42323;
        }

        .reject-btn:hover {
            background: #ffdada;
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
            margin: 0;
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

        @media (max-width: 700px) {

            .navbar {
                padding: 16px 5%;
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

        <a href="manage_funding.php">
            Funding
        </a>

        <a href="../index.php">
            Home
        </a>

    </div>

</header>


<main class="container">


    <section class="hero-box">

        <p>
            Admin Panel
        </p>

        <h1>
            Funding Applications 💰
        </h1>

        <span>
            Review and manage funding applications submitted by students.
        </span>

    </section>


    <?php if ($message != ""): ?>

        <div class="message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <?php if (
        $applications_result &&
        mysqli_num_rows($applications_result) > 0
    ): ?>


        <div class="table-card">

            <table>

                <thead>

                    <tr>

                        <th>
                            Student
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Funding Opportunity
                        </th>

                        <th>
                            Amount
                        </th>

                        <th>
                            Applied On
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php while (
                        $application =
                        mysqli_fetch_assoc($applications_result)
                    ): ?>


                        <?php

                        $status_class =
                            strtolower($application['status']);

                        ?>


                        <tr>


                            <td>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $application['student_name']
                                    );
                                    ?>
                                </strong>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $application['student_email']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $application['funding_title']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $application['amount']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $application['applied_at']
                                    )
                                );
                                ?>

                            </td>


                            <td>

                                <span
                                    class="status <?php echo $status_class; ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $application['status']
                                    );
                                    ?>

                                </span>

                            </td>


                            <td>


                                <?php if (
                                    $application['status'] === 'Pending'
                                ): ?>


                                    <div class="action-form">


                                        <form method="POST">

                                            <input
                                                type="hidden"
                                                name="application_id"
                                                value="<?php
                                                echo (int)
                                                    $application['id'];
                                                ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="status"
                                                value="Approved"
                                            >

                                            <button
                                                type="submit"
                                                name="update_application"
                                                class="action-btn approve-btn"
                                            >
                                                Approve
                                            </button>

                                        </form>


                                        <form method="POST">

                                            <input
                                                type="hidden"
                                                name="application_id"
                                                value="<?php
                                                echo (int)
                                                    $application['id'];
                                                ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="status"
                                                value="Rejected"
                                            >

                                            <button
                                                type="submit"
                                                name="update_application"
                                                class="action-btn reject-btn"
                                            >
                                                Reject
                                            </button>

                                        </form>


                                    </div>


                                <?php else: ?>

                                    <span>
                                        No action required
                                    </span>

                                <?php endif; ?>


                            </td>


                        </tr>


                    <?php endwhile; ?>


                </tbody>

            </table>

        </div>


    <?php else: ?>


        <section class="empty-card">

            <div class="empty-icon">
                💰
            </div>

            <h2>
                No Funding Applications Yet
            </h2>

            <p>
                Students have not submitted any funding applications yet.
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