<?php
 require 'connect.php';
 $date=date('Y-m-d');
  
  foreach ($_POST as $key => $value){
    if(strpos($key,'presences_')===0){
        $registerNumber = str_replace('presences_',"",$key);
        $presences=$_POST ['presences_'.$registerNumber];
        $td=$_POST['td_'.$registerNumber];
         $tp=$_POST['tp_'.$registerNumber];
          $cours=$_POST['cours_'.$registerNumber];
           $participit=$_POST['participit_'.$registerNumber];
           
            
   $query="INSERT INTO  assessments(registerNumber,date,presences,td,tp,cours,participit) Values ('$registerNumber','$date','$presences','$td','$tp','$cours','$participit')";
   mysqli_query($conn,$query);
    }
  }
   mysqli_close($conn);
   ?>