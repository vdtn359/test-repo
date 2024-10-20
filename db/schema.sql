CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50),
    surname VARCHAR(50));

CREATE TABLE IF NOT EXISTS Courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255));

CREATE TABLE IF NOT EXISTS Enrolments (
    enrolment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    course_id INT,
    completion_status ENUM('not started', 'in progress', 'completed'),
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (course_id) REFERENCES Courses(course_id));