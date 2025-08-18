
<?php
session_start();

// التحقق من جلسة الطالب
if (isset($_SESSION['registerNumber']) ){
    header("Location: studenthp.php");
    exit();
}

// التحقق من جلسة الأستاذ
if (isset($_SESSION['teacher_id'])) {
    header("Location: teacherhp.php");
    exit();
}

// التحقق من كوكيز "تذكرني" للطالب
if (isset($_COOKIE['registerNumber']) && isset($_COOKIE['student_token'])) {
    include('connect.php');
    $registerNumber = mysqli_real_escape_string($conn, $_COOKIE['registerNumber']);
    $query = "SELECT * FROM students WHERE registerNumber = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $registerNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        if (password_verify($student['password'], $_COOKIE['student_token'])) {
            $_SESSION['registerNumber'] = $student['registerNumber'];
            $_SESSION['studentName'] = $student['fullName'];
            $_SESSION['loggedin'] = true;
            $_SESSION['user_type'] = 'student';
            header("Location: studenthp.php");
            exit();
        }
    }
}

// التحقق من كوكيز "تذكرني" للأستاذ
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
            header("Location: teacherhp.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title> DEPARTEMENT OF COMPUTER SCIENCES</title>
      <Link rel="stylesheet" type="text/css" href="Departement.css">
 </head>
 <header>
    <center>
    <img src="university_logo.jpg" class="image-circle" >
    <img src="faculty_logo.jpg" class="image-circle" >
    <img src="department_logo.jpg" class="image-circle">
    </center>
 </header>
   <body>
    <script type="module">
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/12.1.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.1.0/firebase-analytics.js";
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  const firebaseConfig = {
    apiKey: "AIzaSyBQf_tjUTzUs688js-Runv6eawj4Hv2FWk",
    authDomain: "department-computer-science.firebaseapp.com",
    projectId: "department-computer-science",
    storageBucket: "department-computer-science.firebasestorage.app",
    messagingSenderId: "189934401736",
    appId: "1:189934401736:web:9bdc4d2e39fa83b4900dfd",
    measurementId: "G-SP2P9WT486"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
</script>
    <center>
      <h1> Welcome to our  web site </h1>
      <p><span style="color: deeppink;">SELECT YOUR CATAGORY </span></p>
     <div class="block-container">
       <div class="block"> 
         <button> <a href="Teachers.html">TEACHERS </a></button> <br><br>
         <img src="teacher.png" class="image-square">
       </div>
       <div class="block">
        <button> <a href="Students.html">STUDENTS  </a></button> <br><br>
        <img src="graduating-student.png"  class="image-square" alt="logo stude">
       </div>
     </div>
    </center>

     <footer>
    <p>&copy; All rights reserved 2025</p> 
    <a href="https://www.facebook.com/dprt.info48">Official Facebook page of our Department</a>
    <a href="https://www.facebook.com/FST.Univ.Relizane">Official Facebook page of our Faculty</a>
    <a href="https://www.facebook.com/relizane.uni">Official Facebook page of our University</a>
    <a href="https://univ-relizane.dz/">Website of university</a>
    
  </footer>
 </body>
</html>
