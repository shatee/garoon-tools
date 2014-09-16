<?php
//======================================================================
// File:            CbgrnSoapAPI_inc.php
// Description:     Configration file for Cybozu Garoon Soap API library
// history:
//                  Version 0.1     2013-06-03
// Copyright (c)    Yujiro Shiwaku [Hokoku Kogyo Co.,Ltd.]
//                  Yoshinao Ohoshi
//======================================================================
//
//----------------------------------------------------------------------
// Location or Type of Cybozu Garoon Server
//----------------------------------------------------------------------
define('CBGRNAPI_SERVER_NAME', \GaroonTools\Conf\Garoon::SERVER_FQDN);    // Cybozu Garoon Server FQDN.
define('CBGRNAPI_SERVER_IS_UNIX', true);                       // Cybozu Garoon Server Type:
                                                                        // Linux: true
                                                                        // Windows: false
define('CBGRNAPI_SERVER_IS_APACHE', true);                     // if Cybozu Garoon Server is Windows
                                                               // HTTPserver is Apache : true
                                                               //            is IIS    : false
define('CBGRNAPI_IDENTIFIER', 'cbgrn');                // Cybozu Garoon install identifier. default as 'cbgrn'
//-----------------------------------------------------------------------
// Cybozu Garoon API Authentications
//-----------------------------------------------------------------------
define('CBGRNAPI_TYPE_OF_SECURITY', 1);                 // Security type: if you use WS-Security: 1
                                                        //                           Cookie:      2
                                                        // Sorry now only ws-security is impremented
define('CBGRNAPI_LOGIN_USER', 'Administrator');         //  set default administrate user name/password
define('CBGRNAPI_LOGIN_PASSWORD', 'password');            //   you can change usr/pass when you use CybozuSoap class
//-----------------------------------------------------------------------
// Localization
//-----------------------------------------------------------------------
define('CBGRNAPI_ERROR_LANG', 'jp');                    // 'jp':you will get error message as japanese.
                                                        // 'en':                           as english.
//-----------------------------------------------------------------------
// Networks, proxy and SOAP settings
//-----------------------------------------------------------------------
define('CBGRNAPI_USE_PROXY', false);                    // if you use proxy set true.
define('CBGRNAPI_PROXY_NAME', 'proxy.yourhost.com');    // PROXY host name or address.
define('CBGRNAPI_PROXY_PORT', '8080');                  // PROXY port.
define('CBGRNAPI_PROXY_USER', 'username');              // if your proxy needs user/pass, set this
define('CBGRNAPI_PROXY_PASS', 'password');              //    proxy password.
                                                        // otherwise set '' to username,password
define('CBGRNAPI_HTTPS_LOCAL_CERT','');                 // NOT imprementation.
define('CBGRNAPI_HTTPS_PASSPHRASE', '');                // NOT imprementation.
define('CBGRNAPI_HTTPS_AUTHENTICATION', '');            // NOT imprementation.
define('SOAP_TRACE',    true);                          // PHP SOAP option. if you need __getLastRequest() etc
                                                        // set true, otherwise set false
define('SOAP_CACHE_WSDL', WSDL_CACHE_BOTH);             // php soap option. you can choose and set
                                                        //      WSDL_CACHE_NONE
                                                        //      WSDL_CACHE_DISK
                                                        //      WSDL_CACHE_MEMORY
                                                        //      WSDL_CACHE_BOTH
//------------------------------------------------------------------------
// Other constants
//------------------------------------------------------------------------
define('CBGRNAPI_THIS_VERSION', 0.1);                  // this API's version.
define('CBGRNAPI_VERSION', '1_2_0');                   // now Cybozu Garoon API version is 1.2.0 2013-06-03

define("W3C_DATETIME_FORMAT", "Y-m-d\TH:i:sP");
//define("W3C_DATE_FORMAT", "Y-m-d");                   // no use

//------------------------------------------------------------------------
//
//------------------------------------------------------------------------

define('CBGRNAPI_PROTOCOL', 'https'); // http or https
?>