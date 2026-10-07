<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

/* Delete user */
if (isset($_POST['delete_user'])) {

    $user_id = (int) $_POST['user_id'];

    mysqli_query(
        $conn,
        "DELETE FROM users WHERE id=$user_id AND role='user'"
    );
}

/* Get all normal users */
$result = mysqli_query(
    $conn,
    "SELECT id, name, email, role, created_at
     FROM users
     WHERE role='user'
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Users | Student Startup Hub</title>

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
            min-width: 800px;
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
        }

        tr:hover td {
            background: #faf9ff;
        }

        .name {
            font-weight: 700;
        }

        .role {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            background: #eee9ff;
            color: #5a45b8;
            font-size: 12px;
            font-weight: 700;
        }

        .delete-btn {
            border: none;
            background: #ffe8e8;
            color: #b42323;
            padding: 9px 14px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        .delete-btn:hover {
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

    <section class="page-header">

        <p>
            Admin Panel
        </p>

        <h1>
            Manage Users 👥
        </h1>

    </section>


    <div class="table-card">

        <table>

            <thead>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        Name
                    </th>

                    <th>
                        Email
                    </th>

                    <th>
                        Role
                    </th>

                    <th>
                        Created At
                    </th>

                    <th>
                        Action
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php if (mysqli_num_rows($result) > 0): ?>


                <?php while ($user = mysqli_fetch_assoc($result)): ?>

                    <tr>

                        <td>

                            <?php echo $user['id']; ?>

                        </td>


                        <td class="name">

                            <?php
                            echo htmlspecialchars(
                                $user['name']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $user['email']
                            );
                            ?>

                        </td>


                        <td>

                            <span class="role">

                                <?php
                                echo htmlspecialchars(
                                    $user['role']
                                );
                                ?>

                            </span>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $user['created_at']
                            );
                            ?>

                        </td>


                        <td>

                            <form
                                method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this user?');"
                            >

                                <input
                                    type="hidden"
                                    name="user_id"
                                    value="<?php echo $user['id']; ?>"
                                >


                                <button
                                    type="submit"
                                    name="delete_user"
                                    class="delete-btn"
                                >
                                    Delete
                                </button>

                            </form>

                        </td>


                    </tr>

                <?php endwhile; ?>


            <?php else: ?>


                <tr>

                    <td
                        colspan="6"
                        style="text-align:center; padding:40px;"
                    >
                        No users registered yet.
                    </td>

                </tr>


            <?php endif; ?>


            </tbody>

        </table>

    </div>


    <a
        href="dashboard.php"
        class="back-btn"
    >
        ← Back to Dashboard
    </a>


    <div class="logout-section">

        <a
            href="logout.php"
            class="bottom-logout-btn"
        >
            Logout
        </a>

    </div>


</main>


<footer>

    <h3>
        Student Startup Hub - Admin Panel
    </h3>

    <p>
        Manage and support student entrepreneurs.
    </p>

</footer>


</body>
</html>