<?php
class Util_General {
    /**
     * 乱数を発生
     */
    public static function generateRandomId($head = '') {
        $password = Util_General::generatePassword(8);
        $id = uniqid($head);
        return ($password . $id);
    }

    /**
     * パスワード用乱数を発生
     */
    public static function generatePassword($length = 8) {
        // 乱数表のシードを決定
        srand((double)microtime() * 54234853);

        // パスワード文字列の配列を作成
        $character = "abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ2345679";
        $pw = preg_split("//", $character, 0, PREG_SPLIT_NO_EMPTY);

        $password = "";
        for($i = 0; $i < $length; $i++ ) {
            $password .= $pw[array_rand($pw, 1)];
        }
        
        return ($password);
    }

    /**
     * 指定された日付から曜日を得る
     */
    public static function getYobi($year, $month, $date) {
        $sday = strtotime(sprintf("%d%02d%02d", $year, $month, $date));
        $res  = date("w", $sday);
        $days = array("日", "月", "火", "水", "木", "金", "土");
        return $days[$res];
    }

    /**
     * メールアドレスの取得
     */
    public static function getMailAddress($s) {
        $matches = array();
        if (preg_match("/^.*\<(.*)\>.*$/", $s, $matches)) {
            $s = $matches[1];
        }
        return $s;
    }

    /**
     * 文字列からBOMを削除する
     */
    public static function deleteBom($str) {
        if (ord($str{0}) == 0xef && ord($str{1}) == 0xbb && ord($str{2}) == 0xbf) {
            $str = substr($str, 3);
        }
        return $str;
    }

    /**
     * メール読み取り
     */
    public static function readMail() {
        // 標準入力から読み取り
        $message = new Zend_Mail_Message(array('file' => 'php://stdin'));

        $obj = array();

        // 差出人
        $obj['from'] = self::getMailAddress($message->from);

        // 宛先
        $obj['to'] = self::getMailAddress($message->to);

        // 件名
        if ( $message->getHeader('subject') ) {
            $obj['subject'] = $message->subject;
        } else {
            $obj['subject'] = '';
        }

        // 本文
        $obj['body'] = $message->getContent();

        // 不正なメールアドレスなので無視
        if ( preg_match("/^MAILER\-DAEMON\@/i", $obj['from']) ||
             preg_match("/^postmaster\@/i", $obj['from']) ||
             $obj['from'] === 'photo-server@docomo-camera.ne.jp' ||
             strlen($obj['from']) === 0 ||
             !preg_match("/.+\@[a-zA-Z0-9][a-zA-Z0-9\-\.]+\.[a-zA-Z]+$/", $obj['from']) )
        {
            exit();
        }

        // 添付ファイル処理
        if ( $message->isMultipart() ) {
            // 添付ファイル数
            $obj['files'] = $message->countParts() - 1;
            // 添付ファイル
            for ($i = 2; $i <= $message->countParts(); $i++) {
                $obj['files_type_' . ($i - 2)] = $message->getPart($i)->getHeader('content-type');
                $obj['files_body_' . ($i - 2)] = $message->getPart($i)->getContent();
            }
        }
        else {
            // 添付ファイル数
            $obj['files'] = 0;
        }

        return $obj;
    }
}
