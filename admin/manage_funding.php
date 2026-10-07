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
   ADD NEW FUNDING
========================== */

if (isset($_POST['add_funding'])) {

    $title = trim($_POST['title']);
    $type = trim($_POST['type']);
    $description = trim($_POST['description']);
    $amount = trim($_POST['amount']);
    $stage = trim($_POST['stage']);
    $status = trim($_POST['status']);

    if (
        $title == "" ||
        $type == "" ||
        $description == "" ||
        $amount == "" ||
        $stage == "" ||
        $status == ""
    ) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO funding
            (title, type, description, amount, stage, status)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssssss",
            $title,
            $type,
            $description,
            $amount,
            $stage,
            $status
        );

        if ($stmt->execute()) {

            $message = "Funding opportunity added successfully!";
            $message_type = "success";

        } else {

            $message = "Could not add funding opportunity.";
            $message_type = "error";
        }

        $stmt->close();
    }
}


/* ==========================
   DELETE FUNDING
========================== */

if (isset($_GET['delete'])) {

    $funding_id = (int) $_GET['delete'];

    if ($funding_id > 0) {

        $stmt = $conn->prepare(
            "DELETE FROM funding WHERE id = ?"
        );

        $stmt->bind_param("i", $funding_id);

        if ($stmt->execute()) {

            $message = "Funding opportunity deleted successfully!";
            $message_type = "success";

        } else {

            $message = "Could not delete funding opportunity.";
            $message_type = "error";
        }

        $stmt->close();
    }
}


/* ==========================
   GET ALL FUNDING
========================== */

$funding_result = mysqli_query(
    $conn,
    "SELECT * FROM funding ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Funding | Student Startup Hub</title>

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

        /* FORM */

        .form-card {
            background: white;
            padding: 32px;
            border-radius: 22px;
            border: 1px solid #eee;
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
            margin-bottom: 35px;
        }

        .form-card h2 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .form-intro {
            color: #777;
            margin-bottom: 25px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            outline: none;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #6c4df6;
            box-shadow: 0 0 0 3px rgba(108,77,246,0.10);
        }

        .form-group textarea {
            min-height: 130px;
            resize: vertical;
        }

        .add-funding-btn {
            margin-top: 22px;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #6c4df6;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .add-funding-btn:hover {
            background: #593de0;
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

        .funding-title {
            font-weight: 700;
        }

        .funding-type {
            color: #6c4df6;
            font-weight: 600;
            margin-top: 5px;
            font-size: 13px;
        }

        .stage {
            display: inline-block;
            background: #eee9ff;
            color: #5b45b9;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .amount {
            font-weight: 700;
            color: #333;
        }

        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .active {
            background: #e4f8ec;
            color: #21733f;
        }

        .closed {
            background: #ffe8e8;
            color: #b42323;
        }

        .delete-btn {
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            background: #ffe8e8;
            color: #b42323;
            font-size: 13px;
            font-weight: 700;
        }

        .delete-btn:hover {
            background: #ffd7d7;
        }

        /* EMPTY */

        .empty-state {
            text-align: center;
            padding: 45px 20px;
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

        @media (max-width: 750px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
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

        <a href="manage_events.php">
            Events
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
            Manage Funding Opportunities 💰
        </h1>

        <span>
            Add and manage grants, startup programs and funding opportunities for students.
        </span>

    </section>


    <?php if ($message != ""): ?>

        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <!-- ADD FUNDING -->

    <section class="form-card">

        <h2>
            Add New Funding Opportunity
        </h2>

        <p class="form-intro">
            Enter the funding details below.
        </p>


        <form method="POST">

            <div class="form-grid">


                <div class="form-group">

                    <label for="title">
                        Funding Title
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        placeholder="Example: Student Startup Grant"
                        maxlength="150"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="type">
                        Funding Type
                    </label>

                    <input
                        type="text"
                        id="type"
                        name="type"
                        placeholder="Example: Early Stage Grant"
                        maxlength="100"
                        required
                    >

                </div>


                <div class="form-group full">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        placeholder="Describe this funding opportunity..."
                        required
                    ></textarea>

                </div>


                <div class="form-group">

                    <label for="amount">
                        Funding / Prize
                    </label>

                    <input
                        type="text"
                        id="amount"
                        name="amount"
                        placeholder="Example: ₹5 Lakh+"
                        maxlength="50"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="stage">
                        Startup Stage
                    </label>

                    <select
                        id="stage"
                        name="stage"
                        required
                    >

                        <option value="">
                            Select Stage
                        </option>

                        <option value="Idea">
                            Idea
                        </option>

                        <option value="Prototype">
                            Prototype
                        </option>

                        <option value="MVP">
                            MVP
                        </option>

                        <option value="Early">
                            Early Stage
                        </option>

                        <option value="Growth">
                            Growth
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="status">
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        <option value="Active">
                            Active
                        </option>

                        <option value="Closed">
                            Closed
                        </option>

                    </select>

                </div>


            </div>


            <button
                type="submit"
                name="add_funding"
                class="add-funding-btn"
            >
                + Add Funding Opportunity
            </button>

        </form>

    </section>


    <!-- FUNDING LIST -->

    <section class="table-card">

        <h2>
            All Funding Opportunities
        </h2>


        <?php if ($funding_result && mysqli_num_rows($funding_result) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Funding</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Stage</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($fund = mysqli_fetch_assoc($funding_result)): ?>

                        <?php
                        $status_class =
                            strtolower($fund['status']) === 'closed'
                            ? 'closed'
                            : 'active';
                        ?>

                        <tr>

                            <td>
                                <?php echo (int) $fund['id']; ?>
                            </td>


                            <td>

                                <div class="funding-title">
                                    <?php echo htmlspecialchars($fund['title']); ?>
                                </div>

                                <div class="funding-type">
                                    <?php echo htmlspecialchars($fund['type']); ?>
                                </div>

                            </td>


                            <td style="color:#666; line-height:1.5;">

                                <?php
                                echo nl2br(
                                    htmlspecialchars($fund['description'])
                                );
                                ?>

                            </td>


                            <td class="amount">

                                <?php echo htmlspecialchars($fund['amount']); ?>

                            </td>


                            <td>

                                <span class="stage">
                                    <?php echo htmlspecialchars($fund['stage']); ?>
                                </span>

                            </td>


                            <td>

                                <span class="status <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($fund['status']); ?>
                                </span>

                            </td>


                            <td>

                                <a
                                    href="?delete=<?php echo (int) $fund['id']; ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this funding opportunity?');"
                                >
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty-state">

                <div class="empty-icon">
                    💰
                </div>

                <h3>
                    No Funding Opportunities Yet
                </h3>

                <p>
                    Add your first funding opportunity using the form above.
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
        Manage. Support. Empower.
    </p>

</footer>

</body>

</html>