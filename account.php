<?php
session_start();
require 'connect.php';

// Redirect if not logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: loginT.html");
    exit();
}

$teacherId = $_SESSION['teacher_id'];

// Get teacher data
$stmt = $conn->prepare("
    SELECT t.*, 
           GROUP_CONCAT(DISTINCT m.module SEPARATOR ', ') AS teaching_modules,
           GROUP_CONCAT(DISTINCT tg.group_id SEPARATOR ', ') AS teaching_groups
    FROM teachers t
    LEFT JOIN teacher_modules tm ON t.idteach = tm.teacher_id
    LEFT JOIN modules m ON tm.module_id = m.id_module
    LEFT JOIN teacher_groupes tg ON t.idteach = tg.teacher_id
    WHERE t.idteach = ?
    GROUP BY t.idteach
");
$stmt->bind_param("s", $teacherId);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();

// Set default values if columns don't exist
$teacher['theme'] = $teacher['theme'] ?? 'light';
$teacher['language'] = $teacher['language'] ?? 'en';
$teacher['profile_picture'] = $teacher['profile_picture'] ?? '';

// Check if theme/language columns exist
$result = $conn->query("SHOW COLUMNS FROM teachers LIKE 'theme'");
$themeColumnExists = ($result->num_rows > 0);

$result = $conn->query("SHOW COLUMNS FROM teachers LIKE 'profile_picture'");
$profilePicColumnExists = ($result->num_rows > 0);

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_personal'])) {
        $fullName = $_POST['fullName'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        
        $updateStmt = $conn->prepare("UPDATE teachers SET name = ?, email = ?, phone = ? WHERE idteach = ?");
        $updateStmt->bind_param("ssss", $fullName, $email, $phone, $teacherId);
        if ($updateStmt->execute()) {
            $_SESSION['teacher_name'] = $fullName;
            $teacher['name'] = $fullName;
            $teacher['email'] = $email;
            $teacher['phone'] = $phone;
            $message = '<div class="message success">تم تحديث البيانات الشخصية بنجاح!</div>';
        } else {
            $message = '<div class="message error">خطأ في تحديث البيانات الشخصية</div>';
        }
    }
    
    if (isset($_POST['update_settings']) && $themeColumnExists) {
        $theme = $_POST['theme'];
        $language = $_POST['language'];
        
        $updateStmt = $conn->prepare("UPDATE teachers SET theme = ?, language = ? WHERE idteach = ?");
        $updateStmt->bind_param("sss", $theme, $language, $teacherId);
        if ($updateStmt->execute()) {
            $message = '<div class="message success">تم تحديث الإعدادات بنجاح!</div>';
        } else {
            $message = '<div class="message error">خطأ في تحديث الإعدادات</div>';
        }
    }
    
    if (isset($_POST['update_password'])) {
        $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $updateStmt = $conn->prepare("UPDATE teachers SET password = ? WHERE idteach = ?");
        $updateStmt->bind_param("ss", $newPassword, $teacherId);
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
    <title>ملف الأستاذ - <?= htmlspecialchars($teacher['name']) ?></title>
    <link rel="stylesheet" href="studenthp.css" />
    <style>
        /* نفس الأنماط المستخدمة في صفحة الطالب */
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
<body class="<?= $teacher['theme'] === 'dark' ? 'dark' : '' ?>">
    <header>
        <div class="navbar">
            <div class="menu">
                <img src="list.png" class="image-square" onclick="window.location.href='teacherhp.php'"/>
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
                <div class="block"><a href="teacherhp.php">العودة للصفحة الرئيسية</a></div>
            </div>
        </aside>

        <main class="main-content">
            <?= $message ?>
            <div class="profile-container">
                <div class="profile-picture" onclick="document.getElementById('fileInput').click()">
                    <?php if (!empty($teacher['profile_picture'])): ?>
                        <img src="<?= htmlspecialchars($teacher['profile_picture']) ?>" alt="صورة الملف الشخصي">
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
                                <input type="text" id="fullName" name="fullName" value="<?= htmlspecialchars($teacher['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="teacherId">رقم الأستاذ</label>
                                <input type="text" id="teacherId" value="<?= $teacher['idteach'] ?>" readonly disabled>
                                <small style="color: #666; display: block; margin-top: 5px;">رقم الأستاذ غير قابل للتعديل</small>
                            </div>
                            <div class="form-group">
                                <label for="email">البريد الإلكتروني</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($teacher['email']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">رقم الهاتف</label>
                                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>">
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
                                    <option value="light" <?= $teacher['theme'] === 'light' ? 'selected' : '' ?>>فاتح</option>
                                    <option value="dark" <?= $teacher['theme'] === 'dark' ? 'selected' : '' ?>>غامق</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="language">اللغة</label>
                                <select id="language" name="language" class="form-control">
                                    <option value="en" <?= $teacher['language'] === 'en' ? 'selected' : '' ?>>English</option>
                                    <option value="ar" <?= $teacher['language'] === 'ar' ? 'selected' : '' ?>>العربية</option>
                                    <option value="fr" <?= $teacher['language'] === 'fr' ? 'selected' : '' ?>>Français</option>
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
                        <h2>المعلومات المهنية</h2>
                        <div class="form-group">
                            <label>تاريخ التوظيف</label>
                            <input type="text" value="<?= $teacher['hire_date'] ?? 'غير محدد' ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>المواد التي يدرسها</label>
                            <p><?= $teacher['teaching_modules'] ?? 'لا توجد مواد مسجلة' ?></p>
                        </div>
                        <div class="form-group">
                            <label>المجموعات التي يدرسها</label>
                            <p><?= $teacher['teaching_groups'] ?? 'لا توجد مجموعات مسجلة' ?></p>
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
                <div class="block"><a href="#">إدارة المواد</a></div>
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
                formData.append('teacher_id', '<?= $teacherId ?>');
                
                fetch('upload_teacher_profile.php', {
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