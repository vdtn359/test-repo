<?php
include "dbconfig.php";

$conn = getConn();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$statuses = ['not started', 'in progress', 'completed'];

try {
    $sql_users = "SELECT user_id FROM Users";
    $users_ids_result = $conn->query($sql_users);
    if ($users_ids_result === FALSE) {
        throw new Exception("Failed to retrieve user IDs: " . $conn->error);
    }

    $sql_courses = "SELECT course_id FROM Courses";
    $courses_ids_result = $conn->query($sql_courses);
    if ($courses_ids_result === FALSE) {
        throw new Exception("Failed to retrieve course IDs: " . $conn->error);
    }

    $user_ids = [];
    while ($user_row = $users_ids_result->fetch_assoc()) {
        $user_ids[] = $user_row['user_id'];
    }

    $course_ids = [];
    while ($course_row = $courses_ids_result->fetch_assoc()) {
        $course_ids[] = $course_row['course_id'];
    }

    $stmt = $conn->prepare("INSERT INTO Enrolments (user_id, course_id, completion_status) VALUES (?, ?, ?)");
    if ($stmt === FALSE) {
        throw new Exception("Failed to prepare the SQL statement: " . $conn->error);
    }

    $stmt->bind_param("iis", $user_id, $course_id, $completion_status);

    foreach ($user_ids as $user_id) {
        foreach ($course_ids as $course_id) {
            $completion_status = $statuses[array_rand($statuses)];

            if (!$stmt->execute()) {
                throw new Exception("Failed to execute the enrolment insertion: " . $stmt->error);
            }
        }
    }

    $stmt->close();
    echo "Enrolments inserted successfully.";

} catch (Exception $e) {
    error_log("Error inserting enrolments: " . $e->getMessage());
    echo "An error occurred while processing the enrolments.";
} finally {
    $conn->close();
}
?>
