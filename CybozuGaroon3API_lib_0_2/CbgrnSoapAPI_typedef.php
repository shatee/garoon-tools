<?php
//====================================================================
// File:            CbgrnSoapAPI_typedef.php
// Description:     Cybozu Garoon (3) API access class library
//                  Type definition of API access data.
// histroy:
//                  Version 0.1     2013-06-04
// Copyright (c)    Yujiro Shiwaku [Hokoku Kogyo Co.,Ltd.]
//                  Yoshinao Ohoshi
//====================================================================

    // base class of cybozu garoon API accessing
    //  several type of API are sub class of this
    class CybozuParam {
        public function __construct() {
        }
        public function _check() {
        }
        public function _get_stdClass($c) {
        }
        public function _print() {
        }
        public function _expand() {
        }
    }
    
    class CbgrnItemVersionType {
        public $id;         // type:IDType == Non blank string
        public $version;    // type:VersionType == Non blank string;

        public function __construct($id, $version = false) {
            if (is_object($id) && (get_class($id) == "stdClass")) {
                $this->setFromStdClass($id);
            } else {
                $this->id = $id;
                if ($version === false) {
                    $this->version = 1;
                } else {
                    $this->version = $version;
                }
            }
        }
        public function _check() {
            if (strlen($this->id) == 0) {
                throw new SoapFault("998", "id is not set in ItemVersionType");
            }
            if (strlen($this->version) == 0) {
                throw new SoapFault("998", "version is not set in ItemVersionType");
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->id   = $std_class->id;
            $this->version = $std_class->version;
        }
        
        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]      = $this->id;
            $ret_val["version"] = $this->version;
            return $ret_val;
        }
    }

    class CbgrnBaseManagerApplicationType
    {
        public $code;       // type: xsd:string
        public $active;     // type: xsd:boolean

        public function __construct($code = false, $status = false) {
            if ($code === false) {
                ;
            } else {
                if (is_object($code) && (get_class($code) == "stdClass")) {
                    $this->setFromStdClass($code);
                } else {
                    $this->code = $code;
                    $this->active = ($status == true) ? true : false;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->code = $std_class->code;
            $this->active = ($std_class->active) ? true : false;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["code"]    = $this->code;
            $ret_val["active"]  = $this->active;
            return $ret_val;
        }
    }

    class CbgrnBaseFileType
    {
        public $content         = NULL;     // base64Binary (base64 decoded)
        public $id              = NULL;     // IDType (string := integer)
        public $version         = NULL;     // VersionType (string)
        public $name            = NULL;     // string
        public $size            = NULL;     // integer
        public $mime_type       = NULL;     // string

        public function __construct ($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->content)) {
                $this->content  = $std_class->content;
            }
            if (isset($std_class->id)) {
                $this->id       = $std_class->id;
            }
            if (isset($std_class->version)) {
                $this->version  = $std_class->version;
            }
            if (isset($std_class->name)) {
                $this->name     = $std_class->name;
            }
            if (isset($std_class->size)) {
                $this->size     = $std_class->size;
            }
            if (isset($std_class->mime_type)) {
                $this->mime_type = $std_class->mime_type;
            }
        }

        function getObjectVars() {
            $ret_val = Array();
            if ($this->content !== NULL) {
                $ret_val["content"] = $this->content;
            }
            if ($this->id !== NULL) {
                $ret_val["id"]      = strval($this->id);
            }
            if ($this->version !== NULL) {
                $ret_val["version"] = strval($this->version);
            }
            if ($this->name !== NULL) {
                $ret_val["name"]    = strval($this->name);
            }
            if ($this->size !== NULL) {
                $ret_val["size"]    = $this->size;
            }
            if ($this->mime_type !== NULL) {
                $ret_val["mime_type"]   = strval($this->mime_type);
            }
            return $ret_val;
        }
    }
    
    class CbgrnUserInfoType {
        public $primary_group   = NULL;     // IDType (string := integer)
        public $position        = NULL;     // integer
        public $invalid         = NULL;     // boolean
        public $sort_key        = NULL;     // string 
        public $email_address   = NULL;     // string 
        public $description     = NULL;     // string 
        public $post            = NULL;     // string 
        public $telephone_number = NULL;    // string 
        public $url             = NULL;     // string 
        public $locale          = NULL;     // IDType (string := integer) Locale ID
        public $base            = NULL;     // IDType (string := integer) 
        public $image           = NULL;     // CbgrnBaseFileType class
        public $organization    = NULL;     // integer or this array (<- organization ID?)

        public function __construct ($arg = false) {
            if ($arg === false) {
                ;
            } else {
                $this->setFromStdClass($arg);
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->primary_group)) {
                $this->primary_group    = $std_class->primary_group;
            }
            if (isset($std_class->position)) {
                $this->position         = $std_class->position;
            }
            if (isset($std_class->invalid)) {
                $this->invalid          = $std_class->invalid;
            }
            if (isset($std_class->sort_key)) {
                $this->sort_key         = $std_class->sort_key;
            }
            if (isset($std_class->email_address)) {
                $this->email_address    = $std_class->email_address;
            }
            if (isset($std_class->description)) {
                $this->description      = $std_class->description;
            }
            if (isset($std_class->post)) {
                $this->post             = $std_class->post;
            }
            if (isset($std_class->telephone_number)) {
                $this->telephone_number = $std_class->telephone_number;
            }
            if (isset($std_class->url)) {
                $this->url              = $std_class->url;
            }
            if (isset($std_class->locale)) {
                $this->locale           = $std_class->locale;
            }
            if (isset($std_class->base)) {
                $this->base             = $std_class->base;
            }
            if (isset($std_class->image)) {
                $this->image            = new CbgrnBaseFileType($std_class->image);
            }
            if (isset($std_class->organization)) {
                $this->organization     = $std_class->organization;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->primary_group !== NULL) {
                $ret_val["primary_group"] = strval($this->primary_group);
            }
            if ($this->position !== NULL) {
                $ret_val["position"]    = strval($this->position);
            }
            if ($this->invalid !== NULL) {
                $ret_val["invalid"]     = ($this->invalid == true) ? true : false;
            }
            if ($this->sort_key !== NULL) {
                $ret_val["sort_key"]    = strval($this->sort_key);
            }
            if ($this->email_address !== NULL) {
                $ret_val["email_address"] = strval($this->email_address);
            }
            if ($this->description !== NULL) {
                $ret_val["description"] = strval($this->description);
            }
            if ($this->post !== NULL) {
                $ret_val["post"]        = strval($this->post);
            }
            if ($this->telephone_number !== NULL) {
                $ret_val["telephone_number"] = strval($this->telephone_number);
            }
            if ($this->url !== NULL) {
                $ret_val["url"]         = strval($this->url);
            }
            if ($this->locale !== NULL) {
                $ret_val["locale"]      = strval($this->locale);
            }
            if ($this->base !== NULL) {
                $ret_val["base"]        = strval($this->base);
            }
            if ($this->image !== NULL) {
                if (is_object($this->image) && (get_class($this->image) == "CbgrnBaseFileType")) {
                    $ret_val["image"]       = $this->image->getObjectVars();
                }
            }
            if ($this->organization !== NULL) {
                $ret_val["organization"]    = $this->organization;
            }
            
            return $ret_val;
        }
    }
    
    class CbgrnChangeLogType
    {
        public $user_id;            // == IdType == string = integer
        public $name;               // == string
        public $date;               // == UNIX Timestamp (convert from xsd:dateTime)

        public function __construct($arg = false, $name = false, $date = false) {
            // $arg : 1. false = no action
            //      : 2. stdClass object = set from stdClass
            //      : 3. user ID, user NAME, (date optional)
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->user_id  = $arg;
                    $this->name = $name;
                    if ($date !== false) {
                        $this->date     = $date;
                    } else {
                        $this->date     = time();
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->user_id  = $std_class->user_id;
            $this->name     = $std_class->name;
            $this->date     = strtotime($std_class->date);
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["user_id"] = $this->user_id;
            $ret_val["name"]    = $this->name;
            $ret_val["date"]    = gmdate(W3C_DATETIME_FORMAT, $this->date);
            return $ret_val;
        }
    }
    
    class CbgrnMemberType
    {
        public $id;                 // == base:IdType == String
        public $order = NULL;       // == base:IdType == String ???? integer?
        public $type = NULL;        // enum ("user", "organization", "facility")
        
        public function __construct($arg = false, $order = false, $type = false) {
            if ($arg === false ) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else if (($arg !== false) &&
                           ($order !== false) &&
                           ($type !== false)) {
                    $this->id = $arg;
                    $this->order = $order;
                    $this->type = $type;
                } else {
                    ;
                }
            }
        }
        
        public function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->user)) {
                $arg_tmp = $std_class->user;
                $this->type = "user";
            } else if (isset($std_class->organization)) {
                $arg_tmp = $std_class->organization;
                $this->type = "organization";
            } else {
                $arg_tmp = $std_class->facility;
                $this->type = "facility";
            }
            $this->id   = $arg_tmp->id;
            if (isset($arg_tmp->order)) {
                $this->order = $arg_tmp->order;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->type === NULL) return false;
            $ret_val[$this->type]["id"]      = $this->id;
            if ($this->order !== NULL) {
                $ret_val[$this->type]["order"]   = $this->order;
            }
            return $ret_val;
        }
        
    }
    
    class CbgrnCustomer
    {
        public $name    = NULL;         // == string
        public $zipcode = NULL;         // == string
        public $address = NULL;         // == string
        public $map     = NULL;         // == string (probably map's URL)
        public $route   = NULL;         // == string
        public $route_time = NULL;      // == string
        public $route_fare = NULL;      // == string
        public $phone   = NULL;         // == string

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->name)) {
                $this->name = $std_class->name;
            }
            if (isset($std_class->zipcode)) {
                $this->zipcode = $std_class->zipcode;
            }
            if (isset($std_class->address)) {
                $this->address = $std_class->address;
            }
            if (isset($std_class->map)) {
                $this->map = $std_class->map;
            }
            if (isset($std_class->route)) {
                $this->route = $std_class->route;
            }
            if (isset($std_class->route_time)) {
                $this->route_time = $std_class->route_time;
            }
            if (isset($std_class->route_fare)) {
                $this->route_fare = $std_class->route_fare;
            }
            if (isset($std_class->phone)) {
                $this->phone = $std_class->phone;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->name !== NULL) {
                $ret_val["name"]        = $this->name;
            }
            if ($this->zipcode !== NULL) {
                $ret_val["zipcode"]     = $this->zipcode;
            }
            if ($this->address !== NULL) {
                $ret_val["address"]     = $this->address;
            }
            if ($this->map !== NULL) {
                $ret_val["map"]         = $this->map;
            }
            if ($this->route !== NULL) {
                $ret_val["route"]       = $this->route;
            }
            if ($this->route_time !== NULL) {
                $ret_val["route_time"]  = $this->route_time;
            }
            if ($this->route_fare !== NULL) {
                $ret_val["route_fare"]  = $this->route_fare;
            }
            if ($this->phone !== NULL) {
                $ret_val["phone"]       = $this->phone;
            }
            return $ret_val;
        }
        
    }
    
    class CbgrnRepeatInfoCondition
    {
        public $type;                   // string (enum)
        public $start_date;             // local "DAY"  (string yyyy-mm-dd)
        public $end_date;               // local "DAY"  (string yyyy-mm-dd)
        public $start_time;             // local "TIME" (string hh:mm:dd)
        public $end_time;               // local "TIME" (string hh:mm:dd)
        public $day;                    // integer (day of month)
        public $week;                   // integer (0=Sun....6=Sat)

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->type         = $std_class->type;
            $this->start_date   = $std_class->start_date;
            $this->end_date     = $std_class->end_date;
            $this->start_time   = $std_class->start_time;
            $this->end_time     = $std_class->end_time;
            $this->day          = $std_class->day;
            $this->week         = $std_class->week;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["type"]        = $this->type;
            $ret_val["start_date"]  = $this->start_date;
            $ret_val["end_date"]    = $this->end_date;
            $ret_val["start_time"]  = $this->start_time;
            $ret_val["end_time"]    = $this->end_time;
            $ret_val["day"]         = $this->day;
            $ret_val["week"]        = $this->week;
            return $ret_val;
        }
        
    }
    
    class CbgrnEventDateTimeType
    {
        public $start;                  // == timestamp (xsd:DateTime to UNIX ts converted)
        public $end;                    // == timestamp (xsd:DateTime to UNIX ts converted)
        public $facility_id = NULL;     // == base:IdType = string = integer

        public function __construct ($arg = false, $end = false, $facility_id = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->start = $arg;
                    $this->end   = $end;
                    if ($facility_id !== false) {
                        $this->facility_id = $facility_id;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->start    = strtotime($std_class->start);
            $this->end      = strtotime($std_class->end);
            if (isset($std_class->facility_id)) {
                $this->facility_id = $std_class->facility_id;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["start"]       = gmdate(W3C_DATETIME_FORMAT, $this->start);
            $ret_val["end"]         = gmdate(W3C_DATETIME_FORMAT, $this->end);
            if ($this->facility_id !== NULL) {
                $ret_val["facility_id"] = $this->facility_id;
            }
            return $ret_val;
        }
    }
    
    class CbgrnEventDateType
    {
        public $start;                  // == local DATE (string yyyy-mm-dd)
        public $end;                    // == local DATE (string yyyy-mm-dd)

        public function __construct ($arg = false, $end = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->start = $arg;
                    $this->end   = $end;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->start    = $std_class->start;
            $this->end      = $std_class->end;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["start"]       = $this->start;
            $ret_val["end"]         = $this->end;
            return $ret_val;
        }
    }
    
    class CbgrnRepeatInfo
    {
        public $condition;                 // == CbgrnRepeatInfoCondition class
        public $exclusive_datetime = NULL; // == Array of CbgrnEventDateType class

        public function __construct($arg = false, $exclusive_datetime = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg)) {
                    switch (get_class($arg)) {
                    case "stdClass":
                        $this->setFromStdClass($arg);
                        break;
                    case "CbgrnRepeatInfoCondition":
                        $this->condition = $arg;
                        if (is_array($exclusive_datetime)) {
                            $this->exclusive_datetime = $exclusive_datetime;
                        }
                        break;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->condition = new CbgrnRepeatInfoCondition($std_class->condition);
            if (isset($std_class->exclusive_datetimes->exclusive_datetime)) {
                $this->exclusive_datetime = Array();
                if (is_array($std_class->exclusive_datetimes->exclusive_datetime)) {
                    foreach ($std_class->exclusive_datetimes->exclusive_datetime as $datetime) {
                        $this->exclusive_datetime[] = new CbgrnEventDateType($datetime);
                    }
                } else {
                    $this->exclusive_datetime[] = new CbgrnEventDateType($std_class->exclusive_datetimes->exclusive_datetime);
                }
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["condition"]   = $this->condition->getObjectVars();
            if ($this->exclusive_datetime !== NULL) {
                $tmp = Array();
                foreach ($this->exclusive_datetime as $m) {
                    $tmp[] = $m->getObjectVars();
                }
                $ret_val["exclusive_datetimes"] = Array("exclusive_datetime" => $tmp);
            }
            return $ret_val;
        }
    }

    class CbgrnWhen
    {
        public $datetime = NULL;               // == CbgrnEventDateTimeType
        public $date = NULL;                   // == CbgrnEventDateType

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg)) {
                    switch (get_class($arg)) {
                    case "stdClass":
                        $this->setFromStdClass($arg);
                        break;
                    case "CbgrnEventDateTimeType":
                        $this->datetime = $arg;
                        break;
                    case "CbgrnEventDateType":
                        $this->date = $arg;
                        break;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->datetime)) {
                $this->datetime = new CbgrnEventDateTimeType($std_class->datetime);
            }
            if (isset($std_class->date)) {
                $this->date     = new CbgrnEventDateType($std_class->date);
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->datetime !== NULL) {
                $ret_val["datetime"]    = $this->datetime->getObjectVars();
            }
            if ($this->date !== NULL) {
                $ret_val["date"]        = $this->date->getObjectVars();
            }
            return $ret_val;
        }
        
    }
    
    class CbgrnFollow
    {
        public $id;                     // == base:IdType == string = integer
        public $version;                // == base:VersionType = string
        public $text = NULL;            // == string
        public $creator = NULL;         // == base:ChangeLogType = CbgrnChangeLogType

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                $this->setFromStdClass($arg);
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->id       = $std_class->id;
            $this->version  = $std_class->version;
            if (isset($std_class->text)) {
                $this->text = $std_class->text;
            }
            if (isset($std_class->creator)) {
                $this->creator  = new CbgrnChangeLogType($std_class->creator);
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]          = $this->id;
            $ret_val["version"]     = $this->version;
            if ($this->text !== NULL) {
                $ret_val["text"]    = $this->text;
            }
            if ($this->creator !== NULL) {
                $ret_val["creator"] = $this->creator->getObjectVars();
            }
            return $ret_val;
        }
    }
    
    class CbgrnEventType
    {
        public $id;                     // == base:IdType == String
        public $event_type;             // == schedule:EventTypeType == String (enum) "normal" or "repeat" or "temporary"
        public $version;                // == base:VersionType == String
        public $public_type = NULL;     // == schedule:PublicType == String(enum) "public" or "private" or "qualified"
        public $plan = NULL;            // == string
        public $detail = NULL;          // == string
        public $description = NULL;     // == string
        public $timezone = NULL;        // == string
        public $end_timezone = NULL;    // == string
        public $allday = NULL;          // == boolean
        public $start_only = NULL;      // == boolean
        public $hidden_private = NULL;  // == boolean
        public $member = NULL;          // == Array of schedule:MemberType (CbgrnMemberType class)
        public $observer = NULL;        // == Array of schedule:MemberType (CbgrnMemberType class)
        public $customer = NULL;        // == CbgrnCustomer class
        public $repeat_info = NULL;     // == CbgrnRepeatInfo class
        public $when = NULL;            // == CbgrnWhen class
        public $follow = NULL;          // == Array of CbgrnFollow class

        public function __construct ($arg = false) {
            if ($arg === false) {
                $this->id = "dummy";
                $this->version = "dummy";
            } else {
                $this->setFromStdClass($arg);
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            if (is_object($std_class) && (get_class($std_class) == "stdClass")) {
                ;
            } else {
                throw new CbgrnSoapFault("005", "CbgrnEventType class");
            }
            $this->id           = $std_class->id;
            $this->event_type   = $std_class->event_type;
            $this->version      = $std_class->version;
            if (isset($std_class->public_type)) {
                $this->public_type  = $std_class->public_type;
            }
            if (isset($std_class->plan)) {
                $this->plan         = $std_class->plan;
            }
            if (isset($std_class->detail)) {
                $this->detail       = $std_class->detail;
            }
            if (isset($std_class->description)) {
                $this->description  = $std_class->description;
            }
            if (isset($std_class->timezone)) {
                $this->timezone     = $std_class->timezone;
            }
            if (isset($std_class->end_timezone)) {
                $this->end_timezone = $std_class->end_timezone;
            }
            $this->allday       = (isset($std_class->allday)) ? $std_class->allday : NULL;
            $this->start_only   = (isset($std_class->start_only)) ? $std_class->start_only : NULL;
            $this->hidden_private = (isset($std_class->hidden_private)) ? $std_class->hidden_private : NULL;
            if (isset($std_class->members->member)) {
                $this->member = Array();
                if (is_array($std_class->members->member)) {
                    foreach ($std_class->members->member as $member) {
                        $this->member[] = new CbgrnMemberType($member);
                    }
                } else {
                    $this->member[] = new CbgrnMemberType($std_class->members->member);
                }
            }
            if (isset($std_class->observers->observer)) {
                $this->observer = Array();
                if (is_array($std_class->observers->observer)) {
                    foreach ($std_class->observers->observer as $observer) {
                        $this->observer[] = new CbgrnMemberType($observer);
                    }
                } else {
                    $this->observer[] = new CbgrnMemberType($std_class->observers->observer);
                }
            }
            if (isset($std_class->customer)) {
                $this->customer = new CbgrnCustomer($std_class->customer);
            }
            if (isset($std_class->repeat_info)) {
                $this->repeat_info = new CbgrnRepeatInfo($std_class->repeat_info);
            }
            if (isset($std_class->when)) {
                $this->when = new CbgrnWhen($std_class->when);
            }
            if (isset($std_class->follows->follow)) {
                $this->follow = Array();
                if (is_array($std_class->follows->follow)) {
                    foreach ($std_class->follows->follow as $follow) {
                        $this->follow[] = new CbgrnFollow($follow);
                    }
                } else {
                    $this->follow[] = new CbgrnFollow($std_class->follows->follow);
                }
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]          = $this->id;
            $ret_val["event_type"]  = $this->event_type;
            $ret_val["version"]     = $this->version;
            if ($this->public_type !== NULL) {
                $ret_val["public_type"] = $this->public_type;
            }
            if ($this->plan !== NULL) {
                $ret_val["plan"]        = $this->plan;
            }
            if ($this->detail !== NULL) {
                $ret_val["detail"]      = $this->detail;
            }
            if ($this->description !== NULL) {
                $ret_val["description"] = $this->description;
            }
            if ($this->timezone !== NULL) {
                $ret_val["timezone"]    = $this->timezone;
            }
            if ($this->end_timezone !== NULL) {
                $ret_val["end_timezone"] = $this->end_timezone;
            }
            if ($this->allday !== NULL) {
                $ret_val["allday"]      = ($this->allday == true) ? true : false;
            }
            if ($this->start_only !== NULL) {
                $ret_val["start_only"]  = ($this->start_only == true) ? true : false;
            }
            if ($this->hidden_private !== NULL) {
                $ret_val["hidden_private"]  = ($this->hidden_private == true) ? true : false;
            }
            if ($this->member !== NULL) {
                $tmp = Array();
                foreach ($this->member as $m) {
                    $tmp[] = $m->getObjectVars();
                }
                $ret_val["members"] = Array("member" => $tmp);
            }
            if ($this->observer !== NULL) {
                $tmp = Array();
                foreach ($this->observer as $m) {
                    $tmp[] = $m->getObjectVars();
                }
                $ret_val["observers"] = Array("observer" => $tmp);
            }
            if ($this->customer !== NULL) {
                $ret_val["customer"] = $this->customer->getObjectVars();
            }
            if ($this->repeat_info !== NULL) {
                $ret_val["repeat_info"] = $this->repeat_info->getObjectVars();
            }
            if ($this->when !== NULL) {
                $ret_val["when"]    = $this->when->getObjectVars();
            }
            if ($this->follow !== NULL) {
                $ret_val["follow"]  = $this->follow->getObjectVars();
            }
            return $ret_val;
        }
    }

    class CbgrnScheduleModifyRepeatEventsOperationType {
        public $schedule_event;     // == EventType == CbgrnEventType class
        public $type;               // == ScheduleRepeatModifyType == String(enum) "this" or "after" or "all"
        public $date = NULL;        // == xsd:date == "YYYY-MM-DD"

        public function __construct ($arg = false, $type = false, $date = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg)) {
                    switch (get_class($arg)) {
                    case "stdClass":
                        $this->setFromStdClass($arg);
                        break;
                    case "CbgrnEventType":
                        $this->schedule_event   = $arg;
                        if ($type !== false) {
                            $this->type             = $type;
                        } else {
                            $this->type             = "this";
                        }
                        if ($date !== false) {
                            $this->date             = $date;
                        }
                    }
                }
            }
        }

        public function setFromStdClass (stdClass $std_class) {
            $this->schedule_event   = new CbgrnEventType($std_class->schedule_event);
            $this->type             = $std_class->type;
            if (isset($std_class->date)) {
                $this->date         = $std_class->date;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["schedule_event"]  = $this->schedule_event->getObjectVars();
            $ret_val["type"]            = $this->type;
            if ($this->date !== NULL) {
                $ret_val["date"]        = $this->date;
            }
            return $ret_val;
        }
        
    }

    class CbgrnScheduleModifyRepeatEventsResultType
    {
        public $original;       // == EventType = CbgrnEventType class
        public $modified;       // == EventType = CbgrnEventType class

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->original = new CbgrnEventType($std_class->original);
            $this->modified = new CbgrnEventType($std_class->modified);
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["original"]    = $this->original->getObjectVars();
            $ret_val["modified"]    = $this->modified->getObjectVars();
            return $ret_val;
        }
    }

    class CbgrnScheduleSearchFreeTimesCandidateType {
        public $start;      // UNIX timestamp (convert from xsd:datetime)
        public $end;        // UNIX timestamp (convert from xsd:datetime)

        public function __construct($arg = false, $arg2 = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    // estimate arg1 as start, arg2 as end
                    if (($arg !== false) && ($arg2 !== false)) {
                        $this->start = $arg;
                        $this->end   = $arg2;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->start = strtotime($std_class->start);
            $this->end   = strtotime($std_class->end);
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["start"]   = gmdate(W3C_DATETIME_FORMAT, $this->start);
            $ret_val["end"]     = gmdate(W3C_DATETIME_FORMAT, $this->end);
            return $ret_val;
        }
    }

    class CbgrnScheduleFreeTimeType {
        public $start;      // UNIX timestamp
        public $end;        // UNIX timestamp
        public $facility_id = NULL;    // base:IdType

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->start = strtotime($std_class->start);
            $this->end   = strtotime($std_class->end);
            if (isset($std_class->facility_id)) {
                $this->facility_id = $std_class->facility_id;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["start"]   = gmdate(W3C_DATETIME_FORMAT, $this->start);
            $ret_val["end"]     = gmdate(W3C_DATETIME_FORMAT, $this->end);
            if ($this->facility_id !== NULL) {
                $ret_val["facility_id"] = $this->facility_id;
            }
            return $ret_val;
        }
    }

    class CbgrnScheduleFollowContentType
    {
        public $event_id;       // base:IdType == String == integer?
        public $content;        // string (utf-8 encoding)

        public function __construct ($arg = false, $arg2 = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->event_id = $arg;
                    $this->content = $arg2;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->event_id = $std_class->event_id;
            $this->content  = $std_class->content;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["event_id"]    = $this->event_id;
            $ret_val["content"]     = $this->content;
            return $ret_val;
        }
    }

    class CbgrnScheduleFollowToRepeatEventContentType
    {
        public $event_id;       // base:IDType == String == integer?
        public $date;           // xsd:date == "YYYY-MM-DD"
        public $content;        // string (utf-8 encoding)

        public function __construct ($arg = false, $arg2 = false, $arg3 = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->event_id = $arg;
                    $this->date     = $arg2;
                    $this->content  = $arg3;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->event_id     = $std_class->event_id;
            $this->date         = $std_class->date;
            $this->content      = $std_class->content;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["event_id"]    = $this->event_id;
            $ret_val["date"]        = $this->date;
            $ret_val["content"]     = $this->content;
            return $ret_val;
        }
    }

    class CbgrnScheduleAddFollowsToRepeatEventResultType
    {
        public $original;       // CbgrnEventType class
        public $modified;       // CbgrnEventType class

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->original = new CbgrnEventType($std_class->original);
            $this->modified = new CbgrnEventType($std_class->modified);
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["original"]    = $this->original->getObjectVars();
            $ret_val["modified"]    = $this->modified->getObjectVars();
            return $ret_val;
        }
    }

    class CbgrnScheduleCandidateItemType
    {
        public $event_id;       // base:IDType == String == integer
        public $start;          // UNIX timestamp (converted from xsd:datetime)
        public $end;            // UNIX timestamp (converted from xsd:datetime)
        public $facility_id = NULL; // base:IDType == String == integer

        public function __construct($arg = false, $start = false, $end = false, $facility_id = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->event_id = $arg;
                    $this->start = $start;
                    $this->end   = $end;
                    if ($facility_id !== false) {
                        $this->facility_id = $facility_id;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->event_id = $std_class->event_id;
            $this->start    = strtotime($std_class->start);
            $this->end      = strtotime($std_class->end);
            if (isset($std_class->facility_id)) {
                $this->facility_id  = $std_class->facility_id;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["event_id"]    = $this->event_id;
            $ret_val["start"]       = gmdate(W3C_DATETIME_FORMAT, $this->start);
            $ret_val["end"]         = gmdate(W3C_DATETIME_FORMAT, $this->end);
            if ($this->facility_id !== NULL) {
                $ret_val["facility_id"] = $this->facility_id;
            }
            return $ret_val;
        }
    }

    class CbgrnSchedulePersonalProfileType
    {
        public $start_time_in_dayview = NULL;   // unsignedInt
        public $end_time_in_dayview = NULL;     // unsignedInt
        public $show_sunday = NULL;             // boolean
        public $show_end_time = NULL;           // boolean
        public $plan_menu = NULL;               // string
        public $nofity_mail = NULL;             // boolean
        public $is_user_address_mail = NULL;    // boolean
        public $nofity_mail_address = NULL;     // string

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->start_time_in_dayview)) {
                $this->start_time_in_dayview = $std_class->start_time_in_dayview;
            }
            if (isset($std_class->end_time_in_dayview)) {
                $this->end_time_in_dayview = $std_class->end_time_in_dayview;
            }
            if (isset($std_class->show_sunday)) {
                $this->show_sunday = $std_class->show_sunday;
            }
            if (isset($std_class->show_end_time)) {
                $this->show_end_time = $std_class->show_end_time;
            }
            if (isset($std_class->plan_menu)) {
                $this->plan_menu = $std_class->plan_menu;
            }
            if (isset($std_class->notify_mail)) {
                $this->notify_mail = $std_class->notify_mail;
            }
            if (isset($std_class->is_user_address_mail)) {
                $this->is_user_address_mail = $std_class->is_user_address_mail;
            }
            if (isset($std_class->nofity_mail_address)) {
                $this->notify_mail_address = $std_class->nofity_mail_address;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->start_time_in_dayview !== NULL) {
                $ret_val["start_time_in_dayview"] = $this->start_time_in_dayview;
            }
            if ($this->end_time_in_dayview !== NULL) {
                $ret_val["end_time_in_dayview"] = $this->end_time_in_dayview;
            }
            if ($this->show_sunday !== NULL) {
                $ret_val["show_sunday"] = $this->show_sunday;
            }
            if ($this->show_end_time !== NULL) {
                $ret_val["show_end_time"]   = $this->show_end_time;
            }
            if ($this->plan_menu !== NULL) {
                $ret_val["plan_menu"]   = $this->plan_menu;
            }
            if ($this->notify_menu !== NULL) {
                $ret_val["notify_menu"] = $this->notify_menu;
            }
            return $ret_val;
        }
    }

    class CbgrnTopicTypeFile
    {
        public $id;                         // base:IDType (string := integer)
        public $name;                       // string
        public $size = NULL;                // long int
        public $mime_type = NULL;           // string

        public function __construct ($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdCass $std_class) {
            $this->id       = $std_class->id;
            $this->name     = $std_class->name;
            if (isset($std_class->size)) {
                $this->size = $std_class->size;
            }
            if (isset($std_class->mime_type)) {
                $this->mime_type = $std_class->mime_type;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]      = $this->id;
            $ret_val["name"]    = $this->name;
            if ($this->size !== NULL) {
                $ret_val["size"]    = $this->size;
            }
            if ($this->mime_type !== NULL) {
                $ret_val["mime_type"]   = $this->mime_type;
            }
            return $ret_val;
        }
    }
    
    class CbgrnTopicTypeContent
    {
        public $body;                       // string (UTF8 encoded)
        public $html_body = NULL;           // string (UTF8 encoded)
        public $file = NULL;                // CbgrnTopicTypeFile class or array

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->body = $arg;   // if arg is string, set body;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->body     = $std_class->body;
            if (isset($std_class->html_body)) {
                $this->html_body = $std_class->html_body;
            }
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    foreach ($std_class->file as $file) {
                        $this->file[] = new CbgrnTopicTypeFile($file);
                    }
                } else {
                    $this->file = new CbgrnTopicTypeFile($std_class->file);
                }
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["body"]        = $this->body;
            if ($this->html_body !== NULL) {
                $ret_val["html_body"]   = $this->html_body;
            }
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    $tmp = Array();
                    foreach ($this->file as $file) {
                        $tmp[] = $file->getObjectVars();
                    }
                    $ret_val["file"]    = $tmp;
                } else {
                    $ret_val["file"]    = $this->file->getObjectVars();
                }
            }
            return $ret_val;
        }
    }

    class CbgrnTopicTypeFollow
    {
        public $id;                         // base:IDType(string := integer)
        public $number;                     // string

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->id = $arg;
                    $this->number = "dummy";
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->id       = $std_class->id;
            $this->number   = $std_class->number;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]      = $this->id;
            $ret_val["number"]  = $this->number;
            return $ret_val;
        }
    }
    
    class CbgrnTopicType
    {
        public $id;                         // base:IDType (string := integer)
        public $creator_group_id = NULL;    // base:IDType (string := integer)
        public $version;                    // base:VersionType (string := integer)
        public $subject;                 // string   (UTF-8 encoded)
        public $is_draft = NULL;            // boolean
        public $start_datetime = NULL;      // UNIX timestamp (converted)
        public $end_datetime = NULL;        // UNIX timestamp (converted)
        public $start_is_datetime = NULL;   // boolean
        public $end_is_datetime = NULL;     // boolean
        public $can_follow;                 // boolean
        public $published = NULL;           // boolean
        public $unread = NULL;              // boolean
        public $expired = NULL;             // boolean
        public $category_id;                // base:IDType (string := integer)
        public $content;                    // CbgrnTopicTypeContent class or this array
        public $follow = NULL;              // CbgrnTopicTypeFollow class (not array)
        public $creator;                    // base:CbgrnChangeLogType
        public $modifier;                   // base:CbgrnChangeLogType

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->id           = $std_class->id;
            if (isset($std_class->creator_group_id)) {
                $this->creator_group_id = $std_class->creator_group_id;
            }
            $this->version      = $std_class->version;
            $this->subject      = $std_class->subject;
            if (isset($std_class->is_draft)) {
                $this->is_draft = $std_class->is_draft;
            }
            if (isset($std_class->start_datetime)) {
                $this->start_datetime = strtotime($std_class->start_datetime);
            }
            if (isset($std_class->end_datetime)) {
                $this->end_datetime = strtotime($std_class->end_datetime);
            }
            if (isset($std_class->can_follow)) {
                $this->can_follow = $std_class->can_follow;
            }
            if (isset($std_class->published)) {
                $this->published = $std_class->published;
            }
            if (isset($std_class->unread)) {
                $this->unread = $std_class->unread;
            }
            if (isset($std_class->expired)) {
                $this->expired = $std_class->expired;
            }
            $this->category_id = $std_class->category_id;
            $this->content = new CbgrnTopicTypeContent($std_class->content);
            if (isset($std_class->follow)) {
                $this->follow   = new CbgrnTopicTypeFollow($std_class->follow);
            }
            $this->creator = new CbgrnChangeLogType($std_class->creator);
            $this->modifier = new CbgrnChangeLogtype($std_class->modifier);
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]          = $this->id;
            if ($this->creator_group_id !== NULL) {
                $ret_val["creator_group_id"]    = $this->creator_group_id;
            }
            $ret_val["version"]     = $this->version;
            $ret_val["subject"]     = $this->subject;
            if ($this->is_draft !== NULL) {
                $ret_val["is_draft"]    = $this->is_draft;
            }
            if ($this->start_datetime !== NULL) {
                $ret_val["start_datetime"]  = gmdate(W3C_DATETIME_FORMAT, $this->start_datetime);
            }
            if ($this->end_datetime !== NULL) {
                $ret_val["end_datetime"]    = gmdate(W3C_DATETIME_FORMAT, $this->end_datetime);
            }
            if ($this->start_is_datetime !== NULL) {
                $ret_val["start_is_datetime"] = $this->start_is_datetime;
            }
            if ($this->end_is_datetime !== NULL) {
                $ret_val["end_is_datetime"] = $this->end_is_datetime;
            }
            $ret_val["can_follow"]      = $this->can_follow;
            if ($this->published !== NULL) {
                $ret_val["published"]   = $this->published;
            }
            if ($this->unread !== NULL) {
                $ret_val["unread"]      = $this->unread;
            }
            if ($this->expired !== NULL) {
                $ret_val["expired"]     = $this->expired;
            }
            $ret_val["category_id"]     = $this->category_id;
            if (get_class($this->content) == "CbgrnTopicTypeContent") {
                $ret_val["content"]         = $this->content->getObjectVars();
            } else {
                throw new CbgrnSoapFault("008", "CbgrnTopicType->getObjectVars:content");
            }
            if ($this->follow !== NULL) {
                $ret_val["follow"]      = $this->follow->getObjectVars();
            }
            if (get_class($this->creator) == "CbgrnChangeLogType") {
                $ret_val["creator"]         = $this->creator->getObjectVars();
            } else {
                throw new CbgrnSoapFault("008", "CbgrnTopicType->getObjectVars:creator");
            }
            if (get_class($this->modifier) == "CbgrnChangeLogType") {
                $ret_val["modifier"]        = $this->modifier->getObjectVars();
            } else {
                throw new CbgrnSoapFault("008", "CbgrnTopicType->getObjectVars:modifier");
            }
            return $ret_val;
        }
    }

    class CbgrnFileType
    {
        public $content;            // file body (not base64encode)
        public $id;                 // base:IDType(string := integer)

        public function __construct($arg = false, $file_path = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->content = $arg;
                    if ($file_path !== false) {
                        $fp = fopen($file_path, "r");
                        if ($fp == false) {
                            throw new CbgrnSoapFault("007", "CbgrnFileType class");
                        }
                        $this->content = fread($fp, filesize($file_path));
                        fclose($fp);
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->content  = $std_class->content;
            $this->id       = $std_class->id;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["content"] = $this->content;
            $ret_val["id"]      = $this->id;
            return $ret_val;
        }
    }
    
    class CbgrnBulletinModifyTopicType
    {
        public $topic;              // CbgrnTopicType class
        public $file = NULL;        // CbgrnFileType class or this array
        public $remove_file_id = NULL; // IDType (string := integer) or this array

        public function __construct($arg = false, $file = false, $remove_file_id = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg)) {
                    switch (get_class($arg)) {
                    case "stdClass":
                        $this->setFromStdClass($arg);
                        break;
                    case "CbgrnTopicType":
                        $this->topic = $arg;
                        if (is_object($file) && (get_class($file) == "CbgrnFileType")) {
                            $this->file = $file;
                        }
                        if (remove_file_id !== false) {
                            $this->remove_file_id = $remove_file_id;
                        }
                        break;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->topic = new CbgrnTopicType($std_class->topic);
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    foreach ($std_class->file as $file) {
                        $this->file[] = new CbgrnFileType($file);
                    }
                } else {
                    $this->file = new CbgrnFileType($std_class->file);
                }
            }
            if (isset($std_class->remove_file_id)) {
                $this->remove_file_id = $std_class->remove_file_id;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["topic"] = $this->topic->getObjectVars();
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    $tmp = Array();
                    foreach ($this->file as $file) {
                        $tmp[] = $file->getObjectVars();
                    }
                    $ret_val["file"]    = $tmp;
                } else {
                    $ret_val["file"]    = $this->file->getObjectVars();
                }
            }
            if ($this->remove_file_id !== NULL) {
                $ret_val["remove_file_id"]  = $this->remove_file_id;
            }
            return $ret_val;
        }
    }

    class CbgrnBulletinCreateTopicType
    {
        public $topic;          // CbgrnTopicType class
        public $file = NULL;    // CbgrnFileType class or this array;
        public $remove_file_id = NULL;  // base:IDType(string := integer) or this array
        public $draft_id = NULL;    // base:IDType

        public function __construct ($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg)) {
                    switch(get_class($arg)) {
                    case "stdClass":
                        $this->setFromStdClass($arg);
                        break;
                    case "CbgrnTopicType":
                        $this->topic = $arg;
                        break;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->topic = new CbgrnTopicType($std_class->topic);
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    $tmp = Array();
                    foreach ($std_class->file as $file) {
                        $tmp[] = new CbgrnFileType($file);
                    }
                    $this->file = $tmp;
                } else {
                    $this->file = new CbgrnFileType($std_class->file);
                }
            }
            if (isset($std_class->remove_file_id)) {
                $this->remove_file_id = $std_class->remove_file_id;
            }
            if (isset($std_class->draft_id)) {
                $this->draft_id = $std_class->draft_id;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["topic"]   = $this->topic->getObjectVars();
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    $tmp = Array();
                    foreach($this->file as $file) {
                        $tmp[] = $file->getObjectVars();
                    }
                    $ret_val["file"]    = $tmp;
                } else {
                    $ret_val["file"]    = $this->file->getObjectVars();
                }
            }
            if ($this->remove_file_id !== NULL) {
                $ret_val["remove_file_id"]  = $this->remove_file_id;
            }
            if ($this->draft_id !== NULL) {
                $ret_val["draft_id"]        = $this->draft_id;
            }
            return $ret_val;
        }
    }

    class CbgrnFollowType
    {
        public $id;             // base:IDType(string := integer)
        public $number;         // string...(ID?)
        public $text;        // string (UTF8 encoded)
        public $html_text = NULL;    // string (UTF8 encoded)
        public $file = NULL;    // CbgrnTopicTypeFile class or this array
        public $creator;        // CbgrnChangeLogType class

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdCass $std_class) {
            $this->id       = $std_class->id;
            $this->number   = $std_class->number;
            $this->text     = $std_class->text;
            if (isset($std_class->html_text)) {
                $this->html_text = $std_class->html_text;
            }
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    foreach ($std_class->file as $file) {
                        $this->file[] = new CbgrnTopicTypeFile($file);
                    }
                } else {
                    $this->file = new CbgrnTopicTypeFile($std_class->file);
                }
            }
            $this->creator  = $std_class->creator;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]      = $this->id;
            $ret_val["number"]  = $this->number;
            $ret_val["text"]    = $this->text;
            if ($this->html_text !== NULL) {
                $ret_val["html_text"]   = $this->html_text;
            }
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    $tmp = Array();
                    foreach ($this->file as $file) {
                        $tmp[] = $file->getObjectVars();
                    }
                    $ret_val["file"]    = $tmp;
                } else {
                    $ret_val["file"]    = $this->file->getObjectVars();
                }
            }
            $ret_val["creator"] = $this->creator->getObjectVars();
            return $ret_val;
        }
    }
    
    class CbgrnBulletinAddFollowType
    {
        public $follow;         // CbgrnFollowType class
        public $file = NULL;    // CbgrnFileType class or array
        public $topic_id;       // base:IDType(string := integer)

        public function __construct($arg = false, $topic_id = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg)) {
                    switch (get_class($arg)) {
                    case "stdClass":
                        $this->setFromStdClass($arg);
                        break;
                    case "CbgrnFollowType":
                        $this->follow = $arg;
                        $this->topic_id = $topic_id;
                        break;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->follow   = new CbgrnFollowType($std_class->follow);
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    foreach ($std_class->file as $file) {
                        $this->file[] = new CbgrnFileType($file);
                    }
                } else {
                    $this->file = new CbgrnFileType($std_class->file);
                }
            }
            $this->topic_id = $std_class->topic_id;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["follow"]  = $this->follow->getObjectVars();
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    $tmp = Array();
                    foreach ($this->file as $file) {
                        $tmp[] = $file->getObjectVars();
                    }
                    $ret_val["file"]    = $tmp;
                } else {
                    $ret_val["file"]    = $this->file->getObjectVars();
                }
            }
            $ret_val["topic_id"]    = $this->topic_id;
            return $ret_val;
        }
    }

    class CbgrnNameFieldValueType
    {
        public $sei;          // string (UTF8 encoded)
        public $mei;          // string (UTF8 encoded)

        public function __construct($arg = false, $mei = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->sei = $arg;
                    $this->mei = $mei;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->sei = $std_class->part[0];
            $this->mei = $std_class->part[1];
        }

        public function getObjectVars() {
            $ret_val["part"][0] = $this->sei;
            $ret_val["part"][1] = $this->mei;
            return $ret_val;
        }
    }

    class CbgrnRouteFieldValueType
    {
        public $path = NULL;     // string (UTF8 encoded)
        public $time = NULL;     // string (UTF8 encoded)
        public $fare = NULL;     // string (UTF8 encoded)

        function __construct($arg = false, $time = false, $fare = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    if ($arg !== false) {
                        $this->path = $arg;
                    }
                    if ($time !== false) {
                        $this->time = $time;
                    }
                    if ($fare !== false) {
                        $this->fare = $fare;
                    }
                }
            }
        }

        function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->path)) {
                $this->path = $std_class->path;
            }
            if (isset($std_class->time)) {
                $this->time = $std_class->time;
            }
            if (isset($std_class->fare)) {
                $this->fare = $std_class->fare;
            }
        }

        function getObjectVars() {
            $ret_val = Array();
            if ($this->path !== NULL) {
                $ret_val["path"]    = $this->path;
            }
            if ($this->time !== NULL) {
                $ret_val["time"]    = $this->time;
            }
            if ($this->fare !== NULL) {
                $ret_val["fare"]    = $this->fare;
            }
            return $ret_val;
        }
    }

    class CbgrnFileFieldValueType
    {
        public $name;            // string (UTF8 encoded)
        public $file_id;            // IDType (=string := integer)
        public $mime_type;          // string
        public $size;               // integer (Long int)

        public function __construct($arg = false, $file_id = false, $mime_type = false, $size = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->name = $arg;
                    $this->file_id = $file_id;
                    $this->mime_type = $mime_type;
                    $this->size = $size;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->name     = $std_class->name;
            $this->file_id  = $std_class->file_id;
            $this->mime_type= $std_class->mime_type;
            $this->size     = $std_class->size;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["name"]    = $this->name;
            $ret_val["file_id"] = $this->file_id;
            $ret_val["mime_type"] = $this->mime_type;
            $ret_val["size"]    = $this->size;
            return $ret_val;
        }
    }

    /*
    class CbgrnAddressCustom
    {
        
    }
    */
    
    class CbgrnCardType
    {
        public $creator;                // CbgrnChangeLogType
        public $modifier = NULL;        // CbgrnChangeLogType
        public $subject;             // string (UTF8 encoded)
        public $personal_name = NULL;   // CbgrnNameFieldValueType
        public $personal_reading = NULL;// CbgrnNameFieldValueType
        public $company_name = NULL;  // string (UTF8 encoded)
        public $company_reading = NULL; // string (UTF8 encoded)
        public $section = NULL;       // string (UTF8 encoded)
        public $zip_code = NULL;      // string (UTF8 encoded)
        public $physical_address = NULL; // string (UTF8 encoded)
        public $map = NULL;              // URI(string)
        public $route = NULL;            // CbgrnRouteFieldValueType
        public $company_tel = NULL;   // string (UTF8 encoded)
        public $company_fax = NULL;   // string (UTF8 encoded)
        public $url = NULL;              // URI(string)
        public $post = NULL;          // string (UTF8 encoded)
        public $personal_tel = NULL;  // string (UTF8 encoded)
        public $email = NULL;         // string (UTF8 encoded)
        public $image = NULL;            // CbgrnFileFieldValueType
        public $description = NULL;   // string (UTF8 encoded)
        // public $custom = NULL;           // CbgrnAddressCustom class
        public $book_id;                 // IDType
        public $id;                      // IDType
        public $version;                 // base:VersionType == string

        public function __construct($arg = false, $book_id = false, $subject = false, $creator = false, $version = false) {
            // construct type
            //  1: NULL
            //  2: stdClass (probably API return value)
            //  3: all value hand set,
            //          1. id : IDType (card ID)
            //          2. book_id : IDType (this card belong to this BOOK)
            //          3. subject : string (card subject)
            //          4. creator : CbgrnChangeLogType or (creator's IDType) (optional, if false get, set this login users ID)
            //          5. version : base:VersionType == string (optional)
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else if (($arg !== false) &&
                           ($book_id !== false) &&
                           ($subject !== false)) {
                    $this->id = $arg;
                    $this->book_id = $book_id;
                    $this->subject = $subject;
                    if ($creator !== false) {
                        if (is_object($creator) && (get_class($creator) == "CbgrnChangeLogType")) {
                            $this->creator = $creator;
                        } else {
                            // assume $creator as IDType
                            $this->creator = new CbgrnChangeLogType($creator, "dummy");
                        }
                    }
                    if ($version !== false) {
                        $this->version = $version;
                    } else {
                        $this->version = "dummy";
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->creator = new CbgrnChangeLogType($std_class->creator);
            if (isset($std_class->modifier)) {
                $this->modifier = new CbgrnChangeLogType($std_class->modifier);
            }
            $this->subject = $std_class->subject;
            if (isset($std_class->personal_name)) {
                $this->personal_name = $std_class->personal_name;
            }
            if (isset($std_class->personal_reading)) {
                $this->personal_reading = $std_class->personal_reading;
            }
            if (isset($std_class->company_name)) {
                $this->company_name = $std_class->company_name;
            }
            if (isset($std_class->company_reading)) {
                $this->company_reading = $std_class->company_reading;
            }
            if (isset($std_class->section)) {
                $this->section = $std_class->section;
            }
            if (isset($std_class->zip_code)) {
                $this->zip_code = $std_class->zip_code;
            }
            if (isset($std_class->physical_address)) {
                $this->physical_address = $std_class->physical_address;
            }
            if (isset($std_class->map)) {
                $this->map = $std_class->map;
            }
            if (isset($std_class->route)) {
                $this->route = new CbgrnRouteFieldValueType($std_class->route);
            }
            if (isset($std_class->company_tel)) {
                $this->company_tel = $std_class->company_tel;
            }
            if (isset($std_class->company_fax)) {
                $this->company_fax = $std_class->company_fax;
            }
            if (isset($std_class->url)) {
                $this->url = $std_class->url;
            }
            if (isset($std_class->post)) {
                $this->post = $std_class->post;
            }
            if (isset($std_class->personal_tel)) {
                $this->personal_tel = $std_class->personal_tel;
            }
            if (isset($std_class->email)) {
                $this->email = $std_class->email;
            }
            if (isset($std_class->image)) {
                $this->image = new CbgrnFileFieldValueType($std_class->image);
            }
            if (isset($std_class->description)) {
                $this->description = $std_class->description;
            }
            //if (isset($std_class->custom)) {
            //    $this->custom = new CbgrnAddressCustom($std_class->custom);
            //}
            $this->book_id  = $std_class->book_id;
            $this->id       = $std_class->id;
            $this->version  = $std_class->version;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["creator"]     = $this->creator->getObjectVars();
            if ($this->modifier !== NULL) {
                $ret_val["modifier"]    = $this->modifier->getObjectVars();
            }
            $ret_val["subject"]     = $this->subject;
            if ($this->personal_name !== NULL) {
                $ret_val["personal_name"]   = $this->personal_name->getObjectVars();
            }
            if ($this->personal_reading !== NULL) {
                $ret_val["personal_reading"]    = $this->personal_reading->getObjectVars();
            }
            if ($this->company_name !== NULL) {
                $ret_val["company_name"]    = $this->company_name;
            }
            if ($this->company_reading !== NULL) {
                $ret_val["company_rading"]  = $this->company_reading;
            }
            if ($this->section !== NULL) {
                $ret_val["section"]         = $this->section;
            }
            if ($this->zip_code !== NULL) {
                $ret_val["zip_code"]        = $this->zip_code;
            }
            if ($this->physical_address !== NULL) {
                $ret_val["physical_address"]    = $this->physical_address;
            }
            if ($this->map !== NULL) {
                $ret_val["map"]             = $this->map;
            }
            if ($this->route !== NULL) {
                $ret_val["route"]           = $this->route->getObjectVars();
            }
            if ($this->company_tel !== NULL) {
                $ret_val["company_tel"]     = $this->company_tel;
            }
            if ($this->company_fax !== NULL) {
                $ret_val["company_fax"]     = $this->company_fax;
            }
            if ($this->url !== NULL) {
                $ret_val["url"]             = $this->url;
            }
            if ($this->post !== NULL) {
                $ret_val["post"]            = $this->post;
            }
            if ($this->personal_tel !== NULL) {
                $ret_val["personal_tel"]    = $this->personal_tel;
            }
            if ($this->email !== NULL) {
                $ret_val["email"]           = $this->email;
            }
            if ($this->image !== NULL) {
                $ret_val["image"]           = $this->image->getObjectVars();
            }
            if ($this->description !== NULL) {
                $ret_val["description"]     = $this->description;
            }
            //if ($this->custom !== NULL) {
            //    $ret_val["custom"]          = $this->custom->getObjectVars();
            //}
            $ret_val["book_id"]             = $this->book_id;
            $ret_val["id"]                  = $this->id;
            $ret_val["version"]             = $this->version;
            return $ret_val;
        }
    }

    class CbgrnAddressFileType {
        public $id;                     // IDType
        public $content;                // base64binary (base64 decoded)

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->id = $std_class->id;
            $this->content = $std_class->content;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]  = $this->id;
            $ret_val["content"] = $this->content;
            return $ret_val;
        }
    }
    
    class CbgrnAddressCardContainsFileType {
        public $card;                   // CbgrnCardType
        public $file = NULL;            // CbgrnAddressFileType

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg)) {
                    switch(get_class($arg)) {
                    case "stdClass":
                        $this->setFromStdClass($arg);
                        break;
                    case "CbgrnCardType":
                        $this->card = $arg;
                        break;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->card = new CbgrnCardType($std_class->card);
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    foreach ($std_class->file as $file) {
                        $this->file[] = new CbgrnAddressFileType($file);
                    }
                } else {
                    $this->file = new CbgrnAddressFileType($std_class->file);
                }
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["card"]    = $this->card->getObjectVars();
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    $tmp = Array();
                    foreach ($this->file as $file) {
                        $tmp[] = $file->getObjectVars();
                    }
                    $ret_val["file"] = $tmp;
                } else {
                    $ret_val["file"] = $this->file->getObjectVars();
                }
            }
            return $ret_val;
        }
    }

    class CbgrnMyAddressGroupCard
    {
        public $key;                    // IDType
        public $type;                   // string (enum : "shared" or "private")

        public function __construct($arg = false, $type = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->key = $arg;
                    $this->type = $type;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->key = $std_class->key;
            $this->type = $std_class->type;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["key"]     = $this->key;
            $ret_val["type"]    = $this->type;
            return $ret_val;
        }
    }
    
    class CbgrnMyAddressGroupType
    {
        public $id;                     // IDType
        public $version;                // VersionType == string
        public $name;                // string (UTF8 encoded)
        public $description = NULL;  // string (UTF8 encoded)
        public $user = NULL;            // array of IDType
        public $card = NULL;            // array of CbgrnMyAddressGroupCard

        public function __construct($arg = false, $name = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->id = $arg;
                    $this->name = $name;
                    $this->version = "dummy";
                }
            }
        }
        
        public function setFromStdClass(stdClass $std_class) {
            $this->id       = $std_class->id;
            $this->version  = $std_class->version;
            $this->name     = $std_class->name;
            if (isset($std_class->description)) {
                $this->description = $std_class->description;
            }
            if (isset($std_class->user)) {
                $this->user = Array();
                if (is_array($std_class->user)) {
                    foreach ($std_class->user as $user) {
                        $this->user[] = $user->key;
                    }
                } else {
                    $this->user[] = $std_class->user->key;
                }
            }
            if (isset($std_class->card)) {
                $this->card = Array();
                if (is_array($std_class->card)) {
                    foreach ($std_class->card as $card) {
                        $this->card[] = new CbgrnMyAddressGroupCard($card);
                    }
                } else {
                    $this->card[] = new CbgrnMyAddressGroupCard($std_class->card);
                }
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]          = $this->id;
            $ret_val["version"]     = $this->version;
            $ret_val["name"]        = $this->name;
            if ($this->description !== NULL) {
                $ret_val["description"] = $this->description;
            }
            if ($this->user !== NULL) {
                for ($i=0; $i<sizeof($this->user); $i++) {
                    $ret_val["user"][$i]["key"] = $this->user[$i];
                }
            }
            if ($this->card !== NULL) {
                foreach ($this->card as $card) {
                    $ret_val["card"][] = $card->getObjectVars();
                }
            }
            return $ret_val;
        }
    }

    class CbgrnMessageAddressee
    {
        public $user_id = NULL;         // IDType (==string==integer)
        public $name;                // non blank string (UTF8 encoded)
        public $deleted;                // boolean
        public $confirmed = NULL;       // boolean

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->name = $arg;
                    $this->deleted = false;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            if (isset($stc_class->user_id)) {
                $this->user_id  = $std_class->user_id;
            }
            $this->name     = $std_class->name;
            $this->deleted  = $std_class->deleted;
            if (isset($std_class->confirmed)) {
                $this->confirmed = $std_class->confirmed;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->user_id !== NULL) {
                $ret_val["user_id"] = $this->user_id;
            }
            $ret_val["name"]        = $this->name;
            $ret_val["deleted"]     = $this->deleted;
            if ($this->confirmed !== NULL) {
                $ret_val["confirmed"]   = $this->confirmed;
            }
            return $ret_val;
        }
    }

    class CbgrnMessageFile
    {
        public $id;                     // IDType
        public $name;                // non blank string (UTF8 encoded)
        public $size = NULL;            // unsigned Long integer
        public $mime_type = NULL;       // string

        public function __construct($arg = false, $name = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->id   = $arg;
                    $this->name = $name;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->id       = $std_class->id;
            $this->name     = $std_class->name;
            if (isset($std_class->size)) {
                $this->size = $std_class->size;
            }
            if (isset($std_class->mime_type)) {
                $this->mime_type = $std_class->mime_type;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]      = $this->id;
            $ret_val["name"]    = $this->name;
            if ($this->size !== NULL) {
                $ret_val["size"]    = $this->size;
            }
            if ($this->mime_type !== NULL) {
                $ret_val["mime_type"]   = $this->mime_type;
            }
            return $ret_val;
        }
    }
    
    class CbgrnMessageContent
    {
        public $file = NULL;            // CbgrnMessageFile class array
        public $body;                // string (UTF8 encoded)
        public $html_body = NULL;    // string (UTF8 encoded)

        public function __construct($arg = false, $file = false, $html_body = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->body = $arg;
                    if ($file !== false) {
                        if (is_object($file) && (get_class($file) == "CbgrnMessageFile")) {
                            $this->file = $file;
                        }
                    }
                    if ($html_body !== false) {
                        $this->html_body = $html_body;
                    }
                }
            }
        }

        public function setFromStdClass(StdClass $std_class) {
            if (isset($std_class->file)) {
                $this->file = new CbgrnMessageFile($std_class->file);
            }
            $this->body = $std_class->body;
            if (isset($std_class->html_body)) {
                $this->html_body = $std_class->html_body;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->file !== NULL) {
                $ret_val["file"] = $this->file->getObjectVars();
            }
            $ret_val["body"]    = $this->body;
            if ($this->html_body !== NULL) {
                $ret_val["html_body"]   = $this->html_body;
            }
            return $ret_val;
        }
    }

    class CbgrnMessageFollow
    {
        public $id;                 // IDType (==string==integer)
        public $number;             // IDType (==string==integer)

        public function __construct($arg = false, $number = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->id = $arg;
                    if ($number !== false) {
                        $this->number = $number;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->id       = $std_class->id;
            $this->number   = $std_class->number;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]      = $this->id;
            $ret_val["number"]  = $this->number;
            return $ret_val;
        }
    }

    class CbgrnThreadType
    {
        public $addressee = NULL;   // CbgrnMessageAddressee class array
        public $content;            // CbgrnMessageContent class (single)
        public $follow = NULL;      // CbgrnMessageFollow class array
        public $folder;             // IDType array
        public $creator = NULL;     // CbgrnChangeLogType class
        public $modifier = NULL;    // CbgrnChangeLogType class
        public $id;                 // IDType (==string==integer)
        public $version;            // VersionType (==string)
        public $subject;         // string (UTF8 encoded)
        public $confirm;            // boolean
        public $snapshot = NULL;    // UNIX timestamp
        public $is_draft = NULL;    // boolean

        public function __construct($arg = false, $folder = false, $id = false, $version = false, $subject = false, $confirm = NULL) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg)) {
                    switch (get_class($arg)) {
                    case "stdClass":
                        $this->setFromStdClass($arg);
                        break;
                    case "CbgrnMessageContent":
                        $this->content = $arg;
                        if ($folder !== false) {
                            $this->folder   = $folder;
                        }
                        if ($id !== false) {
                            $this->id       = $id;
                        }
                        if ($version !== false) {
                            $this->version  = $version;
                        }
                        if ($subject !== false) {
                            $this->subject = $subject;
                        }
                        if ($confirm !== NULL) {
                            $this->confirm  = $confirm;
                        }
                        break;
                    default:
                        throw new CbgrnSoapFault("008", "CbgrnThreadType class");
                    }
                } else {
                    throw new CbgrnSoapFault("008", "CbgrnThreadType class");
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->addressee)) {
                $this->addressee = new CbgrnMessageAddressee($std_class->addressee);
            }
            $this->content = new CbgrnMessageContent($std_class->content);
            if (isset($std_class->follow)) {
                if (is_array($std_class->follow)) {
                    foreach ($std_class->follow as $follow) {
                        $this->follow[] = new CbgrnMessageFollow($follow);
                    }
                } else {
                    $this->follow = new CbgrnMessageFollow($std_class->follow);
                }
            }
            $this->folder = $std_class->folder;
            if (isset($std_class->creator)) {
                $this->creator = new CbgrnChangeLogType($std_class->creator);
            }
            if (isset($std_class->modifier)) {
                $this->modifier = new CbgrnChangeLogType($std_class->modifier);
            }
            $this->id = $std_class->id;
            $this->version = $std_class->version;
            $this->subject = $std_class->subject;
            $this->confirm = $std_class->confirm;
            if (isset($std_class->snapshot)) {
                $this->snapshot = strtotime($std_class->snapshot);
            }
            if (isset($std_class->is_draft)) {
                $this->is_draft = $std_class->is_draft;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->addressee !== NULL) {
                if (is_array($this->addressee)) {
                    foreach ($this->addressee as $key => $tmp) {
                        $ret_val["addressee"][] = $tmp->getObjectVars();
                    }
                } else {
                    $ret_val["addressee"]   = $this->addressee->getObjectVars();
                }
            }
            $ret_val["content"]         = $this->content->getObjectVars();
            if ($this->follow !== NULL) {
                if (is_array($this->follow)) {
                    foreach ($this->follow as $follow) {
                        $ret_val["follow"][] = $follow->getObjectVars();
                    }
                } else {
                    $ret_val["follow"]  = $this->follow->getObjectVars();
                }
            }
            $ret_val["folder"]  = $this->folder;
            if ($this->creator !== NULL) {
                $ret_val["creator"]     = $this->creator->getObjectVars();
            }
            if ($this->modifier !== NULL) {
                $ret_val["modifier"]    = $this->modifier->getObjectVars();
            }
            $ret_val["id"]              = $this->id;
            $ret_val["version"]         = $this->version;
            $ret_val["subject"]         = $this->subject;
            $ret_val["confirm"]         = $this->confirm;
            if ($this->snapshot !== NULL) {
                $ret_val["snapshot"]    = gmdate(W3C_DATETIME_FORMAT, $this->snapshot);
            }
            if ($this->is_draft !== NULL) {
                $ret_val["is_draft"]    = $this->is_draft;
            }
            return $ret_val;
        }
    }

    class CbgrnMessageRemoveThreadType
    {
        public $folder_id;          // IDType
        public $thread_id;          // IDType

        public function __construct($arg = false, $thread_id = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->folder_id = $arg;
                    $this->thread_id = $thread_id;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->folder_id = $std_class->folder_id;
            $this->thread_id = $std_class->thread_id;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["folder_id"]   = $this->folder_id;
            $ret_val["thread_id"]   = $this->thread_id;
            return $ret_val;
        }
    }

    class CbgrnMessageFollowType
    {
        public $file = NULL;        // CbgrnMessageFile class or this array
        public $creator = NULL;     // CbgrnChangeLogType class
        public $id;                 // IDType
        public $number;             // integer
        public $text;            // string (UTF8 encoded)
        public $html_text = NULL; // string (UTF8 encoded)

        public function __construct($arg = false, $number = false, $text = false, $html_text = false, $files = false, $creator = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->id       = $arg; // first argument is ID
                    $this->number   = $number;
                    $this->text = $text;
                    if ($html_text !== false) {
                        $this->html_text = $html_text;
                    }
                    if ($files !== false) {
                        $this->file = $files;
                    }
                    if ($creator !== false) {
                        $this->creator = $creator;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->file)) {
                $this->file = new CbgrnMessageFile($std_class->file);
            }
            if (isset($std_class->creator)) {
                $this->creator = new CbgrnChangeLogType($std_class->creator);
            }
            $this->id = $std_class->id;
            $this->number = $std_class->number;
            $this->text   = $std_class->text;
            if (isset($std_class->html_text)) {
                $this->html_text = $std_class->html_text;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->file !== NULL) {
                $ret_val["file"]    = $this->file->getObjectVars();
            }
            if ($this->creator !== NULL) {
                $ret_val["creator"] = $this->creator->getObjectVars();
            }
            $ret_val["id"]  = $this->id;
            $ret_val["number"]  = $this->number;
            $ret_val["text"]    = $this->text;
            if ($this->html_text !== NULL) {
                $ret_val["html_text"]   = $this->html_text;
            }
            return $ret_val;
        }
    }

    class CbgrnMessagePersonalProfileType
    {
        public $use_trash;          // boolean
        public $trash_duration;     // integer

        public function __construct($arg = false, $trash_duration = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->use_trash = $arg;
                    $this->trash_duration = $trash_duration;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->use_trash = $std_class->use_trash;
            $this->trash_duration = $std_class->trash_duration;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["use_trash"]   = $this->use_trash;
            $ret_val["trash_duration"] = $this->trash_duration;
            return $ret_val;
        }
    }

    class CbgrnReportFollowType
    {
        public $id;                 // IDType
        public $number;             // string
        public $text;            // string (UTF8 encoded)
        public $html_text = NULL; // string (UTF8 encoded) optional
        public $creator;            // CbgrnChangeLogType class
        public $file = NULL;        // CbgrnFileFieldValueType class or this array

        public function __construct($arg = false, $number = false, $text = false, $html_text = false, $creator = false, $file = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->id       = $arg;
                    $this->number   = $number;
                    $this->text = $text;
                    if ($html_text !== false) {
                        $this->html_text = $html_text;
                    }
                    $this->creator  = $creator;
                    if ($file !== false) {
                        $this->file = $file;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->id       = $std_class->id;
            $this->number   = $std_class->number;
            $this->text     = $std_class->text;
            if (isset($std_class->html_text)) {
                $this->html_text = $std_class->html_text;
            }
            $this->creator  = new CbgrnChangeLogType($std_class->creator);
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    foreach ($std_class->file as $tmp_file) {
                        $this->file[] = new CbgrnFileFieldValueType($tmp_file);
                    }
                } else {
                    $this->file = new CbgrnFileFieldValueType($std_class->file);
                }
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]      = $this->id;
            $ret_val["number"]  = $this->number;
            $ret_val["text"]    = $this->text;
            if ($this->html_text !== NULL) {
                $ret_val["html_text"] = $this->html_text;
            }
            $ret_val["creator"] = $this->creator->getObjectVars();
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    foreach ($this->file as $tmp_file) {
                        $ret_val["file"][] = $tmp_file->getObjectVars();
                    }
                } else {
                    $ret_val["file"]    = $this->file->getObjectVars();
                }
            }
            return $ret_val;
        }
    }

    class CbgrnReportAddFollowType
    {
        public $report_id;          // IDType
        public $follow = NULL;      // CbgrnReportFollowType (only ONE)
        public $file = NULL;        // CbgrnFileType or this array

        public function __construct($arg = false, $follow = false, $files = false) {
            // $arg : -> report_id : IDType
            // $follow : CbgrnReportFollowType class (file attrib need not set)
            // $files  : string or string array (file path)
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->report_id    = $arg;
                    if (is_object($follow) && (get_class($follow) == "CbgrnReportFollowType")) {
                        $this->follow = $follow;
                    } else {
                        throw new CbgrnSoapFault("006", "CbgrnReportAddFollowType class");
                    }
                    if ($files !== false) {
                        if (is_array($files)) {
                            $n = 1;
                            foreach ($files as $tmp_file) {
                                $this->file[] = new CbgrnFileType($n, $tmp_file);
                                $n++;
                                if ($this->follow !== NULL) {
                                    $this->follow->file[] = new CbgrnFileFieldValueType(basename($tmp_file), $n, "", filesize($tmp_file));
                                }
                            }
                            
                        } else {
                            $this->file = new CbgrnFileType(1, $files);
                            if ($this->follow !== NULL) {
                                $this->follow->file = new CbgrnFileFieldValueType(basename($files), 1, "", filesize($files));
                            }
                        }
                    }
                }
            }
        }

        public function setFile($path) {
            
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->report_id    = $std_class->report_id;
            $this->follow       = new CbgrnReportFollowType($std_class->follow);
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    foreach ($std_class->file as $tmp_file) {
                        $this->file[] = new CbgrnFileType($tmp_file);
                    }
                } else {
                    $this->file = new CbgrnFileType($std_class->file);
                }
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["report_id"]   = $this->report_id;
            $ret_val["follow"]      = $this->follow->getObjectVars();
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    foreach ($this->file as $tmp_file) {
                        $ret_val["file"][] = $tmp_file->getObjectVars();
                    }
                } else {
                    $ret_val["file"]    = $this->file->getObjectVars();
                }
            }
            return $ret_val;
        }
    }

    class CbgrnWorkflowOperationType
    {
        public $applicant = NULL;
        public $step_id = NULL;         // IDType
        public $approve = NULL;
        public $reject = NULL;
        public $withdraw = NULL;
        public $cancel = NULL;
        public $confirm = NULL;

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->sent_back)) {
                if (isset($std_class->sent_back->applicant)) {
                    $this->applicant = $std_class->sent_back->applicant;
                }
                if (isset($std_class->sent_back->step)) {
                    if (isset($std_class->sent_back->step->step_id)) {
                        $this->step_id = $std_class->sent_back->step->step_id;
                    }
                }
            }
            if (isset($std_class->approve)) {
                $this->approve = $std_class->approve;
            }
            if (isset($std_class->reject)) {
                $this->reject  = $std_class->reject;
            }
            if (isset($std_class->withdraw)) {
                $this->withdraw = $std_class->withdraw;
            }
            if (isset($std_class->cancel)) {
                $this->cancel  = $std_class->cancel;
            }
            if (isset($std_class->confirm)) {
                $this->confirm = $std_class->confirm;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->applicant !== NULL) {
                $ret_val["sent_back"]["applicant"] = $this->applicant;
            }
            if ($this->step_id !== NULL) {
                $ret_val["sent_back"]["step"]["step_id"] = $this->step_id;
            }
            if ($this->approve !== NULL) {
                $ret_val["approve"] = $this->approve;
            }
            if ($this->reject  !== NULL) {
                $ret_val["reject"]  = $this->reject;
            }
            if ($this->withdraw !== NULL) {
                $ret_val["withdraw"] = $this->withdraw;
            }
            if ($this->cancel !== NULL) {
                $ret_val["cancel"]   = $this->cancel;
            }
            if ($this->confirm !== NULL) {
                $ret_val["confirm"] = $this->confirm;
            }
            return $ret_val;
        }
    }
    
    class CbgrnWorkflowHandleApplicationOperationType
    {
        public $application_id;         // IDType
        public $delegator_id = NULL;    // IDType
        public $comment = NULL;      // string (UTF8 encoded)
        public $operation;              // CbgrnWorkflowOperationType

        public function __construct($arg = false, $operation = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->application_id = $arg;
                    if ($operation !== false) {
                        $this->operation = $operation;
                    }
                }
            }
        }
        
        public function setFromStdClass(stdClass $std_class) {
            $this->application_id   = $std_class->application_id;
            if (isset($std_class->delegator_id)) {
                $this->delegator_id = $std_class->delegator_id;
            }
            if (isset($std_class->comment)) {
                $this->comment      = $std_class->comment;
            }
            $this->operation        = new CbgrnWorkflowOperationtype($std_class->operation);
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["application_id"]  = $this->application_id;
            if ($this->delegator_id !== NULL) {
                $ret_val["delegator_id"]    = $this->delegator_id;
            }
            if ($this->comment !== NULL) {
                $ret_val["comment"]         = $this->comment;
            }
            $ret_val["operation"]   = $this->operation->getObjectVars();
            return $ret_val;
        }
    }

    class CbgrnWorkflowGetRequestType
    {
        public $request_form_id;        // IDType
        public $filter = NULL;          // string (enum see WorkflowGetManagerRequestFilter)
        public $start_request_date = NULL;  // UNIX timestamp
        public $end_request_date = NULL;    // UNIX timestamp
        public $start_approval_date = NULL; // UNIX timestamp
        public $end_approval_date = NULL;   // UNIX timestamp
        public $applicant = NULL;       // IDType
        public $last_approval = NULL;   // IDType
        public $start_to_get_information_from = NULL;   // IDType
        public $maximum_request_amount_to_get = NULL;   // string

        public function __construct($arg = false) {
            if ($arg === false) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                } else {
                    $this->request_form_id = $arg;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->request_form_id      = $std_class->request_form_id;
            if (isset($std_class->filter)) {
                $this->filter           = $std_class->filter;
            }
            if (isset($std_class->start_request_date)) {
                $this->start_request_date   = strtotime($std_class->start_request_date);
            }
            if (isset($std_class->end_request_date)) {
                $this->end_request_date     = strtotime($std_class->end_request_date);
            }
            if (isset($std_class->start_approval_date)) {
                $this->start_approval_date  = strtotime($std_class->start_approval_date);
            }
            if (isset($std_class->end_approval_date)) {
                $this->end_approval_date    = strtotime($std_class->end_approval_date);
            }
            if (isset($std_class->applicant)) {
                $this->applicant            = $std_class->applicant;
            }
            if (isset($std_class->last_approval)) {
                $this->last_approval        = $std_class->last_approval;
            }
            if (isset($std_class->start_to_get_information_from)) {
                $this->start_to_get_information_from    = $std_class->start_to_get_information_from;
            }
            if (isset($std_class->maximum_request_amount_to_get)) {
                $this->maximum_request_amount_to_get    = $std_class->maximum_request_amount_to_get;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["request_form_id"]     = $this->request_form_id;
            if ($this->filter !== NULL) {
                $ret_val["filter"]          = $this->filter;
            }
            if ($this->start_request_date !== NULL) {
                $ret_val["start_request_date"]  = gmdate(W3C_DATETIME_FORMAT, $this->start_request_date);
            }
            if ($this->end_request_date !== NULL) {
                $ret_val["end_request_date"]    = gmdate(W3C_DATETIME_FORMAT, $this->end_request_date);
            }
            if ($this->start_approval_date !== NULL) {
                $ret_val["start_approval_date"] = gmdate(W3C_DATETIME_FORMAT, $this->start_approval_date);
            }
            if ($this->end_approval_date !== NULL) {
                $ret_val["end_approval_date"]   = gmdate(W3C_DATETIME_FORMAT, $this->end_approval_date);
            }
            if ($this->applicant !== NULL) {
                $ret_val["applicant"]           = $this->applicant;
            }
            if ($this->last_approval !== NULL) {
                $ret_val["last_approval"]       = $this->last_approval;
            }
            if ($this->start_to_get_information_from !== NULL) {
                $ret_val["start_to_get_information_from"] = $this->start_to_get_information_from;
            }
            if ($this->maximum_request_amount_to_get !== NULL) {
                $ret_val["maximum_request_amount_to_get"] = $this->maximum_request_amount_to_get;
            }
            return $ret_val;
        }
    }

    class CbgrnNotificationIdType
    {
        public $module_id;      // IDType
        public $item;           // string

        public function __construct($module_id = false, $item = false) {
            if ($module_id === false) {
                ;
            } else {
                if (is_object($module_id) && (get_class($module_id) == "stdClass")) {
                    $this->setFromStdClass($module_id);
                } else {
                    $this->module_id    = $module_id;
                    $this->item         = $item;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->module_id    = $std_class->module_id;
            $this->item         = $std_class->item;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["module_id"]   = $this->module_id;
            $ret_val["item"]        = $this->item;
            return $ret_val;
        }
    }
    
    class CbgrnNotificationItemVersionType
    {
        public $notification_id;        // type:CbgrnNotificationIdType
        public $version;                // type:VersionType == Non blank string;

        public function __construct($id, $version = false) {
            if (is_object($id) && (get_class($id) == "stdClass")) {
                $this->setFromStdClass($id);
            } else {
                $this->notification_id = $id;
                if ($version === false) {
                    $this->version = 1;
                } else {
                    $this->version = $version;
                }
            }
        }
        public function _check() {
            if (get_class($this->notification_id) != "CbgrnNotificationIdType") {
                throw new SoapFault("998", "id is not set in CbgrnNotificationItemVersionType");
            }
            if (strlen($this->version) == 0) {
                throw new SoapFault("998", "version is not set in CbgrnNotificationItemVersionType");
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->notification_id   = new CbgrnNotificationIdType($std_class->notification_id);
            $this->version = $std_class->version;
        }
        
        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["notification_id"]      = $this->notification_id->getObjectVars();
            $ret_val["version"] = $this->version;
            return $ret_val;
        }
    }

    class CbgrnMailAddressType
    {
        public $name = NULL;             // string
        public $address = NULL;          // string

        public function __construct($name = NULL, $address = NULL) {
            if ($name === NULL) {
                ;
            } else {
                if (is_object($name) && (get_class($name) == "stdClass")) {
                    $this->setFromStdClass($name);
                } else {
                    if ($name !== NULL) {
                        $this->name = $name;
                    }
                    if ($address !== NULL) {
                        $this->address = $address;
                    }
                }
            }
        }
        
        public function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->name)) {
                $this->name = $std_class->name;
            }
            if (isset($std_class->address)) {
                $this->address = $std_class->address;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->name !== NULL) {
                $ret_val["name"]    = $this->name;
            }
            if ($this->address !== NULL) {
                $ret_val["address"] = $this->address;
            }
            return $ret_val;
        }
    }

    class CbgrnMailSource
    {
        public $id;                         // IDType
        public $size = NULL;                // integer

        public function __construct($id = NULL, $size = NULL) {
            if ($id === NULL) {
                ;
            } else {
                if (is_object($id) && (get_class($std_class) == "stdClass")) {
                    $this->setFromStdClass($id);
                } else {
                    $this->id = $id;
                    if ($size !== NULL) {
                        $this->size = $size;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->id   = $std_class->id;
            if (isset($std_class->size)) {
                $this->size = $std_class->size;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]      = $this->id;
            if ($this->size !== NULL) {
                $ret_val["size"]    = $this->size;
            }
            return $ret_val;
        }
    }

    class CbgrnMailType
    {
        public $key;                        // IDType
        public $version;                    // VersionType := integer
        public $subject;                 // string (UTF8 encoded)
        public $body;                    // string (UTF8 encoded)
        public $html_body = NULL;        // string (UTF8 encoded)
        public $date = NULL;                // UNIX timestamp
        public $read = NULL;                // UNIX timestamp
        public $size = NULL;                // integer
        public $is_sent = NULL;             // boolean
        public $is_draft = NULL;            // boolean
        public $folder_key;                 // IDType
        public $from = NULL;                // CbgrnMailAddressType (0-1)
        public $sender = NULL;              // CbgrnMailAddressType (0-1)
        public $to = NULL;                  // CbgrnMailAddressType or this array
        public $cc = NULL;                  // CbgrnMailAddressType or this array
        public $bcc = NULL;                 // CbgrnMailAddressType or this array
        public $reply_to = NULL;            // CbgrnMailAddressType (0-1)
        public $disposition_notification_to = NULL; // CbgrnMailAddressType (0-1)
        public $file = NULL;                // CbgrnTopicTypeFile class or this array
        public $source = NULL;              // CbgrnMailSource class or this array

        public function __construct($folder_key = NULL, $key = NULL, $version = NULL, $subject = NULL, $body = NULL) {
            if ($folder_key === NULL) {
                ;
            } else {
                if (is_object($folder_key) && (get_class($folder_key) == "stdClass")) {
                    $this->setFromStdClass($folder_key);
                } else {
                    $this->folder_key   = $folder_key;
                    if ($key !== NULL) {
                        $this->key  = $key;
                    } else {
                        $this->key  = "dummy";
                    }
                    if ($version !== NULL) {
                        $this->version = $version;
                    } else {
                        $this->version = "dummy";
                    }
                    $this->subject = $subject;
                    $this->body = $body;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->key          = $std_class->key;
            $this->version      = $std_class->version;
            $this->subject      = $std_class->subject;
            $this->body         = $std_class->body;
            if (isset($std_class->html_body)) {
                $this->html_body    = $std_class->html_body;
            }
            if (isset($std_class->date)) {
                $this->date         = strtotime($std_class->date);
            }
            if (isset($std_class->read)) {
                $this->read         = strtotime($std_class->read);
            }
            if (isset($std_class->size)) {
                $this->size         = $std_class->size;
            }
            if (isset($std_class->is_sent)) {
                $this->is_sent      = $std_class->is_sent;
            }
            if (isset($std_class->is_draft)) {
                $this->is_draft     = $std_class->is_draft;
            }
            $this->folder_key       = $std_class->folder_key;
            if (isset($std_class->from)) {
                $this->from         = new CbgrnMailAddressType($std_class->from);
            }
            if (isset($std_class->sender)) {
                $this->sender       = new CbgrnMailAddressType($std_class->sender);
            }
            if (isset($std_class->to)) {
                if (is_array($std_class->to)) {
                    foreach ($std_class->to as $addr) {
                        $this->to[] = new CbgrnMailAddressType($addr);
                    }
                } else {
                    $this->to       = new CbgrnMailAddressType($std_class->to);
                }
            }
            if (isset($std_class->cc)) {
                if (is_array($std_class->cc)) {
                    foreach ($std_class->cc as $addr) {
                        $this->cc[] = new CbgrnMailAddressType($addr);
                    }
                } else {
                    $this->cc       = new CbgrnMailAddressType($std_class->cc);
                }
            }
            if (isset($std_class->bcc)) {
                if (is_array($std_class->bcc)) {
                    foreach ($std_class->bcc as $addr) {
                        $this->bcc[] = new CbgrnMailAddressType($addr);
                    }
                } else {
                    $this->bcc[]    = new CbgrnMailAddressType($std_class->bcc);
                }
            }
            if (isset($std_class->reply_to)) {
                $this->reply_to = new CbgrnMailAddressType($std_class->reply_to);
            }
            if (isset($std_class->disposition_notification_to)) {
                $this->disposition_notification_to = new CbgrnMailAddressType($std_class->disposition_notification_to);
            }
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    foreach ($std_class->file as $tmp_file) {
                        $this->file[] = new CbgrnTopicTypeFile($tmp_file);
                    }
                } else {
                    $this->file     = new CbgrnTopicTypeFile($std_class->file);
                }
            }
            if (isset($std_class->source)) {
                if (is_array($std_class->source)) {
                    foreach ($std_class->source as $tmp) {
                        $this->source[] = new CbgrnMailSource($tmp);
                    }
                } else {
                    $this->source   = new CbgrnMailSource($std_class->source);
                }
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["key"]     = $this->key;
            $ret_val["version"] = $this->version;
            $ret_val["subject"] = $this->subject;
            $ret_val["body"]    = $this->body;
            if ($this->html_body !== NULL) {
                $ret_val["html_body"]   = $this->html_body;
            }
            if ($this->date !== NULL) {
                $ret_val["date"]        = gmdate(W3C_DATETIME_FORMAT, $this->date);
            }
            if ($this->read !== NULL) {
                $ret_val["read"]        = gmdate(W3C_DATETIME_FORMAT, $this->read);
            }
            if ($this->size !== NULL) {
                $ret_val["size"]        = $this->size;
            }
            if ($this->is_sent !== NULL) {
                $ret_val["is_sent"]     = $this->is_sent;
            }
            if ($this->is_draft !== NULL) {
                $ret_val["is_draft"]    = $this->is_draft;
            }
            $ret_val["folder_key"]  = $this->folder_key;
            if ($this->from !== NULL) {
                $ret_val["from"]        = $this->from->getObjectVars();
            }
            if ($this->sender !== NULL) {
                $ret_val["sender"]      = $this->sender->getObjectVars();
            }
            if ($this->to !== NULL) {
                if (is_array($this->to)) {
                    foreach ($this->to as $addr) {
                        $ret_val["to"][] = $addr->getObjectVars();
                    }
                } else {
                    $ret_val["to"]      = $this->to->getObjectVars();
                }
            }
            if ($this->cc !== NULL) {
                if (is_array($this->cc)) {
                    foreach ($this->cc as $addr) {
                        $ret_val["cc"][] = $addr->getObjectVars();
                    }
                } else {
                    $ret_val["cc"]      = $this->cc->getObjectVars();
                }
            }
            if ($this->bcc !== NULL) {
                if (is_array($this->bcc)) {
                    foreach ($this->bcc as $addr) {
                        $ret_val["bcc"][] = $addr->getObjectVars();
                    }
                } else {
                    $ret_val["bcc"]     = $this->bcc->getObjectVars();
                }
            }
            if ($this->reply_to !== NULL) {
                $ret_val["reply_to"]    = $this->reply_to->getObjectVars();
            }
            if ($this->disposition_notification_to !== NULL) {
                $ret_val["disposition_notification_to"] = $this->disposition_notification_to->getObjectVars();
            }
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    foreach ($this->file as $tmp_file) {
                        $ret_val["file"][] = $tmp_file->getObjectVars();
                    }
                } else {
                    $ret_val["file"]    = $this->file->getObjectVars();
                }
            }
            if ($this->source !== NULL) {
                if (is_array($this->source)) {
                    foreach ($this->source as $tmp) {
                        $ret_val["source"][] = $tmp->getObjectVars();
                    }
                } else {
                    $ret_val["source"]  = $this->source->getObjectVars();
                }
            }
            return $ret_val;
        }
    }

    class CbgrnMailSendMailType
    {
        public $account_id;                 // IDType
        public $from_string = NULL;      // string (UTF8 encoded)
        public $sender_string = NULL;    // string (UTF8 encoded)
        public $to_string = NULL;        // string (UTF8 encoded)
        public $cc_string = NULL;        // string (UTF8 encoded)
        public $bcc_string = NULL;       // string (UTF8 encoded)
        public $reply_to_string = NULL;  // string (UTF8 encoded)
        public $draft_id = NULL;            // IDType
        public $mail;                       // CbgrnMailType class
        public $file = NULL;                // CbgrnFileType or this array
        public $remove_file_id = NULL;      // IDType or this array

        public function __construct($account_id = NULL, CbgrnMailType $mail = NULL) {
            if ($account_id === NULL) {
                ;
            } else {
                if (is_object($account_id) && (get_class($account_id) == "stdClass")) {
                    $this->setFromStdClass($account_id);
                } else {
                    $this->account_id   = $account_id;
                    if ($mail !== NULL) {
                        $this->mail = $mail;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->account_id       = $std_class->account_id;
            if (isset($std_class->from_string)) {
                $this->from_string  = $std_class->from_string;
            }
            if (isset($std_class->sender_string)) {
                $this->sender_string    = $std_class->sender_string;
            }
            if (isset($std_class->to_string)) {
                $this->to_string    = $std_class->to_string;
            }
            if (isset($std_class->cc_string)) {
                $this->cc_string    = $std_class->cc_string;
            }
            if (isset($std_class->bcc_string)) {
                $this->bcc_string   = $std_class->bcc_string;
            }
            if (isset($std_class->reply_to_string)) {
                $this->reply_to_string = $std_class->reply_to_string;
            }
            if (isset($std_class->draft_id)) {
                $this->draft_id     = $std_class->draft_id;
            }
            $this->mail = new CbgrnMailType($std_class->mail);
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    foreach ($std_class->file as $tmp_file) {
                        $this->file[] = new CbgrnFileType($tmp_file);
                    }
                } else {
                    $this->file = new CbgrnFileType($std_class->file);
                }
            }
            if (isset($std_class->remove_file_id)) {
                $this->remove_file_id = $std_class->remove_file_id;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["account_id"]      = $this->account_id;
            if ($this->from_string !== NULL) {
                $ret_val["from_string"] = $this->from_string;
            }
            if ($this->sender_string !== NULL) {
                $ret_val["sender_string"]   = $this->sender_string;
            }
            if ($this->to_string !== NULL) {
                $ret_val["to_string"]       = $this->to_string;
            }
            if ($this->cc_string !== NULL) {
                $ret_val["cc_string"]       = $this->cc_string;
            }
            if ($this->bcc_string !== NULL) {
                $ret_val["bcc_string"]      = $this->bcc_string;
            }
            if ($this->reply_to_string !== NULL) {
                $ret_val["reply_to_string"] = $this->reply_to_string;
            }
            $ret_val["mail"]    = $this->mail->getObjectVars();
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    foreach ($this->file as $tmp_file) {
                        $ret_val["file"][] = $tmp_file->getObjectVars();
                    }
                } else {
                    $ret_val["file"]    = $this->file->getObjectVars();
                }
            }
            if ($this->remove_file_id !== NULL) {
                $ret_val["remove_file_id"]  = $this->remove_file_id;
            }
            return $ret_val;
        }
    }

    class CbgrnMailForwardMailType
    {
        public $account_id;                 // IDType
        public $mail_id;                    // IDType
        public $from_string = NULL;      // string (UTF8 encoded)
        public $sender_string = NULL;    // string (UTF8 encoded)
        public $to_string = NULL;        // string (UTF8 encoded)
        public $cc_string = NULL;        // string (UTF8 encoded)
        public $bcc_string = NULL;       // string (UTF8 encoded)
        public $reply_to_string = NULL;  // string (UTF8 encoded)
        public $draft_id = NULL;            // IDType
        public $mail;                       // CbgrnMailType class
        public $file = NULL;                // CbgrnFileType or this array
        public $remove_file_id = NULL;      // IDType or this array

        public function __construct($account_id = NULL, $mail_id = NULL, CbgrnMailType $mail = NULL) {
            if ($account_id === NULL) {
                ;
            } else {
                if (is_object($account_id) && (get_class($account_id) == "stdClass")) {
                    $this->setFromStdClass($account_id);
                } else {
                    $this->account_id   = $account_id;
                    $this->mail_id      = $mail_id;
                    if ($mail !== NULL) {
                        $this->mail = $mail;
                    }
                }
            }
        }
        
        public function setFromStdClass(stdClass $std_class) {
            $this->account_id       = $std_class->account_id;
            $this->mail_id          = $std_class->mail_id;
            if (isset($std_class->from_string)) {
                $this->from_string  = $std_class->from_string;
            }
            if (isset($std_class->sender_string)) {
                $this->sender_string    = $std_class->sender_string;
            }
            if (isset($std_class->to_string)) {
                $this->to_string    = $std_class->to_string;
            }
            if (isset($std_class->cc_string)) {
                $this->cc_string    = $std_class->cc_string;
            }
            if (isset($std_class->bcc_string)) {
                $this->bcc_string   = $std_class->bcc_string;
            }
            if (isset($std_class->reply_to_string)) {
                $this->reply_to_string = $std_class->reply_to_string;
            }
            if (isset($std_class->draft_id)) {
                $this->draft_id     = $std_class->draft_id;
            }
            $this->mail = new CbgrnMailType($std_class->mail);
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    foreach ($std_class->file as $tmp_file) {
                        $this->file[] = new CbgrnFileType($tmp_file);
                    }
                } else {
                    $this->file = new CbgrnFileType($std_class->file);
                }
            }
            if (isset($std_class->remove_file_id)) {
                $this->remove_file_id = $std_class->remove_file_id;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["account_id"]      = $this->account_id;
            $ret_val["mail_id"]         = $this->mail_id;
            if ($this->from_string !== NULL) {
                $ret_val["from_string"] = $this->from_string;
            }
            if ($this->sender_string !== NULL) {
                $ret_val["sender_string"]   = $this->sender_string;
            }
            if ($this->to_string !== NULL) {
                $ret_val["to_string"]       = $this->to_string;
            }
            if ($this->cc_string !== NULL) {
                $ret_val["cc_string"]       = $this->cc_string;
            }
            if ($this->bcc_string !== NULL) {
                $ret_val["bcc_string"]      = $this->bcc_string;
            }
            if ($this->reply_to_string !== NULL) {
                $ret_val["reply_to_string"] = $this->reply_to_string;
            }
            $ret_val["mail"]    = $this->mail->getObjectVars();
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    foreach ($this->file as $tmp_file) {
                        $ret_val["file"][] = $tmp_file->getObjectVars();
                    }
                } else {
                    $ret_val["file"]    = $this->file->getObjectVars();
                }
            }
            if ($this->remove_file_id !== NULL) {
                $ret_val["remove_file_id"]  = $this->remove_file_id;
            }
            return $ret_val;
        }
    }

    class CbgrnMailDraftMailType
    {
        public $account_id;                 // IDType
        public $operation = NULL;           // string (enum 'send','reply','reply_all','forward')
        public $from_string = NULL;      // string (UTF8 encoded)
        public $sender_string = NULL;    // string (UTF8 encoded)
        public $to_string = NULL;        // string (UTF8 encoded)
        public $cc_string = NULL;        // string (UTF8 encoded)
        public $bcc_string = NULL;       // string (UTF8 encoded)
        public $reply_to_string = NULL;  // string (UTF8 encoded)
        public $draft_id = NULL;            // IDType
        public $mail;                       // CbgrnMailType class
        public $file = NULL;                // CbgrnFileType or this array
        public $remove_file_id = NULL;      // IDType or this array

        public function __construct($account_id = NULL, CbgrnMailType $mail= NULL) {
            if ($account_id === NULL) {
                ;
            } else {
                if (is_object($account_id) && (get_class($account_id) == "stdClass")) {
                    $this->setFromStdClass($account_id);
                } else {
                    $this->account_id   = $account_id;
                    if ($mail !== NULL) {
                        $this->mail = $mail;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->account_id       = $std_class->account_id;
            if (isset($std_class->operation)) {
                $this->operation    = $std_class->operation;
            }
            if (isset($std_class->from_string)) {
                $this->from_string  = $std_class->from_string;
            }
            if (isset($std_class->sender_string)) {
                $this->sender_string    = $std_class->sender_string;
            }
            if (isset($std_class->to_string)) {
                $this->to_string    = $std_class->to_string;
            }
            if (isset($std_class->cc_string)) {
                $this->cc_string    = $std_class->cc_string;
            }
            if (isset($std_class->bcc_string)) {
                $this->bcc_string   = $std_class->bcc_string;
            }
            if (isset($std_class->reply_to_string)) {
                $this->reply_to_string = $std_class->reply_to_string;
            }
            if (isset($std_class->draft_id)) {
                $this->draft_id     = $std_class->draft_id;
            }
            $this->mail = new CbgrnMailType($std_class->mail);
            if (isset($std_class->file)) {
                if (is_array($std_class->file)) {
                    foreach ($std_class->file as $tmp_file) {
                        $this->file[] = new CbgrnFileType($tmp_file);
                    }
                } else {
                    $this->file = new CbgrnFileType($std_class->file);
                }
            }
            if (isset($std_class->remove_file_id)) {
                $this->remove_file_id = $std_class->remove_file_id;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["account_id"]      = $this->account_id;
            if ($this->operation !== NULL) {
                $ret_val["operation"]   = $this->operation;
            }
            if ($this->from_string !== NULL) {
                $ret_val["from_string"] = $this->from_string;
            }
            if ($this->sender_string !== NULL) {
                $ret_val["sender_string"]   = $this->sender_string;
            }
            if ($this->to_string !== NULL) {
                $ret_val["to_string"]       = $this->to_string;
            }
            if ($this->cc_string !== NULL) {
                $ret_val["cc_string"]       = $this->cc_string;
            }
            if ($this->bcc_string !== NULL) {
                $ret_val["bcc_string"]      = $this->bcc_string;
            }
            if ($this->reply_to_string !== NULL) {
                $ret_val["reply_to_string"] = $this->reply_to_string;
            }
            $ret_val["mail"]    = $this->mail->getObjectVars();
            if ($this->file !== NULL) {
                if (is_array($this->file)) {
                    foreach ($this->file as $tmp_file) {
                        $ret_val["file"][] = $tmp_file->getObjectVars();
                    }
                } else {
                    $ret_val["file"]    = $this->file->getObjectVars();
                }
            }
            if ($this->remove_file_id !== NULL) {
                $ret_val["remove_file_id"]  = $this->remove_file_id;
            }
            return $ret_val;
        }
    }

    class CbgrnMailOpenDispositionNotificationOperationType
    {
        public $account_id;             // IDType
        public $mail_id;                // IDType
        public $type;                   // string (enum: 'open' or 'ignore')

        public function __construct($account_id = NULL, $mail_id = NULL, $type = NULL) {
            if ($account_id === NULL) {
                ;
            } else {
                if (is_object($account_id) && (get_class($account_id) == "stdClass")) {
                    $this->setFromStdClass($account_id);
                } else {
                    $this->account_id   = $account_id;
                    $this->mail_id      = $mail_id;
                    $this->type         = $type;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->account_id       = $std_class->account_id;
            $this->mail_id          = $std_class->mail_id;
            $this->type             = $std_class->type;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["account_id"]  = $this->account_id;
            $ret_val["mail_id"]     = $this->mail_id;
            $ret_val["type"]        = $this->type;
            return $ret_val;
        }
    }

    class CbgrnFolderType
    {
        public $key;                        // string -- folder idetified string
        public $description = NULL;      // string
        public $subscribe = NULL;           // boolean
        public $mail_id = NULL;             // IDType or this array
        public $name;                    // string
        public $order;                      // integer

        public function __construct($key = NULL, $name = NULL, $order = NULL) {
            if ($key === NULL) {
                ;
            } else {
                if (is_object($key) && (get_class($key) == "stdClass")) {
                    $this->setFromStdClass($key);
                } else {
                    $this->key      = $key;
                    $this->name = $name;
                    $this->order    = $order;
                }
            }
        }
        
        public function setFromStdClass(stdClass $std_class) {
            $this->key      = $std_class->key;
            if (isset($std_class->description)) {
                $this->description  = $std_class->description;
            }
            if (isset($std_class->subscribe)) {
                $this->subscribe    = $std_class->subscribe;
            }
            if (isset($std_class->mail)) {
                if (is_array($std_class->mail)) {
                    foreach ($std_class->mail as $mail_id) {
                        $this->mail_id[] = $mail_id->id;
                    }
                } else {
                    $this->mail_id  = $std_class->mail->id;
                }
            }
            $this->name     = $std_class->name;
            $this->order    = $std_class->order;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["key"]         = $this->key;
            if ($this->description !== NULL) {
                $ret_val["description"] = $this->description;
            }
            if ($this->subscribe !== NULL) {
                $ret_val["subscribe"]   = $this->subscribe;
            }
            if ($this->mail_id !== NULL) {
                $ret_val["mail"]["id"]  = $this->mail_id;
            }
            $ret_val["name"]        = $this->name;
            $ret_val["order"]       = $this->order;
            return $ret_val;
        }
    }
    
    class CbgrnMailModifyFolderOperationType
    {
        public $account_id;                 // IDType
        public $parent_folder_id = NULL;    // IDType
        public $folder;                     // CbgrnFolderType

        public function __construct($account_id = NULL, $folder = NULL, $parent_folder = NULL) {
            if ($account_id === NULL) {
                ;
            } else {
                if (is_object($account_id) && (get_class($account_id) == "stdClass")) {
                    $this->setFromStdClass($account_id);
                } else {
                    $this->account_id   = $account_id;
                    $this->folder       = $folder;
                    if ($parent_folder !== NULL) {
                        $this->parent_folder_id = $parent_folder;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->account_id   = $std_class->account_id;
            if (isset($std_class->parent_folder_id)) {
                $this->parent_folder_id = $std_class->parent_folder_id;
            }
            $this->folder   = new CbgrnFolderType($std_class->folder);
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["account_id"]      = $this->account_id;
            if ($this->parent_folder_id !== NULL) {
                $ret_val["parent_folder_id"]    = $this->parent_folder_id;
            }
            $ret_val["folder"]          = $this->folder->getObjectVars();
            return $ret_val;
        }
    }

    class CbgrnMailFromName
    {
        public $account_id;             // IDType
        public $name;                // string (UTF8 encoded)

        public function __construct($account_id, $name = NULL) {
            if ($account_id === NULL) {
                ;
            } else {
                if (is_object($account_id) && (get_class($account_id) == "stdClass")) {
                    $this->setFromStdClass($account_id);
                } else {
                    $this->account_id   = $account_id;
                    $this->name = $name;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->account_id   = $std_class->account_id;
            $this->name         = $std_class->name;
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["account_id"]  = $this->account_id;
            $ret_val["name"]        = $this->name;
            return $ret_val;
        }
    }
    
    class CbgrnMailPersonalProfileType
    {
        public $show_preview;           // boolean
        public $send_charset;           // boolean
        public $use_trash;              // boolean
        public $use_message_disposition_notification;   // boolean
        public $use_status = NULL;      // boolean
        public $reply_message_disposition_notification; // boolean
        public $send_vcard;             // boolean (reserve)
        public $wrap;                   // boolean (reserve)
        public $linewidth;              // boolean (reserve)
        public $use_history;            // boolean (reserve)
        public $from_name = NULL;       // CbgrnMailFromName class or this array

        public function __construct($arg = NULL) {
            if ($arg === NULL) {
                ;
            } else {
                if (is_object($arg) && (get_class($arg) == "stdClass")) {
                    $this->setFromStdClass($arg);
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->show_preview         = $std_class->show_preview;
            $this->send_charset         = $std_class->send_charset;
            $this->use_trash            = $std_class->use_trash;
            $this->use_message_disposition_notification = $std_class->use_message_disposition_notification;
            if (isset($std_class->use_status)) {
                $this->use_status       = $std_class->use_status;
            }
            $this->reply_message_disposition_notification = $std_class->reply_message_disposition_notification;
            $this->send_vcard           = $std_class->send_vcard;
            $this->wrap                 = $std_class->wrap;
            $this->linewidth            = $std_class->linewidth;
            $this->use_history          = $std_class->use_history;
            if (isset($std_class->from_name)) {
                if (is_array($std_class->from_name)) {
                    foreach ($std_class->from_name as $tmp) {
                        $this->from_name[] = new CbgrnMailFromName($tmp);
                    }
                } else {
                    $this->from_name    = new CbgrnMailFromName($std_class->from_name);
                }
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["show_preview"]    = $this->show_preview;
            $ret_val["send_charset"]    = $this->send_charset;
            $ret_val["use_trash"]       = $this->use_trash;
            $ret_val["use_message_disposition_notification"]    = $this->use_message_disposition_notification;
            if ($this->use_status !== NULL) {
                $ret_val["use_status"]  = $this->use_status;
            }
            $ret_val["reply_message_disposition_notification"]  = $this->reply_message_disposition_notification;
            $ret_val["send_vcard"]      = $this->send_vcard;
            $ret_val["wrap"]            = $this->wrap;
            $ret_val["linewidth"]       = $this->line_width;
            $ret_val["use_history"]     = $this->use_history;
            if ($this->from_name !== NULL) {
                if (is_array($this->from_name)) {
                    foreach ($this->from_name as $tmp) {
                        $ret_val["from_name"][] = $tmp->getObjectVars();
                    }
                } else {
                    $ret_val["from_name"] = $this->from_name->getObjectVars();
                }
            }
            return $ret_val;
        }
    }

    class CbgrnMailServerInfoType
    {
        public $id;                     // IDType
        public $server_code;            // string
        public $server_name;         // string (UTF8 encoded)
        public $outgoing_server_name;   // string (UTF8 encoded)
        public $outgoing_port_number;   // integer
        public $outgoing_encrypted_connection = NULL;   // string(enum: 'NONE','SSL','TLS')
        public $outgoing_smtp_auth = NULL;              // string
        public $outgoing_account_for_send = NULL;       // boolean
        public $outgoing_pop_before_smtp = NULL;        // boolean
        public $outgoing_pop_before_smtp_wait_time = NULL;  // integer
        public $outgoing_timeout = NULL;                // boolean
        public $incoming_server_name;                   // string (UTF8 encoded)
        public $incoming_receive_protocol;              // string
        public $incoming_port_number;                   // integer
        public $incoming_use_ssl = NULL;                // boolean
        public $incoming_apop_auth_for_pop3 = NULL;     // boolean
        public $incoming_timeout = NULL;                // integer

        public function __construct($id = NULL, $server_code = NULL, $server_name = NULL) {
            if ($id === NULL) {
                ;
            } else {
                if (is_object($id) && (get_class($id) == "stdClass")) {
                    $this->setFromStdClass($id);
                } else {
                    $this->id       = $id;
                    if ($server_code !== NULL) {
                        $this->server_code      = $server_code;
                    }
                    if ($server_name !== NULL) {
                        $this->server_name = $server_name;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->id                   = $std_class->id;
            $this->server_code          = $std_class->server_code;
            $this->server_name          = $std_class->server_name;
            $this->outgoing_server_name = $std_class->outgoing->server_name;
            $this->outgoing_port_number = $std_class->outgoing->port_number;
            if (isset($std_class->outgoing->encrypted_connection)) {
                $this->outgoing_encrypted_connection    = $std_class->outgoing->encrypted_connection;
            }
            if (isset($std_class->outgoing->smtp_auth)) {
                $this->outgoing_smtp_auth               = $std_class->outgoing->smtp_auth;
            }
            if (isset($std_class->outgoing->account_for_send)) {
                $this->outgoing_account_for_send        = $std_class->outgoing->account_for_send;
            }
            if (isset($std_class->outgoing->pop_before_smtp)) {
                $this->outgoing_pop_before_smtp         = $std_class->outgoing->pop_before_smtp;
            }
            if (isset($std_class->outgoing->pop_before_smtp_wait_time)) {
                $this->outgoing_pop_before_smtp_wait_time = $std_class->outgoing->pop_before_smtp_wait_time;
            }
            if (isset($std_class->outgoing->timeout)) {
                $this->outgoing_timeout                 = $std_class->outgoing->timeout;
            }
            $this->incoming_server_name = $std_class->incoming->server_name;
            $this->incoming_receive_protocol = $std_class->incoming->receive_protocol;
            $this->incoming_port_number = $std_class->incoming->port_number;
            if (isset($std_class->incoming->use_ssl)) {
                $this->incoming_use_ssl                 = $std_class->incoming->use_ssl;
            }
            if (isset($std_class->incoming->apop_auth_for_pop3)) {
                $this->incoming_apop_auth_for_pop3      = $std_class->incoming->apop_auth_for_pop3;
            }
            if (isset($std_class->incoming->timeout)) {
                $this->incoming_timeout                 = $std_class->incoming->timeout;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["id"]              = $this->id;
            $ret_val["server_code"]     = $this->server_code;
            $ret_val["server_name"]     = $this->server_name;
            $ret_val["outgoing"]["server_name"]     = $this->outgoing_server_name;
            $ret_val["outgoing"]["port_number"]     = $this->outgoing_port_number;
            if ($this->outgoing_encrypt_connection !== NULL) {
                $ret_val["outgoing"]["encrypt_connection"]  = $this->outgoing_encrypt_connection;
            }
            if ($this->outgoing_smtp_auth !== NULL) {
                $ret_val["outgoing"]["smtp_auth"]           = $this->outgoing_smtp_auth;
            }
            if ($this->outgoing_account_for_send !== NULL) {
                $ret_val["outgoing"]["account_for_send"]    = $this->outgoing_account_for_send;
            }
            if ($this->outgoing_pop_before_smtp !== NULL) {
                $ret_val["outgoing"]["pop_before_smtp"]     = $this->outgoing_pop_before_smtp;
            }
            if ($this->outgoing_pop_before_smtp_wait_time !== NULL) {
                $ret_val["outgoing"]["pop_before_smtp_wait_time"] = $this->outgoing_pop_before_smtp_wait_time;
            }
            if ($this->outgoing_timeout !== NULL) {
                $ret_val["outgoing"]["timeout"]             = $this->outgoing_timeout;
            }
            $ret_val["incoming"]["server_name"]     = $this->incoming_server_name;
            $ret_val["incoming"]["receive_protocol"] = $this->incoming_receive_protocol;
            $ret_val["incoming"]["port_number"]     = $this->incoming_port_number;
            if ($this->incoming_use_ssl !== NULL) {
                $ret_val["incoming"]["use_ssl"]             = $this->incoming_use_ssl;
            }
            if ($this->incoming_apop_auth_for_pop3 !== NULL) {
                $ret_val["incoming"]["apop_auth_for_pop3"]  = $this->incoming_apop_auth_for_pop3;
            }
            if ($this->incoming_timeout !== NULL) {
                $ret_val["incoming"]["timeout"]             = $this->incoming_timeout;
            }
            return $ret_val;
        }
    }

    class CbgrnAccount_Info
    {
        public $account_id;                     // IDType
        public $user_id;                        // IDType
        public $user_account_code;              // string
        public $user_account_name = NULL;    // string;

        public function __construct($account_id = NULL, $user_id = NULL, $user_account_code = NULL) {
            if ($account_id === NULL) {
                ;
            } else {
                if (is_object($account_id) && (get_class($account_id) == "stdClass")) {
                    $this->setFromStdClass($account_id);
                } else {
                    $this->account_id   = $account_id;
                    $this->user_id      = $user_id;
                    $this->user_account_code = $user_account_code;
                }
            }
        }
        
        public function setFromStdClass(stdClass $std_class) {
            $this->account_id           = $std_class->account_id;
            $this->user_id              = $std_class->user_id;
            $this->user_account_code    = $std_class->user_account_code;
            if (isset($std_class->user_account_name)) {
                $this->user_account_name = $std_class->user_account_name;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["account_id"]      = $this->account_id;
            $ret_val["user_id"]         = $this->user_id;
            $ret_val["user_acount_code"]   = $this->user_account_code;
            if ($this->user_account_name !== NULL) {
                $ret_val["user_account_name"]   = $this->user_account_name;
            }
            return $ret_val;
        }
    }

    class CbgrnMailSetting
    {
        public $mail_server_id;                 // string
        public $email;                          // string (mail address)
        public $account_name;                // string
        public $password = NULL;                // string
        public $leave_server_mail = NULL;       // boolean
        public $deactivate_user_account = NULL; // boolean

        public function __construct($mail_server_id = NULL, $email = NULL, $account_name = NULL) {
            if ($mail_server_id === NULL) {
                ;
            } else {
                if (is_object($mail_server_id) && (get_class($mail_server_id) == "stdClass")) {
                    $this->setFromStdClass($mail_server_id);
                } else {
                    $this->mail_server_id   = $mail_server_id;
                    $this->email            = $email;
                    $this->account_name = $account_name;
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->mail_server_id           = $std_class->mail_server_id;
            $this->email                    = $std_class->email;
            $this->account_name             = $std_class->account_name;
            if (isset($std_class->password)) {
                $this->password             = $std_class->password;
            }
            if (isset($std_class->leave_server_mail)) {
                $this->leave_server_mail    = $std_class->leave_server_mail;
            }
            if (isset($std_class->deactivate_user_account)) {
                $this->deactivate_user_account = $std_class->deactivate_user_account;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["mail_server_id"]      = $this->mail_server_id;
            $ret_val["email"]               = $this->email;
            $ret_val["acount_name"]        = $this->account_name;
            if ($this->password !== NULL) {
                $ret_val["password"]        = $this->password;
            }
            if ($this->leave_server_mail !== NULL) {
                $ret_val["leave_server_mail"] = $this->leave_server_mail;
            }
            if ($this->deactivate_user_account !== NULL) {
                $ret_val["deactivate_user_account"] = $this->deactivate_user_account;
            }
            return $ret_val;
        }
    }

    class CbgrnMailUserAccountType
    {
        public $account_info = NULL;            // CbgrnAccount_Info class (0-1)
        public $mail_setting = NULL;            // CbgrnMailSetting class (0-1)

        public function __construct($account_info = NULL, CbgrnMailSetting $mail_setting = NULL) {
            if ($account_info === NULL) {
                ;
            } else {
                if (is_object($account_info) && (get_class($account_info) == "stdClass")) {
                    $this->setFromStdClass($account_info);
                } else {
                    if (is_object($account_info) && (get_class($account_info) == "CbgrnAccount_Info")) {
                        $this->account_info = $account_info;
                    }
                    if (is_object($mail_setting) && (get_class($mail_setting) == "CbgrnMailSetting")) {
                        $this->mail_setting = $mail_setting;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            if (isset($std_class->account_info)) {
                $this->account_info = new CbgrnAccount_Info($std_class->account_info);
            }
            if (isset($std_class->mail_setting)) {
                $this->mail_setting = new CbgrnMailSetting($std_class->mail_setting);
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            if ($this->account_info !== NULL) {
                $ret_val["account_info"]    = $this->account_info->getObjectVars();
            }
            if ($this->mail_setting !== NULL) {
                $ret_val["mail_setting"]    = $this->mail_setting->getObjectVars();
            }
            return $ret_val;
        }
    }

    class CbgrnDeleteUserAccount
    {
        public $account_id;                 // IDType
        public $delete_all_email = NULL;    // boolean

        public function __construct($account_id = NULL, $delete_all_email = NULL) {
            if ($account_id === NULL) {
                ;
            } else {
                if (is_object($account_id) && (get_class($account_id) == "stdClass")) {
                    $this->setFromStdClass($account_id);
                } else {
                    $this->account_id   = $account_id;
                    if ($delete_all_email !== NULL) {
                        $this->delete_all_email = $delete_all_email;
                    }
                }
            }
        }

        public function setFromStdClass(stdClass $std_class) {
            $this->account_id   = $std_class->account_id;
            if (isset($std_class->delete_all_email)) {
                $this->delete_all_email = $std_class->delete_all_email;
            }
        }

        public function getObjectVars() {
            $ret_val = Array();
            $ret_val["account_id"]  = $this->account_id;
            if ($this->delete_all_email !== NULL) {
                $ret_val["delete_all_email"]    = $this->delete_all_email;
            }
            return $ret_val;
        }
    }

?>