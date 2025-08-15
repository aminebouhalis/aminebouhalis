<?php
session_start();
require 'connect.php';

// التحقق من تسجيل الدخول كأستاذ
if (!isset($_SESSION['teacher_id'])) {
    header("Location: loginT.php");
    exit();
}

// التحقق من وجود المادة المحددة
if (!isset($_POST['module'])) {
    $_SESSION['error'] = "لم يتم اختيار المادة";
    header("Location: Exams.php");
    exit();
}

$selectedModule = (int)$_POST['module'];

// جلب اسم المادة
$moduleQuery = $conn->prepare("SELECT module FROM modules WHERE id_module = ?");
$moduleQuery->bind_param("i", $selectedModule);
$moduleQuery->execute();
$moduleResult = $moduleQuery->get_result();

if ($moduleResult->num_rows === 0) {
    $_SESSION['error'] = "المادة المحددة غير موجودة";
    header("Location: Exams.php");
    exit();
}

$moduleName = $moduleResult->fetch_assoc()['module'];

// جلب الطلاب المسجلين في هذه المادة
$studentsQuery = $conn->prepare("
    SELECT registerNumber, fullName 
    FROM students s
    JOIN student_modules sm ON s.registerNumber = sm.student_id
    WHERE sm.module_id = ?
    ORDER BY s.fullName
");

if (!$studentsQuery) {
    die("خطأ في إعداد الاستعلام: " . $conn->error);
}
$studentsQuery->bind_param("i", $selectedModule);
$studentsQuery->execute();
$students = $studentsQuery->get_result();

if (!$students) {
    die("خطأ في جلب بيانات الطلاب: " . $conn->error);
}

if ($students->num_rows === 0) {
    $_SESSION['error'] = "لا يوجد طلاب مسجلين في هذه المادة";
    header("Location: Exams.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدخال العلامات - <?= htmlspecialchars($moduleName) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }
        .module-name {
            text-align: center;
            font-size: 1.2em;
            margin-bottom: 20px;
            color: #3498db;
        }
        form {
            margin-top: 20px;
        }
        .student-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .student-name {
            flex: 2;
        }
        .student-mark {
            flex: 1;
            margin-left: 10px;
        }
        input[type="number"] {
            width: 80px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 4px;
            display: block;
            width: 100%;
        }
        button:hover {
            background-color: #27ae60;
        }
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>إدخال العلامات</h1>
        <div class="module-name">المادة: <?= htmlspecialchars($moduleName) ?></div>
        
        <form method="post" action="save_marks.php">
            <input type="hidden" name="module" value="<?= $selectedModule ?>">
            
            <?php while ($student = $students->fetch_assoc()): ?>
                <div class="student-row">
                    <div class="student-name"><?= htmlspecialchars($student['fullName']) ?></div>
                    <div class="student-mark">
                        <input type="number" 
                               name="marks[<?= $student['registerNumber'] ?>]" 
                               min="0" max="20" step="0.01" required
                               placeholder="0.00">
                    </div>
                </div>
            <?php endwhile; ?>
            
            <button type="submit">حفظ العلامات</button>
        </form>
    </div>
</body>
</html>