<?php
include "dbconfig.php";

$conn = getConn();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$courses_data_file = "courses.csv";

try {
    if (!is_readable($courses_data_file)) {
        throw new Exception("The file $courses_data_file does not exist or is not readable.");
    }

    if (($file = fopen($courses_data_file, "r")) === FALSE) {
        throw new Exception("Failed to open the file $courses_data_file.");
    }

    fgetcsv($file);

    $stmt = $conn->prepare("INSERT INTO Courses (description) VALUES (?)");
    if ($stmt === FALSE) {
        throw new Exception("Failed to prepare the SQL statement: " . $conn->error);
    }

    // Bind the parameter to the statement
    $stmt->bind_param("s", $description);

    // Process each row of the CSV file
    while (($data = fgetcsv($file)) !== FALSE) {
        // Validate the CSV row data
        if (isset($data[0]) && !empty($data[0])) {
            $description = trim($data[0]);

            // Execute the statement
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute the statement: " . $stmt->error);
            }
        } else {
            throw new Exception("Invalid data found in the CSV file.");
        }
    }

    $stmt->close();

    echo "Courses inserted successfully.";

} catch (Exception $e) {
    error_log("Error inserting courses: " . $e->getMessage());
    echo "An error occurred while processing the file.";
} finally {
    if (isset($file) && is_resource($file)) {
        fclose($file);
    }
    if ($conn) {
        $conn->close();
    }
}
?>
