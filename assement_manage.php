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
    header("Location: choise.php");
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
    header("Location: choise.php");
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
    header("Location: choise.php");
    exit();
}
  $query ="SELECT registerNumber,fullName FROM students" ;

 $result =mysqli_query($conn,$query);


?>
<!doctype html>
<html en>
    <head>
        <title> Assesments</title>
        <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                table{
                    border-collapse: collapse;
                    width: 100%;
                    margin-top: 20px;
                }

                th,td{
                    border: 1px solid #ddd;
                    padding: 10px;
                    text-align: center;
                }
                th{
                    background-color: #f0f0f0;
                }
                td{
                    background-color: #fff;
                }
                select{
                    width: 100%;
                    padding: 5PX;
                    border: none;
                    border-radius: 5px;
                    background-color: #f0f0f0;
                }
                select:focus{
                    background-color: #fff;
                    border: 1px solid #ddd;
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
        header {
  width: 100%;
  background: linear-gradient(to top, silver, white);
}

.image-circle{
       border-radius: 50%;
       width: 30px;
       height: 30px;
       object-fit: cover;
      }
        @media (max-width: 768px) {
  .container {
    flex-direction: column;
  }
  .main-content {
    min-height: 300px;
  }
}

            </style>
    </head>
<body>
     <header>
        <img src="department_logo.jpg" class="image-circle">
        <h1> DEPTCOMSCI</h1>
     </header>
    
     <center>
      <input type="date" name="date" value="<?php echo date('Y-m-d');?>">
     </center>
     <form action="save_data.php" method="post">
      <table>
        <thead>
            <tr>
                <th>ID-STU</th>
                <th>Name</th>
                <th>Presences</th>
                <th>Td</th>
                <th>Tp</th>
                <th>Cours</th>
                <th>Participit</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) {?>
                <tr>
                    <td>
                        <?php echo  $row['registerNumber'];?>
                    </td>
                    <td> <?php echo $row['fullName'];?></td>
                <td> <SELECT name="presences_<?php echo $row['registerNumber'];?>" >
                      <option value="+">+</option>
                       <option value="-">-</option>
                </SELECT>
               </td> 
                 <td> <SELECT name="td_<?php echo $row['registerNumber'];?>" >
                      <option value="+">+</option>
                       <option value="-">-</option>
                </SELECT>
               </td> 
                 <td> <SELECT name="tp_<?php echo $row['registerNumber'];?>" >
                      <option value="+">+</option>
                       <option value="-">-</option>
                </SELECT>
               </td> 
                 <td> <SELECT name="cours_<?php echo $row['registerNumber'];?>" >
                      <option value="+">+</option>
                       <option value="-">-</option>
                </SELECT>
               </td> 
                 <td> <SELECT name="participit_<?php echo $row['registerNumber'];?>" >
                      <option value="+">+</option>
                       <option value="-">-</option>
                </SELECT>
               </td> 
            </tr>
            <?php }?>
        </tbody>
      </table>
      <button type="submit">SAVE</button>
        </form>

</body>
</html>