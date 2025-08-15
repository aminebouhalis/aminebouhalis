<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
session_start();
require 'connect.php';

// إعدادات التصحيح (يجب تعطيلها في بيئة الإنتاج)
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // تغيير إلى 0 في بيئة الإنتاج

if (!isset($_SESSION['registerNumber'])) {
    error_log("Access attempt without valid session: " . print_r($_SERVER, true));
    die(json_encode([
        'success' => false,
        'message' => 'يجب تسجيل الدخول أولاً'
    ]));
}

$studentId = $_SESSION['registerNumber'];

// التحقق من صحة رقم الطالب
if (!is_numeric($studentId)) {
    die(json_encode([
        'success' => false,
        'message' => 'معرّف طالب غير صالح'
    ]));
}

try {
    $stmt = $conn->prepare("
        SELECT m.module, e.mark 
        FROM ExamResults e
        JOIN modules m ON e.idM = m.id_module
        WHERE e.idS = ?
    ");
    
    if (!$stmt) {
        throw new Exception('تحضير الاستعلام فشل');
    }
    
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $grades = $result->fetch_all(MYSQLI_ASSOC);
    
    if (empty($grades)) {
        echo json_encode([
            'success' => true,
            'grades' => [],
            'message' => 'لا توجد نتائج مسجلة بعد'
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'grades' => $grades
    ]);
    
} catch (Exception $e) {
    error_log("Grades query error: " . $e->getMessage());
    die(json_encode([
        'success' => false,
        'message' => 'حدث خطأ في استرجاع البيانات'
    ]));
}
?>