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
<html lang="en">
<head>
 <title> Cours</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
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
        form {
            margin-top: 20px;
        }
        select, input, button {
            padding: 8px;
            margin: 5px 0;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #2980b9;
        }
</style>
</head>
<body>
          <h1> <b> Upload your Cours </b> </h1>
          <form action="upload.php" method="POST" enctype="multipart/form-data">
              <label for="module">اختر المادة:</label>
            <select name="module" id="module" required>
                <option value="">-- اختر المادة --</option>
                <?php while ($module = $modules->fetch_assoc()): ?>
                    <option value="<?= $module['id_module'] ?>">
                    
                        <?= htmlspecialchars($module['module']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            
            <label for="file"> Choose a file :</label>
            <input type="file" id="file" name="file" required>
            <br><br>
       <button type="submit"> Upload</button>
          </form>  
</body>
</html>