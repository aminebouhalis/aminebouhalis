<?php
$host = "sql309.infinityfree.com";
$username = "if0_39647801";
$password = "${{ secrets.FTP_PASSWORD }}";
$database = "if0_39647801_DepartementCS";

// تفعيل الإبلاغ عن الأخطاء
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // إنشاء اتصال جديد
    $conn = new mysqli($host, $username, $password, $database);
    
    // تحديد الترميز لضعم التعامل مع اللغة العربية
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // تسجيل الخطأ في ملف السجلات
    error_log("Database connection error: " . $e->getMessage());
    
    // رسالة خطأ تظهر للمستخدم
    die("حدث خطأ في الاتصال بقاعدة البيانات. الرجاء المحاولة لاحقاً.");
}
?>
