<?php
class Util_Google {
    /**
     * 設定値取得
     */
    private static function getValues() {
        $result  = array();
        $session = new Zend_Session_Namespace('GoogleAnalytics');

        // var_first
        if (!$session->var_first) {
            $result['var_first'] = time();
            $session->var_first  = $result['var_first'];
        }
        else {
            $result['var_first'] = $session->var_first;
        }
        
        // var_prev
        if (!$session->var_prev) {
            $result['var_prev'] = time();
        } else {
            $result['var_prev'] = $session->var_prev;
        }
        $session->var_prev = time();
        
        // var_referer
        if (!$session->var_referer) {
            $result['var_referer'] = $_SERVER['REQUEST_URI'];
        } else {
            $result['var_referer'] = $session->var_referer;
        }
        $session->var_referer = $_SERVER['REQUEST_URI'];

        // var_count
        if (!$session->var_count) {
            $result['var_count'] = 1;
        } else {
            $result['var_count'] = $session->var_count + 1;
        }
        $session->var_count = $session->var_count + 1;

        // ver_domain, ver_uid
        $config = Zend_Registry::get('config');
        $result['var_domain'] = $config->analytics->domain;
        $result['var_uid']    = $config->analytics->uid;

        // var_cookie
        $result['var_cookie'] = Util_Google::generateHash($result['var_domain']);

        // var_sid
        $result['var_sid'] = Util_Google::generateHash(session_id());

        // var_uservar
        $result['var_uservar'] = '-';

        // var_screen
        $result['var_screen'] = '-';

        // var_flash
        $result['var_flash'] = '-';

        // var_color
        $result['var_color'] = '-';

        // var_ua
        $result['var_ua'] = $_SERVER['HTTP_USER_AGENT'];

        return $result;
    }

    /**
     * 与えられた文字列に対応するハッシュを生成する。
     * このメソッドは com.google.analytics.core.Utils.generateHash() の移植である。
     *
     * @param string $input
     * @return integer
     * @link http://code.google.com/p/gaforflash/source/browse/trunk/src/com/google/analytics/core/Utils.as
     */
    private static function generateHash($input) {
        $hash = 1;
        $leftMost7 = 0;

        for ($position = strlen($input) - 1; $position >= 0; --$position) {
            $current = ord(substr($input, $position, 1));
            $hash = (($hash << 6) & 0xfffffff) + $current + ($current << 14);
            $leftMost7 = $hash & 0xfe00000;

            if ($leftMost7 != 0) {
                $hash ^= $leftMost7 >> 21;
            }
        }

        return $hash;
    }

    /**
     * Google Analytics コードの生成
     */
    public static function createAnalyticsCode($title = '', $utmp = '') {
        $result = Util_Google::getValues();

        if (!$utmp) {
            $utmp = $_SERVER['REQUEST_URI'];
        }

        $var_utmsr   = $result['var_screen'];       // screen resolution
        $var_utmfl   = $result['var_flash'];        // flash version
        $var_utmn    = rand(1000000000,9999999999); // random request number
        $var_utmhn   = $result['var_domain'];       // domain
        $var_referer = $result['var_referer'];      // referer
        $var_utmp    = urlencode(preg_replace("/(\?|&|\/)(uniqid|sid)(=|\/)[0-9a-zA-Z]+/", "", $utmp)); // request uri
        $var_utmac   = $result['var_uid'];          // enter the new urchin code
        $var_utmsc   = $result['var_color'];        // color depth
        $var_utmdt   = urlencode($title);           // page title
        $var_cookie  = $result['var_cookie'];       // cookie number
        $var_random  = rand(1000000000,2147483647); // number under 2147483647
        $var_sid     = $result['var_sid'];          // session id
        $var_first   = $result['var_first'];        // first cookie set time
        $var_prev    = $result['var_prev'];         // prev cookie set time
        $var_count   = $result['var_count'];        // session counter
        $var_now     = time();                      // today
        $var_uservar = $result['var_uservar'];      // enter your own user defined variable

        $urchinUrl = 'http://www.google-analytics.com/__utm.gif?utmwv=1'
                   . '&utmsr='  . $var_utmsr
                   . '&utmul=-' /* language */
                   . '&utmje=0' /* java_enabled */
                   . '&utmfl='  . $var_utmfl
                   . '&utmn='   . $var_utmn
                   . '&utmhn='  . $var_utmhn
                   . '&utmr='   . $var_referer
                   . '&utmp='   . $var_utmp
                   . '&utmac='  . $var_utmac
                   . '&utmsc='  . $var_utmsc
                   . '&utmdt='  . $var_utmdt
                   . '&utmcc=__utma%3D'
                   . $var_cookie
                   . '.'
                   . $var_sid
                   . '.'
                   . $var_first
                   . '.'
                   . $var_prev
                   . '.'
                   . $var_now
                   . '.'
                   . $var_count
                   . '%3B%2B__utmb%3D'
                   . $var_cookie
                   . '%3B%2B__utmc%3D'
                   . $var_cookie
                   . '%3B%2B__utmz%3D'
                   . $var_cookie
                   . '.'
                   . $var_now
                   . '.'
                   . $var_count
                   . '.'
                   . $var_count
                   . '.utmccn%3D(referral)%7Cutmcsr%3D'
                   . $var_referer
                   . '%7Cutmcmd%3Dreferral%3B%2B__utmv%3D'
                   . $var_cookie
                   . '.'
                   . $var_uservar
                   . '%3B';

        $header = '';

        // Set the language to that of the client so analytics can track it.
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $header = 'Accept-language: ' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '\r\n';
        }
        // Set the user agent to that of the client so analytics can track it.
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $header = 'User-Agent: ' . $result['var_ua'] . '\r\n';
        }

        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => $header
            )
        );

        //error_log($urchinUrl);
        $handle = fopen($urchinUrl, 'r', false, stream_context_create($opts));
        $test = fgets($handle);
        fclose($handle);
    }
}
