
<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // التحقق من وجود جميع الحقول المطلوبة
    $required_fields = ['bacyear', 'id', 'user', 'email', 'pass', 'cpass', 'current_level'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION["error"] = "جميع الحقول مطلوبة";
            header("Location: signupS.html");
            exit();
        }
    }

    // تنظيف البيانات المدخلة
    $yearBac = mysqli_real_escape_string($conn, $_POST['bacyear']);
    $registerNumber = mysqli_real_escape_string($conn, $_POST['id']);
    $username = mysqli_real_escape_string($conn, $_POST['user']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['pass']; // لا نستخدم mysqli_real_escape_string على كلمة المرور قبل التشفير
    $current_level = mysqli_real_escape_string($conn, $_POST['current_level']);
    $cpassword = $_POST['cpass'];

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

    // 2. التحقق من البريد الإلكتروني المكرر (باستثناء الطالب الحالي)
    $check_email = $conn->prepare("SELECT * FROM students WHERE email = ? AND registerNumber != ?");
    $check_email->bind_param("ss", $email, $registerNumber);
    $check_email->execute();
    $res_email = $check_email->get_result();

    if ($res_email->num_rows > 0) {
        $_SESSION["error"] = "البريد الإلكتروني مستخدم بالفعل من قبل طالب آخر.";
        header("Location: signupS.html");
        exit();
    }

    // 3. التحقق من تطابق كلمة المرور
    if ($password !== $cpassword) {
        $_SESSION["error"] = "كلمتا المرور غير متطابقتين.";
        header("Location: signupS.html");
        exit();
    }

    // 4. التحقق من قوة كلمة المرور (إضافة اختيارية)
    if (strlen($password) < 8) {
        $_SESSION["error"] = "كلمة المرور يجب أن تكون 8 أحرف على الأقل.";
        header("Location: signupS.html");
        exit();
    }

    // 5. تشفير كلمة المرور
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // 6. تحديث بيانات الطالب باستخدام Prepared Statements
    $update = $conn->prepare("UPDATE students SET 
                            fullName = ?,
                            email = ?,
                            password = ?,
                            yearBac = ?,
                            current_level = ?
                          WHERE registerNumber = ?");

    $update->bind_param("ssssss", $username, $email, $hash, $yearBac, $current_level, $registerNumber);
    $done = $update->execute();

    if ($done) {
        $_SESSION["success"] = "تم تحديث بيانات الحساب بنجاح! يمكنك الآن تسجيل الدخول.";
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