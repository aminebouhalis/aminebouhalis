<?php
session_start();
require 'connect.php';

// التحقق من تسجيل الدخول كأستاذ
if (!isset($_SESSION['teacher_id'])) {
    die("لا تملك صلاحية الوصول لهذه الصفحة");
}

// جلب مواد الأستاذ
$teacherId = $_SESSION['teacher_id'];
$modulesQuery = $conn->prepare("
    SELECT id_module, module 
    FROM modules 
    WHERE idT = ?
    ORDER BY module
");
$modulesQuery->bind_param("i", $teacherId);
$modulesQuery->execute();
$modules = $modulesQuery->get_result();

if (!$modules) {
    die("خطأ في جلب بيانات المواد: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدخال العلامات</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; margin-bottom: 20px; }
        select, button { padding: 8px; margin: 5px 0; width: 100%; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #3498db; color: white; border: none; cursor: pointer; padding: 10px; }
        button:hover { background-color: #2980b9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>إدخال العلامات</h1>
        <form method="post" action="enter_marks.php">
            <label for="module">اختر المادة:</label>
            <select name="module" id="module" required>
                <option value="">-- اختر المادة --</option>
                <?php while ($module = $modules->fetch_assoc()): ?>
                    <option value="<?= $module['id_module'] ?>"><?= htmlspecialchars($module['module']) ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit">التالي</button>
        </form>
    </div>
</body>
</html>