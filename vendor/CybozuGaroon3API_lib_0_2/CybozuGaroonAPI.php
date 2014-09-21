<?php
//====================================================================
// File:            CybozuGaroonAPI.php
// Description:     Cybozu Garoon (3) API access class library
// histroy:
//                  Version 0.2     2013-06-05 method chages callAPI -> wrapper method
//                  Version 0.1     2013-06-04
// Copyright (c)    Yujiro Shiwaku [Hokoku Kogyo Co.,Ltd.]
//                  Yoshinao Ohoshi
//====================================================================
    require_once("CbgrnSoapAPI_inc.php");
    require_once("CbgrnSoapAPI_funclist_" . CBGRNAPI_VERSION . ".php");
    require_once("CbgrnSoapFault.php");
    require_once("CbgrnSoapAPI_typedef.php");
    // require_once("CbgrnUTClib.php");

if (!defined("__CBGRNAPI__")) {
  define("__CBGRNAPI__", 1);



    // check php has mb_string
    if (function_exists("mb_internal_encoding")) {
        // mb_internal_encoding("utf-8");      // (Cybozu Garoon) Soap API only works in UTF-8
        // if convert encoding to utf-8 this flag is ON.
        $script_encoding = ini_get("mbstring.script_encoding"); // < PHP 5.4.0
        if (strlen(trim($script_encoding)) == 0) {
            $script_encoding = ini_get("zend.script_encoding"); // PHP5.4.0 >=
        }
	    if ($script_encoding == "") {
		    $script_encoding = "utf-8";
	    }
        if (strtolower($script_encoding) != "utf-8") {
            define("__PHP_HAS_MBSTRING__" , 1);
            define("__PHP_SCRIPT_ENCODING__", $script_encoding);
        }
    }

    /*
    function encodeString2UTF8($text) {
        // if you have mbstring, convert to UTF8 from script encoding chars
        if (__PHP_HAS_MBSTRING__) {
            return mb_convert_encoding($text, "UTF-8", __PHP_SCRIPT_ENCODING__);
        } else {
            return $text;
        }
    }
    function decodeStringFromUTF8($text) {
        if (__PHP_HAS_MBSTRING__) {
            return mb_convert_encoding($text, __PHP_SCRIPT_ENCODING__, "UTF-8");
        } else {
            return $text;
        }
    }
    */

    class CybozuGaroonAPI extends SoapClient {
        protected $auth_user;
        protected $auth_passwd;
        protected $headers;
        protected $is_login;
        protected $is_convert_string;
        // protected $session_str;

        // Constructor
        function __construct($username = false, $password = false) {
            // set default login user/pass
            if (($username !== false) &&
                ($password !== false)) {
                $this->auth_user = $username;
                $this->auth_passwd = $password;
            } else {
                $this->auth_user = CBGRNAPI_LOGIN_USER;
                $this->auth_passwd = CBGRNAPI_LOGIN_PASSWORD;
            }
            // make WSDL URL
            if (CBGRNAPI_SERVER_IS_UNIX) {
                // type UNIX (linux)
                $wsdl = CBGRNAPI_PROTOCOL . "://" . CBGRNAPI_SERVER_NAME . "/cgi-bin/" . CBGRNAPI_IDENTIFIER . "/grn.cgi?WSDL";
            } else {
                // type Windows
                if (CBGRNAPI_SERVER_IS_APACHE) {
                    $wsdl = CBGRNAPI_PROTOCOL . "://" . CBGRNAPI_SERVER_NAME . "/cgi-bin/" . CBGRNAPI_IDENTIFIER . "/grn.exe?WSDL";
                } else {
                    $wsdl = CBGRNAPI_PROTOCOL . "://" . CBGRNAPI_SERVER_NAME . "/scripts/" . CBGRNAPI_IDENTIFIER . "/grn.exe?WSDL";
                }
            }
            // set SOAP options
            $soap_options = Array();
            $soap_options["soap_version"]   = SOAP_1_2; // Cybozu API needs soap 1.2
            if (SOAP_TRACE) {
                $soap_options["trace"]      = true;
            }
            if (CBGRNAPI_USE_PROXY) {
                $soap_options["proxy_host"] = CBGRNAPI_PROXY_NAME;
                $soap_options["proxy_port"] = CBGRNAPI_PROXY_PORT;
                $soap_options["proxy_login"] = CBGRNAPI_PROXY_USER;
                $soap_options["proxy_password"] = CBGRNAPI_PROXY_PASS;
            }
            $soap_options["cache_wsdl"]     = SOAP_CACHE_WSDL;

            $this->is_login = false;
            $this->is_convert_string = false;
            
            parent::__construct($wsdl, $soap_options);
        }

        public function __doRequest($request, $location, $action, $version, $one_way = 0)
        {
            // Garoon SOAP API need not ns2 namespace (bug?)., so ommit this modifier
            // $request = preg_replace('/xmlns:ns2/', 'xmlns:myns', $request);
            //$request = encodeString2UTF8($request);
            $request = preg_replace('/<ns2:/', '<', $request);
            $request = preg_replace('/<\/ns2:/', '</', $request);
            return parent::__doRequest($request, $location, $action, $version, $one_way);
        }

        protected function encodeString2UTF8($text) {
            // if you have mbstring, convert to UTF8 from script encoding chars
            if (__PHP_HAS_MBSTRING__) {
                return mb_convert_encoding($text, "UTF-8", __PHP_SCRIPT_ENCODING__);
            } else {
                return $text;
            }
        }
        protected function decodeStringFromUTF8($text) {
            if (__PHP_HAS_MBSTRING__) {
                return mb_convert_encoding($text, __PHP_SCRIPT_ENCODING__, "UTF-8");
            } else {
                return $text;
            }
        }

        protected function decodeString_sub(&$arg) {    // rewrite $arg's item(s) by recursive call
            if (is_array($arg)) {
                foreach ($arg as $key => $item) {
                    $this->decodeString_sub($arg[$key]);
                }
            } else if (is_object($arg)){
                $object_data = get_object_vars($arg);
                foreach ($object_data as $key => $item) {
                    $this->decodeString_sub($arg->$key);
                }
            } else if (is_string($arg)) {
                $arg = $this->decodeStringFromUTF8($arg);
            } else {
                ;   // non string ... no convert
            }
        }

        protected function decodeString($arg) {
            if ($this->is_convert_string || !defined("__PHP_HAS_MBSTRING__")) {
                ;   // already converted
            } else {
                $this->decodeString_sub($arg);
                $this->is_convert_string = true;
            }
            return $arg;
        }

        protected function encodeString(&$arg, $exclude_key = NULL) { // encode some object (array,class,string)'s member to UTF8 string by recursive call
            if (defined("__PHP_HAS_MBSTRING__")) {
                if (is_array($arg)) {
                    foreach ($arg as $key => $item) {
                        if (($exclude_key !== NULL) && ($key === $exclude_key)) {
                            ;
                        } else {
                            $this->encodeString($arg[$key], $exclude_key);
                        }
                    }
                } else if (is_object($arg)){
                    $object_data = get_object_vars($arg);
                    foreach ($object_data as $key => $item) {
                        if (($exclude_key !== NULL) && ($key === $exclude_key)) {
                            ;
                        } else {
                            $this->encodeString($arg->$key, $exclude_key);
                        }
                    }
                } else if (is_string($arg)) {
                    $arg = $this->encodeString2UTF8($arg);
                } else {
                    ;   // non string ... no convert
                }
            }
        }

        // destractor -- autologout to cybozu garoon server
        function __destruct() {
            $this->UtilLogout();
        }

        // you can change authentication user/pass
        public function setUser($username, $password) {
            $this->auth_user = $username;
            $this->auth_passwd = $password;
            $this->is_login = false;    // when logged in, but cybozu needs new auth to this user.
            $this->UtilLogin();
            return true;
        }

        protected function _setHeader($action_name) {
            $headers = Array(); // array of SoapHeader -> makes soap header strings
            // soap:Header <Action> area.
            $work_str = <<<CYBOZUGAROONAPI
<Action xmlns="http://schemas.xmlsoap.org/ws/2003/03/addressing">
    $action_name
</Action>
CYBOZUGAROONAPI;
            $headers[] = new SoapHeader("dummyNameSpace", "Action", new SoapVar($work_str, XSD_ANYXML));
            // soap:Header <Security> area.
            $work_str = <<<CYBOZUGAROONAPI
<Security xmlns:wsu="http://schemas.xmlsoap.org/ws/2002/12/secext">
    <UsernameToken>
        <Username>{$this->auth_user}</Username>
        <Password>{$this->auth_passwd}</Password>
    </UsernameToken>
</Security>
CYBOZUGAROONAPI;
            //if ($this->is_login) {
            if (false) {    // why already logged in, but cannot login sometimes, so alltime add header.
                ;   // already get CBSESSID cookie. php-soap automatically send this auth,
                ;   // so no longer needs <Security> Header 
            } else {
                $headers[] = new SoapHeader("dummyNameSpace", "Security", new SoapVar($work_str, XSD_ANYXML));
            }
            // soap:Header <Timestamp> area.
            // created time : this time on yesterday
            // expired time : this thime on tommorow
            $created_date = gmdate(W3C_DATETIME_FORMAT, time() - 24 * 3600);
            $expires_date = gmdate(W3C_DATETIME_FORMAT, time() + 24 * 3600);
            $work_str = <<<CYBOZUGAROONAPI
<Timestamp>
    <Created>$created_date</Created>
    <Expires>$expires_date</Expires>
</Timestamp>
CYBOZUGAROONAPI;
            $headers[] = new SoapHeader("dummyNameSpace", "Timestamp", new SoapVar($work_str, XSD_ANYXML));
            // soap:Header <Locale> area.
            $tmp = CBGRNAPI_ERROR_LANG;
            $work_str = <<<CYBOZUGAROONAPI
<Locale>
    $tmp;
</Locale>
CYBOZUGAROONAPI;
            $headers[] = new SoapHeader("dummyNameSpace", "Locale", new SoapVar($work_str, XSD_ANYXML));
            // set Soap Header
            $this->__setSoapHeaders(NULL);
            $this->__setSoapHeaders($headers);
        }


        protected function CheckAndSetHeader($function_name) {
            global $CbgrnAPI_FunctionNames;
            if (!in_array($function_name, $CbgrnAPI_FunctionNames)) {
                throw new CbgrnSoapFault("001", $function_name);
            }
            // OK, function_name is legal.
            $this->_setHeader($function_name);
            return true;
        }

        // standard argumets pattern expanding function.
        protected function getRegularArgs($arg, $function_name, $object_class_name = false) {
            // $arg : method's argument or arguments (array), string(s) or class object(s)
            // $function_name : called method name for display error message
            // $object_class_name : if you wish checking arg's class name, set this
            $ret_val = Array();
            if (is_array($arg)) {
                foreach ($arg as $tmp) {
                    if (is_object($tmp)) {
                        if ($object_class_name === false) { // no check
                            if (method_exists($tmp, "getObjectVars")) {
                                $ret_val[] = $tmp->getObjectVars();
                            } else {
                                $ret_val[] = get_object_vars($tmp);
                            }
                        } else {
                            if (get_class($tmp) == $object_class_name) {
                                if (method_exists($tmp, "getObjectVars")) {
                                    $ret_val[] = $tmp->getObjectVars();
                                } else {
                                    $ret_val[] = get_object_vars($tmp);
                                }
                            } else {
                                throw new CbgrnSoapFault("002", $function_name);
                            }
                        }
                    } else {
                        if (strlen($tmp) == 0) {
                            throw new CbgrnSoapFault("002", $function_name);
                        }
                        $ret_val[] = strval($tmp);
                    }
                }
            } else {
                if (is_object($arg)) {
                    if ($object_class_name === false) { // no check
                        if (method_exists($arg, "getObjectVars")) {
                            $ret_val[] = $arg->getObjectVars();
                        } else {
                            $ret_val[] = get_object_vars($arg);
                        }
                    } else {
                        if (get_class($arg) == $object_class_name) {
                            if (method_exists($arg, "getObjectVars")) {
                                $ret_val[] = $arg->getObjectVars();
                            } else {
                                $ret_val[] = get_object_vars($arg);
                            }
                        } else {
                            throw new CbgrnSoapFault("006", $function_name);
                        }
                    }
                } else {
                    if ($arg !== NULL) {
                        $ret_val[] = $arg;
                    }
                }
            }
            return $ret_val;
        }

        // all Application method call this end routine
        protected function methodClose() {
            $this->is_login = true; // if this function called, method call is succeeded, and server logged in.
            return true;
        }

        protected function RetvalConvertArray($arg) {
            $this->decodeString($arg);
            if (is_array($arg)) {
                return $arg;
            } else {
                if ($arg === NULL) {
                    return NULL;
                } else {
                    $ret_val = Array();
                    $ret_val[] = $arg;
                    return $ret_val;
                }
            }
        }


        //-------------------------------------------------------------
        // Base: BaseGetUsersById
        //-------------------------------------------------------------
        public function BaseGetUsersById($arg) {
            // $arg : IDType(string := integer) or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::BaseGetUsersById($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->user);   // returns array of stdClass
        }

        //-------------------------------------------------------------
        // Base: BaseGetUsersByLoginName
        //-------------------------------------------------------------
        public function BaseGetUsersByLoginName($arg) {
            // $arg : string or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::BaseGetUsersByLoginName($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->user);   // returns array of stdClass
        }

        //-------------------------------------------------------------
        // Base: BaseGetUserVersions
        //-------------------------------------------------------------
        public function BaseGetUserVersions($arg) {
            // $arg : CbgrnItemVersionType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnItemVersionType");
            $this->encodeString($reg_args);
            $results = parent::BaseGetUserVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->user_item); // returns array of stdClass
        }

        //-------------------------------------------------------------
        // Base: BaseGetApplicationStatus
        //-------------------------------------------------------------
        public function BaseGetApplicationStatus() {
            $this->CheckAndSetHeader(__FUNCTION__);

            $this->encodeString($reg_args);
            $results = parent::BaseGetApplicationStatus();
            $this->methodClose();
            return $this->RetvalConvertArray($results->application); // return array of stdClass
        }

        //-------------------------------------------------------------
        // Base: BaseGetApplicationInformation (API1.3.0-)
        //-------------------------------------------------------------
        public function BaseGetApplicationInformation() {
            $this->CheckAndSetHeader(__FUNCTION__);

            $this->encodeString($reg_args);
            $results = parent::BaseGetApplicationInformation();
            $this->methodClose();
            return $this->RetvalConvertArray($results->application); // returns array of stdClass
        }
        
        //-------------------------------------------------------------
        // Base: BaseGetOrganizationsVersions
        //-------------------------------------------------------------
        public function BaseGetOrganizationVersions($arg) {
            // $arg : CbgrnItemVersionType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnItemVersionType");
            $this->encodeString($reg_args);
            $results = parent::BaseGetOrganizationVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->organization_item); // return array of stdClass
        }
        
        //-------------------------------------------------------------
        // Base: BaseGetOrganizationsById
        //-------------------------------------------------------------
        public function BaseGetOrganizationsById($arg) {
            // $arg : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::BaseGetOrganizationsById($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->organization); // returns array of stdClass
        }

        //-------------------------------------------------------------
        // Base: BaseGetMyGroupVersions
        //-------------------------------------------------------------
        public function BaseGetMyGroupVersions($arg) {
            // $arg : CbgrnItemVersionType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnItemVersionType");
            $this->encodeString($reg_args);
            $results = parent::BaseGetMyGroupVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->my_group_item); // returns array of stdClass
        }

        //-------------------------------------------------------------
        // Base: BaseGetMyGroupsById
        //-------------------------------------------------------------
        public function BaseGetMyGroupsById($arg) {
            // $arg : IDType (string := integer) or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::BaseGetMyGroupsById($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->my_group); // returns array of StdClass
        }
        
        //-------------------------------------------------------------
        // Base: BaseGetFrequentUsers
        //-------------------------------------------------------------
        public function BaseGetFrequentUsers() {    // no argument
            $this->CheckAndSetHeader(__FUNCTION__);

            $this->encodeString($reg_args);
            $results = parent::BaseGetFrequentUsers();
            $this->methodClose();
            return $this->RetvalConvertArray($results->user_id);    // returns array of stdClass
        }
        
        //-------------------------------------------------------------
        // Base: BaseGetFrequentOrganizations
        //-------------------------------------------------------------
        public function BaseGetFrequentOrganizations() { // no argument
            $this->CheckAndSetHeader(__FUNCTION__);

            $this->encodeString($reg_args);
            $results = parent::BaseGetFrequentOrganizations();
            $this->methodClose();
            return $this->RetvalConvertArray($results->organization_id); // returns array of stdClass
        }
        //-------------------------------------------------------------
        // Base: BaseGetCalendarEvents
        //-------------------------------------------------------------
        public function BaseGetCalendarEvents() {   // no argument
            $this->CheckAndSetHeader(__FUNCTION__);

            $this->encodeString($reg_args);
            $results = parent::BaseGetCalendarEvents();
            $this->methodClose();
            return $this->RetvalConvertArray($results->calendar_event);     // returns array of stdClass
        }
        
        //-------------------------------------------------------------
        // Base: BaseFileDownload
        //-------------------------------------------------------------
        public function BaseFileDownload($file_id) {
            // $file_id : IDType (string := integer) and no array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["file_id"]    = $file_id;
            $this->encodeString($reg_args);
            $results = parent::BaseFileDownload($reg_args);
            $this->methodClose();
            return $results->file->content; // returns file body (already base64decode)
        }
        
        //-------------------------------------------------------------
        // Base: BaseGetRegionsList
        //-------------------------------------------------------------
        public function BaseGetRegionsList() {  // no arguments
            $this->CheckAndSetHeader(__FUNCTION__);

            $this->encodeString($reg_args);
            $results = parent::BaseGetRegionsList();
            $this->methodClose();
            return $this->RetvalConvertArray($results->region); // returns array of stdClass
        }
        
        //-------------------------------------------------------------
        // Base: BaseGetTimezoneVersion
        //-------------------------------------------------------------
        public function BaseGetTimezoneVersion() {  // no arguments
            $this->CheckAndSetHeader(__FUNCTION__);

            $this->encodeString($reg_args);
            $results = parent::BaseGetTimezoneVersion();
            $this->methodClose();
            return $this->decodeString($results->timezone_version);  // returns string
        }
        
        //-------------------------------------------------------------
        // Base: BaseManagerApplication
        //-------------------------------------------------------------
        public function BaseManagerApplication($arg) {
            // $arg : CbgrnBaseManagerApplicationType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnBaseManagerApplicationType");
            $this->encodeString($reg_args);
            $results = parent::BaseManagerApplication($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->application); // returns array of stdClass
        }

        //-------------------------------------------------------------
        // Util: UtilLogin
        //-------------------------------------------------------------
        public function UtilLogin($login_name = false, $password = false) {
            // $login_name : string
            // $password   : string
            $this->CheckAndSetHeader(__FUNCTION__);
            if (($login_name === false) && ($password === false)) {
                $reg_args["login_name"] = $this->auth_user;
                $reg_args["password"]   = $this->auth_passwd;
            } else {
                $reg_args["login_name"] = $login_name;
                $reg_args["password"]   = $password;
            }
            $this->encodeString($reg_args);
            $results = parent::UtilLogin($reg_args);
            $this->methodClose();
            $this->is_login = true;
            return $this->decodeString($results);    // returns stdClass
        }

        //-------------------------------------------------------------
        // Util: UtilLogout
        //-------------------------------------------------------------
        public function UtilLogout() {  // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::UtilLogout();
            $this->methodClose();
            $this->is_login = false;
            return $this->decodeString($results);    // returns stdClass
        }

        //-------------------------------------------------------------
        // Util: UtilGetRequestToken
        //-------------------------------------------------------------
        public function UtilGetRequestToken() {  // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::UtilGetRequestToken();
            $this->methodClose();
            return $this->decodeString($results->request_token); // returns string
        }

        //-------------------------------------------------------------
        // Util: UtilGetLoginUserId
        //-------------------------------------------------------------
        public function UtilGetLoginUserId() {  // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::UtilGetLoginUserId();
            $this->methodClose();
            return $this->decodeString($results->user_id);   // returns string
        }

        
        //-------------------------------------------------------------
        // Admin: AdminAddUserAccount
        //-------------------------------------------------------------
        public function AdminAddUserAccount($login_name, $display_name, $password_raw = false, $user_info = false) {
            // $login_name : string
            // $display_name : string
            // $password_raw : string (optional, default is cybozu)
            // $user_info : CbgrnUserInfoType class (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            // $reg_args = $this->getRegularArgs($arg, __FUNCTION__, xxx);
            if (strlen($login_name) == 0) {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            }
            if (strlen($display_name) == 0) {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            }
            $reg_args = Array();
            $reg_args["login_name"] = strval($login_name);
            $reg_args["display_name"] = strval($display_name);
            if ($password_raw === false) {
                ;
            } else {
                if (strlen($password_raw) > 0) {
                    $reg_args["password_raw"] = strval($password_raw);
                } else {
                    throw new CbgrnSoapFault("002", __FUNCTION__);
                }
            }
            if ($user_info === false) {
                ;
            } else {
                if (is_object($user_info) && (get_class($user_info) == "CbgrnUserInfoType")) {
                    $reg_args["user_info"] = $user_info->getObjectVars();
                } else {
                    throw new CbgrnSoapFault("003", __FUNCTION__);
                }
            }
            $this->encodeString($reg_args);
            $results = parent::AdminAddUserAccount($reg_args);
            $this->methodClose();
            return $this->decodeString($results->userAccount);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminRemoveUsersByIds
        //-------------------------------------------------------------
        public function AdminRemoveUsersByIds($userId) {
            // $userId : IDType(string := integer) not Array (only one user can remove)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["userId"] = $userId;
            $this->encodeString($reg_args);
            $results = parent::AdminRemoveUsersByIds($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminModifyUserAccount -- DEBUG --
        //-------------------------------------------------------------
        public function AdminModifyUserAccount($userId, $login_name = false, $display_name = false, $password_raw = false, $user_info = false) {
            // $userId : IDType (string := integer)
            // $login_name : string (if no changes, set false)
            // $display_name : string (if no changes, set false)
            // $password_raw : string (if no changes, set false)
            // $user_info : CbgrnUserInfoType class (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($userId < 1) {
                throw new CbgrnSoapFault("004", __FUNCTION__);
            }
            $reg_args["userId"] = $userId;
            if ($login_name !== false) {
                if (strlen($login_name) > 0) {
                    $reg_args["login_name"] = $login_name;
                } else {
                    throw new CbgrnSoapFault("002", __FUNCTION__);
                }
            }
            if ($display_name !== false) {
                if (strlen($display_name) > 0) {
                    $reg_args["display_name"] = $display_name;
                } else {
                    throw new CbgrnSoapFault("002", __FUNCTION__);
                }
            }
            if ($password_raw !== false) {
                if (strlen($password_raw) > 0) {
                    $reg_args["password_raw"] = $password_raw;
                } else {
                    throw new CbgrnSoapFault("002", __FUNCTION__);
                }
            }
            if ($user_info !== false) {
                if (is_object($user_info) && (get_class($user_info) == "CbgrnUserInfoType")) {
                    $reg_args["user_info"] = $user_info->getObjectVars();
                } else {
                    throw new CbgrnSoapFault("003", __FUNCTION__);
                }
            }
            $this->encodeString($reg_args);
            $results = parent::AdminModifyUserAccount($reg_args);
            $this->methodClose();
            return $this->decodeString($results->userAccount);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminGetUserIds
        //-------------------------------------------------------------
        public function AdminGetUserIds($offset = false, $limit = false) {
            // $offset : integer (optional)
            // limit   : integer (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($offset !== false) {
                $reg_args["offset"] = intval($offset);
            }
            if ($limit !== false) {
                $reg_args["limit"]  = intval($limit);
            }
            $this->encodeString($reg_args);
            $results = parent::AdminGetUserIds($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->userId); // returns array of string
        }
        
        //-------------------------------------------------------------
        // Admin: AdminGetUserDetailByIds
        //-------------------------------------------------------------
        public function AdminGetUserDetailByIds($arg) {
            // $arg : IDType or this array (single or multiple request)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AdminGetUserDetailByIds($reg_args);
            $this->methodClose();
            return $this->decodeString($results->userDetail);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminCountUsers
        //-------------------------------------------------------------
        public function AdminCountUsers() { // no argument
            $this->CheckAndSetHeader(__FUNCTION__);

            $this->encodeString($reg_args);
            $results = parent::AdminCountUsers();
            $this->methodClose();
            return $this->decodeString($results->number_users);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminCountOrgs
        //-------------------------------------------------------------
        public function AdminCountOrgs() { // no argument
            $this->CheckAndSetHeader(__FUNCTION__);

            $this->encodeString($reg_args);
            $results = parent::AdminCountOrgs($reg_args);
            $this->methodClose();
            return $this->decodeString($results->number_orgs);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminGetOrgIds
        //-------------------------------------------------------------
        public function AdminGetOrgIds($offset = false, $limit = false) {
            // $offset : integer (optional)
            // $limit  : integer (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($offset !== false) {
                $reg_args["offset"] = intval($offset);
            }
            if ($limit !== false) {
                $reg_args["limit"]  = intval($limit);
            }
            $this->encodeString($reg_args);
            $results = parent::AdminGetOrgIds($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->orgId);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminGetOrgDetailByIds -- WSDL bug occured returns OrgDetail (not orgDetail) -- 
        //-------------------------------------------------------------
        public function AdminGetOrgDetailByIds($arg) {
            // $arg : IDType or this array (single or multiple request)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["orgId"] = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AdminGetOrgDetailByIds($reg_args);
            $this->methodClose();
            return $this->decodeString($results->orgDetail);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminCountChildOrgs
        //-------------------------------------------------------------
        public function AdminCountChildOrgs($orgId) {
            // $orgId : IDType (parent organization ID)
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($orgId > 0) {
                ;
            } else {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            }
            $results = parent::AdminCountChildOrgs(Array("parent_orgId" => intval($orgId)));
            $this->methodClose();
            return $this->decodeString($results->number_child_orgs);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminGetChildOrgs
        //-------------------------------------------------------------
        public function AdminGetChildOrgs($orgId, $offset = false, $limit = false) {
            // $orgId : IDType (parent organization ID)
            // $offset : integer (optional, if false is set, no offset)
            // $limit  : integer (optional, if false is set, no limits)
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($orgId > 0) {
                ;
            } else {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            }
            $reg_args["parent_orgId"] = $orgId;
            if ($offset !== false) {
                $reg_args["offset"]     = $offset;
            }
            if ($limit !== false) {
                $reg_args["limit"]      = $limit;
            }
            $this->encodeString($reg_args);
            $results = parent::AdminGetChildOrgs($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->orgId);  // returns IDType array
        }
        
        //-------------------------------------------------------------
        // Admin: AdminCountUsersInOrg
        //-------------------------------------------------------------
        public function AdminCountUsersInOrg($orgId) {
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($orgId > 0) {
                $reg_args = Array("orgId" => $orgId);
            } else {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            }
            $this->encodeString($reg_args);
            $results = parent::AdminCountUsersInOrg($reg_args);
            $this->methodClose();
            return $this->decodeString($results->number_users);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminGetUserIdsInOrg
        //-------------------------------------------------------------
        public function AdminGetUserIdsInOrg($orgId, $offset = false, $limit = false) {
            // $orgId : IDType (organization ID)
            // $offset : integer (optional)
            // $limit  : integer (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($orgId > 0) {
                $reg_args["orgId"]  = $orgId;
            } else {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            }
            if ($offset !== false) {
                $reg_args["offset"] = $offset;
            }
            if ($limit !== false) {
                $reg_args["limit"]  = $limit;
            }
            $this->encodeString($reg_args);
            $results = parent::AdminGetUserIdsInOrg($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->userId); // returns IDType array
        }
        
        //-------------------------------------------------------------
        // Admin: AdminCountNoGroupUsers
        //-------------------------------------------------------------
        public function AdminCountNoGroupUsers() {
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::AdminCountNoGroupUsers();
            $this->methodClose();
            return $this->decodeString($results->number_users);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminGetNoGroupUserIds
        //-------------------------------------------------------------
        public function AdminGetNoGroupUserIds($offset = false, $limit = false) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($offset !== false) {
                $reg_args["offset"] = $offset;
            }
            if ($limit !== false) {
                $reg_args["limit"]  = $limit;
            }
            $this->encodeString($reg_args);
            $results = parent::AdminGetNoGroupUserIds($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->userId); // returns array of IDType
        }
        
        //-------------------------------------------------------------
        // Admin: AdminCountOrgsOfUser
        //-------------------------------------------------------------
        public function AdminCountOrgsOfUser($userId) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($userId > 0) {
                $reg_args["userId"] = $userId;
            } else {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            }
            $this->encodeString($reg_args);
            $results = parent::AdminCountOrgsOfUser($reg_args);
            $this->methodClose();
            return $this->decodeString($results->number_orgs);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminGetOrgIdsOfUser
        //-------------------------------------------------------------
        public function AdminGetOrgIdsOfUser($userId) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($userId > 0) {
                $reg_args["userId"] = $userId;
            } else {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            }
            $this->encodeString($reg_args);
            $results = parent::AdminGetOrgIdsOfUser($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->orgId); // returns array of OrgID
        }
        
        //-------------------------------------------------------------
        // Admin: AdminGetParentOrgId
        //-------------------------------------------------------------
        public function AdminGetParentOrgId($orgId) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($orgId > 0) {
                $reg_args["child_orgId"] = $orgId;
            } else {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            }
            $this->encodeString($reg_args);
            $results = parent::AdminGetParentOrgId($reg_args);
            $this->methodClose();
            return $this->decodeString($results->parent_orgId);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminGetUserIdByLoginName
        //-------------------------------------------------------------
        public function AdminGetUserIdByLoginName($login_name) {
            $this->CheckAndSetHeader(__FUNCTION__);
            if (strlen($login_name) == 0) {
                throw new CbgrnSoapFault("00", __FUNCTION__);
            } else {
                $reg_args = Array();
                $reg_args["login_name"] = $login_name;
            }
            $this->encodeString($reg_args);
            $results = parent::AdminGetUserIdByLoginName($reg_args);
            $this->methodClose();
            return $this->decodeString($results->userId);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminGetOrgIdByOrgCode
        //-------------------------------------------------------------
        public function AdminGetOrgIdByOrgCode($org_code) {
            $this->CheckAndSetHeader(__FUNCTION__);
            if (strlen($org_code) == 0) {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            } else {
                $reg_args = Array();
                $reg_args["org_code"] = $org_code;
            }
            $this->encodeString($reg_args);
            $results = parent::AdminGetOrgIdByOrgCode($reg_args);
            $this->methodClose();
            return $this->decodeString($results->orgId);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminAddOrg
        //-------------------------------------------------------------
        public function AdminAddOrg($org_code, $org_name, $parent_orgId = false) {
            // $org_code : string
            // $org_name : string
            // $parent_orgId : IDType (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if (strlen($org_code) == 0) {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            } else {
                $reg_args["org_code"] = $org_code;
            }
            if (strlen($org_name) == 0) {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            } else {
                $reg_args["org_name"] = $org_name;
            }
            if ($parent_orgId === false) {
                ;
            } else {
                $reg_args["parent_orgId"]   = $parent_orgId;
            }
            $this->encodeString($reg_args);
            $results = parent::AdminAddOrg($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminRemoveOrgsByIds
        //-------------------------------------------------------------
        public function AdminRemoveOrgsByIds($orgId) {
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($orgId > 0) {
                $reg_args = Array();
                $reg_args["orgId"]  = $orgId;
            } else {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            }
            $this->encodeString($reg_args);
            $results = parent::AdminRemoveOrgsByIds($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminAddUsersToOrg
        //-------------------------------------------------------------
        public function AdminAddUsersToOrg($orgId, $userIds) {
            // $orgId : IDType (only one ID)
            // $userIds : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($orgId == 0) {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            } else {
                $reg_args["orgId"]  = $orgId;
            }
            $reg_args["userId"] = $this->getRegularArgs($userIds, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AdminAddUsersToOrg($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminSetOrgsOfUser
        //-------------------------------------------------------------
        public function AdminSetOrgsOfUser($userId, $orgIds) {
            // $userId : IDtype (only one ID)
            // $orgIds : IDtype or array
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($userId == 0) {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            } else {
                $reg_args = Array();
                $reg_args["userId"] = $userId;
            }
            $reg_args["orgId"]  = $this->getRegularArgs($orgIds, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AdminSetOrgsOfUser($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminAddChildrenOfOrg
        //-------------------------------------------------------------
        public function AdminAddChildrenOfOrg($parent_orgId, $child_orgIds) {
            // $parent_orgId : IDType (only one ID)
            // $child_orgIds : IDType or this array (single or multiple)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($parent_orgId == 0) {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            } else {
                $reg_args["parent_orgId"] = intval($parent_orgId);
            }
            $reg_args["child_orgId"] = $this->getRegularArgs($child_orgIds, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AdminAddChildrenOfOrg($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }
        
        //-------------------------------------------------------------
        // Admin: AdminRemoveUsersFromOrg
        //-------------------------------------------------------------
        public function AdminRemoveUsersFromOrg($orgId, $userIds) {
            // $orgId : IDType (only one ID)
            // $userIds : IDType or this array (single or multiple request)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($orgId == 0) {
                throw new CbgrnSoapFault("002", __FUNCTION__);
            } else {
                $reg_args["orgId"]  = intval($orgId);
            }
            $reg_args["userId"] = $this->getRegularArgs($userIds, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AdminRemoveUsersFromOrg($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleGetEventVersions
        //-------------------------------------------------------------
        public function ScheduleGetEventVersions($event_item, $start, $end = false, $start_for_daily = false, $end_for_daily = false) {
            // $event_item : CbgrnItemVersionType or this array
            // $start, $end : UNIX timestamp (integer)
            // $start_for_daily, $end_for_daily : xsd:date == "YYYY-MM-DD"
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            $reg_args["event_item"] = $this->getRegularArgs($event_item, __FUNCTION__, "CbgrnItemVersionType");
            $reg_args["start"]      = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== false) {
                $reg_args["end"]    = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($start_for_daily !== false) {
                $reg_args["start_for_daily"] = $start_for_daily;
            }
            if ($end_for_daily !== false) {
                $reg_args["end_for_daily"] = $end_for_daily;
            }
            $this->encodeString($reg_args);
            $results = parent::ScheduleGetEventVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->event_item); // returns array of stdClass
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleGetEvents
        //-------------------------------------------------------------
        public function ScheduleGetEvents($start, $end, $start_for_daily = false, $end_for_daily = false) {
            // $start, $end : UNIX timestamp (integer)
            // $start_for_daily, $end_for_daily : xsd:date == "YYYY-MM-DD"
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            $reg_args["start"]      = gmdate(W3C_DATETIME_FORMAT, $start);
            $reg_args["end"]        = gmdate(W3C_DATETIME_FORMAT, $end);
            if ($start_for_daily !== false) {
                $reg_args["start_for_daily"] = $start_for_daily;
            }
            if ($end_for_daily !== false) {
                $reg_args["end_for_daily"] = $end_for_daily;
            }
            $this->encodeString($reg_args);
            $results = parent::ScheduleGetEvents($reg_args);
            $this->methodClose();
            $ret_val = Array();
            if (is_array($results->schedule_event)) {
                foreach ($results->schedule_event as $event) {
                    $ret_val[] = new CbgrnEventType($event);
                }
            } else {
                if ($results->schedule_event !== NULL) {
                    $ret_val[] = new CbgrnEventType($results->schedule_event);
                }
            }
            return $this->decodeString($ret_val);
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleGetEventsById
        //-------------------------------------------------------------
        public function ScheduleGetEventsById($eventId) {
            // $eventId : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = $this->getRegularArgs($eventId, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::ScheduleGetEventsById($reg_args);
            $this->methodClose();
            $ret_val = Array();
            if (is_array($results->schedule_event)) {
                foreach ($results->schedule_event as $event) {
                    $ret_val[] = new CbgrnEventType($event);
                }
            } else {
                if ($results->schedule_event !== NULL) {
                    $ret_val = new CbgrnEventType($results->schedule_event);
                } else {
                    $ret_val = NULL;
                }
            }
            return $this->decodeString($ret_val);
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleGetEventsByTarget
        //-------------------------------------------------------------
        public function ScheduleGetEventsByTarget($target_flag, $id, $start, $end, $start_for_daily = false, $end_for_daily = false) {
            // $target_flag : "user" or "group" or "facility"
            // $id : IDType style data (only 1 id, NOT array)
            // $start, $end : UNIX timestamp
            // $start_for_daily, $end_for_daily : xsd:date == "YYYY-MM-DD"
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            $reg_args["start"]      = gmdate(W3C_DATETIME_FORMAT, $start);
            $reg_args["end"]        = gmdate(W3C_DATETIME_FORMAT, $end);
            if ($start_for_daily !== false) {
                $reg_args["start_for_daily"] = $start_for_daily;
            }
            if (end_for_daily !== false) {
                $reg_args["end_for_daily"]   = $end_for_daily;
            }
            $tmp = Array("id" => $id);
            $reg_args[$target_flag] = $tmp;
            $this->encodeString($reg_args);
            $results = parent::ScheduleGetEventsByTarget($reg_args);
            $this->methodClose();
            $ret_val = Array();
            if (is_array($results->schedule_event)) {
                foreach ($results->schedule_event as $event) {
                    $ret_val[] = new CbgrnEventType($event);
                }
            } else {
                $ret_val[] = new CbgrnEventType($results->schedule_event);
            }
            return $this->decodeString($ret_val);
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleAddEvents
        //-------------------------------------------------------------
        public function ScheduleAddEvents($arg) {
            // $arg : CbgrnEventType class or Array of CbgrnEventType
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["schedule_event"] = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnEventType");
            $this->encodeString($reg_args);
            $results = parent::ScheduleAddEvents($reg_args);
            $this->methodClose();
            $ret_val = Array();
            if (is_array($results->schedule_event)) {
                foreach ($results->schedule_event as $event) {
                    $ret_val[] = new CbgrnEventType($event);
                }
            } else {
                $ret_val = new CbgrnEventType($results->schedule_event);
            }
            return $this->decodeString($ret_val);
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleModifyEvents
        //-------------------------------------------------------------
        public function ScheduleModifyEvents($arg) {
            // $arg : CbgrnEventType class or array of CbgrnEventType
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["schedule_event"] = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnEventType");
            $this->encodeString($reg_args);
            $results = parent::ScheduleModifyEvents($reg_args);
            $this->methodClose();
            $ret_val = Array();
            if (is_array($results->schedule_event)) {
                foreach ($results->schedule_event as $event) {
                    $ret_val[] = new CbgrnEventType($event);
                }
            } else {
                $ret_val[] = new CbgrnEventType($results->schedule_event);
            }
            return $this->decodeString($ret_val);
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleModifyRepeatEvents
        //-------------------------------------------------------------
        public function ScheduleModifyRepeatEvents($arg) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["operation"] = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnScheduleModifyRepeatEventsOperationType");
            $this->encodeString($reg_args);
            $results = parent::ScheduleModifyRepeatEvents($reg_args);
            $this->methodClose();
            $ret_val = Array();
            if (is_array($results->result)) {
                foreach ($results->result as $result) {
                    $ret_val[] = new CbgrnScheduleModifyRepeatEventsResultType($result);
                }
            } else {
                $ret_val[] = new CbgrnScheduleModifyRepeatEventsResultType($results->result);
            }
            return $this->decodeString($ret_val);
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleRemoveEvents
        //-------------------------------------------------------------
        public function ScheduleRemoveEvents($arg) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["event_id"] = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::ScheduleRemoveEvents($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleRemoveEventsFromRepeatEvent
        //-------------------------------------------------------------
        public function ScheduleRemoveEventsFromRepeatEvent($event_id, $type, $date = false) {
            // $event_id :      // base:IdType == String == integer
            // $type :          // ScheduleRepeatModifyType == String(enum) {'this' or 'after' or 'all'}
            // $date :          // xsd:date == "YYYY-MM-DD"
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            $reg_args["operation"]["event_id"]   = $event_id;
            $reg_args["operation"]["type"]       = $type;
            if ($date !== false) {
                $reg_args["operation"]["date"]   = $date;
            }
            $this->encodeString($reg_args);
            $results = parent::ScheduleRemoveEventsFromRepeatEvent($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results);
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleSearchEvents
        //-------------------------------------------------------------
        public function ScheduleSearchEvents($text, $title_search, $customer_search, $memo_search, $follow_search, $all_repeat_events, $start = false, $end = false, $start_for_daily = false, $end_for_daily = false) {
            // $text : "search text" == string
            // $title_search : boolean (true or false)
            // $customer_search : boolean
            // $memo_search : boolean
            // $follow_search : boolean
            // $all_repeat_events : boolean
            // $start : UNIX timestamp (option)
            // $end   : UNIX timestamp (option)
            // $start_for_daily : xsd:date (option) == "YYYY-MM-DD"
            // $end_for_daily   : xsd:date (option) == "YYYY-MM-DD"
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            $reg_args["text"]   = $text;
            $reg_args["title_search"] = ($title_search == true) ? true : false;
            $reg_args["customer_search"] = ($customer_search == true) ? true : false;
            $reg_args["memo_search"] = ($memo_search == true) ? true : false;
            $reg_args["follow_search"] = ($follow_search == true) ? true : false;
            $reg_args["all_repeat_events"] = ($all_repeat_events == true) ? true : false;
            if ($start !== false) {
                $reg_args["start"]      = gmdate(W3C_DATETIME_FORMAT, $start);
            }
            if ($end !== false) {
                $reg_args["end"]        = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($start_for_daily !== false) {
                $reg_args["start_for_daily"] = $start_for_daily;
            }
            if ($end_for_daily !== false) {
                $reg_args["end_for_daily"]   = $end_for_daily;
            }
            $this->encodeString($reg_args);
            $results = parent::ScheduleSearchEvents($reg_args);
            $this->methodClose();
            $ret_val = Array();
            if (isset($results->schedule_event)) {
               if (is_array($results->schedule_event)) {
                   foreach ($results->schedule_event as $event) {
                       $ret_val[] = new CbgrnEventType($event);
                   }
               } else {
                   $ret_val[] = new CbgrnEventType($results->schedule_event);
               }
            } else {
                ; // no schedule found
            }
            return $this->decodeString($ret_val);
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleSearchFreeTimes -- DEBUG -- ??? buggy?
        //-------------------------------------------------------------
        public function ScheduleSearchFreeTimes($candidate, $member, $search_time, $search_condition) {
            // $candidate : CbgrnScheduleSearchFreeTimesCandidateType class or that Array
            // $member    : base:memberType == CbgrnMemberType or that Array
            // $search_time : xsd:time == (interval seconds(s))
            // $serch_condition : ScheduleSearchConditionType == String(enum) {'and' or 'or'}
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["candidate"]  = $this->getRegularArgs($candidate, __FUNCTION__, "CbgrnScheduleSearchFreeTimesCandidateType");
            $reg_args["member"] = $this->getRegularArgs($member, __FUNCTION__, "CbgrnMemberType");
            $h = $search_time / 3600;
            $m = ($search_time % 3600) / 60;
            $s = $search_time % 60;
            $reg_args["search_time"] = sprintf("%02d:%02d:%02d", $h, $m, $s);
            $reg_args["search_condition"] = $search_condition;
            $this->encodeString($reg_args);
            $results = parent::ScheduleSearchFreeTimes($reg_args);
            $this->methodClose();
            $ret_val = Array();
            if (isset($results->candidate)) {
               if (is_array($results->candidate)) {
                   foreach ($results->candidate as $candidate) {
                       $ret_val[] = new CbgrnScheduleFreeTimeType($candidate);
                   }
               } else {
                   $ret_val[] = new CbgrnScheduleFreeTimeType($results->candidate);
               }
            } else {
                ; // no schedule found
            }
            return $this->decodeString($ret_val);
        }
        
        //-------------------------------------------------------------
        // Schedule: ScheduleAddFollows
        //-------------------------------------------------------------
        public function ScheduleAddFollows(CbgrnScheduleFollowContentType $follow) {
            // $follow : CbgrnScheduleFollowContentType class (NOT array)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["follow"] = $follow->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::ScheduleAddFollows($reg_args);
            $this->methodClose();
            if (isset($results->schedule_event)) {
               if (is_array($results->schedule_event)) {
                   foreach ($results->schedule_event as $event) {
                       $ret_val[] = new CbgrnEventType($event);
                   }
               } else {
                   $ret_val[] = new CbgrnEventType($results->schedule_event);
               }
            } else {
                ; // no schedule found
            }
            return $this->decodeString($ret_val);
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleAddFollowsToRepeatEvent
        //-------------------------------------------------------------
        public function ScheduleAddFollowsToRepeatEvent(CbgrnScheduleFollowToRepeatEventContentType $follow) {
            // $follow : CbgrnScheduleFollowToRepeatEventContentType class (NOT array)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["follow"] = $follow->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::ScheduleAddFollowsToRepeatEvent($reg_args);
            $this->methodClose();
            $ret_val = Array();
            if (isset($results->result)) {
               if (is_array($results->result)) {
                   foreach ($results->result as $result) {
                       $ret_val[] = new CbgrnScheduleAddFollowsToRepeatEventResultType($result);
                   }
               } else {
                   $ret_val[] = new CbgrnScheduleAddFollowsToRepeatEventResultType($results->result);
               }
            } else {
                ; // no schedule found
            }
            return $this->decodeString($ret_val);
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleRemoveFollows
        //-------------------------------------------------------------
        public function ScheduleRemoveFollows($arg) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["follow_id"] = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::ScheduleRemoveFollows($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleParticipateEvents
        //-------------------------------------------------------------
        public function ScheduleParticipateEvents($arg) {
            // $arg : IdType == String = integer or this Array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["event_id"] = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::ScheduleParticipateEvents($reg_args);
            $this->methodClose();
            if (isset($results->schedule_event)) {
               if (is_array($results->schedule_event)) {
                   foreach ($results->schedule_event as $event) {
                       $ret_val[] = new CbgrnEventType($event);
                   }
               } else {
                   $ret_val[] = new CbgrnEventType($results->schedule_event);
               }
            } else {
                ; // no schedule found
            }
            return $this->decodeString($ret_val);
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleLeaveEvents
        //-------------------------------------------------------------
        public function ScheduleLeaveEvents($arg) {
            // $arg : IdType == String = integer or this Array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["event_id"] = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::ScheduleLeaveEvents($reg_args);
            $this->methodClose();
            if (isset($results->schedule_event)) {
               if (is_array($results->schedule_event)) {
                   foreach ($results->schedule_event as $event) {
                       $ret_val[] = new CbgrnEventType($event);
                   }
               } else {
                   $ret_val[] = new CbgrnEventType($results->schedule_event);
               }
            } else {
                ; // no schedule found
            }
            return $this->decodeString($ret_val);
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleDetermineTemporaryEvents
        //-------------------------------------------------------------
        public function ScheduleDetermineTemporaryEvents(CbgrnScheduleCandidateItemType $candidate) {
            // $candidate : CbgrnScheduleCandidateItemType class (NOT Array)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["candidate"]  = $candidate->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::ScheduleDetermineTemporaryEvents($reg_args);
            $this->methodClose();
            if (isset($results->schedule_event)) {
               if (is_array($results->schedule_event)) {
                   foreach ($results->schedule_event as $event) {
                       $ret_val[] = new CbgrnEventType($event);
                   }
               } else {
                   $ret_val[] = new CbgrnEventType($results->schedule_event);
               }
            } else {
                ; // no schedule found
            }
            return $this->decodeString($ret_val);
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleRemoveTemporaryEventCandidates
        //-------------------------------------------------------------
        public function ScheduleRemoveTemporaryEventCandidates(CbgrnScheduleCandidateItemType $candidate) {
            // $candidate : CbgrnScheduleCandidateItemType class (NOT Array)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["candidate"] = $candidate->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::ScheduleRemoveTemporaryEventCandidates($reg_args);
            $this->methodClose();
            if (isset($results->schedule_event)) {
               if (is_array($results->schedule_event)) {
                   foreach ($results->schedule_event as $event) {
                       $ret_val[] = new CbgrnEventType($event);
                   }
               } else {
                   $ret_val[] = new CbgrnEventType($results->schedule_event);
               }
            } else {
                ; // no schedule found
            }
            return $this->decodeString($ret_val);
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleParticipateEventsToRepeatEvent
        //-------------------------------------------------------------
        public function ScheduleParticipateEventsToRepeatEvent($event_id, $type, $date = false) {
            // $event_id : base:IDType == String == integer
            // $type : ScheduleRepeatModifyType == string(enum) {'this' or 'after' or 'all'}
            // $date : xsd:date "YYYY-MM-DD"
            $this->CheckAndSetHeader(__FUNCTION__);
            $tmp = Array();
            $tmp["event_id"]    = $event_id;
            $tmp["type"]        = $type;
            if ($date !== false) {
                $tmp["date"]    = $date;
            }
            $reg_args = Array();
            $reg_args["operation"]   = $tmp;
            $this->encodeString($reg_args);
            $results = parent::ScheduleParticipateEventsToRepeatEvent($reg_args);
            $this->methodClose();
            return $this->decodeString(new CbgrnScheduleModifyRepeatEventsResultType($results->result));
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleLeaveEventsFromRepeatEvent
        //-------------------------------------------------------------
        public function ScheduleLeaveEventsFromRepeatEvent($event_id, $type, $date = false) {
            // $event_id : base:IDType == String == integer
            // $type : ScheduleRepeatModifyType == string(enum) {'this' or 'after' or 'all'}
            // $date : xsd:date "YYYY-MM-DD"
            $this->CheckAndSetHeader(__FUNCTION__);
            $tmp = Array();
            $tmp["event_id"]    = $event_id;
            $tmp["type"]        = $type;
            if ($date !== false) {
                $tmp["date"]    = $date;
            }
            $reg_args = Array();
            $reg_args["operation"]   = $tmp;
            $this->encodeString($reg_args);
            $results = parent::ScheduleLeaveEventsFromRepeatEvent($reg_args);
            $this->methodClose();
            return $this->decodeString(new CbgrnScheduleModifyRepeatEventsResultType($results->result));
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetProfiles
        //-------------------------------------------------------------
        public function ScheduleGetProfiles($include_system_profile = true) {
            // $include_system_profile : boolean
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["include_system_profile"] = $include_system_profile;
            $this->encodeString($reg_args);
            $results = parent::ScheduleGetProfiles($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleSetProfiles
        //-------------------------------------------------------------
        public function ScheduleSetProfiles(CbgrnSchedulePersonalProfileType $profile) {
            // $profile : CbgrnSchedulePersonalProfileType class
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["personal_profile"] = $profile->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::ScheduleSetProfiles($reg_args);
            $this->methodClose();
            return $this->decodeString(new CbgrnSchedulePersonalProfileType($results->personal_profile));
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetFacilityVersions
        //-------------------------------------------------------------
        public function ScheduleGetFacilityVersions($arg) {
            // $arg : CbgrnItemVersionType class or this Array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["facility_item"] = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnItemVersionType");
            $this->encodeString($reg_args);
            $results = parent::ScheduleGetFacilityVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->facility_item);
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetFacilitiesById
        //-------------------------------------------------------------
        public function ScheduleGetFacilitiesById($facility_item) {
            // $facility_item : IdType(integer) or this Array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["facility_id"] = $this->getRegularArgs($facility_item, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::ScheduleGetFacilitiesById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->facility);  // if array give, return array, an id give return item
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetFacilityGroupsVersions
        //-------------------------------------------------------------
        public function ScheduleGetFacilityGroupsVersions($facility_item) {
            // $facility_item : ItemVersionType or this Array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["facility_item"] = $this->getRegularArgs($facility_item, __FUNCTION__, "CbgrnItemVersionType");
            $this->encodeString($reg_args);
            $results = parent::ScheduleGetFacilityGroupsVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->facility_group_item);    // returns array of stdClass
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetFacilityGroupsById
        //-------------------------------------------------------------
        public function ScheduleGetFacilityGroupsById($facility_group_item) {
            // $facility_group_item : IDType(integer) or this Array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["facility_group_id"] = $facility_group_item;
            $this->encodeString($reg_args);
            $results = parent::ScheduleGetFacilityGroupsById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->facility_group); // if array give return array, an id give return item(stdClass)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetReadAllowUsers
        //-------------------------------------------------------------
        public function ScheduleGetReadAllowUsers() {   // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetReadAllowUsers();
            $this->methodClose();
            return $this->decodeString($results->user_id);   // returns Array of IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetReadAllowGroups
        //-------------------------------------------------------------
        public function ScheduleGetReadAllowGroups() {  // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetReadAllowGroups();
            $this->methodClose();
            return $this->decodeString($results->group_id);  // returns array of (group)IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetReadAllowFacilities
        //-------------------------------------------------------------
        public function ScheduleGetReadAllowFacilities() {  // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetReadAllowFacilities();
            $this->methodClose();
            return $this->decodeString($results->facility_id);   // returns array of (facility)IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetAddAllowUsers
        //-------------------------------------------------------------
        public function ScheduleGetAddAllowUsers() {    // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetAddAllowUsers();
            $this->methodClose();
            return $this->decodeString($results->user_id);   // returns array of (user)IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetAddAllowGroups
        //-------------------------------------------------------------
        public function ScheduleGetAddAllowGroups() {   // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetAddAllowGroups();
            $this->methodClose();
            return $this->decodeString($results->group_id);  // returns array of (group)IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetAddAllowFacilities
        //-------------------------------------------------------------
        public function ScheduleGetAddAllowFacilities() {   // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetAddAllowFacilities();
            $this->methodClose();
            return $this->decodeString($results->facility_id);   // returns array of (facility)IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetModifyAllowUsers
        //-------------------------------------------------------------
        public function ScheduleGetModifyAllowUsers() { // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetModifyAllowUsers();
            $this->methodClose();
            return $this->decodeString($results->user_id);   // returns array of (user)IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetModifyAllowGroups
        //-------------------------------------------------------------
        public function ScheduleGetModifyAllowGroups() {    // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetModifyAllowGroups();
            $this->methodClose();
            return $this->decodeString($results->group_id);  // returns array of (group)IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetModifyAllowFacilities
        //-------------------------------------------------------------
        public function ScheduleGetModifyAllowFacilities() {    // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetModifyAllowFacilities();
            $this->methodClose();
            return $this->decodeString($results->facility_id);   // returns array of (facility)IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetRemoveAllowUsers
        //-------------------------------------------------------------
        public function ScheduleGetRemoveAllowUsers() { // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetRemoveAllowUsers();
            $this->methodClose();
            return $this->decodeString($results->user_id);   // returns array of (user)IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetRemoveAllowGroups
        //-------------------------------------------------------------
        public function ScheduleGetRemoveAllowGroups() {    // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetRemoveAllowGroups();
            $this->methodClose();
            return $this->decodeString($results->group_id);  // returns array of (group)IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Schedule: ScheduleGetRemoveAllowFacilities
        //-------------------------------------------------------------
        public function ScheduleGetRemoveAllowFacilities() {    // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::ScheduleGetRemoveAllowFacilities();
            $this->methodClose();
            return $this->decodeString($results->facility_id);   // returns array of (facility)IDType(string := integer)
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinSearchTopics
        //-------------------------------------------------------------
        public function BulletinSearchTopics($text, $sensitive, $start, $end, $category_id, $search_sub_categories = true, $title_search = true, $body_search = true, $from_search = false, $follow_search = false) {
            // $text : search "string"
            // $sensitive : boolean (case sensitive : true, otherwise : false)
            // $start : UNIX timestamp (search start datetime)
            // $end   : UNIX timestamp (search end datetime) or false (not determine end datetime)
            // $category_id : bulletin board IDType or -1=only waiting -2=under writting 0=all bulletin board
            // $search_sub_categories : boolean (true:search sub categories, false:only this board) (default true)
            // $title_search : boolean (default true)
            // $body_search : boolean (default true)
            // $from_search : boolean (default false)
            // $follow_search : boolean (default false)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            $reg_args["text"]           = $text;
            $reg_args["sensitive"]      = ($sensitive == true) ? true : false;
            $reg_args["start"]          = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== false) {
                $reg_args["end"]        = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            $reg_args["category_id"]    = $category_id;
            $reg_args["search_sub_categories"]  = ($search_sub_categories == true) ? true : false;
            $reg_args["title_search"]   = ($title_search == true) ? true : false;
            $reg_args["body_search"]    = ($body_search == true) ? true : false;
            $reg_args["from_search"]    = ($from_search == true) ? true : false;
            $reg_args["follow_search"]  = ($follow_search == true) ? true : false;
            $this->encodeString($reg_args);
            $results = parent::BulletinSearchTopics($reg_args);
            $this->methodClose();
            if (is_array($results->topic)) {
                return $this->decodeString($results->topic);
            } else {
                $tmp = Array();
                $tmp[] = $this->decodeString($results->topic);
                return $tmp;
            }
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinGetTopicVersions
        //-------------------------------------------------------------
        public function BulletinGetTopicVersions($start, $end = false, $topic_item = false, $category_id = false) {
            // $start : UNIX timestamp
            // $end   : UNIX timestamp (optional)
            // $topic_item : base:ItemVersionType (optional)
            // $category_id : base:IDType(string := integer) (optional) or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            $reg_args["start"]      = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== false) {
                $reg_args["end"]    = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($topic_item !== false) {
                $reg_args["topic_item"] = $this->getRegularArgs($topic_item, __FUNCTION__, "CbgrnItemVersionType");
            }
            if ($category_id !== false) {
                $reg_args["category_id"] = $category_id;
            }
            $this->encodeString($reg_args);
            $results = parent::BulletinGetTopicVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->topic_item); // returns array of stdClass
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinGetDraftTopicVersions
        //-------------------------------------------------------------
        public function BulletinGetDraftTopicVersions($start, $end = false, $topic_item = false) {
            // $start : UNIX timestamp
            // $end   : UNIX timestamp (optional)
            // $topic_item : ItemVersionType or this array or false (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            $reg_args["start"]  = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== false) {
                $reg_args["end"] = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($topic_item !== false) {
                $reg_args["topic_item"] = $this->getRegularArgs($topic_item, __FUNCTION__, "CbgrnItemVersionType");
            }
            $this->encodeString($reg_args);
            $results = parent::BulletinGetDraftTopicVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->topic_item); // returns array of stdClass
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinGetCategoryVersions
        //-------------------------------------------------------------
        public function BulletinGetCategoryVersions($category_item = false) {
            // $category_item : (optional) CbgrnItemVersionType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = Array();
            if ($category_item !== false) {
                $reg_args["category_item"]  = $this->getRegularArgs($category_item, __FUNCTION__, "CbgrnItemVersionType");
            }
            $this->encodeString($reg_args);
            $results = parent::BulletinGetCategoryVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results->category_item);
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinGetCategories
        //-------------------------------------------------------------
        public function BulletinGetCategories() { // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::BulletinGetCategories();
            $this->methodClose();
            return $this->decodeString($results->categories);
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinFileDownload
        //-------------------------------------------------------------
        public function BulletinFileDownload($file_id) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["file_id"] = $file_id;
            $this->encodeString($reg_args);
            $results = parent::BulletinFileDownload($reg_args);
            $this->methodClose();
            return $results->file->content; // already base64_decode
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinModifyTopics
        //-------------------------------------------------------------
        public function BulletinModifyTopics($modify_topic) {
            // $modify_topic : CbgrnBulletinModifyTopicType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["modify_topic"] = $this->getRegularArgs($modify_topic, __FUNCTION__, "CbgrnBulletinModifyTopicType");
            $this->encodeString($reg_args);
            $results = parent::BulletinModifyTopics($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->topic);
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinRemoveTopics
        //-------------------------------------------------------------
        public function BulletinRemoveTopics($topic_id, $is_draft = false) {
            // $topic_id : base:IDType (string := integer)
            // $is_draft : boolean
            $this->CheckAndSetHeader(__FUNCTION__);
            $tmp = Array();
            $tmp["topic_id"]   = $topic_id;
            $tmp["is_draft"]   = ($is_draft == true) ? true : false;
            $reg_args["topics"] = $tmp;
            $this->encodeString($reg_args);
            $results = parent::BulletinRemoveTopics($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinSaveDraftTopics
        //-------------------------------------------------------------
        public function BulletinSaveDraftTopics(CbgrnBulletinCreateTopicType $create_topic) {
            // $create_topic : CbgrnBulletinCreateTopicType class (NOT array)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["save_draft_topic"] = $create_topic->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::BulletinSaveDraftTopics($reg_args);
            $this->methodClose();
            return $this->decodeString(new CbgrnTopicType($results->topic));
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinCreateTopics
        //-------------------------------------------------------------
        public function BulletinCreateTopics(CbgrnBulletinCreateTopicType $create_topic) {
            // $create_topic : CbgrnBulletinCreateTopicType class (NOT array)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["create_topic"]   = $create_topic->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::BulletinCreateTopics($reg_args);
            $this->methodClose();
            return $this->decodeString($results->topic);
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinGetFollows
        //-------------------------------------------------------------
        public function BulletinGetFollows($topic_id, $offset, $limit) {
            // $topic_id : base:IDType(string := integer) .. Bulletin board ID
            // $offset   : integer
            // $limit    : integer
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["topic_id"]   = $topic_id;
            $reg_args["offset"]     = $offset;
            $reg_args["limit"]      = $limit;
            $this->encodeString($reg_args);
            $results = parent::BulletinGetFollows($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->follow);
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinAddFollows
        //-------------------------------------------------------------
        public function BulletinAddFollows(CbgrnBulletinAddFollowType $add_follow) {
            // $add_follow : CbgrnBulletinAddFollowType class (NOT array)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["add_follow"] = $add_follow->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::BulletinAddFollows($reg_args);
            $this->methodClose();
            return $this->decodeString(new CbgrnTopicType($results->topic));
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinRemoveFollows
        //-------------------------------------------------------------
        public function BulletinRemoveFollows($arg) {
            // $arg : base:IDType (follow id or ids)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["follow_id"] = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::BulletinRemoveFollows($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinGetTopics
        //-------------------------------------------------------------
        public function BulletinGetTopics($category_id) {
            // $category_id : base:IDType (NOT Array, only ONE id gives)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["category_id"] = $category_id;
            $this->encodeString($reg_args);
            $results = parent::BulletinGetTopics($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->category->topic);
        }

        //-------------------------------------------------------------
        // Bulletin: BulletinGetTopicByIds
        //-------------------------------------------------------------
        public function BulletinGetTopicByIds($topic_id, $is_draft = false) {
            // $topic_id : IDType (not Array, only ONE id gives)
            // is_draft : boolean
            $this->CheckAndSetHeader(__FUNCTION__);
            $tmp = Array();
            $tmp["topic_id"]   = $topic_id;
            $tmp["is_draft"]   = $is_draft;
            $reg_args["topics"] = $tmp;
            $this->encodeString($reg_args);
            $results = parent::BulletinGetTopicByIds($reg_args);
            $this->methodClose();
            return $this->decodeString(new CbgrnTopicType($results->topic));
        }

        //-------------------------------------------------------------
        // Address: AddressGetSharedCardsById
        //-------------------------------------------------------------
        public function AddressGetSharedCardsById($card_id, $book_id) {
            // $card_id : IDType or this array
            // $book_id : IDType (not Array)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["card_id"] = $this->getRegularArgs($card_id, __FUNCTION__);
            $reg_args["book_id"] = $book_id;
            $this->encodeString($reg_args);
            $results = parent::AddressGetSharedCardsById($reg_args);
            $this->methodClose();
            if (is_array($results->card)) {// if card_id is single returns single stdClass, multiple ids returns array of stdClass
                $ret_val = Array();
                foreach ($results->card as $card) {
                    $ret_val[] = new CbgrnCardType($card);
                }
                return $this->decodeString($ret_val);
            } else {
                return $this->decodeString(new CbgrnCardType($results->card));
            }
        }

        //-------------------------------------------------------------
        // Address: AddressGetSharedBooksById
        //-------------------------------------------------------------
        public function AddressGetSharedBooksById($book_id) {
            // $book_id : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["book_id"] = $this->getRegularArgs($book_id, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AddressGetSharedBooksById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->book);  // if book_id is single returns single stdClass, multiple ids returns array
        }

        //-------------------------------------------------------------
        // Address: AddressGetPersonalCardsById
        //-------------------------------------------------------------
        public function AddressGetPersonalCardsById($card_id) {
            // $card_id : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["card_id"] = $this->getRegularArgs($card_id, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AddressGetPersonalCardsById($reg_args);
            $this->methodClose();
            if (is_array($results->card)) {// if card_id is single returns single stdClass, multiple ids returns array of stdClass
                $ret_val = Array();
                foreach ($results->card as $card) {
                    $ret_val[] = new CbgrnCardType($card);
                }
                return $this->decodeString($ret_val);
            } else {
                return $this->decodeString(new CbgrnCardType($results->card));
            }
        }

        //-------------------------------------------------------------
        // Address: AddressGetPersonalBooksById
        //-------------------------------------------------------------
        public function AddressGetPersonalBooksById($book_id) {
            // $book_id : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["book_id"] = $this->getRegularArgs($book_id, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AddressGetPersonalBooksById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->book);
        }

        //-------------------------------------------------------------
        // Address: AddressGetSharedBookVersions
        //-------------------------------------------------------------
        public function AddressGetSharedBookVersions($book_item = false) {
            // $book_item : CbgrnItemVersionType class or this array (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($book_item === false) {
                $reg_args["book_item"] = Array();
            } else {
                $reg_args["book_item"] = $this->getRegularArgs($book_item, __FUNCTION__, "CbgrnItemVersionType");
            }
            $this->encodeString($reg_args);
            $results = parent::AddressGetSharedBookVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results->book_item);
        }

        //-------------------------------------------------------------
        // Address: AddressGetPersonalBookVersions
        //-------------------------------------------------------------
        public function AddressGetPersonalBookVersions($book_item = false) {
            // $book_item : CbgrnItemVersionType class or this array (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($book_item === false) {
                $reg_args["book_item"] = Array();
            } else {
                $reg_args["book_item"] = $this->getRegularArgs($book_item, __FUNCTION__, "CbgrnItemVersionType");
            }
            $this->encodeString($reg_args);
            $results = parent::AddressGetPersonalBookVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results->book_item);
        }

        //-------------------------------------------------------------
        // Address: AddressGetPersonalCardVersions
        //-------------------------------------------------------------
        public function AddressGetPersonalCardVersions($card_item = false) {
            // $card_item : CbgrnItemVersionType class or this array (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($card_item === false) {
                $reg_args["card_item"] = Array();
            } else {
                $reg_args["card_item"] = $this->getRegularArgs($card_item, __FUNCTION__, "CbgrnItemVersionType");
            }
            $this->encodeString($reg_args);
            $results = parent::AddressGetPersonalCardVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($this->RetvalConvertArray($results->card_item));
        }

        //-------------------------------------------------------------
        // Address: AddressGetSharedCardVersions
        //-------------------------------------------------------------
        public function AddressGetSharedCardVersions($book_id, $card_item = false) {
            // $book_id : IDType
            // $card_item : CbgrnItemVersionType class or this array (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["book_id"]    = $book_id;
            if ($card_item === false) {
                $reg_args["card_item"] = Array();
            } else {
                $reg_args["card_item"] = $this->getRegularArgs($card_item, __FUNCTION__, "CbgrnItemVersionType");
            }
            $this->encodeString($reg_args);
            $results = parent::AddressGetSharedCardVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results->card_item);
        }

        //-------------------------------------------------------------
        // Address: AddressAddCards
        //-------------------------------------------------------------
        public function AddressAddCards($arg) {
            // $arg : CbgrnAddressCardContainsFileType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["add_card"] = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnAddressCardContainsFileType");
            $this->encodeString($reg_args);
            $results = parent::AddressAddCards($reg_args);
            $this->methodClose();
            return $this->decodeString($results->card);
        }

        //-------------------------------------------------------------
        // Address: AddressModifyCards -- DEBUG -- -- namespace prevents an action --
        //-------------------------------------------------------------
        public function AddressModifyCards($arg) {
            // $arg : CbgrnAddressCardContainsFileType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["modify_card"] = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnAddressCardContainsFileType");
            $this->encodeString($reg_args);
            $results = parent::AddressModifyCards($reg_args);
            $this->methodClose();
            return $this->decodeString($results->card);
        }

        //-------------------------------------------------------------
        // Address: AddressRemovePersonalCards
        //-------------------------------------------------------------
        public function AddressRemovePersonalCards($ids) {
            // $ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["card_id"] = $this->getRegularArgs($ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AddressRemovePersonalCards($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Address: AddressRemoveSharedCards
        //-------------------------------------------------------------
        public function AddressRemoveSharedCards($book_id, $card_ids) {
            // $book_id : IDType (only one book specified)
            // $card_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["book_id"]    = $book_id;
            $reg_args["card_id"] = $this->getRegularArgs($card_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AddressRemoveSharedCards($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Address: AddressSearchCards
        //-------------------------------------------------------------
        public function AddressSearchCards($book_id, $case_sensitive, $text) {
            // $book_id : IDType (only one book specified)
            // $case_sensitive : boolean
            // $text : string (search text)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["book_id"]    = $book_id;
            $reg_args["case_sensitive"] = ($case_sensitive == true) ? true : false;
            $reg_args["text"] = $text;
            $this->encodeString($reg_args);
            $results = parent::AddressSearchCards($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->card);
        }

        //-------------------------------------------------------------
        // Address: AddressCopyPersonalCardsToOtherBook
        //-------------------------------------------------------------
        public function AddressCopyPersonalCardsToOtherBook($copied_book_id, $card_id) {
            // $copied_book_id : IDType (destination book ID)
            // $card_id : IDType (copy card ID)
            // this function copies only ONE card to destination book
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["copy_item"]["copied_book_id"] = $copied_book_id;
            $reg_args["copy_item"]["card_id"]        = $card_id;
            $this->encodeString($reg_args);
            $results = parent::AddressCopyPersonalCardsToOtherBook($reg_args);
            $this->methodClose();
            return $this->decodeString($results->card);
        }

        //-------------------------------------------------------------
        // Address: AddressGetMyAddressGroupVersions
        //-------------------------------------------------------------
        public function AddressGetMyAddressGroupVersions($arg = false) {
            // $arg : ItemVersionType or this array (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($arg === false) {
                $reg_args["my_address_group_item"] = Array();
            } else {
                $reg_args["my_address_group_item"] = $this->getRegularArgs($arg, __FUNCTION__);
            }
            $this->encodeString($reg_args);
            $results = parent::AddressGetMyAddressGroupVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->my_address_group_item);
        }

        //-------------------------------------------------------------
        // Address: AddressGetMyAddressGroupsById
        //-------------------------------------------------------------
        public function AddressGetMyAddressGroupsById($arg) {
            // $arg : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["my_address_group_id"] = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AddressGetMyAddressGroupsById($reg_args);
            $this->methodClose();
            if (is_array($results->my_address_group)) {
                $ret_val = Array();
                foreach ($results->my_address_group as $my_address_group) {
                    $ret_val[] = new CbgrnMyAddressGroupType($my_address_group);
                }
                return $this->decodeString($ret_val);
            } else {
                return $this->decodeString(new CbgrnMyAddressGroupType($results->my_address_group));
            }
        }

        //-------------------------------------------------------------
        // Address: AddressAddMyAddressGroups
        //-------------------------------------------------------------
        public function AddressAddMyAddressGroups($arg) {
            // $arg : CbgrnMyAddressGroupType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["my_address_group"] = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnMyAddressGroupType");
            $this->encodeString($reg_args);
            $results = parent::AddressAddMyAddressGroups($reg_args);
            $this->methodClose();
            if (is_array($results->my_address_group)) {
                $ret_val = Array();
                foreach ($results->my_address_group as $my_address_group) {
                    $ret_val[] = new CbgrnMyAddressGroupType($my_address_group);
                }
                return $this->decodeString($ret_val);
            } else {
                return $this->decodeString(new CbgrnMyAddressGroupType($results->my_address_group));
            }
        }

        //-------------------------------------------------------------
        // Address: AddressModifyMyAddressGroups
        //-------------------------------------------------------------
        public function AddressModifyMyAddressGroups($arg) {
            // $arg : CbgrnMyAddressGroupType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["my_address_group"] = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnMyAddressGroupType");
            $this->encodeString($reg_args);
            $results = parent::AddressModifyMyAddressGroups($reg_args);
            $this->methodClose();
            if (is_array($results->my_address_group)) {
                $ret_val = Array();
                foreach ($results->my_address_group as $my_address_group) {
                    $ret_val[] = new CbgrnMyAddressGroupType($my_address_group);
                }
                return $this->decodeString($ret_val);
            } else {
                return $this->decodeString(new CbgrnMyAddressGroupType($results->my_address_group));
            }
        }

        //-------------------------------------------------------------
        // Address: AddressRemoveMyAddressGroups
        //-------------------------------------------------------------
        public function AddressRemoveMyAddressGroups($arg) {
            // $arg : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["my_address_group_id"] = $this->getRegularArgs($arg, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::AddressRemoveMyAddressGroups($reg_args);
            $this->methodClose();
            return $this->decodeString($results);    // probably NULL
        }

        //-------------------------------------------------------------
        // Address: AddressModifyCardsInMyAddressGroup -- DEBUG cannot move and similar(same?) function to AddressModifyMyAddressGroups --
        //-------------------------------------------------------------
        public function AddressModifyCardsInMyAddressGroup($arg) {
            // $arg : CbgrnMyAddressGroupType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["my_address_group"] = $this->getRegularArgs($arg, __FUNCTION__, "CbgrnMyAddressGroupType");
            $this->encodeString($reg_args);
            $results = parent::AddressModifyCardsInMyAddressGroup($reg_args);
            $this->methodClose();
            if (is_array($results->my_address_group)) {
                $ret_val = Array();
                foreach ($results->my_address_group as $my_address_group) {
                    $ret_val[] = new CbgrnMyAddressGroupType($my_address_group);
                }
                return $this->decodeString($ret_val);
            } else {
                return $this->decodeString(new CbgrnMyAddressGroupType($results->my_address_group));
            }
        }

        //-------------------------------------------------------------
        // Address: AddressGetReadAllowBooks
        //-------------------------------------------------------------
        public function AddressGetReadAllowBooks() {    // no arguments
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::AddressGetReadAllowBooks();
            $this->methodClose();
            return $this->RetvalConvertArray($results->book_id);    // returns array of IDType
        }

        //-------------------------------------------------------------
        // Address: AddressGetModifyAllowBooks
        //-------------------------------------------------------------
        public function AddressGetModifyAllowBooks() {  // no arguments
            $this->CheckAndSetHeader(__FUNCTION__);
            
            $results = parent::AddressGetModifyAllowBooks();
            $this->methodClose();
            return $this->RetvalConvertArray($results->book_id);    // returns array of IDType
        }

        //-------------------------------------------------------------
        // Address: AddressFileDownload
        //-------------------------------------------------------------
        public function AddressFileDownload($file_id) {
            // $file_id : IDType (only ONE ID)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["file_id"]    = $file_id;
            $this->encodeString($reg_args);
            $results = parent::AddressFileDownload($reg_args);
            $this->methodClose();
            return $results->file->content; // returns file body (already base64decoded)
        }

        //-------------------------------------------------------------
        // Cabinet: CabinetGetFolderInfo
        //-------------------------------------------------------------
        public function CabinetGetFolderInfo() {    // no arguments
            $this->CheckAndSetHeader(__FUNCTION__);
            $results = parent::CabinetGetFolderInfo();
            $this->methodClose();
            return $this->decodeString($results->folder_information);
        }

        //-------------------------------------------------------------
        // Cabinet: CabinetGetFileInfo
        //-------------------------------------------------------------
        public function CabinetGetFileInfo($arg) {
            // $arg : IDType (not array)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["hid"]    = $arg;
            $this->encodeString($reg_args);
            $results = parent::CabinetGetFileInfo($reg_args);
            $this->methodClose();
            return $this->decodeString($results->file_information);
        }

        //-------------------------------------------------------------
        // Cabinet: CabinetAddFile
        //-------------------------------------------------------------
        public function CabinetAddFile($hid, $file_path, $title = false, $version = false, $description = false) {
            // $hid : base:IDType == string == integer (folder ID)
            // $file_path : add file location (string)
            // $title : (optional) file title
            // $version : integer (0 - )
            // $description : string (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["hid"]    = $hid;
            $basename = basename($file_path);
            $reg_args["name"]   = $basename;
            if ($title !== false) {
                $reg_args["title"]  = $title;
            }
            if ($version !== false) {
                $reg_args["version"] = $version;
            }
            if ($description !== false) {
                $reg_args["description"] = $description;
            }
            // file body
            $fp = fopen($file_path, "r");
            if ($fp == false) {
                throw new CbgrnSoapFault("007", __FUNCTION__);
            }
            $this->encodeString($reg_args); // file body do not convert encoding
            $reg_args["content"] = fread($fp, filesize($file_path));
            fclose($fp);
            $results = parent::CabinetAddFile($reg_args);
            $this->methodClose();
            return $this->decodeString($results->file);
        }

        //-------------------------------------------------------------
        // Cabinet: CabinetFileDownload
        //-------------------------------------------------------------
        public function CabinetFileDownload($file_id) {
            // $file_id : IDType (== string == integer)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["file_id"]    = $file_id;
            $this->encodeString($reg_args);
            $results = parent::CabinetFileDownload($reg_args);
            $this->methodClose();
            return $results->file->content;
        }

        //-------------------------------------------------------------
        // Cabinet: CabinetUpdateFile
        //-------------------------------------------------------------
        public function CabinetUpdateFile($file_id, $name, $file_path, $comment = false) {
            // $file_id : IDType (== string == integer) update file ID
            // $name    : string (Download File Name ,almost basename($file_path))
            // $file_path : new file location (string)
            // $commnet : string (optional) file description
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["file_id"]    = $file_id;
            $reg_args["name"]       = $name;
            if ($commnet !== false) {
                $reg_args["comment"] = $comment;
            }
            $this->encodeString($reg_args);
            // update file body
            $fp = fopen($file_path, "r");
            if ($fp == false) {
                throw new CbgrnSoapFault("007", __FUNCTION__);
            }
            $reg_args["content"]    = fread($fp, filesize($file_path));
            fclose($fp);
            $results = parent::CabinetUpdateFile($reg_args);
            $this->methodClose();
            return $this->decodeString($results->file);
        }

        //-------------------------------------------------------------
        // Cabinet: CabinetUpdateFileInformation
        //-------------------------------------------------------------
        public function CabinetUpdateFileInformation($file_id, $title = false, $version = false, $description = false) {
            // $file_id : IDType == string == integer (file ID for update information)
            // $title   : string (file title (not file name)
            // $version : IDType (== integer?)
            // $description : string
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["file_id"]    = $file_id;
            if ($title !== false) {
                $reg_args["title"]  = $title;
            }
            if ($version !== false) {
                $reg_args["version"] = $version;
            }
            if ($description !== false) {
                $reg_args["description"] = $description;
            }
            $this->encodeString($reg_args);
            $results = parent::CabinetUpdateFileInformation($reg_args);
            $this->methodClose();
            return $this->decodeString($results->file);
        }

        //-------------------------------------------------------------
        // Cabinet: CabinetDeleteFiles
        //-------------------------------------------------------------
        public function CabinetDeleteFiles($file_id) {
            // $file_id : IDType (==string==integer) file ID for DELETE
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["file_id"]    = $file_id;
            $this->encodeString($reg_args);
            $results = parent::CabinetDeleteFiles($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Message: MessageGetFolderVersions
        //-------------------------------------------------------------
        public function MessageGetFolderVersions($folder_item) {
            // $folder_item : CbgrnItemVersionType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["folder_item"] = $this->getRegularArgs($folder_item, __FUNCTION__, "CbgrnItemVersionType");
            $this->encodeString($reg_args);
            $results = parent::MessageGetFolderVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results->folder_item);
        }

        //-------------------------------------------------------------
        // Message: MessageGetFoldersById
        //-------------------------------------------------------------
        public function MessageGetFoldersById($folder_id) {
            // $folder_id : string or string array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args = $this->getRegularArgs($folder_id, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::MessageGetFoldersById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->folder);
        }

        //-------------------------------------------------------------
        // Message: MessageGetThreadVersions
        //-------------------------------------------------------------
        public function MessageGetThreadVersions($start, $end = false, $thread_items = false, $folder_ids = false) {
            // $start : search start datetime   UNIX timestamp
            // $end   : search end   datetime   UNIX timestamp    (optional)
            // $thread_items : CbgrnItemVersionType or this array (optional)
            // $folder_ids   : IDType (== string) or this array (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["start"]      = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== false) {
                $reg_args["end"]    = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($thread_items !== false) {
                $reg_args["thread_item"] = getRegularArgs($thread_items, __FUNCTION__, "CbgrnItemVersionType");
            }
            if ($folder_ids !== false) {
                $reg_args["folder_id"]   = getRegularArgs($folder_ids, __FUNCTION__);
            }
            $this->encodeString($reg_args);
            $results = parent::MessageGetThreadVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results->thread_item);
        }

        //-------------------------------------------------------------
        // Message: MessageGetThreadsById
        //-------------------------------------------------------------
        public function MessageGetThreadsById($thread_ids) {
            // $thread_ids : string or string array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["thread_id"] = $this->getRegularArgs($thread_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::MessageGetThreadsById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->thread);
        }

        //-------------------------------------------------------------
        // Message: MessageCreateThreads
        //-------------------------------------------------------------
        public function MessageCreateThreads(CbgrnThreadType $thread) {
            // $thread : CbgrnThreadType class (only ONE)
            //      remark: thread has files, so MessageCreateThreadsRequestType's file no needs?
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["create_thread"]["thread"] = $thread;
            $this->encodeString($reg_args);
            $results = parent::MessageCreateThreads($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Message: MessageModifyThreads
        //-------------------------------------------------------------
        public function MessageModifyThreads(CbgrnThreadType $thread) {
            // $thread : CbgrnThreadType class (only ONE)
            //      remark: thread has files, so MessageCreateThreadsRequestType's file no needs?
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["modify_thread"]["thread"] = $thread;
            $this->encodeString($reg_args);
            $results = parent::MessageModifyThreads($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Message: MessageSaveDraftThreads
        //-------------------------------------------------------------
        public function MessageSaveDraftThreads(CbgrnThreadType $thread) {
            // $thread : CbgrnThreadType class (only ONE)
            //      remark: thread has files, so MessageCreateThreadsRequestType's file no needs?
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["save_draft_thread"]["thread"] = $thread;
            $this->encodeString($reg_args);
            $results = parent::MessageSaveDraftThreads($reg_args);
            $this->methodClose();
            return $this->decodeString($results->thread);
        }

        //-------------------------------------------------------------
        // Message: MessageConfirmThreads
        //-------------------------------------------------------------
        public function MessageConfirmThreads($thread_ids) {
            // $thread_ids : string or string array (id:message ID)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["thread_id"] = $this->getRegularArgs($thread_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::MessageConfirmThreads($reg_args);
            $this->methodClose();
            return $this->decodeString($results->thread);
        }

        //-------------------------------------------------------------
        // Message: MessageRemoveThreads
        //-------------------------------------------------------------
        public function MessageRemoveThreads($thread_ids, $delete_all_inbox = false) {
            // $thread_ids : CbgrnMessageRemoveThreadType class or this array
            // $delete_all_inbox : boolean
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["param"] = $this->getRegularArgs($thread_ids, __FUNCTION__, "CbgrnMessageRemoveThreadType");
            $reg_args["delete_all_inbox"] = $delete_all_inbox;
            $this->encodeString($reg_args);
            $results = parent::MessageRemoveThreads($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Message: MessageSearchThreads
        //-------------------------------------------------------------
        public function MessageSearchThreads($text, $start, $end = false, $folder_id = false, $search_sub_folder = true, $title_search = true, $body_search = true, $from_search = true, $addressee_search = true, $follow_search = true) {
            // $text    : string
            // $start   : UNIX timestamp
            // $end     : UNIX timestamp (optional)
            // $folder_id : IDType (optional)
            // $search_sub_folder : boolean (default true)
            // $title_search : boolean (default true)
            // $body_search  : boolean (default true)
            // $from_search  : boolean (default true)
            // $addressee_search : boolean (default true)
            // $follow_search : boolean (default true)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["text"]   = $text;
            $reg_args["start"]  = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== false) {
                $reg_args["end"] = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($folder_id !== false) {
                $reg_args["folder_id"]  = $folder_id;
            }
            $reg_args["search_sub_folder"]  = $search_sub_folder;
            $reg_args["title_search"]       = $title_search;
            $reg_args["body_search"]        = $body_search;
            $reg_args["from_search"]        = $from_search;
            $reg_args["addressee_search"]   = $addressee_search;
            $reg_args["follow_search"]      = $follow_search;
            $this->encodeString($reg_args);
            $results = parent::MessageSearchThreads($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->thread); // returns array of stdClass
        }

        //-------------------------------------------------------------
        // Message: MessageGetFollows
        //-------------------------------------------------------------
        public function MessageGetFollows($thread_id, $offset = 0, $limit = 20) {
            // $thread_id : IDType
            // $offset    : integer (optional default 0)
            // $limit     : integer (optional default 20)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["thread_id"]  = $thread_id;
            $reg_args["offset"]     = $offset;
            $reg_args["limit"]      = $limit;
            $this->encodeString($reg_args);
            $results = parent::MessageGetFollows($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->follow);
        }

        //-------------------------------------------------------------
        // Message: MessageAddFollows
        //-------------------------------------------------------------
        public function MessageAddFollows($id, CbgrnMessageFollowType $follow, $files = false) {
            // $id      : IDType .. thread ID
            // $follow : CbgrnMessageFollowType class (only ONE)
            // $files  : attached file's path (string, optional) CbgrnFileType class or this Array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["add_follow"]["thread_id"] = $id;
            if ($files !== false) {
                if (is_array($files)) {
                    $n = 0;
                    foreach ($files as $file_path) {
                        $fp = fopen($file_path ,"r");
                        if ($fp == false) {
                            throw new CbgrnSoapFault("007", __FUNCTION__);
                        }
                        $reg_args["add_follow"]["file"][$n]["content"] = fread($fp, filesize($file_path));
                        fclose($fp);
                        $reg_args["add_follow"]["file"][$n]["id"] = $n + 1;
                        $file = new CbgrnMessageFile($n + 1, basename($file_path));
                        $follow->file[] = $file;
                        $n++;
                    }
                } else {
                    $fp = fopen($files, "r");
                    if ($fp == false) {
                        throw new CbgrnSoapFault("007", __FUNCTION__);
                    }
                    $reg_args["add_follow"]["file"]["content"] = fread($fp, filesize($files));
                    fclose($fp);
                    $reg_args["add_follow"]["file"]["id"] = "1";
                    $file = new CbgrnMessageFile(1, basename($files));
                    $follow->file = $file;
                }
            }
            $reg_args["add_follow"]["follow"] = $follow->getObjectVars();
            $this->encodeString($reg_args, "content");

            $results = parent::MessageAddFollows($reg_args);
            $this->methodClose();
            return $this->decodeString($results->thread);
        }

        //-------------------------------------------------------------
        // Message: MessageRemoveFollows
        //-------------------------------------------------------------
        public function MessageRemoveFollows($follow_id) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["follow_id"]  = $follow_id;
            $this->encodeString($reg_args);
            $results = parent::MessageRemoveFollows($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Message: MessageGetProfiles
        //-------------------------------------------------------------
        public function MessageGetProfiles($include_system_profile = true) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["include_system_profile"] = $include_system_profile;
            $this->encodeString($reg_args);
            $results = parent::MessageGetProfiles($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Message: MessageSetProfiles
        //-------------------------------------------------------------
        public function MessageSetProfiles($use_trush, $trush_duration) {
            // $use_trush : boolean
            // $trush_duration : integer
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["personal_profile"]["use_trash"] = $use_trush;
            $reg_args["personal_profile"]["trash_duration"] = $trush_duration;
            $this->encodeString($reg_args);
            $results = parent::MessageSetProfiles($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Message: MessageFileDownload
        //-------------------------------------------------------------
        public function MessageFileDownload($file_id) {
            // $file_id : IDType
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["file_id"]    = $file_id;
            $this->encodeString($reg_args);
            $results = parent::MessageFileDownload($reg_args);
            $this->methodClose();
            return $results->file->content;
        }

        //-------------------------------------------------------------
        // Report: ReportGetReportVersions
        //-------------------------------------------------------------
        public function ReportGetReportVersions(CbgrnItemVersionType $report_item, $start, $end = false, $target = false) {
            // $report_item : CbgrnItemVersionType class (only ONE)
            // $start       : UNIX timestamp
            // $end         : UNIX timestamp (optional)
            // $target      : string ("received" or "send" or "draft" or "all") (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["report_item"]    = $report_item;
            $reg_args["start"]          = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== false) {
                $reg_args["end"]        = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($target !== false) {
                $reg_args["target"]     = $target;
            }
            $this->encodeString($reg_args);
            $results = parent::ReportGetReportVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Report: ReportGetReportById
        //-------------------------------------------------------------
        public function ReportGetReportById($report_id) {
            // $report_id : IDType (==string==integer) or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["report_id"] = $this->getRegularArgs($report_id, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::ReportGetReportById($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->report);
        }

        //-------------------------------------------------------------
        // Report: ReportAddFollows
        //-------------------------------------------------------------
        public function ReportAddFollows(CbgrnReportAddFollowType $add_follow) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["add_follow"] = $add_follow;
            $this->encodeString($reg_args, "content");
            $results = parent::ReportAddFollows($reg_args);
            $this->methodClose();
            return $this->decodeString($results->report);
        }

        //-------------------------------------------------------------
        // Report: ReportGetFollows
        //-------------------------------------------------------------
        public function ReportGetFollows($report_id, $limit = false, $offset = false) {
            // $report_id   : IDType
            // $limit       : integer (optional) default 10
            // $offset      : integer (optional) default 0
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["report_id"]  = $report_id;
            if ($limit !== false) {
                $reg_args["limit"]      = $limit;
            } else {
                $reg_args["limit"]      = 10;
            }
            if ($offset !== false) {
                $reg_args["offset"]     = $offset;
            } else {
                $reg_args["offset"]     = 0;
            }
            $this->encodeString($reg_args);
            $results = parent::ReportGetFollows($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Report: ReportRemoveReports
        //-------------------------------------------------------------
        public function ReportRemoveReports($report_ids) {
            // $report_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["report_id"] = $this->getRegularArgs($report_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::ReportRemoveReports($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Report: ReportRemoveFollows
        //-------------------------------------------------------------
        public function ReportRemoveFollows($follow_ids) {
            // $follow_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["follow_id"] = $this->getRegularArgs($follow_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::ReportRemoveFollows($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Report: ReportSearchReports
        //-------------------------------------------------------------
        public function ReportSearchReports($keyword, $target = false) {
            // $keyword       : string
            // $search_target : string ('received' or 'send' or 'draft' or 'all') default all
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($target === false) {
                $target = "all";
            }
            $reg_args["target"]         = $target;
            $reg_args["keyword"]        = $keyword;
            $this->encodeString($reg_args);
            $results = parent::ReportSearchReports($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->report);
        }

        //-------------------------------------------------------------
        // Report: ReportFileDownload
        //-------------------------------------------------------------
        public function ReportFileDownload($file_id) {
            // $file_id : IDType (not array)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["file_id"] = $file_id;
            $this->encodeString($reg_args);
            $results = parent::ReportFileDownload($reg_args);
            $this->methodClose();
            return $results->file->content;
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetUnprocessedApplicationVersions
        //-------------------------------------------------------------
        public function WorkflowGetUnprocessedApplicationVersions($application_items) {
            // $application_items : CbgrnItemVersionType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["application_item"] = $this->getRegularArgs($application_items, __FUNCTION__, "CbgrnItemVersionType");
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetUnprocessedApplicationVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->application_item);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetUnprocessedApplicationsById
        //-------------------------------------------------------------
        public function WorkflowGetUnprocessedApplicationsById($application_ids) {
            // $application_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["application_id"] = $this->getRegularArgs($application_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetUnprocessedApplicationsById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->application);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetSentApplicationVersions
        //-------------------------------------------------------------
        public function WorkflowGetSentApplicationVersions($start, $end = false, $application_items = false) {
            // $start   : UNIX timestamp
            // $end     : UNIX timestamp (optional)
            // $application_items : CbgrnItemVersionType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["start"]      = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== false) {
                $reg_args["end"]    = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($application_items !== false) {
                $reg_args["application_item"] = $this->getRegularArgs($application_items, __FUNCTION__, "CbgrnItemVersionType");
            }
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetSentApplicationVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->application_item);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetSentApplicationsById
        //-------------------------------------------------------------
        public function WorkflowGetSentApplicationsById($application_ids) {
            // $application_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["application_id"] = $this->getRegularArgs($application_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetSentApplicationsById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->application);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetReceivedApplicationVersions
        //-------------------------------------------------------------
        public function WorkflowGetReceivedApplicationVersions($start, $end = false, $application_items = false) {
            // $start   : UNIX timestamp
            // $end     : UNIX timestamp (optional)
            // $application_items : CbgrnItemVersionType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["start"]      = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== false) {
                $reg_args["end"]    = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($application_items !== false) {
                $reg_args["application_item"] = $this->getRegularArgs($application_items, __FUNCTION__, "CbgrnItemVersionType");
            }
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetReceivedApplicationVersions($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->application_item);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetReceivedApplicationsById
        //-------------------------------------------------------------
        public function WorkflowGetReceivedApplicationsById($application_ids) {
            // $application_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["application_id"] = $this->getRegularArgs($application_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetReceivedApplicationsById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->application);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetApprovalDelegators
        //-------------------------------------------------------------
        public function WorkflowGetApprovalDelegators() {   // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            $results = parent::WorkflowGetApprovalDelegators();
            $this->methodClose();
            return $this->RetvalConvertArray($results->delegator_id);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetProxyApprovalsByDelegatorId
        //-------------------------------------------------------------
        public function WorkflowGetProxyApprovalsByDelegatorId($delegator_id, $start, $end = false) {
            // $delegator_id : IDType (only one)
            // $start        : UNIX timestamp
            // $end          : UNIX timestamp (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["delegator_id"]   = $delegator_id;
            $reg_args["start"]          = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== false) {
                $reg_args["end"]        = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetProxyApprovalsByDelegatorId($reg_args);
            $this->methodClose();
            return $this->decodeString($results->application);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetPendingApprovals
        //-------------------------------------------------------------
        public function WorkflowGetPendingApprovals($start, $end = false) {
            // $start : UNIX timestamp
            // $end   : UNIX timestamp (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["start"]  = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== false) {
                $reg_args["end"] = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetPendingApprovals($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->application);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowHandleApplications
        //-------------------------------------------------------------
        public function WorkflowHandleApplications(CbgrnWorkflowHandleApplicationOperationType $handle) {
            // $handle : CbgrnWorkflowHandleApplicationOperationType class (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["handle"] = $handle->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::WorkflowHandleApplications($reg_args);
            $this->methodClose();
            return $this->decodeString($results->application);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetProxies
        //-------------------------------------------------------------
        public function WorkflowGetProxies() {  // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            $results = parent::WorkflowGetProxies();
            $this->methodClose();
            return $this->decodeString($results->proxies);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowSetProxies
        //-------------------------------------------------------------
        public function WorkflowSetProxies($user_ids = false, $approver_ids = false, $applicant_ids = false) {
            // $user_ids : IDType or this array (if user ID is empty, set false)
            // $approver_ids : IDType or this array ( if user ID is empty, set false)
            // $applicant_ids : IDType or this array ( if user ID is empty, set false)
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($user_ids !== false) {
                if (is_array($user_ids)) {
                    $n = 0;
                    foreach ($user_ids as $user_id) {
                        $reg_args["proxies"]["user_proxy"][$n]["user_id"] = $user_id;
                        $n++;
                    }
                } else {
                    $reg_args["proxies"]["user_proxy"]["user_id"] = $user_ids;
                }
            }
            if ($approver_ids !== false) {
                if (is_array($approver_ids)) {
                    $n = 0;
                    foreach ($approver_ids as $approver_id) {
                        $reg_args["proxies"]["user_proxy"]["proxy_approver"][$n]["approver_id"] = $approver_id;
                        $n++;
                    }
                } else {
                    $reg_args["proxies"]["user_proxy"]["proxy_approver"]["approver_id"] = $approver_ids;
                }
            }
            if ($applicant_ids !== false) {
                if (is_array($applicant_ids)) {
                    $n = 0;
                    foreach ($applicant_ids as $applicant_id) {
                        $reg_args["proxies"]["user_proxy"]["proxy_applicant"][$n]["applicant_id"] = $applicant_id;
                        $n++;
                    }
                } else {
                    $reg_args["proxies"]["user_proxy"]["proxy_applicant"]["applicant_id"] = $applicant_ids;
                }
            }
            $this->encodeString($reg_args);
            $results = parent::WorkflowSetProxies($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetProfiles
        //-------------------------------------------------------------
        public function WorkflowGetProfiles() { // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            $results = parent::WorkflowGetProfiles();
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowFileDownload
        //-------------------------------------------------------------
        public function WorkflowFileDownload($file_id) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["file_id"]    = $file_id;
            $this->encodeString($reg_args);
            $results = parent::WorkflowFileDownload($reg_args);
            $this->methodClose();
            return $results->file->content;
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetRequests
        //-------------------------------------------------------------
        public function WorkflowGetRequests(CbgrnWorkflowGetRequestType $manage_request_parameter = NULL) {
            // $manager_request_parameter : CbgrnWorkfolowGetRequestType class (0 or 1)
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($manage_request_parameter !== NULL) {
                $reg_args["manage_request_parameter"] = $manage_request_parameter->getObjectVars();
            } else {
                $reg_args = NULL;
            }
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetRequests($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetRequestById
        //-------------------------------------------------------------
        public function WorkflowGetRequestById($request_ids) {
            // $request_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["request_id"] = $this->getRegularArgs($request_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetRequestById($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetAvailabilityUsers
        //-------------------------------------------------------------
        public function WorkflowGetAvailabilityUsers($user_ids) {
            // $user_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["user_id"] = $this->getRegularArgs($user_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetAvailabilityUsers($reg_args);
            $this->methodClose();
            return $this->decodeString($results->user);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetAttachedFileBody
        //-------------------------------------------------------------
        public function WorkflowGetAttachedFileBody($request_form_id, $file_id) {
            // $request_form_id : IDType
            // $file_id         : IDType
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["parameter"]["request_form_id"]   = $request_form_id;
            $reg_args["parameter"]["file_id"]           = $file_id;
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetAttachedFileBody($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetCategories
        //-------------------------------------------------------------
        public function WorkflowGetCategories() {   // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            $results = parent::WorkflowGetCategories();
            $this->methodClose();
            return $this->decodeString($results->root);
        }

        //-------------------------------------------------------------
        // Workflow: WorkflowGetRequestFormByCategoryIds
        //-------------------------------------------------------------
        public function WorkflowGetRequestFormByCategoryIds($category_ids) {
            // $category_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["category_id"] = $this->getRegularArgs($category_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::WorkflowGetRequestFormByCategoryIds($reg_args);
            $this->methodClose();
            return $this->decodeString($results->category);
        }

        //-------------------------------------------------------------
        // Star: StarGetStarVersions
        //-------------------------------------------------------------
        public function StarGetStarVersions($star_item = false) {
            // $star_item : CbgrnItemVersionType class or this array (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($star_item !== false) {
                $reg_args = $this->getRegularArgs($star_item, __FUNCTION__, "CbgrnItemVersionType");
            }
            $this->encodeString($reg_args);
            $results = parent::StarGetStarVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results->star_item);
        }

        //-------------------------------------------------------------
        // Star: StarGetStarsById
        //-------------------------------------------------------------
        public function StarGetStarsById($star_ids) {
            // $star_id : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["star_id"] = $this->getRegularArgs($star_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::StarGetStarsById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->star_data);
        }

        //-------------------------------------------------------------
        // Star: StarAddStars
        //-------------------------------------------------------------
        public function StarAddStars($module_id, $item, $date = NULL, $is_draft = NULL) {
            // $module_id : string (enum : grn.schedule, grn.mail, grn.message, grn.cabinet, grn.report, grn.bulletin)
            // $item      : item ID (IDType?)
            // $date      : UNIX timestamp (if module_id is 'grn.schedule' set target date)
            // $is_draft  : boolean (if you wish set stars on draft item, set is_draft=true)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["star_item"]["module_id"]     = $module_id;
            $reg_args["star_item"]["item"]          = $item;
            if ($date !== NULL) {
                $reg_args["star_item"]["date"]      = gmdate(W3C_DATETIME_FORMAT, $date);
            }
            if ($is_draft !== NULL) {
                $reg_args["star_item"]["is_draft"]  = $is_draft;
            }
            $this->encodeString($reg_args);
            $results = parent::StarAddStars($reg_args);
            $this->methodClose();
            return $this->decodeString($results->star_data);
        }

        //-------------------------------------------------------------
        // Star: StarRemoveStars
        //-------------------------------------------------------------
        public function StarRemoveStars($module_id, $item, $date = NULL, $is_draft = NULL) {
            // $module_id : string (enum : grn.schedule, grn.mail, grn.message, grn.cabinet, grn.report, grn.bulletin)
            // $item      : item ID (IDType?)
            // $date      : UNIX timestamp (if module_id is 'grn.schedule' set target date)
            // $is_draft  : boolean (if you wish set stars on draft item, set is_draft=true)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["star_item"]["module_id"]     = $module_id;
            $reg_args["star_item"]["item"]          = $item;
            if ($date !== NULL) {
                $reg_args["star_item"]["date"]      = gmdate(W3C_DATETIME_FORMAT, $date);
            }
            if ($is_draft !== NULL) {
                $reg_args["star_item"]["is_draft"]  = $is_draft;
            }
            $this->encodeString($reg_args);
            $results = parent::StarRemoveStars($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Star: StarGetProfiles
        //-------------------------------------------------------------
        public function StarGetProfiles() { // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            $results = parent::StarGetProfiles();
            $this->methodClose();
            return $this->decodeString($results->star_num_allow);
        }

        //-------------------------------------------------------------
        // Notification: NotificationGetNotificationVersions
        //-------------------------------------------------------------
        public function NotificationGetNotificationVersions($notification_items = NULL, $start, $end = NULL, $module_id = NULL) {
            // $notification_items : CbgrnNotificationItemVersionType class or this array
            // $start              : UNIX timestamp
            // $end                : UNIX timestamp (optional)
            // $module_id          : string (enum : grn.schedule, grn.message ....)
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($notification_items !== NULL) {
                $reg_args["notification_item"] = $this->getRegularArgs($notification_items, __FUNCTION__, "CbgrnNotificationItemVersionType");
            }
            $reg_args["start"]      = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== NULL) {
                $reg_args["end"]    = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($module_id !== NULL) {
                $reg_args["module_id"]  = $module_id;
            }
            $this->encodeString($reg_args);
            $results = parent::NotificationGetNotificationVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results->notification_item);
        }

        //-------------------------------------------------------------
        // Notification: NotificationGetNotificationsById
        //-------------------------------------------------------------
        public function NotificationGetNotificationsById(CbgrnNotificationIdType $notification_id) {
            // $notification_id : CbgrnNotificationIdType class (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["notification_id"]    = $notification_id;
            $this->encodeString($reg_args);
            $results = parent::NotificationGetNotificationsById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->notification);
        }

        //-------------------------------------------------------------
        // Notification: NotificationGetNotificationHistoryVersions
        //-------------------------------------------------------------
        public function NotificationGetNotificationHistoryVersions($notification_history_item = NULL, $start, $end = NULL, $module_id = NULL) {
            // $notification_hisotory_item : CbgrnNotificationItemVersionType class (only one)
            // $start                      : UNIX timestamp
            // $end                        : UNIX timestamp (optional)
            // $module_id                  : string ( grn.schedule, grn.message ...)
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($notification_history_item !== NULL) {
                $reg_args["notification_history_item"] = $notification_history_item->getObjectVars();
            }
            $reg_args["start"]  = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== NULL) {
                $reg_args["end"] = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($module_id !== NULL) {
                $reg_args["module_id"] = $module_id;
            }
            $this->encodeString($reg_args);
            $results = parent::NotificationGetNotificationHistoryVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results->notification_history_item);
        }

        //-------------------------------------------------------------
        // Notification: NotificationGetNotificationHistoriesById
        //-------------------------------------------------------------
        public function NotificationGetNotificationHistoriesById($notification_history_id) {
            // $notification_history_id : CbgrnNotificationIdType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["notification_history_id"] = $this->getRegularArgs($notification_history_id, __FUNCTION__, "CbgrnNotificationIdType");
            $this->encodeString($reg_args);
            $results = parent::NotificationGetNotificationHistoriesById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->notification_history);
        }

        //-------------------------------------------------------------
        // Notification: NotificationConfirmNotification
        //-------------------------------------------------------------
        public function NotificationConfirmNotification($notification_id) {
            // $notification_id : CbgrnNotificationIdType class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["notification_id"] = $this->getRegularArgs($notification_id, __FUNCTION__, "CbgrnNotificationIdType");
            $this->encodeString($reg_args);
            $results = parent::NotificationConfirmNotification($reg_args);
            $this->methodClose();
            return $this->decodeString($results->notification);
        }

        //-------------------------------------------------------------
        // Notification: NotificationGetProfiles
        //-------------------------------------------------------------
        public function NotificationGetProfiles() {     // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::NotificationGetProfiles();
            $this->methodClose();
            return $this->decodeString($results->personal_profile);
        }

        //-------------------------------------------------------------
        // Notification: NotificationSetProfiles
        //-------------------------------------------------------------
        public function NotificationSetProfiles($save_notification_duration = NULL, $save_notification_history_duration = NULL) {
            // $save_notification_duration : integer
            // $save_notification_history_duration : integer
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["personal_profile"]["save_notification_duration"] = $save_notification_duration;
            $reg_args["personal_profile"]["save_notification_history_duration"] = $save_notification_history_duration;
            $this->encodeString($reg_args);
            $results = parent::NotificationSetProfiles($reg_args);
            $this->methodClose();
            return $this->decodeString($results->personal_profile);
        }

        //-------------------------------------------------------------
        // Mail: MailGetAccountVersions
        //-------------------------------------------------------------
        public function MailGetAccountVersions($account_items = NULL) {
            // $account_items : CbgrnItemVersionType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            if ($account_items !== NULL) {
                $reg_args["account_item"] = $this->getRegularArgs($account_items, __FUNCTION__, "CbgrnItemVersionType");
            }
            $this->encodeString($reg_args);
            $results = parent::MailGetAccountVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results->account_item);
        }

        //-------------------------------------------------------------
        // Mail: MailGetAccountsById
        //-------------------------------------------------------------
        public function MailGetAccountsById($account_ids) {
            // $account_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["account_id"] = $this->getRegularArgs($account_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::MailGetAccountsById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->account);
        }

        //-------------------------------------------------------------
        // Mail: MailGetMailVersions
        //-------------------------------------------------------------
        public function MailGetMailVersions($start, $end = NULL, $mail_items = NULL, $folder_ids = NULL) {
            // $start : UNIX timestamp
            // $end   : UNIX timestamp (optional)
            // $mail_items : CbgrnItemVersionType or this array (optional)
            // $folder_ids : IDType or this array (optional)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["start"]      = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== NULL) {
                $reg_args["end"]    = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            if ($mail_items !== NULL) {
                $reg_args["mail_item"]  = $this->getRegularArgs($mail_items, __FUNCTION__, "CbgrnItemVersionType");
            }
            if ($folder_ids !== NULL) {
                $reg_args["folder_id"]  = $folder_ids;
            }
            $this->encodeString($reg_args);
            $results = parent::MailGetMailVersions($reg_args);
            $this->methodClose();
            return $this->decodeString($results->mail_item);
        }

        //-------------------------------------------------------------
        // Mail: MailGetMailsById
        //-------------------------------------------------------------
        public function MailGetMailsById($mail_ids) {
            // $mail_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["mail_id"] = $this->getRegularArgs($mail_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::MailGetMailsById($reg_args);
            $this->methodClose();
            return $this->decodeString($results->mail);
        }

        //-------------------------------------------------------------
        // Mail: MailSendMails
        //-------------------------------------------------------------
        public function MailSendMails(CbgrnMailSendMailType $send_mail) {
            // $send_mails  : CbgrnMailSendMailType class (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["send_mail"] = $send_mail->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::MailSendMails($reg_args);
            $this->methodClose();
            return $this->decodeString($results->mail);
        }

        //-------------------------------------------------------------
        // Mail: MailReplyMails
        //-------------------------------------------------------------
        public function MailReplyMails(CbgrnMailSendMailType $reply_mail, $reply_all = false) {
            // $reply_mail : CbgrnMailSendMailType class (only one)
            // $reply_all  : boolean (true:reply all recipient, false, reply only sender)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["reply_mail"] = $reply_mail->getObjectVars();
            $reg_args["reply_all"]  = $reply_all;
            $this->encodeString($reg_args);
            $results = parent::MailReplyMails($reg_args);
            $this->methodClose();
            return $this->decodeString($results->mail);
        }

        //-------------------------------------------------------------
        // Mail: MailForwardMails
        //-------------------------------------------------------------
        public function MailForwardMails(CbgrnMailForwardMailType $forward_mail = NULL) {
            // $forward_mail : CbgrnMailForwardMailType class (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["forward_mail"]   = $forward_mail->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::MailForwardMails($reg_args);
            $this->methodClose();
            return $this->decodeString($results->mail);
        }

        //-------------------------------------------------------------
        // Mail: MailSaveDraftMails
        //-------------------------------------------------------------
        public function MailSaveDraftMails(CbgrnMailDraftMailType $save_draft) {
            // $save_draft : CbgrnMailDraftMailType class (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["save_mail"]     = $save_draft->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::MailSaveDraftMails($reg_args);
            $this->methodClose();
            return $this->decodeString($results->mail);
        }

        //-------------------------------------------------------------
        // Mail: MailRemoveMails
        //-------------------------------------------------------------
        public function MailRemoveMails($mail_ids) {
            // mail_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["mail_id"] = $this->getRegularArgs($mail_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::MailRemoveMails($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Mail: MailSearchMails
        //-------------------------------------------------------------
        public function MailSearchMails($text, $start, $end = NULL, $end = NULL, $search_all_accounts = true, $account_id = NULL, $folder_id = NULL, $search_sub_folders = true, $title_search = true, $body_search = true, $from_search = true, $to_search = true, $cc_search = true, $bcc_search = true) {
            // $text    : string
            // $start   : UNIX timestamp
            // $end     : UNIX timestamp (optional)
            // $search_all_accounts : boolean (default = true)
            // $account_id      : IDType (if $search_all_account is false, set this. otherwise set NULL)
            // $folder_id       : IDType (same above)
            // $search_sub_folders : boolean (default true)
            // $title_search    : boolean (default true)
            // $body_search     : boolean (default true)
            // $from_search     : boolean (default true)
            // $to_search       : boolean (default true)
            // $cc_search       : boolean (default true)
            // $bcc_search      : boolean (default true)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["text"]       = $text;
            $reg_args["start"]      = gmdate(W3C_DATETIME_FORMAT, $start);
            if ($end !== NULL) {
                $reg_args["end"]    = gmdate(W3C_DATETIME_FORMAT, $end);
            }
            $reg_args["search_all_accounts"] = $search_all_accounts;
            if ($account_id !== NULL) {
                $reg_args["account_id"]     = $account_id;
            }
            if ($folder_id !== NULL) {
                $reg_args["folder_id"]      = $folder_id;
            }
            $reg_args["search_sub_folders"] = $search_sub_folders;
            $reg_args["title_search"]       = $title_search;
            $reg_args["body_search"]        = $body_search;
            $reg_args["from_search"]        = $from_search;
            $reg_args["to_search"]          = $to_search;
            $reg_args["cc_search"]          = $cc_search;
            $reg_args["bcc_search"]         = $bcc_search;
            $this->encodeString($reg_args);
            $results = parent::MailSearchMails($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->mail);
        }

        //-------------------------------------------------------------
        // Mail: MailOpenDispositionNotifications
        //-------------------------------------------------------------
        public function MailOpenDispositionNotifications($account_id, $mail_id, $type = "open") {
            // $account_id  :   IDType
            // $mail_id     :   IDType
            // $type        :   string (enum 'open' or 'ignore')
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["operation"]["account_id"] = $account_id;
            $reg_args["operation"]["mail_id"]    = $mail_id;
            $reg_args["operation"]["type"]       = $type;
            $this->encodeString($reg_args);
            $results = parent::MailOpenDispositionNotifications($reg_args);
            $this->methodClose();
            return $this->decodeString($results->mail);
        }

        //-------------------------------------------------------------
        // Mail: MailAddFolders
        //-------------------------------------------------------------
        public function MailAddFolders(CbgrnMailModifyFolderOperationType $add_folder) {
            // $add_folder : CbgrnMailModifyFolderOperationType class (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["add_folder"] = $add_folder->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::MailAddFolders($reg_args);
            $this->methodClose();
            return $this->decodeString($results->folder);
        }

        //-------------------------------------------------------------
        // Mail: MailModifyFolders
        //-------------------------------------------------------------
        public function MailModifyFolders(CbgrnMailModifyFolderOperationType $modify_folder) {
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["modify_folder"]  = $modify_folder->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::MailModifyFolders($reg_args);
            $this->methodClose();
            return $this->decodeString($results->folder);
        }

        //-------------------------------------------------------------
        // Mail: MailRemoveFolders
        //-------------------------------------------------------------
        public function MailRemoveFolders($folder_ids) {
            // $folder_ids : IDType or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["folder_id"] = $this->getRegularArgs($folder_ids, __FUNCTION__);
            $this->encodeString($reg_args);
            $results = parent::MailRemoveFolders($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        // template
        //-------------------------------------------------------------
        // Mail: MailMoveMailsToOtherFolder
        //-------------------------------------------------------------
        public function MailMoveMailsToOtherFolder($folder_id, $mail_id) {
            // $folder_id   : IDType
            // $mail_id     : IDType
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["operation"]["folder_id"] = $folder_id;
            $reg_args["operation"]["mail_id"]   = $mail_id;
            $this->encodeString($reg_args);
            $results = parent::MailMoveMailsToOtherFolder($reg_args);
            $this->methodClose();
            return $this->decodeString($results->mail);
        }

        //-------------------------------------------------------------
        // Mail: MailGetSignatures
        //-------------------------------------------------------------
        public function MailGetSignatures($account_id) {
            // $account_id  : IDType (mail account ID)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["account_id"] = $account_id;
            $this->encodeString($reg_args);
            $results = parent::MailGetSignatures($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->signature);
        }

        //-------------------------------------------------------------
        // Mail: MailGetFilters
        //-------------------------------------------------------------
        public function MailGetFilters($account_id) {
            // $account_id  : IDType (mail account ID)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["account_id"] = $account_id;
            $this->encodeString($reg_args);
            $results = parent::MailGetFilters($reg_args);
            $this->methodClose();
            return $this->RetvalConvertArray($results->filter);
        }

        //-------------------------------------------------------------
        // Mail: MailGetProfiles
        //-------------------------------------------------------------
        public function MailGetProfiles($include_system_profile = true) {
            // $include_system_profile : boolean (default true)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["include_system_profile"]     = $include_system_profile;
            $this->encodeString($reg_args);
            $results = parent::MailGetProfiles($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Mail: MailSetProfiles
        //-------------------------------------------------------------
        public function MailSetProfiles(CbgrnMailPersonalProfileType $personal_profile) {
            // $personal_profile : CbgrnMailPersonalProfileType class (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["personal_profile"]   = $personal_profile;
            $this->encodeString($reg_args);
            $results = parent::MailSetProfiles($reg_args);
            $this->methodClose();
            return $this->decodeString($results->personal_profile);
        }

        //-------------------------------------------------------------
        // Mail: MailSourceDownload
        //-------------------------------------------------------------
        public function MailSourceDownload($mail_id) {
            // $mail_id : IDType (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["mail_id"]    = $mail_id;
            $this->encodeString($reg_args);
            $results = parent::MailSourceDownload($reg_args);
            $this->methodClose();
            return $results->source->content;
        }

        //-------------------------------------------------------------
        // Mail: MailFileDownload
        //-------------------------------------------------------------
        public function MailFileDownload($mail_id, $file_id) {
            // $mail_id : IDType (only one)
            // $file_id : IDType (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["mail_id"]    = $mail_id;
            $reg_args["file_id"]    = $file_id;
            $this->encodeString($reg_args);
            $results = parent::MailFileDownload($reg_args);
            $this->methodClose();
            return $results->file->content;
        }

        //-------------------------------------------------------------
        // Mail: MailAddMailServers
        //-------------------------------------------------------------
        public function MailAddMailServers(CbgrnMailServerInfoType $server) {
            // $server : CbgrnMailServerInfoType class (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["server"] = $server->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::MailAddMailServers($reg_args);
            $this->methodClose();
            return $this->decodeString($results->server);
        }

        //-------------------------------------------------------------
        // Mail: MailModifyMailServers
        //-------------------------------------------------------------
        public function MailModifyMailServers(CbgrnMailServerInfoType $server) {
            // $server : CbgrnMailServerInfoType class (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["server"] = $server->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::MailModifyMailServers($reg_args);
            $this->methodClose();
            return $this->decodeString($results->server);
        }

        //-------------------------------------------------------------
        // Mail: MailRemoveMailServers
        //-------------------------------------------------------------
        public function MailRemoveMailServers($server_id) {
            // $server_id : IDType (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["server_id"]  = $server_id;
            $this->encodeString($reg_args);
            $results = parent::MailRemoveMailServers($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Mail: MailCreateUserAccount
        //-------------------------------------------------------------
        public function MailCreateUserAccount(CbgrnMailUserAccountType $mail_user_account) {
            // $mail_user_account : CbgrnMailUserAccountType class (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["mail_user_accounts"] = $mail_user_account->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::MailCreateUserAccount($reg_args);
            $this->methodClose();
            return $this->decodeString($results->user_accounts);
        }

        //-------------------------------------------------------------
        // Mail: MailEditUserAccount
        //-------------------------------------------------------------
        public function MailEditUserAccount(CbgrnMailUserAccountType $edit_user_account) {
            // $edit_user_account : CbgrnMailUserAccoutType class (only one)
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["edit_user_accounts"] = $edit_user_account->getObjectVars();
            $this->encodeString($reg_args);
            $results = parent::MailEditUserAccount($reg_args);
            $this->methodClose();
            return $this->decodeString($results->edit_user_accounts);
        }

        //-------------------------------------------------------------
        // Mail: MailDeleteUserAccount
        //-------------------------------------------------------------
        public function MailDeleteUserAccount($delete_user_accounts) {
            // $delete_user_accounts : CbgrnDeleteUserAccount class or this array
            $this->CheckAndSetHeader(__FUNCTION__);
            $reg_args["delete_user_accounts"] = $this->getRegularArgs($delete_user_accounts, __FUNCTION__, "CbgrnDeleteUserAccount");
            $this->encodeString($reg_args);
            $results = parent::MailDeleteUserAccount($reg_args);
            $this->methodClose();
            return $this->decodeString($results);
        }

        //-------------------------------------------------------------
        // Mail: MailGetNewArrivingEmail
        //-------------------------------------------------------------
        public function MailGetNewArrivingEmail() { // no argument
            $this->CheckAndSetHeader(__FUNCTION__);
            $results = parent::MailGetNewArrivingEmail();
            $this->methodClose();
            return $this->retvalConvertArray($results->account);
        }

    }   // end of class

}   // end of this include file. (if (!defined(...)))
?>