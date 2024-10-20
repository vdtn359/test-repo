<?php
include "dbconfig.php";

try {
    $conn = getConn();

    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    $sql = "
    SELECT u.firstname, u.surname, c.description, e.completion_status
    FROM Enrolments e
    JOIN Users u ON e.user_id = u.user_id
    JOIN Courses c ON e.course_id = c.course_id";

    $stmt = $conn->prepare($sql);
    if ($stmt === FALSE) {
        throw new Exception("Failed to prepare the SQL statement: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    header("Content-Type: application/json");
    echo json_encode($data);

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        "error" => "Internal Server Error",
        "message" => "An error occurred while processing your request."
    ]);
} finally {
    if (isset($conn) && $conn !== null) {
        $conn->close();
    }
}
?>
