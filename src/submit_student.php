<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "school_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student data
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$birthdate = $_POST['birthdate'];

// Get course data
$course_name = $_POST['course_name'];
$course_code = $_POST['course_code'];
$credits = $_POST['credits'];
$grade = $_POST['grade'];

// Insert student data
$stmt = $conn->prepare("INSERT INTO students (first_name, last_name, email, birthdate) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $first_name, $last_name, $email, $birthdate);
$stmt->execute();
$student_id = $stmt->insert_id;
$stmt->close();

// Check if course already exists
$course_id = null;
$check_course = $conn->prepare("SELECT course_id FROM courses WHERE course_code = ?");
$check_course->bind_param("s", $course_code);
$check_course->execute();
$check_course->bind_result($course_id);
if (!$check_course->fetch()) {
    $check_course->close();
    // Insert course if not exists
    $insert_course = $conn->prepare("INSERT INTO courses (course_name, course_code, credits) VALUES (?, ?, ?)");
    $insert_course->bind_param("ssi", $course_name, $course_code, $credits);
    $insert_course->execute();
    $course_id = $insert_course->insert_id;
    $insert_course->close();
} else {
    $check_course->close();
}

// Insert into enrollments
$enrollment = $conn->prepare("INSERT INTO enrollments (student_id, course_id, grade) VALUES (?, ?, ?)");
$enrollment->bind_param("iis", $student_id, $course_id, $grade);
$enrollment->execute();
$enrollment->close();

$conn->close();

// Success message
echo "<h3 style='text-align:center;margin-top:50px;'>Student and Course Registered Successfully!</h3>";
echo "<div style='text-align:center;margin-top:20px;'><a href='index.html'>Register Another</a></div>";
?>
