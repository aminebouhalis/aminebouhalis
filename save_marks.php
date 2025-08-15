<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marks'])) {
    // التحقق من صلاحية المستخدم (أستاذ)
    if (!isset($_SESSION['teacher_id'])) {
        $_SESSION['error'] = "يجب تسجيل الدخول كأستاذ";
        header("Location: loginT.php");
        exit();
    }

    $marks = $_POST['marks'];
    $successCount = 0;
    
    try {
        // بدء transaction
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("INSERT INTO ExamResults (idS, idM, mark) 
                               VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE mark = VALUES(mark)");
        
        foreach ($marks as $studentId => $modules) {
            foreach ($modules as $moduleId => $data) {
                if (!empty($data['mark'])) {
                    $mark = floatval($data['mark']);
                    $stmt->bind_param("ssd", $studentId, $moduleId, $mark);
                    $stmt->execute();
                    $successCount++;
                }
            }
        }
        
        // تأكيد العملية
        $conn->commit();
        $stmt->close();
        
        $_SESSION['success'] = "تم حفظ $successCount علامة بنجاح";
    } catch (Exception $e) {
        // التراجع في حالة خطأ
        $conn->rollback();
        $_SESSION['error'] = "حدث خطأ أثناء الحفظ: " . $e->getMessage();
    }
    
    header("Location: Exams.php");
    exit();
} else {
    header("Location: Exams.php");
    exit();
}
?>