<?php
class Util_Csv {
    /**
     * CSVファイルを文字化けせずに読み込む
     * 使い方は純正の fgetcsv と同じ
     * @see http://www.phppro.jp/qa/371
     */
    public static function fgetcsv_reg (&$handle, $length = null, $d = ',', $e = '"') {
        // リミッター解除
        ini_set('memory_limit','-1');

        $d = preg_quote($d);
        $e = preg_quote($e);
        $_line = "";
        $_eof  = false;
        while ($_eof != true) {
            $_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
            $itemcnt = preg_match_all('/'.$e.'/', $_line, $dummy);
            if ($itemcnt % 2 == 0) $_eof = true;
        }
        $_csv_line = preg_replace('/(?:\\r\\n|[\\r\\n])?$/', $d, trim($_line));
        $_csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
        preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
        $_csv_data = $_csv_matches[1];
        for($_csv_i=0;$_csv_i<count($_csv_data);$_csv_i++){
            $_csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$_csv_data[$_csv_i]);
            $_csv_data[$_csv_i]=str_replace($e.$e, $e, $_csv_data[$_csv_i]);
        }
        return empty($_line) ? false : $_csv_data;
    }
}
