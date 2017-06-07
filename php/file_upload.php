<?php
$target_dir = "../../uploads/";
$mzid_file = $target_dir . basename($_FILES["mzidToUpload"]["name"]);
$mzml_file = $target_dir . basename($_FILES["mzmlToUpload"]["name"]);

$files = array("mzidToUpload" => $mzid_file, "mzmlToUpload" => $mzml_file);


foreach ($files as $key => $value) {
    if (move_uploaded_file($_FILES[$key]["tmp_name"], $value)) {
        echo "The file ". basename( $_FILES[$key]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

$command = escapeshellcmd('../py/test.py '.$mzid_file.' '.$mzml_file);
echo $command;
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