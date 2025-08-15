<?php
session_start();
include('connect.php');

if (isset($_POST['submit'])) {
    $idteach = mysqli_real_escape_string($conn, $_POST['idteach']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $hiredate = mysqli_real_escape_string($conn, $_POST['hiredate']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpass']);

      $check_id = "SELECT * FROM teachers WHERE idteach = '$idteach'";
    $res_id = mysqli_query($conn, $check_id);

    if (mysqli_num_rows($res_id) == 0) {
        $_SESSION["error"] = "this id not found";
        header("Location: signupT.html");
        exit();
    }

      $check_email = "SELECT * FROM teachers WHERE email = '$email'";
    $res_email = mysqli_query($conn, $check_email);

    if (mysqli_num_rows($res_email) > 0) {
        $_SESSION["error"] = "this mail is used";
        header("Location: signupT.html");
        exit();
    }

    if ($pass !== $cpass) {
        $_SESSION["error"] = "the passwords not same";
        header("Location: signupT.html");
        exit();
    }
    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    
    $update = "UPDATE teachers SET 
                    name = '$name',
                    hiredate = '$hiredate',
                    email = '$email',
                    phone = '$phone',
                    password = '$hashed'
               WHERE idteach = '$idteach'";

    $done = mysqli_query($conn, $update);

    if ($done) {
        header("Location: loginT.html"); 
        exit();
    } else {
        $_SESSION["error"] =" error to register this account";
        header("Location: signupT.html");
        exit();
    }
}
?>
