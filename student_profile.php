<?php
session_start();
require 'connect.php';

// Redirect if not logged in
if (!isset($_SESSION['registerNumber'])) {
    header("Location: login.html");
    exit();
}

$studentId = $_SESSION['registerNumber'];

// Get student data
$stmt = $conn->prepare("
    SELECT s.*, 
           GROUP_CONCAT(m.module SEPARATOR ', ') AS enrolled_modules
    FROM students s
    LEFT JOIN student_modules sm ON s.registerNumber = sm.student_id
    LEFT JOIN modules m ON sm.module_id = m.id_module
    WHERE s.registerNumber = ?
    GROUP BY s.registerNumber
");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Set default values if columns don't exist
$student['theme'] = $student['theme'] ?? 'light';
$student['language'] = $student['language'] ?? 'en';
$student['profile_picture'] = $student['profile_picture'] ?? '';

// Check if theme/language columns exist
$result = $conn->query("SHOW COLUMNS FROM students LIKE 'theme'");
$themeColumnExists = ($result->num_rows > 0);

$result = $conn->query("SHOW COLUMNS FROM students LIKE 'profile_picture'");
$profilePicColumnExists = ($result->num_rows > 0);

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_personal'])) {
        $fullName = $_POST['fullName'];
        $email = $_POST['email'];
        
        $updateStmt = $conn->prepare("UPDATE students SET fullName = ?, email = ? WHERE registerNumber = ?");
        $updateStmt->bind_param("ssi", $fullName, $email, $studentId);
        if ($updateStmt->execute()) {
            $_SESSION['fullName'] = $fullName;
            $student['fullName'] = $fullName;
            $student['email'] = $email;
            $message = '<div class="message success">تم تحديث البيانات الشخصية بنجاح!</div>';
        } else {
            $message = '<div class="message error">خطأ في تحديث البيانات الشخصية</div>';
        }
    }
    
    if (isset($_POST['update_settings']) && $themeColumnExists) {
        $theme = $_POST['theme'];
        $language = $_POST['language'];
        
        $updateStmt = $conn->prepare("UPDATE students SET theme = ?, language = ? WHERE registerNumber = ?");
        $updateStmt->bind_param("ssi", $theme, $language, $studentId);
        if ($updateStmt->execute()) {
            $message = '<div class="message success">تم تحديث الإعدادات بنجاح!</div>';
        } else {
            $message = '<div class="message error">خطأ في تحديث الإعدادات</div>';
        }
    }
    
    if (isset($_POST['update_password'])) {
        $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $updateStmt = $conn->prepare("UPDATE students SET password = ? WHERE registerNumber = ?");
        $updateStmt->bind_param("si", $newPassword, $studentId);
        if ($updateStmt->execute()) {
            $message = '<div class="message success">تم تحديث كلمة المرور بنجاح!</div>';
        } else {
            $message = '<div class="message error">خطأ في تحديث كلمة المرور</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملف الطالب - <?= htmlspecialchars($student['fullName']) ?></title>
    <link rel="stylesheet" href="studenthp.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            flex: 1;
            display: flex;
            width: 100%;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f5f5f5;
            overflow-y: auto;
        }
        
        .profile-container {
            display: flex;
            gap: 30px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-details {
            flex: 1;
            min-width: 300px;
        }
        
        .section {
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .logout-btn {
            background-color: #f44336;
        }
        
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
        
        footer {
            background-color: black;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: auto;
        }
        
        footer a {
            color: white;
            margin: 0 10px;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }
            
            .profile-picture {
                align-self: center;
            }
        }
        
        /* Dark theme styles */
        body.dark {
            background-color: #333;
            color: #fff;
        }

        body.dark .section,
        body.dark .main-content {
            background-color: #444;
            color: #fff;
        }

        body.dark input,
        body.dark select {
            background-color: #555;
            color: #fff;
            border-color: #666;
        }

        body.dark .section {
            border-color: #555;
        }
    </style>
</head>
<body class="<?= $student['theme'] === 'dark' ? 'dark' : '' ?>">
    <header>
        <div class="navbar">
            <div class="menu">
                <img src="list.png" class="image-square" onclick="window.location.href='studenthp.php'"/>
            </div>
            <div class="icon">
                <img src="department_logo.jpg" class="image-circle" />
                <div class="logo">
                    <h3>DCS</h3>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <aside class="left-sidebar">
            <div class="block-container">
                <div class="block"><a href="studenthp.php">العودة للصفحة الرئيسية</a></div>
            </div>
        </aside>

        <main class="main-content">
            <?= $message ?>
            <div class="profile-container">
                <div class="profile-picture" onclick="document.getElementById('fileInput').click()">
                    <?php if (!empty($student['profile_picture'])): ?>
                        <img src="<?= htmlspecialchars($student['profile_picture']) ?>" alt="صورة الملف الشخصي">
                    <?php else: ?>
                        <span>انقر لرفع صورة</span>
                    <?php endif; ?>
                    <input type="file" id="fileInput" style="display:none" accept="image/*">
                </div>
                
                <div class="profile-details">
                    <form method="POST" action="">
                        <div class="section">
                            <h2>البيانات الشخصية</h2>
                            <div class="form-group">
                                <label for="fullName">الاسم الكامل</label>
                                <input type="text" id="fullName" name="fullName" value="<?= htmlspecialchars($student['fullName']) ?>" required>
                            </div>
                            <div class="form-group">
                                 <label for="registerNumber">رقم التسجيل</label>
    <input type="text" id="registerNumber" value="<?= $student['registerNumber'] ?>" readonly disabled>
    <small style="color: #666; display: block; margin-top: 5px;">رقم التسجيل غير قابل للتعديل</small>
                            <div class="form-group">
                                <label for="email">البريد الإلكتروني</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                            </div>
                            <button type="submit" name="update_personal" class="btn">حفظ التغييرات</button>
                        </div>
                    </form>
                    
                    <?php if ($themeColumnExists): ?>
                    <form method="POST" action="" id="settingsForm">
                        <div class="section">
                            <h2>إعدادات الحساب</h2>
                            <div class="form-group">
                                <label for="theme">السمة</label>
                                <select id="theme" name="theme" class="form-control">
                                    <option value="light" <?= $student['theme'] === 'light' ? 'selected' : '' ?>>فاتح</option>
                                    <option value="dark" <?= $student['theme'] === 'dark' ? 'selected' : '' ?>>غامق</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="language">اللغة</label>
                                <select id="language" name="language" class="form-control">
                                    <option value="en" <?= $student['language'] === 'en' ? 'selected' : '' ?>>English</option>
                                    <option value="ar" <?= $student['language'] === 'ar' ? 'selected' : '' ?>>العربية</option>
                                    <option value="fr" <?= $student['language'] === 'fr' ? 'selected' : '' ?>>Français</option>
                                </select>
                            </div>
                            <button type="submit" name="update_settings" class="btn">تحديث الإعدادات</button>
                        </div>
                    </form>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="passwordForm">
                        <div class="section">
                            <h2>تغيير كلمة المرور</h2>
                            <div class="form-group">
                                <label for="password">كلمة المرور الجديدة</label>
                                <input type="password" id="password" name="password" placeholder="أدخل كلمة المرور الجديدة">
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">تأكيد كلمة المرور</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="أعد إدخال كلمة المرور الجديدة">
                            </div>
                            <button type="submit" name="update_password" class="btn">تحديث كلمة المرور</button>
                        </div>
                    </form>
                    
                    <div class="section">
                        <h2>المعلومات الأكاديمية</h2>
                        <div class="form-group">
                            <label>سنة البكالوريا</label>
                            <input type="text" value="<?= $student['yearBac'] ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>المستوى الحالي</label>
                            <input type="text" value="<?= $student['current_level'] ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>المواد المسجلة</label>
                            <p><?= $student['enrolled_modules'] ?? 'لا توجد مواد مسجلة' ?></p>
                        </div>
                    </div>
                    
                    <form action="logout.php" method="POST">
                        <button type="submit" class="btn logout-btn">تسجيل الخروج</button>
                    </form>
                </div>
            </div>
        </main>

        <aside class="right-sidebar">
            <div class="block-container">
                <div class="block"><a href="#">عرض الدرجات</a></div>
                <div class="block"><a href="#">المواد الدراسية</a></div>
            </div>
        </aside>
    </div>

    <footer>
        <p>&copy; جميع الحقوق محفوظة 2025</p>
        <div>
            <a href="https://www.facebook.com/dprt.info48">الصفحة الرسمية للقسم</a>
            <a href="https://www.facebook.com/FST.Univ.Relizane">الصفحة الرسمية للكلية</a>
            <a href="https://www.facebook.com/relizane.uni">الصفحة الرسمية للجامعة</a>
            <a href="https://univ-relizane.dz/">موقع الجامعة</a>
            <a href="https://elearning.univ-relizane.dz/">منصة التعلم عن بعد</a>
        </div>
    </footer>

    <script>
        // Handle profile picture upload
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('يجب أن يكون حجم الملف أقل من 2MB');
                    return;
                }
                
                const formData = new FormData();
                formData.append('profile_picture', file);
                formData.append('registerNumber', '<?= $studentId ?>');
                
                fetch('upload_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const profilePic = document.querySelector('.profile-picture');
                        profilePic.innerHTML = `<img src="${data.filePath}" alt="صورة الملف الشخصي">`;
                        location.reload();
                    } else {
                        alert(data.message || 'خطأ في رفع صورة الملف الشخصي');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('خطأ في رفع صورة الملف الشخصي');
                });
            }
        });

        // Password validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('كلمات المرور غير متطابقة');
                return false;
            }
            
            if (password.length > 0 && password.length < 8) {
                e.preventDefault();
                alert('يجب أن تكون كلمة المرور 8 أحرف على الأقل');
                return false;
            }
            
            return true;
        });

        // Theme switcher
        const themeSelect = document.getElementById('theme');
        if (themeSelect) {
            themeSelect.addEventListener('change', function() {
                document.body.className = this.value;
            });
        }
    </script>
</body>
</html>