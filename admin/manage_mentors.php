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
   ADD NEW MENTOR
========================== */

if (isset($_POST['add_mentor'])) {

    $name = trim($_POST['name']);
    $role = trim($_POST['role']);
    $bio = trim($_POST['bio']);
    $expertise = trim($_POST['expertise']);
    $status = trim($_POST['status']);

    if (
        $name == "" ||
        $role == "" ||
        $bio == "" ||
        $expertise == "" ||
        $status == ""
    ) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO mentors
            (name, role, bio, expertise, status)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sssss",
            $name,
            $role,
            $bio,
            $expertise,
            $status
        );

        if ($stmt->execute()) {

            $message = "Mentor added successfully!";
            $message_type = "success";

        } else {

            $message = "Could not add mentor. Please try again.";
            $message_type = "error";
        }

        $stmt->close();
    }
}


/* ==========================
   DELETE MENTOR
========================== */

if (isset($_GET['delete'])) {

    $mentor_id = (int) $_GET['delete'];

    if ($mentor_id > 0) {

        $stmt = $conn->prepare(
            "DELETE FROM mentors WHERE id = ?"
        );

        $stmt->bind_param("i", $mentor_id);

        if ($stmt->execute()) {

            $message = "Mentor deleted successfully!";
            $message_type = "success";

        } else {

            $message = "Could not delete mentor.";
            $message_type = "error";
        }

        $stmt->close();
    }
}


/* ==========================
   GET ALL MENTORS
========================== */

$mentors_result = mysqli_query(
    $conn,
    "SELECT * FROM mentors ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Mentors | Student Startup Hub</title>

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

        .add-mentor-btn {
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

        .add-mentor-btn:hover {
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
            min-width: 1000px;
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

        .mentor-role {
            color: #6c4df6;
            font-weight: 600;
            margin-top: 5px;
            font-size: 13px;
        }

        .expertise {
            display: inline-block;
            background: #eee9ff;
            color: #5b45b9;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
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

        .inactive {
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

        <a href="../index.php">
            Home
        </a>

    </div>

</header>


<main class="container">


    <section class="hero-box">

        <p>Admin Panel</p>

        <h1>
            Manage Mentors 👨‍🏫
        </h1>

        <span>
            Add experienced mentors and manage the guidance available to students.
        </span>

    </section>


    <?php if ($message != ""): ?>

        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <!-- ADD MENTOR -->

    <section class="form-card">

        <h2>
            Add New Mentor
        </h2>

        <p class="form-intro">
            Enter the mentor's details below.
        </p>


        <form method="POST">

            <div class="form-grid">


                <div class="form-group">

                    <label for="name">
                        Mentor Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Example: Rahul Sharma"
                        maxlength="100"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="role">
                        Role / Specialization
                    </label>

                    <input
                        type="text"
                        id="role"
                        name="role"
                        placeholder="Example: Startup & Technology Mentor"
                        maxlength="150"
                        required
                    >

                </div>


                <div class="form-group full">

                    <label for="bio">
                        Bio
                    </label>

                    <textarea
                        id="bio"
                        name="bio"
                        placeholder="Write a short introduction about the mentor..."
                        required
                    ></textarea>

                </div>


                <div class="form-group">

                    <label for="expertise">
                        Expertise
                    </label>

                    <input
                        type="text"
                        id="expertise"
                        name="expertise"
                        placeholder="Example: Technology, Product, Startups"
                        maxlength="255"
                        required
                    >

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

                        <option value="Inactive">
                            Inactive
                        </option>

                    </select>

                </div>


            </div>


            <button
                type="submit"
                name="add_mentor"
                class="add-mentor-btn"
            >
                + Add Mentor
            </button>

        </form>

    </section>


    <!-- MENTOR LIST -->

    <section class="table-card">

        <h2>
            All Mentors
        </h2>


        <?php if ($mentors_result && mysqli_num_rows($mentors_result) > 0): ?>

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Mentor</th>
                        <th>Bio</th>
                        <th>Expertise</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>

                    <?php while ($mentor = mysqli_fetch_assoc($mentors_result)): ?>

                        <tr>

                            <td>
                                <?php echo (int) $mentor['id']; ?>
                            </td>


                            <td>

                                <div class="mentor-name">
                                    <?php echo htmlspecialchars($mentor['name']); ?>
                                </div>

                                <div class="mentor-role">
                                    <?php echo htmlspecialchars($mentor['role']); ?>
                                </div>

                            </td>


                            <td>

                                <?php
                                echo nl2br(
                                    htmlspecialchars($mentor['bio'])
                                );
                                ?>

                            </td>


                            <td>

                                <span class="expertise">
                                    <?php echo htmlspecialchars($mentor['expertise']); ?>
                                </span>

                            </td>


                            <td>

                                <?php
                                $status_class =
                                    strtolower($mentor['status']) === 'inactive'
                                    ? 'inactive'
                                    : 'active';
                                ?>

                                <span class="status <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($mentor['status']); ?>
                                </span>

                            </td>


                            <td>

                                <a
                                    href="?delete=<?php echo (int) $mentor['id']; ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this mentor?');"
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
                    👨‍🏫
                </div>

                <h3>
                    No mentors yet
                </h3>

                <p>
                    Add your first mentor using the form above.
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
        Manage. Guide. Empower.
    </p>

</footer>

</body>

</html>