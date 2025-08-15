<?php
session_start();

// التحقق من الجلسة والكوكيز
if (!isset($_SESSION['teacher_id'])) {
    // إذا كانت هناك كوكيز "تذكرني"، حاول تسجيل الدخول تلقائياً
    if (isset($_COOKIE['teacher_id']) && isset($_COOKIE['teacher_token'])) {
        include('connect.php');
        
        $idteach = mysqli_real_escape_string($conn, $_COOKIE['teacher_id']);
        $query = "SELECT * FROM teachers WHERE idteach = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $idteach);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $teacher = $result->fetch_assoc();
            if (password_verify($teacher['password'], $_COOKIE['teacher_token'])) {
                $_SESSION['teacher_id'] = $teacher['idteach'];
                $_SESSION['teacher_name'] = $teacher['name'];
                $_SESSION['loggedin'] = true;
                $_SESSION['user_type'] = 'teacher';
            } else {
                header("Location: loginT.html");
                exit();
            }
        } else {
            header("Location: loginT.html");
            exit();
        }
    } else {
        header("Location: loginT.html");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Teachers Homepage</title>
   <script src="teacherhp.js" defer></script>
    <link rel="stylesheet" href="studenthp.css" />
</head>
<body>

  <header>
    <div class="navbar">
      <div class="menu">
        <img src="list.png" class="image-square" id="menu-toggle"/>
          <div class="account-menu" id="accountMenu">
          <a href="account.php">Manage Account</a>
          <a href="logout.php">Log Out</a>
        </div>
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
        <div class="block"><a href="#">Exam Guards</a></div>
        <div class="block"><a href="#">Assessment</a></div>
        <div class="block"><a href="#">Time Tables</a></div>
        <div class="block"><a href="#">Exam Tables</a></div>
      </div>
    </aside>

    <main class="main-content" id="mainContent">
      <h2>welcome to your page dear teacher</h2>
    </main>

    <aside class="right-sidebar">
      <div class="block-container">
        <div class="block"><a href="#">Section & Group</a></div>
        <div class="block"><a href="#">Cours</a></div>
        <div class="block"><a href="#">TD & TP</a></div>
        <div class="block"><a href="#">list students</a></div>
      </div>
    </aside>
  </div>

  <footer>
    <p>&copy; All rights reserved 2025</p> 
    <a href="https://www.facebook.com/dprt.info48">Official Facebook page of our Department</a>
    <a href="https://www.facebook.com/FST.Univ.Relizane">Official Facebook page of our Faculty</a>
    <a href="https://www.facebook.com/relizane.uni">Official Facebook page of our University</a>
    <a href="https://univ-relizane.dz/">Website of university</a>
    <a href="https://elearning.univ-relizane.dz/">Platform Moodle Relizane</a>
  </footer>

</body>
</html>
