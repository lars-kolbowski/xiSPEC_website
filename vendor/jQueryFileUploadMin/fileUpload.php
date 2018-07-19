<?php
session_start();
/*
 * jQuery File Upload Plugin PHP Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

$target_dir = "../../../uploads/".session_id()."/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$options = array('upload_dir' => $target_dir, 'upload_url' => $target_dir);

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');
try {
    //if ($_SESSION["canAddNewSearch"]) {
        $upload_handler = new UploadHandler ($options);

        // if (property_exists ($upload_handler, "response") && array_key_exists ("files", $upload_handler->response)) {
        //     $resp = $upload_handler->response;
        //     $upFiles = $resp["files"];
            /*
            foreach ($upFiles as $upFile) {
                if (array_key_exists ("url", $upFile)) {

                    error_log (print_r($upFile, TRUE));
                    //$fperms = fileperms ($upFile->url);
                    //$octal = substr(sprintf('%o', $fperms), -4);
                    //error_log (print_r($octal, TRUE));
                    //$stat = stat ($upFile->url);

                    //$chsucc = chmod ($upFile->url, 0766);

                    //error_log ("chmodok to 0766 ".$chsucc);
                    //$fperms = fileperms ($upFile->url);
                    //$octal = substr(sprintf('%o', $fperms), -4);
                    //error_log (print_r($octal, TRUE));
                    //$stat = stat ($upFile->url);
                    //error_log (print_r($stat, TRUE));
                }
            }
            */
        //}
    //}
}
catch (Exception $e) {
    error_log (print_r($e, TRUE));
}

?>
