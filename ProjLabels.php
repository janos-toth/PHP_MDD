<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_GET["project"])){

    //echo $_GET["project"];
    $projName = $_GET["project"];
    $oMDM = new COM("MDM.Document",NULL,CP_UTF8) or die("Unable to instantiate MDM.Document"); 
    $oMDM->Open("C:\\Program Files\\IBM\\SPSS\\DataCollection\\7\\Interviewer Server\\FMRoot\\Master\\$projName\\$projName.mdd", '', 2);    

    $oShell = new COM("WScript.Shell");
    //$oShell->Run("cmd /K cd C:\Program Files\IBM\SPSS\DataCollection\7\Interviewer Server\FMRoot\Master\KUTIJAVENA\ & mrScriptCL Create_Random_TXT.mrs  > .\Masterlog.txt & Exit");
    //$oShell->Run("cmd /K mrScriptCL Create_Random_TXT.mrs  > .\Masterlog.txt & Exit");

    if (isset($_GET["lang"])){
      $currLang = $_GET["lang"];
      $oMDM->languages->Current = $currLang;
    }

}else{
  $projName = "";
}


if(isset($_POST["project_code"])){
  header('Location: ProjLabels.php?project=' . $_POST["project_code"]);
  return;
}



?>

<!DOCTYPE html>
<html>
<head>
    <!--<meta charset="utf-8">-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="IMG/dataexpert.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/style.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ></script>
    <script src="JS/xlsx.full.min.js" ></script>
    <script src="JS/fileSaver.js" ></script>
    <script src="JS/coreJS.js" ></script>
    <title>II. JANI CÁR PÁPA</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#"><img id="dexLogo" src="IMG/dataexpert.png" alt="DataExpert logo"><span id="prodLabel">LABEL SEEKER</span></a>
</nav>
<!-- Back to top button -->
<a id="topButton"></a>

<!--INPUT FIELD -->
<form id="getProjForm" class="form-inline" method="post">
  <div class="form-group">
    <input type="text" class="form-control" id="formGroupExampleInput" name="project_code" placeholder="Enter Project code...">
  </div>
  <button type="submit" class="btn btn-primary GetProject">Get Labels!</button>
  <button class="btn my-2" id="button-a">Download</button>
</form>

<!--PROJECT NAME -->
<div style="display:none;" class="jumbotron">
  <h2 class="display-1"><a href="download.php?filename=<?=$projName;?>"><?=$projName;?></a></h2>
</div>
<div id="plsWait" style="display:none;">
  <!--<img id="retardedLoading" src="IMG/Loading.gif" alt="loading">-->
  <img id="loading" src="IMG/LoadingGIF.gif" alt="loading">
  <h3>Loading content...</h3>
</div>

<!--TABS -->
<div class="container">
  <h3 style="display:none;" >Available languages</h3>

  <ul class="nav nav-tabs">
    <?php 
    if($projName != ""){
      $langCnt = 1;
      //$oMDM->languages->Current = 'ESP';

        foreach ($oMDM->languages as $oLangs){

          $tempLang = $oLangs->name;

          if($oLangs->name == $oMDM->languages->Current){
            echo "<li class='active'><a href='#' title='$oLangs->name'>$tempLang</a></li>";
          }else{
            echo "<li><a href='#' title='$oLangs->name'>$tempLang</a></li>";
          }
          $langCnt += 1;
        }
      }
    ?>

  </ul>
</div>
<!--TABLE -->
<table class="table" id="tableToDownload">
  <thead class="thead-dark">
    <tr>
        <th scope="col">#</th>
        <th scope="col">Variable IDs</th>
        <th scope="col">Labels</th>
    </tr>
  </thead>
  <tbody>
    <?php
      
      if($projName != ""){
        $cnt = 1;
        $qstsToExclude = "DataCollection;Respondent;DataCleaning;CompleteText;completTextHelper1;completTextHelper2;CompleteTextFTF;ConjointVersionMulti;ConjointVersion";
        $qstCnt = 1;
        $subQstCnt = 1;
        $InnerElemzCnt = 1;
        $subInnerQstCnt = 1;

        //traversing all fields
        foreach ($oMDM->Fields as $oField) {

          $ifNeeded = ($oField->Name == "" ? false : strpos($qstsToExclude,$oField->Name));

          if( $ifNeeded === false){
            if ( $oField->properties['noTR'] != TRUE ){

              echo "<tr class='qstBorder' id='qst_$qstCnt'><td>Question:</td><td>$oField->Name</td><td>$oField->Label</td></tr>";
              $qstCnt += 1;
            }
            //----------------------------------------------------------------------------------------------------------------------------------------------------------------
            //Checking if the fields is LOOP
            if($oField->ObjectTypeValue == 1 && $oField->properties['noTR'] != TRUE){
              
              //traversing all slices' of a loop
              foreach ($oField->Elements as $Elemz){
                if($Elemz->TypeName == "CategoryList"){
                  foreach($Elemz->Elements as $SubElemz){
                    if( $SubElemz->Label != "" && $SubElemz->Label != "Codes"){
                      echo "<tr><td></td><td>$SubElemz->Name</td><td>$SubElemz->Label</td></tr>";
                      $cnt +=1;
                    }
                  }
                }else{
                  if( $Elemz->Label != "" && $Elemz->Label != "Codes"){
                    echo "<tr><td></td><td>$Elemz->Name</td><td>$Elemz->Label</td></tr>";
                    $cnt +=1;
                  }               
                }
              }
              //traversing all categories of the loop
              foreach ($oField->Fields as $oSubFields){

                if($oSubFields->ObjectTypeValue == 1 && $oSubFields->properties['noTR'] != TRUE){

                  echo "<tr id='innerQst_$qstCnt'><td>InnerQuestion:</td><td>$oSubFields->Name</td><td>$oSubFields->Label</td></tr>";

                  foreach ($oSubFields->Elements as $InnerElemz){
                    if($InnerElemz->TypeName == "CategoryList"){
                      foreach($InnerElemz->Elements as $SubInnerElemz){
                        if( $SubInnerElemz->Label != "" && $SubInnerElemz->Label != "Codes"){
                          echo "<tr><td></td><td>$SubInnerElemz->Name</td><td>$SubInnerElemz->Label</td></tr>";
                          $InnerElemzCnt +=1;
                        }
                      }
                    }else{
                      if( $InnerElemz->Label != "" && $InnerElemz->Label != "Codes"){
                        echo "<tr><td></td><td>$InnerElemz->Name</td><td>$InnerElemz->Label</td></tr>";
                        $InnerElemzCnt +=1;
                      }               
                    }
                  }

                  foreach( $oSubFields->Fields as $oSubInnerFields ){
                    if($oSubInnerFields->Elements->Count > 0){
                      echo "<tr class='subQstBorder' id='innerQst_$subInnerQstCnt'><td><span class='caretDown'></span>innerSubquestion:</td><td>$oSubInnerFields->Name</td><td>$oSubInnerFields->Label</td></tr>";
  
                      foreach ($oSubInnerFields->Elements as $subElemz) {
                        if($subElemz->TypeName == "CategoryList"){
                            foreach ($subElemz->Elements as $subSubElemz) {
                              //if( $subSubElemz->Label != "" && $subSubElemz->Label != "Codes"){
                              if( $subSubElemz->Label != "Codes"){
                                echo "<tr class='innerQst_".$subInnerQstCnt."_subCateg' style='display: none;'><td></td><td>$subSubElemz->Name</td><td>$subSubElemz->Label</td></tr>";
                              }
                            }
                        }else{
                          //if( $subElemz->Label != "" && $subElemz->Label != "Codes"){
                          if( $subElemz->Label != "Codes"){
                            echo "<tr class='innerQst_".$subInnerQstCnt."_subCateg' style='display: none;'><td></td><td>$subElemz->Name</td><td>$subElemz->Label</td></tr>";
                          }
                        }
                      }
                    }else{
                      echo "<tr class='subQstBorder' id='innerQst_$subInnerQstCnt'><td>innerSubquestion:</td><td>$oSubInnerFields->Name</td><td>$oSubInnerFields->Label</td></tr>";
                    }
                    $subInnerQstCnt += 1;
                  }

                }else{
                  //traversing all categories the the specific slice
                  if($oSubFields->Elements->Count > 0){
                    echo "<tr class='subQstBorder' id='subQst_$subQstCnt'><td><span class='caretDown'></span>Subquestion:</td><td>$oSubFields->Name</td><td>$oSubFields->Label</td></tr>";

                    foreach ($oSubFields->Elements as $subElemz) {
                      if($subElemz->TypeName == "CategoryList"){
                          foreach ($subElemz->Elements as $subSubElemz) {
                            if( $subSubElemz->Label != "" && $subSubElemz->Label != "Codes"){
                              echo "<tr class='subQst_".$subQstCnt."_subCateg' style='display: none;'><td></td><td>$subSubElemz->Name</td><td>$subSubElemz->Label</td></tr>";
                            }
                          }
                      }else{
                        if( $subElemz->Label != "" && $subElemz->Label != "Codes"){
                          echo "<tr class='subQst_".$subQstCnt."_subCateg' style='display: none;'><td></td><td>$subElemz->Name</td><td>$subElemz->Label</td></tr>";
                        }
                      }
                    }
                  }else{
                    echo "<tr class='subQstBorder' id='subQst_$subQstCnt'><td>Subquestion:</td><td>$oSubFields->Name</td><td>$oSubFields->Label</td></tr>";
                  }
                }
                  
               // }
                $subQstCnt += 1;
              }
            //----------------------------------------------------------------------------------------------------------------------------------------------------------------
            //Checking if the fields is CATEGORICAL
          }else if($oField->ObjectTypeValue == 0 && $oField->properties['noTR'] != TRUE){

              if($oField->DataType == 3){
                foreach ($oField->Elements as $categElemz){
                  if($categElemz->TypeName == "CategoryList"){
                    foreach($categElemz->Elements as $SubCategElemz){
                      if( $SubCategElemz->Label != "" && $SubCategElemz->Label != "Codes"){
                        echo "<tr class='qst_".$qstCnt."_subCateg'><td></td><td>$SubCategElemz->Name</td><td>$SubCategElemz->Label</td></tr>";
                        $cnt +=1;
                      }
                    }
                  }else{
                    if( $categElemz->Label != "Codes"){
                      echo "<tr class='qst_".$qstCnt."_subCateg'><td></td><td>$categElemz->Name</td><td>$categElemz->Label</td></tr>";
                      $cnt +=1;
                    }               
                  }
                }
              //categorical || text || date || double || boolean
              }
              /*else if($oField->Datatype == 1 || $oField->Datatype == 2 || $oField->Datatype == 5 || $oField->Datatype == 6 || $oField->Datatype == 7){

              }*/

            }
          }
        }
      }

    ?>
  </tbody>
</table>
</body>
</html>

