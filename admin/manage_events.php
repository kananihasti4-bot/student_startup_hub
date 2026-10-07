<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

/* --------------------------------
   ADD NEW EVENT
--------------------------------- */

if (isset($_POST['add_event'])) {

    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $event_date = trim($_POST['event_date']);
    $location = trim($_POST['location']);
    $status = trim($_POST['status']);

    if (
        $title == "" ||
        $category == "" ||
        $description == "" ||
        $event_date == "" ||
        $location == "" ||
        $status == ""
    ) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO events
            (title, category, description, event_date, location, status)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssssss",
            $title,
            $category,
            $description,
            $event_date,
            $location,
            $status
        );

        if ($stmt->execute()) {

            $message = "Event added successfully!";
            $message_type = "success";

        } else {

            $message = "Could not add the event. Please try again.";
            $message_type = "error";
        }

        $stmt->close();
    }
}


/* --------------------------------
   DELETE EVENT
--------------------------------- */

if (isset($_GET['delete'])) {

    $event_id = (int) $_GET['delete'];

    if ($event_id > 0) {

        $stmt = $conn->prepare(
            "DELETE FROM events WHERE id = ?"
        );

        $stmt->bind_param("i", $event_id);

        if ($stmt->execute()) {

            $message = "Event deleted successfully!";
            $message_type = "success";

        } else {

            $message = "Could not delete the event.";
            $message_type = "error";
        }

        $stmt->close();
    }
}


/* --------------------------------
   GET ALL EVENTS
--------------------------------- */

$events_result = mysqli_query(
    $conn,
    "SELECT * FROM events ORDER BY event_date ASC, id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Events | Student Startup Hub</title>

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

        /* ADD EVENT */

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
            min-height: 120px;
            resize: vertical;
        }

        .add-event-btn {
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

        .add-event-btn:hover {
            background: #593de0;
        }

        /* EVENTS TABLE */

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
            min-width: 950px;
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

        .event-title {
            font-weight: 700;
        }

        .category {
            display: inline-block;
            background: #eee9ff;
            color: #5b45b9;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .date {
            font-weight: 700;
            color: #444;
        }

        .status {
            display: inline-block;
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

        .action-btn {
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
        }

        .delete-btn {
            background: #ffe8e8;
            color: #b42323;
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

        .empty-state-icon {
            font-size: 50px;
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

        /* RESPONSIVE */

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
            Manage Startup Events 📅
        </h1>

        <span>
            Create and manage events that students can discover on Student Startup Hub.
        </span>

    </section>


    <?php if ($message != ""): ?>

        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <!-- ADD EVENT FORM -->

    <section class="form-card">

        <h2>
            Add New Event
        </h2>

        <p class="form-intro">
            Enter the event details below and it will be saved to the database.
        </p>


        <form method="POST">

            <div class="form-grid">


                <div class="form-group">

                    <label for="title">
                        Event Title
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        placeholder="Example: Startup Workshop"
                        maxlength="150"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="category">
                        Category
                    </label>

                    <select id="category" name="category" required>

                        <option value="">
                            Select Category
                        </option>

                        <option value="Workshop">
                            Workshop
                        </option>

                        <option value="Competition">
                            Competition
                        </option>

                        <option value="Networking">
                            Networking
                        </option>

                        <option value="Guest Session">
                            Guest Session
                        </option>

                        <option value="Funding">
                            Funding
                        </option>

                        <option value="Other">
                            Other
                        </option>

                    </select>

                </div>


                <div class="form-group full">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        placeholder="Describe the event..."
                        required
                    ></textarea>

                </div>


                <div class="form-group">

                    <label for="event_date">
                        Event Date
                    </label>

                    <input
                        type="date"
                        id="event_date"
                        name="event_date"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="location">
                        Location
                    </label>

                    <input
                        type="text"
                        id="location"
                        name="location"
                        placeholder="Example: Innovation Center"
                        maxlength="150"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="status">
                        Status
                    </label>

                    <select id="status" name="status" required>

                        <option value="Upcoming">
                            Upcoming
                        </option>

                        <option value="Closed">
                            Closed
                        </option>

                    </select>

                </div>


            </div>


            <button
                type="submit"
                name="add_event"
                class="add-event-btn"
            >
                + Add Event
            </button>

        </form>

    </section>


    <!-- EVENTS LIST -->

    <section class="table-card">

        <h2>
            All Events
        </h2>


        <?php if ($events_result && mysqli_num_rows($events_result) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Event</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($event = mysqli_fetch_assoc($events_result)): ?>

                        <tr>

                            <td>
                                <?php echo (int) $event['id']; ?>
                            </td>


                            <td>

                                <div class="event-title">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </div>

                                <div style="color:#777; margin-top:6px;">
                                    <?php echo htmlspecialchars($event['description']); ?>
                                </div>

                            </td>


                            <td>

                                <span class="category">
                                    <?php echo htmlspecialchars($event['category']); ?>
                                </span>

                            </td>


                            <td class="date">

                                <?php
                                echo date(
                                    "d M Y",
                                    strtotime($event['event_date'])
                                );
                                ?>

                            </td>


                            <td>
                                <?php echo htmlspecialchars($event['location']); ?>
                            </td>


                            <td>

                                <?php
                                $status_class =
                                    strtolower($event['status']) === 'closed'
                                    ? 'closed'
                                    : 'upcoming';
                                ?>

                                <span class="status <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($event['status']); ?>
                                </span>

                            </td>


                            <td>

                                <a
                                    href="?delete=<?php echo (int) $event['id']; ?>"
                                    class="action-btn delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this event?');"
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

                <div class="empty-state-icon">
                    📅
                </div>

                <h3>
                    No events yet
                </h3>

                <p>
                    Add your first startup event using the form above.
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
        Manage. Organize. Empower.
    </p>

</footer>

</body>

</html>
```