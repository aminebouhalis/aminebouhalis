<?php
session_start();

// مسح متغيرات الجلسة
$_SESSION = array();

// إذا تم تعطيل الجلسة، احذف كوكيز الجلسة
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// مسح كوكيز "تذكرني"
setcookie('teacher_id', '', time() - 3600, "/");
setcookie('teacher_token', '', time() - 3600, "/");
setcookie('registerNumber', '', time() - 3600, "/");
setcookie('student_token', '', time() - 3600, "/");

// تدمير الجلسة
session_destroy();

// توجيه إلى الصفحة الرئيسية
header("Location: index.php");
exit();
?>