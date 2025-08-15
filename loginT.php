<?php
session_start();
include('connect.php');

if (isset($_POST['idteach'], $_POST['password'])) {
    $idteach = mysqli_real_escape_string($conn, $_POST['idteach']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM teachers WHERE idteach = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $idteach);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $teacher = $result->fetch_assoc();
        if (password_verify($password, $teacher['password'])) {
            // تسجيل بيانات الجلسة
            $_SESSION['teacher_id'] = $teacher['idteach'];
            $_SESSION['teacher_name'] = $teacher['name'];
            $_SESSION['loggedin'] = true;
            $_SESSION['user_type'] = 'teacher';
            
            // تذكرني - حفظ الكوكيز لمدة 30 يوم
            if (isset($_POST['remember'])) {
                setcookie('teacher_id', $teacher['idteach'], time() + (30 * 24 * 60 * 60), "/");
                setcookie('teacher_token', password_hash($teacher['password'], PASSWORD_DEFAULT), time() + (30 * 24 * 60 * 60), "/");
            }
            
            header("Location: teacherhp.php"); 
            exit();
        } else {
            $_SESSION['error'] = "كلمة السر غير صحيحة.";
        }
    } else {
        $_SESSION['error'] = "لا يوجد حساب بهذا المعرف.";
    }
} else {
    $_SESSION['error'] = "يرجى ملء جميع الحقول.";
}

header("Location: loginT.html");
exit();
?>