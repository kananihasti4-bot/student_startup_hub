<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

if (isset($_POST['submit_idea'])) {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);

    $user_id = (int) $_SESSION['user_id'];
$student_name = $_SESSION['user_name'];
$status = "Pending";

$query = "INSERT INTO startup_ideas
          (title, description, category, student_name, student_id, status)
          VALUES
          ('$title', '$description', '$category', '$student_name', '$user_id', '$status')";

    if (mysqli_query($conn, $query)) {
        $message = "Your startup idea has been submitted successfully!";
        $message_type = "success";
    } else {
        $message = "Something went wrong. Please try again.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Submit Startup Idea | Student Startup Hub</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f7f5ff;
            color: #1d2330;
        }

        /* Navbar */

        .navbar {
            background: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
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

        /* Main */

        .page-container {
            max-width: 1050px;
            margin: 0 auto;
            padding: 55px 25px 70px;
        }

        .hero-box {
            background: linear-gradient(135deg, #6c4df6, #8d70ff);
            color: white;
            padding: 40px;
            border-radius: 25px;
            margin-bottom: 30px;
            box-shadow: 0 20px 45px rgba(108, 77, 246, 0.20);
        }

        .hero-box p {
            margin: 0 0 10px;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.85;
        }

        .hero-box h1 {
            margin: 0 0 10px;
            font-size: 40px;
        }

        .hero-box span {
            opacity: 0.9;
            line-height: 1.6;
        }

        /* Form Card */

        .form-card {
            background: white;
            padding: 40px;
            border-radius: 22px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.07);
            border: 1px solid #eee;
        }

        .form-card h2 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .form-card .intro {
            color: #777;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .message {
            padding: 14px 18px;
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

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 15px;
            font-weight: 700;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #ddd;
            border-radius: 11px;
            font-size: 15px;
            font-family: inherit;
            background: #fff;
            outline: none;
            transition: 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #6c4df6;
            box-shadow: 0 0 0 3px rgba(108, 77, 246, 0.10);
        }

        .form-group textarea {
            min-height: 170px;
            resize: vertical;
            line-height: 1.6;
        }

        .hint {
            margin-top: 7px;
            color: #999;
            font-size: 12px;
        }

        .submit-button {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 11px;
            background: #6c4df6;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }

        .submit-button:hover {
            background: #593de0;
            transform: translateY(-1px);
        }

        /* Bottom Links */

        .bottom-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
        }

        .back-link {
            color: #6c4df6;
            text-decoration: none;
            font-weight: 700;
        }

        .logout-button {
            color: #fff;
            background: #6c4df6;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 9px;
            font-weight: 700;
        }

        .logout-button:hover {
            background: #593de0;
        }

        /* Footer */

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

        /* Responsive */

        @media (max-width: 700px) {

            .navbar {
                padding: 16px 5%;
            }

            .nav-links a {
                font-size: 14px;
            }

            .page-container {
                padding: 35px 15px 50px;
            }

            .hero-box {
                padding: 30px;
            }

            .hero-box h1 {
                font-size: 31px;
            }

            .form-card {
                padding: 25px;
            }

            .bottom-links {
                flex-direction: column;
                gap: 20px;
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

        <a href="../index.php">
            Home
        </a>

    </div>

</header>


<main class="page-container">

    <section class="hero-box">

        <p>Student Startup Hub</p>

        <h1>Share Your Startup Idea 💡</h1>

        <span>
            Have an innovative idea? Submit it here and let your idea
            become the next big opportunity.
        </span>

    </section>


    <section class="form-card">

        <h2>Submit a New Idea</h2>

        <p class="intro">
            Tell us about your startup idea. Our platform will keep it
            pending until an admin reviews it.
        </p>


        <?php if ($message != ""): ?>

            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label for="title">
                    Startup Idea Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="Example: Campus Food Delivery"
                    maxlength="150"
                    required
                >

                <div class="hint">
                    Give your startup idea a short and clear title.
                </div>

            </div>


            <div class="form-group">

                <label for="category">
                    Category
                </label>

                <select id="category" name="category" required>

                    <option value="">
                        Select a category
                    </option>

                    <option value="Technology">
                        Technology
                    </option>

                    <option value="Education">
                        Education
                    </option>

                    <option value="Healthcare">
                        Healthcare
                    </option>

                    <option value="Finance">
                        Finance
                    </option>

                    <option value="E-commerce">
                        E-commerce
                    </option>

                    <option value="Social Impact">
                        Social Impact
                    </option>

                    <option value="Food & Beverage">
                        Food & Beverage
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label for="description">
                    Describe Your Startup Idea
                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Explain your idea, the problem it solves, your target users and how it could work..."
                    required
                ></textarea>

                <div class="hint">
                    Explain your idea clearly so the admin can understand it.
                </div>

            </div>


            <button
                type="submit"
                name="submit_idea"
                class="submit-button"
            >
                Submit Startup Idea 🚀
            </button>

        </form>


        <div class="bottom-links">

            <a href="dashboard.php" class="back-link">
                ← Back to Dashboard
            </a>

            <a href="logout.php" class="logout-button">
                Logout
            </a>

        </div>

    </section>

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