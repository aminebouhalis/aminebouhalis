<?php
session_start();
include('connect.php');

if (isset($_POST['uid']) && isset($_POST['email'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $query = "SELECT * FROM students WHERE firebase_uid = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $uid, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        $_SESSION['studentName'] = $student['fullName'];
        $_SESSION['registerNumber'] = $student['registerNumber'];
        $_SESSION['loggedin'] = true;
        $_SESSION['user_type'] = 'student';

        echo "success";
    } else {
        echo "لم يتم العثور على الطالب في قاعدة البيانات.";
    }
} else {
    echo "بيانات غير صالحة.";
}
