<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";


/* ==========================
   APPROVE REQUEST
========================== */

if (isset($_GET['approve'])) {

    $request_id = (int) $_GET['approve'];

    if ($request_id > 0) {

        $stmt = $conn->prepare(
            "UPDATE mentor_requests
             SET status = 'Approved'
             WHERE id = ?"
        );

        $stmt->bind_param("i", $request_id);

        if ($stmt->execute()) {

            $message = "Mentor request approved successfully!";
            $message_type = "success";

        } else {

            $message = "Could not approve the request.";
            $message_type = "error";
        }

        $stmt->close();
    }
}


/* ==========================
   REJECT REQUEST
========================== */

if (isset($_GET['reject'])) {

    $request_id = (int) $_GET['reject'];

    if ($request_id > 0) {

        $stmt = $conn->prepare(
            "UPDATE mentor_requests
             SET status = 'Rejected'
             WHERE id = ?"
        );

        $stmt->bind_param("i", $request_id);

        if ($stmt->execute()) {

            $message = "Mentor request rejected.";
            $message_type = "success";

        } else {

            $message = "Could not reject the request.";
            $message_type = "error";
        }

        $stmt->close();
    }
}


/* ==========================
   GET REQUESTS
========================== */

$requests_result = mysqli_query(
    $conn,
    "SELECT *
     FROM mentor_requests
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mentor Requests | Student Startup Hub</title>

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
            margin-bottom: 35px;
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
        }

        /* MESSAGE */

        .message {
            padding: 15px 18px;
            border-radius: 10px;
            margin-bottom: 25px;
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

        /* TABLE */

        .table-card {
            background: white;
            padding: 25px;
            border-radius: 22px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
            overflow-x: auto;
        }

        .table-card h2 {
            margin-top: 0;
            font-size: 28px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1050px;
        }

        th {
            background: #f1eeff;
            color: #4e3ca3;
            padding: 15px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        tr:hover td {
            background: #faf9ff;
        }

        .mentor-name {
            font-weight: 700;
        }

        .student-id {
            font-weight: 700;
            color: #555;
        }

        .message-cell {
            max-width: 330px;
            color: #666;
            line-height: 1.5;
        }

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

        .action-btn {
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            margin-right: 6px;
            margin-bottom: 6px;
        }

        .approve-btn {
            background: #e4f8ec;
            color: #21733f;
        }

        .reject-btn {
            background: #ffe8e8;
            color: #b42323;
        }

        /* EMPTY */

        .empty-state {
            text-align: center;
            padding: 55px 20px;
            color: #777;
        }

        .empty-icon {
            font-size: 55px;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            color: #333;
            margin: 0 0 8px;
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
            margin: 0;
            color: #aaa;
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

        <a href="manage_mentors.php">
            Mentors
        </a>

        <a href="../index.php">
            Home
        </a>

    </div>

</header>


<main class="container">


    <section class="hero-box">

        <p>Admin Panel</p>

        <h1>
            Mentor Connection Requests 🤝
        </h1>

        <span>
            Review student requests and manage mentor connections.
        </span>

    </section>


    <?php if ($message != ""): ?>

        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <section class="table-card">

        <h2>
            All Mentor Requests
        </h2>


        <?php if ($requests_result && mysqli_num_rows($requests_result) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Mentor</th>
                        <th>Student ID</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Requested On</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($request = mysqli_fetch_assoc($requests_result)): ?>

                        <?php
                        $request_status = strtolower($request['status']);

                        if ($request_status === 'approved') {
                            $status_class = 'approved';
                        } elseif ($request_status === 'rejected') {
                            $status_class = 'rejected';
                        } else {
                            $status_class = 'pending';
                        }
                        ?>

                        <tr>

                            <td>
                                <?php echo (int) $request['id']; ?>
                            </td>


                            <td>

                                <div class="mentor-name">
                                    <?php echo htmlspecialchars($request['mentor_name']); ?>
                                </div>

                            </td>


                            <td class="student-id">

                                <?php echo (int) $request['student_id']; ?>

                            </td>


                            <td class="message-cell">

                                <?php
                                echo nl2br(
                                    htmlspecialchars($request['message'])
                                );
                                ?>

                            </td>


                            <td>

                                <span class="status <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($request['status']); ?>
                                </span>

                            </td>


                            <td>

                                <?php
                                echo date(
                                    "d M Y, h:i A",
                                    strtotime($request['created_at'])
                                );
                                ?>

                            </td>


                            <td>

                                <?php if ($request_status === 'pending'): ?>

                                    <a
                                        href="?approve=<?php echo (int) $request['id']; ?>"
                                        class="action-btn approve-btn"
                                        onclick="return confirm('Approve this mentor request?');"
                                    >
                                        ✓ Approve
                                    </a>

                                    <a
                                        href="?reject=<?php echo (int) $request['id']; ?>"
                                        class="action-btn reject-btn"
                                        onclick="return confirm('Reject this mentor request?');"
                                    >
                                        ✕ Reject
                                    </a>

                                <?php else: ?>

                                    <span style="color:#999;">
                                        No action
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty-state">

                <div class="empty-icon">
                    🤝
                </div>

                <h3>
                    No Mentor Requests
                </h3>

                <p>
                    Student mentor requests will appear here.
                </p>

            </div>

        <?php endif; ?>


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
        Student Startup Hub - Admin Panel
    </h3>

    <p>
        Review. Connect. Empower.
    </p>

</footer>

</body>

</html>