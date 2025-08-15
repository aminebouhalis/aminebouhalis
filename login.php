<?php
session_start();
include('connect.php');

if (isset($_POST['registerNumber'], $_POST['password'])) {
    $registerNumber = mysqli_real_escape_string($conn, $_POST['registerNumber']);
    $password = $_POST['password']; // لا حاجة لتشفير كلمة المرور المدخلة

    $query = "SELECT * FROM students WHERE registerNumber = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $registerNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        if (password_verify($password, $student['password'])) {
            $_SESSION['studentName'] = $student['fullName']; // أو أي شيء تريد حفظه
            $_SESSION['registerNumber'] = $student['registerNumber'];
              $_SESSION['loggedin'] = true;
            $_SESSION['user_type'] = 'student';
            // تذكرني - حفظ الكوكيز لمدة 30 يوم
            if (isset($_POST['remember'])) {
                setcookie('registerNumber', $student['registerNumber'], time() + (30 * 24 * 60 * 60), "/");
                setcookie('student_token', password_hash($student['password'], PASSWORD_DEFAULT), time() + (30 * 24 * 60 * 60), "/");
            }
            header("Location: studenthp.php"); 
            exit();
        } else {
            $_SESSION['error'] = "كلمة السر غير صحيحة.";
        }
    } else {
        $_SESSION['error'] = "رقم التسجيل غير موجود.";
    }
} else {
    $_SESSION['error'] = "يرجى ملء جميع الحقول.";
}

header("Location: login.html");
exit();
?>
