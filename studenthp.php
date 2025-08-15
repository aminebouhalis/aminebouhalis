<?php
session_start();
if (!isset($_SESSION['registerNumber'])) {
    header("Location: login.html");
    exit();
}

$studentName = $_SESSION['studentName'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Homepage</title>
  <script src="studenthp.js" defer></script>
  <link rel="stylesheet" href="studenthp.css" />
</head>
<body>

  <header>
    <div class="navbar">
      <div class="menu">
        <img src="list.png" class="image-square" id="menu-toggle"/>
          <div class="account-menu" id="accountMenu">
          <a href="student_profile.php">Manage Account</a>
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
        <div class="block"><a href="#" id="gradesLink">Exam Guards</a></div>
        <div class="block"><a href="#">Assessment</a></div>
        <div class="block"><a href="#">Section & Group</a></div>
        <div class="block"><a href="#">Time Tables</a></div>
        <div class="block"><a href="#">Semi-Annual Deliberation</a></div>
      </div>
    </aside>

 
   
      <main class="main-content" id="mainContent">
    <div class="welcome-content">
        <h2>welcome to your page dear<?= $studentName ?></h2>
      <p>choice one of this options in sides</p>
    </div>
    
      
    <div class="grades-section" style="display:none;">
        <!-- سيتم ملؤها بواسطة JavaScript -->
    </div>
       
</main>
    </main>

    <aside class="right-sidebar">
      <div class="block-container">
        <div class="block"><a href="#">Cours</a></div>
        <div class="block"><a href="#">TD & TP</a></div>
        <div class="block"><a href="#"> كشف النقاط</a></div>
        <div class="block"><a href="#">Exam Tables</a></div>
        <div class="block"><a href="#">General Deliberation</a></div>
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
