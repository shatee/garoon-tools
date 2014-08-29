<?php
static $CbgrnAPIErrorMessage = Array(
    "jp" => Array(
        /* i tried mb string error message, but test is too bad. now DEBUGGING */
    ),
    "en" => Array(
         "001" => "wrong or invalid function name"
        ,"002" => "zero length argument appear"
        ,"003" => "wrong argument type"
        ,"004" => "id cannot set zero or minus value"
        ,"005" => "cannot set this class except stdClass"
        ,"006" => "different class name appear"
        ,"007" => "cannot open file"
    )
);

class CbgrnSoapFault extends SoapFault
{
    public function __construct($error_no, $function_name){
        global $CbgrnAPIErrorMessage;

        $locale = CBGRNAPI_ERROR_LANG;
        switch ($locale) {
        case "jp":
            $locale = "en";
            break;
        default:
            $locale = "en";
        }
        $error_no = sprintf("%03d", $error_no);
        $err_code = "CbgrnAPI" . $error_no;
        $err_msg  = $function_name . " : " . $CbgrnAPIErrorMessage[$locale][$error_no];
        parent::__construct($err_code, $err_msg);
    }
}
?>
