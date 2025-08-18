<?php
session_start();
include('connect.php');

if (isset($_POST['uid']) && isset($_POST['email'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['uid']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $query = "SELECT * FROM teachers WHERE firebase_uid = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $uid, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $teacher = $result->fetch_assoc();
        $_SESSION['teacher_id'] = $teacher['idteach'];
        $_SESSION['teacher_name'] = $teacher['name'];
        $_SESSION['loggedin'] = true;
        $_SESSION['user_type'] = 'teacher';

        echo "success";
    } else {
        echo "لم يتم العثور على الأستاذ في قاعدة البيانات.";
    }
} else {
    echo "بيانات غير صالحة.";
}
