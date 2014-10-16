<?php
class Util_Mail {
    /**
     * MIMEヘッダーを作成する
     * @args $str 文字列（SJIS）
     * @returns JIS文字列
     */
    public static function create_mimeheader($str)
    {
        $KI = "\x1B\$B";
        $KO = "\x1B(B";
        $KF = 0;

        $SI = "\x0F";
        $SO = "\x0E";
        $SI = "\x1B(B";
        $SO = "\x1B(I";

        $SF = 0;

        $t = 0;       #0=ASCII 1=KANJI 2=KANA

        $Header = "=?ISO-2022-JP?B?";
        $maxbites = 70;

        $line = $Header;

        $hex = bin2hex($str);
        $buffer = "";
        for($i=0;$i<strlen($hex);$i+=2)
        {
            $b = hexdec(substr($hex,$i,2));
            $c = pack("C",$b);

            if(($b >= 0x81 && $b <= 0x9F) || ($b >= 0xE0 && $b <= 0xFF))
            {
                $t = 1;
                //2バイト目読み出し
                $c2 = pack("C",hexdec(substr($hex,$i+2,2)));
                $i+=2;

                //切り替え
                if($SF){
                    $buffer .= $SI;
                    $SF = 0;
                }

                if(!$KF){
                    $buffer .= $KI;
                    $KF = 1;
                }

                $buffer .= self::StoJ($c.$c2);
            }
            elseif($b >= 0xA0 && $b <= 0xDF)
            {
                $t = 2;

                //切り替え
                if($KF){
                    $buffer .= $KO;
                    $KF = 0;
                }
                if(!$SF){
                    $buffer .= $SO;
                    $SF = 1;
                }

                $buffer .= pack("C",$b - 0x80);
            }
            else{
                //切り替え
                if($KF){
                    $buffer .= $KO;
                    $KF = 0;
                }
                if($SF){
                    $buffer .= $SI;
                    $SF = 0;
                }

                $t = 0;
                $buffer .= $c;
            }

            //新規行に移動
            if(strlen($buffer) *1.5 + strlen($line) > $maxbites)
            {
                if($KF == 1) $buffer .= $KO;
                if($SF == 1) $buffer .= $SI;
                $KF = 0;
                $SF = 0;
                $t = 0;
                $line .= base64_encode($buffer) . "?=";
                $ar[] = $line;
                $line = "\n\t".$Header;
                $buffer = "";
            }

        }

        //バッファ破棄
        if ($buffer != "")
        {
            if($KF == 1) $buffer .= $KO;
            if($SF == 1) $buffer .= $SI;
            $line .= base64_encode($buffer) . "?=";
            $ar[] = $line;
        }

        //メール送信
        $body = implode ($ar);
        return $body;
    }

    /**
     * 文字列変換
     */
    public static function StoJ($s)
    {
        $jis = unpack("C2j", $s);
        if($jis['j1'] <=0x9F)
        {
            $jis['j1']-=0x71;
        }else{
            $jis['j1']-=0xb1;
        }
        $jis['j1']*=2;
        $jis['j1']++;

        if($jis['j2']>=0x7F)
        {
            $jis['j2']-=0x01;
        }

        if($jis['j2']>=0x9E)
        {
            $jis['j2']-=0x7D;
            $jis['j1']++;
        }else{
            $jis['j2']-=0x1F;
        }
        #print_r ($jis);
        return pack("C2",$jis['j1'],$jis['j2']);
    }

    /**
     * メール送信
     * @args from_name 差出人名（オプション）
     * @args from      差出人メールアドレス（指定がなければコンフィグのデフォルト値を使う）
     * @args bcc       ブラインドカーボンコピー（オプション）
     * @args to_name   宛先名（オプション）
     * @args to        宛先メールアドレス
     * @args subject   件名
     * @args body      本文
     */
    public static function send( $params = Array() ) {
        // コンフィグの取得
        $config = Zend_Registry::get('config');

        // 入力値取得
        $default   = isset($params['default']) ? $params['default'] : $config->app->mailfrom;
        $subject   = isset($params['subject']) ? $params['subject'] : '無題';
        $body      = isset($params['body']) ? $params['body'] : '';
        $to        = isset($params['to']) ? $params['to'] : $default;
        $from      = isset($params['from']) ? $params['from'] : $default;
        $from_name = isset($params['from_name']) ? $params['from_name'] : $default;

        // 改行コード変換
        $body = str_replace("\r\n", "\n", $body);

        // 文字コード変換
        /*
        mb_language('ja');
        mb_internal_encoding("JIS");
        $subject   = mb_encode_mimeheader(mb_convert_encoding($subject, "JIS", "SJIS"), "JIS");
        $from_name = mb_encode_mimeheader(mb_convert_encoding($from_name, "JIS", "SJIS"), "JIS");
        mb_internal_encoding("SJIS");
        */
        $subject = self::create_mimeheader($subject);
        $from_name = self::create_mimeheader($from_name);

        // 本文エンコード
        $body = mb_convert_encoding($body,"JIS","SJIS");

        // メール送信開始
        $mp = popen("/usr/sbin/sendmail -t -f " . $default ." -- $to", "w");
        fputs($mp, "MIME-Version: 1.0\n");
        fputs($mp, "Content-Type: text/plain; charset=ISO-2022-JP\n");
        fputs($mp, "Content-Transfer-Encoding: 7bit\n");
        fputs($mp, "From: " . $from_name ."<" .$from .">\n");
        if (isset($params['bcc'])) {
            fputs($mp, "Bcc: " .$params['bcc'] ."\n");
        }
        fputs($mp, "To: $to\n");
        fputs($mp, "Subject: $subject\n\n");
        fputs($mp, "$body");
        pclose($mp);
    }

    /**
     * HTMLメール送信
     * @args from_name 差出人名（オプション）
     * @args from      差出人メールアドレス（指定がなければコンフィグのデフォルト値を使う）
     * @args bcc       ブラインドカーボンコピー（オプション）
     * @args to_name   宛先名（オプション）
     * @args to        宛先メールアドレス
     * @args subject   件名
     * @args body      本文
     */
    public static function sendHTML( $params = Array() ) {
        // コンフィグの取得
        $config = Zend_Registry::get('config');

        // 入力値取得
        $default   = isset($params['default']) ? $params['default'] : $config->app->mailfrom;
        $subject   = isset($params['subject']) ? $params['subject'] : '無題';
        $body      = isset($params['body']) ? $params['body'] : '';
        $to        = isset($params['to']) ? $params['to'] : $default;
        $from      = isset($params['from']) ? $params['from'] : $default;
        $from_name = isset($params['from_name']) ? $params['from_name'] : $default;

        // 改行コード変換
        $body = str_replace("\r\n", "\n", $body);

        // 文字コード変換
        /*
        mb_internal_encoding("JIS");
        $subject   = mb_encode_mimeheader(mb_convert_encoding($subject, "JIS", "SJIS"), "JIS");
        $from_name = mb_encode_mimeheader(mb_convert_encoding($from_name, "JIS", "SJIS"), "JIS");
        mb_internal_encoding("SJIS");
        */
        $subject = self::create_mimeheader($subject);
        $from_name = self::create_mimeheader($from_name);

        // 本文エンコード
        $body = mb_convert_encoding($body,"JIS","SJIS");

        // メール送信開始
        $mp = popen("/usr/sbin/sendmail -t -f " . $default . " -- $to", "w");
        fputs($mp, "MIME-Version: 1.0\n");
        fputs($mp, "Content-Type: text/html; charset=ISO-2022-JP\n");
        fputs($mp, "Content-Transfer-Encoding: 7bit\n");
        fputs($mp, "Reply-To: " . $default ."<" .$default .">\n");
        fputs($mp, "From: " . $from_name ."<" .$from .">\n");
        if (isset($params['bcc'])) {
            fputs($mp, "Bcc: " .$params['bcc'] ."\n");
        }
        fputs($mp, "To: $to\n");
        fputs($mp, "Subject: $subject\n\n");
        fputs($mp, "$body");
        pclose($mp);
    }
}
