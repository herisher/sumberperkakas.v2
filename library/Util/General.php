<?php
class Util_General {
    /**
     * �����𔭐�
     */
    public static function generateRandomId($head = '') {
        $password = Util_General::generatePassword(8);
        $id = uniqid($head);
        return ($password . $id);
    }

    /**
     * �p�X���[�h�p�����𔭐�
     */
    public static function generatePassword($length = 8) {
        // �����\�̃V�[�h������
        srand((double)microtime() * 54234853);

        // �p�X���[�h������̔z����쐬
        $character = "abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ2345679";
        $pw = preg_split("//", $character, 0, PREG_SPLIT_NO_EMPTY);

        $password = "";
        for($i = 0; $i < $length; $i++ ) {
            $password .= $pw[array_rand($pw, 1)];
        }
        
        return ($password);
    }

    /**
     * �w�肳�ꂽ���t����j���𓾂�
     */
    public static function getYobi($year, $month, $date) {
        $sday = strtotime(sprintf("%d%02d%02d", $year, $month, $date));
        $res  = date("w", $sday);
        $days = array("��", "��", "��", "��", "��", "��", "�y");
        return $days[$res];
    }

    /**
     * ���[���A�h���X�̎擾
     */
    public static function getMailAddress($s) {
        $matches = array();
        if (preg_match("/^.*\<(.*)\>.*$/", $s, $matches)) {
            $s = $matches[1];
        }
        return $s;
    }

    /**
     * �����񂩂�BOM���폜����
     */
    public static function deleteBom($str) {
        if (ord($str{0}) == 0xef && ord($str{1}) == 0xbb && ord($str{2}) == 0xbf) {
            $str = substr($str, 3);
        }
        return $str;
    }

    /**
     * ���[���ǂݎ��
     */
    public static function readMail() {
        // �W�����͂���ǂݎ��
        $message = new Zend_Mail_Message(array('file' => 'php://stdin'));

        $obj = array();

        // ���o�l
        $obj['from'] = self::getMailAddress($message->from);

        // ����
        $obj['to'] = self::getMailAddress($message->to);

        // ����
        if ( $message->getHeader('subject') ) {
            $obj['subject'] = $message->subject;
        } else {
            $obj['subject'] = '';
        }

        // �{��
        $obj['body'] = $message->getContent();

        // �s���ȃ��[���A�h���X�Ȃ̂Ŗ���
        if ( preg_match("/^MAILER\-DAEMON\@/i", $obj['from']) ||
             preg_match("/^postmaster\@/i", $obj['from']) ||
             $obj['from'] === 'photo-server@docomo-camera.ne.jp' ||
             strlen($obj['from']) === 0 ||
             !preg_match("/.+\@[a-zA-Z0-9][a-zA-Z0-9\-\.]+\.[a-zA-Z]+$/", $obj['from']) )
        {
            exit();
        }

        // �Y�t�t�@�C������
        if ( $message->isMultipart() ) {
            // �Y�t�t�@�C����
            $obj['files'] = $message->countParts() - 1;
            // �Y�t�t�@�C��
            for ($i = 2; $i <= $message->countParts(); $i++) {
                $obj['files_type_' . ($i - 2)] = $message->getPart($i)->getHeader('content-type');
                $obj['files_body_' . ($i - 2)] = $message->getPart($i)->getContent();
            }
        }
        else {
            // �Y�t�t�@�C����
            $obj['files'] = 0;
        }

        return $obj;
    }
}
