<?php
class Util_Mail {
    /**
     * MIME�w�b�_�[���쐬����
     * @args $str ������iSJIS�j
     * @returns JIS������
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
                //2�o�C�g�ړǂݏo��
                $c2 = pack("C",hexdec(substr($hex,$i+2,2)));
                $i+=2;

                //�؂�ւ�
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

                //�؂�ւ�
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
                //�؂�ւ�
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

            //�V�K�s�Ɉړ�
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

        //�o�b�t�@�j��
        if ($buffer != "")
        {
            if($KF == 1) $buffer .= $KO;
            if($SF == 1) $buffer .= $SI;
            $line .= base64_encode($buffer) . "?=";
            $ar[] = $line;
        }

        //���[�����M
        $body = implode ($ar);
        return $body;
    }

    /**
     * ������ϊ�
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
     * ���[�����M
     * @args from_name ���o�l���i�I�v�V�����j
     * @args from      ���o�l���[���A�h���X�i�w�肪�Ȃ���΃R���t�B�O�̃f�t�H���g�l���g���j
     * @args bcc       �u���C���h�J�[�{���R�s�[�i�I�v�V�����j
     * @args to_name   ���於�i�I�v�V�����j
     * @args to        ���惁�[���A�h���X
     * @args subject   ����
     * @args body      �{��
     */
    public static function send( $params = Array() ) {
        // �R���t�B�O�̎擾
        $config = Zend_Registry::get('config');

        // ���͒l�擾
        $default   = isset($params['default']) ? $params['default'] : $config->app->mailfrom;
        $subject   = isset($params['subject']) ? $params['subject'] : '����';
        $body      = isset($params['body']) ? $params['body'] : '';
        $to        = isset($params['to']) ? $params['to'] : $default;
        $from      = isset($params['from']) ? $params['from'] : $default;
        $from_name = isset($params['from_name']) ? $params['from_name'] : $default;

        // ���s�R�[�h�ϊ�
        $body = str_replace("\r\n", "\n", $body);

        // �����R�[�h�ϊ�
        /*
        mb_language('ja');
        mb_internal_encoding("JIS");
        $subject   = mb_encode_mimeheader(mb_convert_encoding($subject, "JIS", "SJIS"), "JIS");
        $from_name = mb_encode_mimeheader(mb_convert_encoding($from_name, "JIS", "SJIS"), "JIS");
        mb_internal_encoding("SJIS");
        */
        $subject = self::create_mimeheader($subject);
        $from_name = self::create_mimeheader($from_name);

        // �{���G���R�[�h
        $body = mb_convert_encoding($body,"JIS","SJIS");

        // ���[�����M�J�n
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
     * HTML���[�����M
     * @args from_name ���o�l���i�I�v�V�����j
     * @args from      ���o�l���[���A�h���X�i�w�肪�Ȃ���΃R���t�B�O�̃f�t�H���g�l���g���j
     * @args bcc       �u���C���h�J�[�{���R�s�[�i�I�v�V�����j
     * @args to_name   ���於�i�I�v�V�����j
     * @args to        ���惁�[���A�h���X
     * @args subject   ����
     * @args body      �{��
     */
    public static function sendHTML( $params = Array() ) {
        // �R���t�B�O�̎擾
        $config = Zend_Registry::get('config');

        // ���͒l�擾
        $default   = isset($params['default']) ? $params['default'] : $config->app->mailfrom;
        $subject   = isset($params['subject']) ? $params['subject'] : '����';
        $body      = isset($params['body']) ? $params['body'] : '';
        $to        = isset($params['to']) ? $params['to'] : $default;
        $from      = isset($params['from']) ? $params['from'] : $default;
        $from_name = isset($params['from_name']) ? $params['from_name'] : $default;

        // ���s�R�[�h�ϊ�
        $body = str_replace("\r\n", "\n", $body);

        // �����R�[�h�ϊ�
        /*
        mb_internal_encoding("JIS");
        $subject   = mb_encode_mimeheader(mb_convert_encoding($subject, "JIS", "SJIS"), "JIS");
        $from_name = mb_encode_mimeheader(mb_convert_encoding($from_name, "JIS", "SJIS"), "JIS");
        mb_internal_encoding("SJIS");
        */
        $subject = self::create_mimeheader($subject);
        $from_name = self::create_mimeheader($from_name);

        // �{���G���R�[�h
        $body = mb_convert_encoding($body,"JIS","SJIS");

        // ���[�����M�J�n
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
