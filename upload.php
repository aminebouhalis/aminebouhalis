<?php
if($_SERVER['REQUEST_METHOD']==='POST'){
    $targetDirectory ="cours/";
    $targetFile = $targetDirectory.basename($_FILES["file"]["name"]);

    if($_FILES["file"]["error"]=== UPLOAD_ERR_OK){
        if(move_uploaded_file($_FILES["file"]["tmp_name"],$targetFile)){
            echo "success uploade:".htmlspecialchars(basename($_FILES["file"]["name"]));
    }

    else{
        echo "fail upload.";
    }
}

    else {
       echo "fail upload: " .$_FILES["file"]["error"];
    }
}

?>