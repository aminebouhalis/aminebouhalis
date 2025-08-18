<?php
session_start();
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idteach   = mysqli_real_escape_string($conn, $_POST['idteach']);
    $name      = mysqli_real_escape_string($conn, $_POST['name']);
    $hiredate  = mysqli_real_escape_string($conn, $_POST['hiredate']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
    $firebase_uid = mysqli_real_escape_string($conn, $_POST['uid']); // UID من Firebase

    // 1. التحقق من وجود الأستاذ برقم التعريف
    $check_id = $conn->prepare("SELECT * FROM teachers WHERE idteach = ?");
    $check_id->bind_param("s", $idteach);
    $check_id->execute();
    $res_id = $check_id->get_result();

    if ($res_id->num_rows === 0) {
        $_SESSION["error"] = "معرف الأستاذ غير موجود. يرجى مراجعة الإدارة.";
        header("Location: signupT.html");
        exit();
    }

    // 2. التحقق من البريد الإلكتروني المكرر
    $check_email = $conn->prepare("SELECT * FROM teachers WHERE email = ? AND idteach != ?");
    $check_email->bind_param("ss", $email, $idteach);
    $check_email->execute();
    $res_email = $check_email->get_result();

    if ($res_email->num_rows > 0) {
        $_SESSION["error"] = "البريد الإلكتروني مستخدم من قبل أستاذ آخر.";
        header("Location: signupT.html");
        exit();
    }

    // 3. تحديث بيانات الأستاذ
    $update = $conn->prepare("UPDATE teachers SET 
                                name = ?,
                                hiredate = ?,
                                email = ?,
                                phone = ?,
                                firebase_uid = ?
                              WHERE idteach = ?");
    $update->bind_param("ssssss", $name, $hiredate, $email, $phone, $firebase_uid, $idteach);
    $done = $update->execute();

    if ($done) {
        $_SESSION["success"] = "تم إنشاء الحساب بنجاح! تحقق من بريدك الإلكتروني قبل تسجيل الدخول.";
        header("Location: loginT.html");
        exit();
    } else {
        $_SESSION["error"] = "حدث خطأ أثناء تحديث الحساب: " . $conn->error;
        header("Location: signupT.html");
        exit();
    }
} else {
    $_SESSION["error"] = "الطلب غير صحيح.";
    header("Location: signupT.html");
    exit();
}
