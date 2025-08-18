<?php
session_start();
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحقول المطلوبة
    $required_fields = ['bacyear', 'id', 'user', 'email', 'current_level', 'uid'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION["error"] = "جميع الحقول مطلوبة";
            header("Location: signupS.html");
            exit();
        }
    }

    $yearBac = mysqli_real_escape_string($conn, $_POST['bacyear']);
    $registerNumber = mysqli_real_escape_string($conn, $_POST['id']);
    $username = mysqli_real_escape_string($conn, $_POST['user']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $current_level = mysqli_real_escape_string($conn, $_POST['current_level']);
    $firebase_uid = mysqli_real_escape_string($conn, $_POST['uid']); // UID من Firebase

    // 1. التحقق من وجود الطالب برقم التسجيل
    $check_id = $conn->prepare("SELECT * FROM students WHERE registerNumber = ?");
    $check_id->bind_param("s", $registerNumber);
    $check_id->execute();
    $res_id = $check_id->get_result();

    if ($res_id->num_rows === 0) {
        $_SESSION["error"] = "رقم التسجيل غير موجود في قاعدة البيانات. الرجاء مراجعة الإدارة.";
        header("Location: signupS.html");
        exit();
    }

    // 2. التحقق من البريد الإلكتروني المكرر
    $check_email = $conn->prepare("SELECT * FROM students WHERE email = ? AND registerNumber != ?");
    $check_email->bind_param("ss", $email, $registerNumber);
    $check_email->execute();
    $res_email = $check_email->get_result();

    if ($res_email->num_rows > 0) {
        $_SESSION["error"] = "البريد الإلكتروني مستخدم بالفعل من قبل طالب آخر.";
        header("Location: signupS.html");
        exit();
    }

    // 3. تحديث بيانات الطالب (بدون كلمة سر لأن Firebase مسؤول عنها)
    $update = $conn->prepare("UPDATE students SET 
                                fullName = ?,
                                email = ?,
                                firebase_uid = ?,
                                yearBac = ?,
                                current_level = ?
                              WHERE registerNumber = ?");

    $update->bind_param("ssssss", $username, $email, $firebase_uid, $yearBac, $current_level, $registerNumber);
    $done = $update->execute();

    if ($done) {
        $_SESSION["success"] = "تم إنشاء الحساب بنجاح باستخدام Firebase! يمكنك الآن تسجيل الدخول.";
        header("Location: login.html");
        exit();
    } else {
        $_SESSION["error"] = "حدث خطأ أثناء تحديث الحساب: " . $conn->error;
        header("Location: signupS.html");
        exit();
    }
} else {
    $_SESSION["error"] = "الطلب غير صحيح.";
    header("Location: signupS.html");
    exit();
}
