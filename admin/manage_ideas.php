<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

/* Approve / Reject action */
if (isset($_POST['action']) && isset($_POST['idea_id'])) {

    $idea_id = (int) $_POST['idea_id'];
    $action = $_POST['action'];

    if ($action === "approve") {
        $status = "Approved";
    } elseif ($action === "reject") {
        $status = "Rejected";
    } else {
        $status = "";
    }

    if ($status !== "") {
        mysqli_query(
            $conn,
            "UPDATE startup_ideas SET status='$status' WHERE id=$idea_id"
        );
    }
}

/* Get all startup ideas */
$result = mysqli_query(
    $conn,
    "SELECT * FROM startup_ideas ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Startup Ideas | Student Startup Hub</title>

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

        /* Navbar */
        .navbar {
            background: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 10;
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

        .logout {
            background: #6c4df6;
            color: white !important;
            padding: 11px 20px;
            border-radius: 9px;
        }

        /* Main */
        .container {
            padding: 55px 7%;
        }

        .page-header {
            background: linear-gradient(135deg, #6c4df6, #8d70ff);
            color: white;
            padding: 35px 40px;
            border-radius: 22px;
            margin-bottom: 35px;
            box-shadow: 0 20px 45px rgba(108,77,246,.20);
        }

        .page-header p {
            margin: 0 0 8px;
            opacity: .85;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 13px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 38px;
        }

        /* Table Card */
        .table-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 28px rgba(0,0,0,.06);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        th {
            background: #f3f0ff;
            color: #4e3ca3;
            text-align: left;
            padding: 16px;
            font-size: 14px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        tr:hover td {
            background: #faf9ff;
        }

        .title {
            font-weight: 700;
            color: #222;
        }

        .description {
            max-width: 320px;
            color: #666;
            line-height: 1.5;
        }

        /* Status */
        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .pending {
            background: #fff4d8;
            color: #9a6b00;
        }

        .approved {
            background: #e4f8ec;
            color: #21733f;
        }

        .rejected {
            background: #ffe8e8;
            color: #b42323;
        }

        /* Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-buttons button {
            border: none;
            padding: 9px 13px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        .approve {
            background: #e8f8ee;
            color: #21733f;
        }

        .reject {
            background: #ffe8e8;
            color: #b42323;
        }

        .approve:hover {
            background: #d4f0df;
        }

        .reject:hover {
            background: #ffd5d5;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            background: #6c4df6;
            color: white;
            padding: 13px 22px;
            border-radius: 10px;
            font-weight: 700;
        }

        /* Footer */
        footer {
            margin-top: 50px;
            background: #17171c;
            color: white;
            text-align: center;
            padding: 30px 7%;
        }

        footer p {
            color: #aaa;
        }

        @media (max-width: 800px) {
            .nav-links a:not(.logout) {
                display: none;
            }

            .container {
                padding: 35px 5%;
            }

            .page-header h1 {
                font-size: 30px;
            }
        }
        .logout-section {
    text-align: center;
    margin-top: 35px;
    margin-bottom: 20px;
}

.bottom-logout-btn {
    display: inline-block;
    text-decoration: none;
    background: #6c4df6;
    color: white;
    padding: 14px 35px;
    border-radius: 10px;
    font-weight: 700;
}

.bottom-logout-btn:hover {
    background: #583be0;
}
    </style>
</head>

<body>

<header class="navbar">

    <div class="logo">
        Student<span>Startup</span>Hub
    </div>

    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="../index.php">Home</a>
        <div class="nav-links">
    <a href="dashboard.php">Dashboard</a>
    <a href="../index.php">Home</a>
</div>
    </div>

</header>

<main class="container">

    <section class="page-header">
        <p>Admin Panel</p>
        <h1>Manage Startup Ideas 💡</h1>
    </section>

    <div class="table-card">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Student</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if (mysqli_num_rows($result) > 0): ?>

                <?php while ($idea = mysqli_fetch_assoc($result)): ?>

                    <tr>

                        <td>
                            <?php echo $idea['id']; ?>
                        </td>

                        <td class="title">
                            <?php echo htmlspecialchars($idea['title']); ?>
                        </td>

                        <td class="description">
                            <?php echo htmlspecialchars($idea['description']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($idea['category']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($idea['student_name']); ?>
                        </td>

                        <td>

                            <?php
                            $status_class = strtolower($idea['status']);
                            ?>

                            <span class="status <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($idea['status']); ?>
                            </span>

                        </td>

                        <td>

                            <div class="action-buttons">

                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="idea_id"
                                        value="<?php echo $idea['id']; ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="action"
                                        value="approve"
                                        class="approve"
                                    >
                                        ✓ Approve
                                    </button>

                                </form>

                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="idea_id"
                                        value="<?php echo $idea['id']; ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="action"
                                        value="reject"
                                        class="reject"
                                    >
                                        ✕ Reject
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7" style="text-align:center; padding:40px;">
                        No startup ideas submitted yet.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

    <a href="dashboard.php" class="back-btn">
        ← Back to Dashboard
    </a>
<div class="logout-section">
    <a href="logout.php" class="bottom-logout-btn">Logout</a>
</div>
</main>

<footer>
    <h3>Student Startup Hub - Admin Panel</h3>
    <p>Review and support student entrepreneurs.</p>
</footer>

</body>
</html>