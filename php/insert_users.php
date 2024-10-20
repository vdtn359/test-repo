<?php
include "dbconfig.php";

$conn = getConn();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$users_data_file = "users.csv";

try {
    if (!is_readable($users_data_file)) {
        throw new Exception("The file $users_data_file does not exist or is not readable.");
    }

    if (($file = fopen($users_data_file, "r")) === FALSE) {
        throw new Exception("Failed to open the file $users_data_file.");
    }

    fgetcsv($file);

    $stmt = $conn->prepare("INSERT INTO Users (firstname, surname) VALUES (?, ?)");
    if ($stmt === FALSE) {
        throw new Exception("Failed to prepare the SQL statement: " . $conn->error);
    }

    $stmt->bind_param("ss", $firstname, $surname);

    while (($data = fgetcsv($file)) !== FALSE) {
        if (isset($data[0], $data[1]) && !empty(trim($data[0])) && !empty(trim($data[1]))) {
            $firstname = trim($data[0]);
            $surname = trim($data[1]);

            if (!$stmt->execute()) {
                throw new Exception("Failed to execute the statement: " . $stmt->error);
            }
        } else {
            error_log("Skipping row with invalid data: " . implode(",", $data));
        }
    }

    $stmt->close();

    echo "Users inserted successfully.";

} catch (Exception $e) {
    error_log("Error inserting users: " . $e->getMessage());
    echo "An error occurred while processing the users data.";
} finally {
    if (isset($file) && is_resource($file)) {
        fclose($file);
    }
    if ($conn) {
        $conn->close();
    }
}
?>
