<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['teacher_id']) || !isset($_FILES['profile_picture'])) {
    die(json_encode(['success' => false, 'message' => 'غير مصرح به']));
}

$teacherId = $_SESSION['teacher_id'];
$uploadDir = 'uploads/teacher_profile_pictures/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxSize = 2 * 1024 * 1024; // 2MB

// تحقق إذا كان العمود موجوداً
$result = $conn->query("SHOW COLUMNS FROM teachers LIKE 'profile_picture'");
if ($result->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'ميزة صورة الملف الشخصي غير متاحة']));
}

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
    die(json_encode(['success' => false, 'message' => 'خطأ في الرفع']));
}

if ($_FILES['profile_picture']['size'] > $maxSize) {
    die(json_encode(['success' => false, 'message' => 'الملف كبير جداً']));
}

if (!in_array($_FILES['profile_picture']['type'], $allowedTypes)) {
    die(json_encode(['success' => false, 'message' => 'نوع الملف غير مسموح به']));
}

$fileExt = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
$fileName = 'teacher_profile_' . $teacherId . '.' . $fileExt;
$filePath = $uploadDir . $fileName;

if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
    $stmt = $conn->prepare("UPDATE teachers SET profile_picture = ? WHERE idteach = ?");
    $stmt->bind_param("ss", $filePath, $teacherId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'filePath' => $filePath]);
    } else {
        unlink($filePath);
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'خطأ في حفظ الملف']);
}
?>