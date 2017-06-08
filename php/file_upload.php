<?php
session_start();
$target_dir = "../../uploads/";
$mzid_file = $target_dir . basename($_FILES["mzidToUpload"]["name"]);
$mzml_file = $target_dir . basename($_FILES["mzmlToUpload"]["name"]);

$files = array("mzidToUpload" => $mzid_file, "mzmlToUpload" => $mzml_file);
$uploadStatus = "";

foreach ($files as $key => $value) {
    if (move_uploaded_file($_FILES[$key]["tmp_name"], $value)) {
        $uploadStatus .= "The file ". basename( $_FILES[$key]["name"]). " has been uploaded.<br/>";
    } else {
        $uploadStatus .= "Sorry, there was an error uploading the file ".basename( $_FILES[$key]["name"]).".<br/>";
    }
}

$command = escapeshellcmd('../py/parser.py '.$mzid_file.' '.$mzml_file. ' '.session_id());
echo $command;
echo "<br/>";
$output = shell_exec($command);
echo $output;



// $uploadOk = 1;
// $fileType = pathinfo($target_file,PATHINFO_EXTENSION);
// // // Check if file already exists
// // if (file_exists($target_file)) {
// //     echo "Sorry, file already exists.";
// //     $uploadOk = 0;
// // }
// // // Check file size
// // if ($_FILES["fileToUpload"]["size"] > 1000000000) {
// //     echo "Sorry, your file is too large.";
// //     $uploadOk = 0;
// // }
// // // Allow certain file formats
// // if($fileType != "mzid" && $fileType != "mzml" ) {
// //     echo "Sorry, only MGF files are allowed.";
// //     $uploadOk = 0;
// // }
// // Check if $uploadOk is set to 0 by an error
// if ($uploadOk == 0) {
//     echo "Sorry, your file was not uploaded.";
// // if everything is ok, try to upload file
// } else {
//     if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
//         echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
//         $command = escapeshellcmd('../py/test.py -c .$target_file');
//         $output = shell_exec($command);
//         echo $output;
//     } else {
//         echo "Sorry, there was an error uploading your file.";
//     }
// }
?>


<!DOCTYPE html>
<html>
    <head>
        <title>xiSPEC</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="description" content="common platform for downstream analysis of CLMS data" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">



    </head>
    <body>
        <div id="main">
           <?php echo $uploadStatus; ?> 

         </div>
    </body>
</html>