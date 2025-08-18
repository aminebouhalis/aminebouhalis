<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marks']) && isset($_POST['module'])) {
    if (!isset($_SESSION['teacher_id'])) {
        $_SESSION['error'] = "يجب تسجيل الدخول كأستاذ";
        header("Location: loginT.php");
        exit();
    }

    $marks = $_POST['marks'];
    $moduleId = (int)$_POST['module'];
    $successCount = 0;

    try {
        $conn->begin_transaction();
        $stmt = $conn->prepare("
            INSERT INTO ExamResults (idS, idM, mark)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE mark = VALUES(mark)
        ");

        foreach ($marks as $studentId => $mark) {
            if ($mark !== '' && $mark !== null) {
                $markValue = floatval($mark);
                $stmt->bind_param("iid", $studentId, $moduleId, $markValue);
                $stmt->execute();
                $successCount++;
            }
        }

        $conn->commit();
        $stmt->close();
        $_SESSION['success'] = "تم حفظ $successCount علامة بنجاح";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "حدث خطأ أثناء الحفظ: " . $e->getMessage();
    }

    header("Location: Exams.php");
    exit();
} else {
    header("Location: Exams.php");
    exit();
}
