<?php
    
    if(isset($_GET['filename'])){
        $projName = $_GET['filename'];
        $filePath = "C:\\Program Files\\IBM\\SPSS\\DataCollection\\7\\Interviewer Server\\FMRoot\\Master\\$projName\\$projName.mdd";
        if(!empty($projName) && file_exists($filePath)){
            // Define headers
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$projName.mdd");
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: binary");
            
            // Read the file
            readfile($filePath);
            exit;
        }else{
            echo 'The file does not exist.';
        }

    }
    

?>