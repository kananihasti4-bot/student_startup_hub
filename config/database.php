<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "student_startup_hub"
);

if (!$conn) {
    die("Database Connection Failed");
}

?>