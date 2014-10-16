<?php
class Util_Mobile {
    public $career = 0;
    public $device = '';
    public $uid    = '';
    public $name   = '';
    public $gene   = 0;
    public $width  = 0;
    public $height = 0;
    public $colors = 0;
    public $png    = 0;
    public $gif    = 0;
    public $jpeg   = 0;
    public $flash  = false;
    public $decome = 0;
    public $mova   = false;
    public $foma   = false;

    public static $DOCOMO   = 1;
    public static $AU       = 2;
    public static $SOFTBANK = 3;
    public static $IPHONE     = 4;
    public static $ANDROID    = 5;
    public static $BLACKBERRY = 6;

    /**
     * ‹@Ží”»•Ê
     */
    public function Util_Mobile() {
        $ua = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match("/^DoCoMo\/1\.0/", $ua)) {
            $ua_array = explode("/", $ua);
            $this->career = Util_Mobile::$DOCOMO;
            $this->device = $ua_array[2];
            $this->mova   = true;
            $this->uid    = isset($_SERVER['HTTP_X_DCMGUID']) ? $_SERVER['HTTP_X_DCMGUID'] : '';
        }
        elseif (preg_match("/^J\-PHONE|^Vodafone|^SoftBank/", $ua)) {
            $ua_array = explode("/", $ua);
            $this->career = Util_Mobile::$SOFTBANK;
            $this->device = $ua_array[2];
            $this->uid    = isset($_SERVER['HTTP_X_JPHONE_UID']) ? $_SERVER['HTTP_X_JPHONE_UID'] : '';
        }
        elseif (isset($_SERVER['HTTP_X_JPHONE_MSNAME'])) {
            $this->career = Util_Mobile::$SOFTBANK;
            $this->device = $_SERVER['HTTP_X_JPHONE_MSNAME'];
            $this->uid    = isset($_SERVER['HTTP_X_JPHONE_UID']) ? $_SERVER['HTTP_X_JPHONE_UID'] : '';
        }
        elseif (preg_match("/UP\.Browser/", $ua)) {
            $this->career = Util_Mobile::$AU;
            $this->device = preg_replace("/^[^\-]+\-([^\ ]+)\ .+$/","$1", $ua);
            if (isset($_SERVER['HTTP_X_UP_SUBNO'])) {
                $sub_no_array = explode(".", $_SERVER['HTTP_X_UP_SUBNO']);
                $this->uid = $sub_no_array[0];
            }
        }
        elseif (preg_match("/^DoCoMo\/2\.0/", $ua)) {
            $ua_array = explode("/", $ua);
            $this->career = Util_Mobile::$DOCOMO;
            $this->device = preg_replace("/^2\.0 ([0-9A-Za-z\+]+)\(.+$/","$1", $ua_array[1]);
            $this->foma   = true;
            $this->uid    = isset($_SERVER['HTTP_X_DCMGUID']) ? $_SERVER['HTTP_X_DCMGUID'] : '';
        }
        elseif (preg_match("/iPhone/", $ua)) {
            $this->career = Util_Mobile::$IPHONE;
        }
        elseif (preg_match("/iPad/", $ua)) {
            $this->career = Util_Mobile::$IPHONE;
        }
        elseif (preg_match("/iPod/", $ua)) {
            $this->career = Util_Mobile::$IPHONE;
        }
        elseif (preg_match("/Android/", $ua)) {
            $this->career = Util_Mobile::$ANDROID;
        }
        elseif (preg_match("/T-01A/", $ua)) {
            $this->career = Util_Mobile::$ANDROID;
        }
        elseif (preg_match("/KDDI-TS01/", $ua)) {
            $this->career = Util_Mobile::$ANDROID;
        }
        elseif (preg_match("/BlackBerry/", $ua)) {
            $this->career = Util_Mobile::$BLACKBERRY;
        }

        return $this;
    }
}
