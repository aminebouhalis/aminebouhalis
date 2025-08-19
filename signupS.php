}
<?php
session_start();
include('connect.php');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['bacyear', 'id', 'user', 'email', 'current_level', 'uid'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(["status" => "error", "message" => "جميع الحقول مطلوبة."]);
            exit();
        }
    }

    $yearBac        = mysqli_real_escape_string($conn, $_POST['bacyear']);
    $registerNumber = mysqli_real_escape_string($conn, $_POST['id']);
    $username       = mysqli_real_escape_string($conn, $_POST['user']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);
    $current_level  = mysqli_real_escape_string($conn, $_POST['current_level']);
    $firebase_uid   = mysqli_real_escape_string($conn, $_POST['uid']);

    // ✅ تحقق: هل رقم التسجيل موجود؟
    $check_id = $conn->prepare("SELECT registerNumber FROM students WHERE registerNumber = ?");
    $check_id->bind_param("s", $registerNumber);
    $check_id->execute();
    $check_id->store_result();

    if ($check_id->num_rows === 0) {
        echo json_encode([
            "status" => "error",
            "message" => "رقم التسجيل غير موجود في قاعدة البيانات. الرجاء مراجعة الإدارة."
        ]);
        exit();
    }

    // ✅ تحقق: البريد الإلكتروني مكرر؟
    $check_email = $conn->prepare("SELECT email FROM students WHERE email = ? AND registerNumber != ?");
    $check_email->bind_param("ss", $email, $registerNumber);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "البريد الإلكتروني مستخدم بالفعل من قبل طالب آخر."
        ]);
        exit();
    }

    // ✅ تحديث بيانات الطالب
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
        echo json_encode([
            "status" => "success",
            "message" => "تم إنشاء الحساب بنجاح! تحقق من بريدك الإلكتروني قبل تسجيل الدخول."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "خطأ أثناء تحديث الحساب: " . $update->error
        ]);
    }
    exit();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "الطلب غير صحيح."
    ]);
    exit();
}
