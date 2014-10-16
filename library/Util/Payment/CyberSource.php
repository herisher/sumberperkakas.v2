<?php
class Util_Payment_CyberSource {
    /** エラーコード */
    public static $reason_codes = array(
        '100' => 'トランザクションは成功しました',
        '101' => 'リクエストに1つまたは複数の必須フィールドが欠けています',
        '102' => 'リクエスト内の1つまたは複数のフィールドに無効なデータが含まれています',
        '103' => '日本では利用できないサービスです',
        '150' => '一般的なシステム障害です',
        '151' => 'タイムアウトしました',
        '201' => 'カード会社が与信を保留しました',
        '202' => 'カードの有効期限が切れています',
        '203' => 'カード会社が与信を拒否しました',
        '207' => 'カード会社との通信エラーです',
        '211' => '誤ったセキュリティコードが入力されました',
        '231' => '無効なカード番号が入力されました',
        '233' => 'フィールドに設定された値が不正です',
        '234' => 'サイバーソースでのマーチャントIDの設定に問題があります',
        '235' => '売上しようとする金額が与信金額を超えています',
        '237' => '与信は既に取消処理されています',
        '238' => '与信は既に売上処理されています',
        '239' => '処理しようとする金額に誤りがあります',
        '240' => 'クレジットカードタイプが間違っています',
        '241' => 'リクエストIDに誤りがあります',
        '242' => '与信が見つかりません',
        '243' => 'その他のエラー',
        '246' => '売上を無効にすることができません',
        '247' => '売上は既に取消処理されています',
        '250' => 'タイムアウトしました',
    );

    /**
     * ログ書き込み
     */
    public function writeLog($func, $str, $file_id = '') {
        $fp = fopen(APPLICATION_PATH . '/../logs/cyber_source.log', 'a');
        $stamp = microtime();
        if ($file_id) {
            fwrite($fp, date("Y-m-d H:i:s:") . substr($stamp,2,6) . "\t" . $func ."\t" . $file_id . "\t" . $str . "\n");
        } else {
            fwrite($fp, date("Y-m-d H:i:s:") . substr($stamp,2,6) . "\t" . $func ."\t" . $str . "\n");
        }
        fclose($fp);
    }

    /**
     * UUIDを返す
     */
    public function getUUID() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    /**
     * 与信
     *
     * @param name1 姓
     * @param name2 名
     * @param email メールアドレス
     * @param price 総額（税込）
     * @param card_number カード番号
     * @param exp_month 01-12
     * @param exp_year  YYYY
     */
    public function apiAuth($datas) {
        $this->writeLog("apiAuth","start");

        $config = Zend_Registry::get('config');
        
        $file = @file_get_contents(APPLICATION_PATH . '/../library/soap/pay_auth.xml');
        $file = str_replace('%USER_NAME%',    $config->cs->user_name, $file);
        $file = str_replace('%MERCHANT_ID%',  $config->cs->merchant_id, $file);
        $file = str_replace('%PASSWORD%',     $config->cs->password, $file);
        $file = str_replace('%ORDER_CODE%',   $this->getUUID(), $file);
        $file = str_replace('%NAME1%',        $datas['name1'], $file);
        $file = str_replace('%NAME2%',        $datas['name2'], $file);
        $file = str_replace('%EMAIL%',        $datas['email'], $file);
        $file = str_replace('%PRICE%',        $datas['price'], $file);
        $file = str_replace('%CARD_NUMBER%',  $datas['card_number'], $file);
        $file = str_replace('%EXPIRE_MONTH%', $datas['exp_month'], $file);
        $file = str_replace('%EXPIRE_YEAR%',  $datas['exp_year'], $file);
        
        $this->writeLog("apiAuth","request: ".$file);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$config->cs->url_api);
        curl_setopt($curl, CURLOPT_POST,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $file);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml; charset=utf-8"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        $this->writeLog("apiAuth","response: ".$result);

        if ($result) {
            $matches = array();

            @preg_match("/<c:requestID>(.+?)<\/c:requestID>/", $result, $matches);
            $request_id = $matches[1];
            @preg_match("/<c:decision>(.+?)<\/c:decision>/", $result, $matches);
            $decision = $matches[1];
            @preg_match("/<c:reasonCode>(.+?)<\/c:reasonCode>/", $result, $matches);
            $reason_code = $matches[1];
            @preg_match("/<c:requestToken>(.+?)<\/c:requestToken>/", $result, $matches);
            $request_token = $matches[1];

            if (!$decision) {
                $this->writeLog("apiAuth","end (failed: no result)");
                return null;
            }

            $this->writeLog("apiAuth","request_id: ".$request_id);
            $this->writeLog("apiAuth","decision: ".$decision);
            $this->writeLog("apiAuth","reason_code: ".$reason_code);
            $this->writeLog("apiAuth","request_token: ".$request_token);

            if ($decision == 'ACCEPT') {
                $this->writeLog("apiAuth","end (succeed)");
            } else {
                $this->writeLog("apiAuth","end (failed: $reason_code)");
            }

            return array(
                'request_id'    => $request_id,
                'decision'      => $decision,
                'reason_code'   => $reason_code,
                'request_token' => $request_token,
            );
        } else {
            $this->writeLog("apiAuth","end (failed: no response)");
            
            return null;
        }
    }

    /**
     * 与信取消
     *
     * @param price 総額（税込）
     * @param request_id リクエストID
     * @param request_token リクエストトークン
     */
    public function apiAuthCancel($datas) {
        $this->writeLog("apiAuthCancel","start");

        $config = Zend_Registry::get('config');
        
        $file = @file_get_contents(APPLICATION_PATH . '/../library/soap/pay_auth_cancel.xml');
        $file = str_replace('%USER_NAME%',     $config->cs->user_name, $file);
        $file = str_replace('%MERCHANT_ID%',   $config->cs->merchant_id, $file);
        $file = str_replace('%PASSWORD%',      $config->cs->password, $file);
        $file = str_replace('%ORDER_CODE%',    $this->getUUID(), $file);
        $file = str_replace('%PRICE%',         $datas['price'], $file);
        $file = str_replace('%REQUEST_ID%',    $datas['request_id'], $file);
        $file = str_replace('%REQUEST_TOKEN%', $datas['request_token'], $file);
        
        $this->writeLog("apiAuthCancel","request: ".$file);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$config->cs->url_api);
        curl_setopt($curl, CURLOPT_POST,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $file);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml; charset=utf-8"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        $this->writeLog("apiAuthCancel","response: ".$result);

        if ($result) {
            $matches = array();

            @preg_match("/<c:requestID>(.+?)<\/c:requestID>/", $result, $matches);
            $request_id = $matches[1];
            @preg_match("/<c:decision>(.+?)<\/c:decision>/", $result, $matches);
            $decision = $matches[1];
            @preg_match("/<c:reasonCode>(.+?)<\/c:reasonCode>/", $result, $matches);
            $reason_code = $matches[1];
            @preg_match("/<c:requestToken>(.+?)<\/c:requestToken>/", $result, $matches);
            $request_token = $matches[1];

            if (!$decision) {
                $this->writeLog("apiAuthCancel","end (failed: no result)");
                return null;
            }

            $this->writeLog("apiAuthCancel","request_id: ".$request_id);
            $this->writeLog("apiAuthCancel","decision: ".$decision);
            $this->writeLog("apiAuthCancel","reason_code: ".$reason_code);
            $this->writeLog("apiAuthCancel","request_token: ".$request_token);

            if ($decision == 'ACCEPT') {
                $this->writeLog("apiAuthCancel","end (succeed)");
            } else {
                $this->writeLog("apiAuthCancel","end (failed: $reason_code)");
            }

            return array(
                'request_id'    => $request_id,
                'decision'      => $decision,
                'reason_code'   => $reason_code,
                'request_token' => $request_token,
            );
        } else {
            $this->writeLog("apiAuthCancel","end (failed: no response)");
            
            return null;
        }
    }

    /**
     * 売上処理
     *
     * @param price 総額（税込）
     * @param request_id リクエストID
     * @param request_token リクエストトークン
     */
    public function apiAuthCapture($datas) {
        $this->writeLog("apiAuthCancel","start");

        $config = Zend_Registry::get('config');
        
        $file = @file_get_contents(APPLICATION_PATH . '/../library/soap/pay_capture.xml');
        $file = str_replace('%USER_NAME%',     $config->cs->user_name, $file);
        $file = str_replace('%MERCHANT_ID%',   $config->cs->merchant_id, $file);
        $file = str_replace('%PASSWORD%',      $config->cs->password, $file);
        $file = str_replace('%ORDER_CODE%',    $this->getUUID(), $file);
        $file = str_replace('%PRICE%',         $datas['price'], $file);
        $file = str_replace('%REQUEST_ID%',    $datas['request_id'], $file);
        $file = str_replace('%REQUEST_TOKEN%', $datas['request_token'], $file);
        
        $this->writeLog("apiAuthCapture","request: ".$file);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$config->cs->url_api);
        curl_setopt($curl, CURLOPT_POST,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $file);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml; charset=utf-8"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        $this->writeLog("apiAuthCapture","response: ".$result);

        if ($result) {
            $matches = array();

            @preg_match("/<c:requestID>(.+?)<\/c:requestID>/", $result, $matches);
            $request_id = $matches[1];
            @preg_match("/<c:decision>(.+?)<\/c:decision>/", $result, $matches);
            $decision = $matches[1];
            @preg_match("/<c:reasonCode>(.+?)<\/c:reasonCode>/", $result, $matches);
            $reason_code = $matches[1];
            @preg_match("/<c:requestToken>(.+?)<\/c:requestToken>/", $result, $matches);
            $request_token = $matches[1];

            if (!$decision) {
                $this->writeLog("apiAuthCapture","end (failed: no result)");
                return null;
            }

            $this->writeLog("apiAuthCapture","request_id: ".$request_id);
            $this->writeLog("apiAuthCapture","decision: ".$decision);
            $this->writeLog("apiAuthCapture","reason_code: ".$reason_code);
            $this->writeLog("apiAuthCapture","request_token: ".$request_token);

            if ($decision == 'ACCEPT') {
                $this->writeLog("apiAuthCapture","end (succeed)");
            } else {
                $this->writeLog("apiAuthCapture","end (failed: $reason_code)");
            }

            return array(
                'request_id'    => $request_id,
                'decision'      => $decision,
                'reason_code'   => $reason_code,
                'request_token' => $request_token,
            );
        } else {
            $this->writeLog("apiAuthCapture","end (failed: no response)");
            
            return null;
        }
    }

    /**
     * 返金
     *
     * @param price 総額（税込）
     * @param request_id リクエストID
     * @param request_token リクエストトークン
     */
    public function apiRefund($datas) {
        $this->writeLog("apiRefund","start");

        $config = Zend_Registry::get('config');
        
        $file = @file_get_contents(APPLICATION_PATH . '/../library/soap/pay_refund.xml');
        $file = str_replace('%USER_NAME%',    $config->cs->user_name, $file);
        $file = str_replace('%MERCHANT_ID%',  $config->cs->merchant_id, $file);
        $file = str_replace('%PASSWORD%',     $config->cs->password, $file);
        $file = str_replace('%ORDER_CODE%',   $this->getUUID(), $file);
        $file = str_replace('%PRICE%',        $datas['price'], $file);
        $file = str_replace('%REQUEST_ID%',    $datas['request_id'], $file);
        $file = str_replace('%REQUEST_TOKEN%', $datas['request_token'], $file);
        
        $this->writeLog("apiRefund","request: ".$file);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$config->cs->url_api);
        curl_setopt($curl, CURLOPT_POST,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $file);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml; charset=utf-8"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        $this->writeLog("apiRefund","response: ".$result);

        if ($result) {
            $matches = array();

            @preg_match("/<c:requestID>(.+?)<\/c:requestID>/", $result, $matches);
            $request_id = $matches[1];
            @preg_match("/<c:decision>(.+?)<\/c:decision>/", $result, $matches);
            $decision = $matches[1];
            @preg_match("/<c:reasonCode>(.+?)<\/c:reasonCode>/", $result, $matches);
            $reason_code = $matches[1];
            @preg_match("/<c:requestToken>(.+?)<\/c:requestToken>/", $result, $matches);
            $request_token = $matches[1];

            if (!$decision) {
                $this->writeLog("apiRefund","end (failed: no result)");
                return null;
            }

            $this->writeLog("apiRefund","request_id: ".$request_id);
            $this->writeLog("apiRefund","decision: ".$decision);
            $this->writeLog("apiRefund","reason_code: ".$reason_code);
            $this->writeLog("apiRefund","request_token: ".$request_token);

            if ($decision == 'ACCEPT') {
                $this->writeLog("apiRefund","end (succeed)");
            } else {
                $this->writeLog("apiRefund","end (failed: $reason_code)");
            }

            return array(
                'request_id'    => $request_id,
                'decision'      => $decision,
                'reason_code'   => $reason_code,
                'request_token' => $request_token,
            );
        } else {
            $this->writeLog("apiRefund","end (failed: no response)");
            
            return null;
        }
    }


    /**
     * 売上無効
     *
     * @param price 総額（税込）
     * @param request_id リクエストID
     * @param request_token リクエストトークン
     */
    public function apiAuthVoid($datas) {
        $this->writeLog("apiAuthVoid","start");

        $config = Zend_Registry::get('config');
        
        $file = @file_get_contents(APPLICATION_PATH . '/../library/soap/pay_auth_cancel.xml');
        $file = str_replace('%USER_NAME%',     $config->cs->user_name, $file);
        $file = str_replace('%MERCHANT_ID%',   $config->cs->merchant_id, $file);
        $file = str_replace('%PASSWORD%',      $config->cs->password, $file);
        $file = str_replace('%ORDER_CODE%',    $this->getUUID(), $file);
        $file = str_replace('%PRICE%',         $datas['price'], $file);
        $file = str_replace('%REQUEST_ID%',    $datas['request_id'], $file);
        $file = str_replace('%REQUEST_TOKEN%', $datas['request_token'], $file);
        
        $this->writeLog("apiAuthVoid","request: ".$file);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$config->cs->url_api);
        curl_setopt($curl, CURLOPT_POST,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $file);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml; charset=utf-8"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        $this->writeLog("apiAuthVoid","response: ".$result);

        if ($result) {
            $matches = array();

            @preg_match("/<c:requestID>(.+?)<\/c:requestID>/", $result, $matches);
            $request_id = $matches[1];
            @preg_match("/<c:decision>(.+?)<\/c:decision>/", $result, $matches);
            $decision = $matches[1];
            @preg_match("/<c:reasonCode>(.+?)<\/c:reasonCode>/", $result, $matches);
            $reason_code = $matches[1];
            @preg_match("/<c:requestToken>(.+?)<\/c:requestToken>/", $result, $matches);
            $request_token = $matches[1];

            if (!$decision) {
                $this->writeLog("apiAuthVoid","end (failed: no result)");
                return null;
            }

            $this->writeLog("apiAuthVoid","request_id: ".$request_id);
            $this->writeLog("apiAuthVoid","decision: ".$decision);
            $this->writeLog("apiAuthVoid","reason_code: ".$reason_code);
            $this->writeLog("apiAuthVoid","request_token: ".$request_token);

            if ($decision == 'ACCEPT') {
                $this->writeLog("apiAuthVoid","end (succeed)");
            } else {
                $this->writeLog("apiAuthVoid","end (failed: $reason_code)");
            }

            return array(
                'request_id'    => $request_id,
                'decision'      => $decision,
                'reason_code'   => $reason_code,
                'request_token' => $request_token,
            );
        } else {
            $this->writeLog("apiAuthVoid","end (failed: no response)");
            
            return null;
        }
    }

    /**
     * カード種別を得る
     */
    public function getCardType($card_number) {
        if(substr($card_number, 0, 1) == '4') {
            return '001';
        } elseif (substr($card_number, 0, 1) == '5') {
            return '002';
        } elseif (substr($card_number, 0, 2) == '34') {
            return '003';
        } elseif (substr($card_number, 0, 2) == '37') {
            return '003';
        } elseif (substr($card_number, 0, 2) == '30') {
            return '005';
        } elseif (substr($card_number, 0, 2) == '36') {
            return '005';
        } elseif (substr($card_number, 0, 2) == '38') {
            return '005';
        } elseif (substr($card_number, 0, 2) == '39') {
            return '007';
        } elseif (substr($card_number, 0, 2) == '35') {
            return '007';
        } else {
            return '001';
        }
    }

    /**
     * サブスクリプション作成
     *
     * @param name1 姓
     * @param name2 名
     * @param email メールアドレス
     * @param price 総額（税込）
     * @param card_number カード番号
     * @param exp_month 01-12
     * @param exp_year  YYYY
     * @param start_date 課金開始日（YYYYMMDD）
     */
    public function apiSubscriptionCreate($datas) {
        $this->writeLog("apiSubscriptionCreate","start");

        $config = Zend_Registry::get('config');
        
        $file = @file_get_contents(APPLICATION_PATH . '/../library/soap/sub_create.xml');
        $file = str_replace('%USER_NAME%',    $config->cs->user_name, $file);
        $file = str_replace('%MERCHANT_ID%',  $config->cs->merchant_id, $file);
        $file = str_replace('%PASSWORD%',     $config->cs->password, $file);
        $file = str_replace('%ORDER_CODE%',   $this->getUUID(), $file);
        $file = str_replace('%NAME1%',        $datas['name1'], $file);
        $file = str_replace('%NAME2%',        $datas['name2'], $file);
        $file = str_replace('%EMAIL%',        $datas['email'], $file);
        $file = str_replace('%PRICE%',        $datas['price'], $file);
        $file = str_replace('%CARD_NUMBER%',  $datas['card_number'], $file);
        $file = str_replace('%EXPIRE_MONTH%', $datas['exp_month'], $file);
        $file = str_replace('%EXPIRE_YEAR%',  $datas['exp_year'], $file);
        $file = str_replace('%CARD_TYPE%',    $this->getCardType($datas['card_number']), $file);
        $file = str_replace('%START_DATE%',   $datas['start_date'], $file);
        
        $this->writeLog("apiSubscriptionCreate","request: ".$file);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$config->cs->url_api);
        curl_setopt($curl, CURLOPT_POST,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $file);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml; charset=utf-8"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        $this->writeLog("apiSubscriptionCreate","response: ".$result);

        if ($result) {
            $matches = array();

            @preg_match("/<c:requestID>(.+?)<\/c:requestID>/", $result, $matches);
            $request_id = $matches[1];
            @preg_match("/<c:decision>(.+?)<\/c:decision>/", $result, $matches);
            $decision = $matches[1];
            @preg_match("/<c:reasonCode>(.+?)<\/c:reasonCode>/", $result, $matches);
            $reason_code = $matches[1];
            @preg_match("/<c:requestToken>(.+?)<\/c:requestToken>/", $result, $matches);
            $request_token = $matches[1];
            @preg_match("/<c:subscriptionID>(.+?)<\/c:subscriptionID>/", $result, $matches);
            $subscription_id = $matches[1];

            if (!$decision) {
                $this->writeLog("apiSubscriptionCreate","end (failed: no result)");
                return null;
            }

            $this->writeLog("apiSubscriptionCreate","request_id: ".$request_id);
            $this->writeLog("apiSubscriptionCreate","decision: ".$decision);
            $this->writeLog("apiSubscriptionCreate","reason_code: ".$reason_code);
            $this->writeLog("apiSubscriptionCreate","request_token: ".$request_token);
            $this->writeLog("apiSubscriptionCreate","subscription_id: ".$subscription_id);

            if ($decision == 'ACCEPT') {
                $this->writeLog("apiSubscriptionCreate","end (succeed)");
            } else {
                $this->writeLog("apiSubscriptionCreate","end (failed: $reason_code)");
            }

            return array(
                'request_id'      => $request_id,
                'decision'        => $decision,
                'reason_code'     => $reason_code,
                'request_token'   => $request_token,
                'subscription_id' => $subscription_id,
            );
        } else {
            $this->writeLog("apiSubscriptionCreate","end (failed: no response)");
            
            return null;
        }
    }

    /**
     * サブスクリプション削除
     *
     * @param subscription_id サブスクリプションID
     */
    public function apiSubscriptionDelete($datas) {
        $this->writeLog("apiSubscriptionDelete","start");

        $config = Zend_Registry::get('config');
        
        $file = @file_get_contents(APPLICATION_PATH . '/../library/soap/sub_delete.xml');
        $file = str_replace('%USER_NAME%',       $config->cs->user_name, $file);
        $file = str_replace('%MERCHANT_ID%',     $config->cs->merchant_id, $file);
        $file = str_replace('%PASSWORD%',        $config->cs->password, $file);
        $file = str_replace('%ORDER_CODE%',      $this->getUUID(), $file);
        $file = str_replace('%SUBSCRIPTION_ID%', $datas['subscription_id'], $file);
        $file = str_replace('%START_DATE%',      date("Ymd"), $file);
        
        $this->writeLog("apiSubscriptionDelete","request: ".$file);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$config->cs->url_api);
        curl_setopt($curl, CURLOPT_POST,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $file);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml; charset=utf-8"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        $this->writeLog("apiSubscriptionDelete","response: ".$result);

        if ($result) {
            $matches = array();

            @preg_match("/<c:requestID>(.+?)<\/c:requestID>/", $result, $matches);
            $request_id = $matches[1];
            @preg_match("/<c:decision>(.+?)<\/c:decision>/", $result, $matches);
            $decision = $matches[1];
            @preg_match("/<c:reasonCode>(.+?)<\/c:reasonCode>/", $result, $matches);
            $reason_code = $matches[1];
            @preg_match("/<c:requestToken>(.+?)<\/c:requestToken>/", $result, $matches);
            $request_token = $matches[1];
            //@preg_match("/<c:subscriptionID>(.+?)<\/c:subscriptionID>/", $result, $matches);
            //$subscription_id = $matches[1];

            if (!$decision) {
                $this->writeLog("apiSubscriptionDelete","end (failed: no result)");
                return null;
            }

            $this->writeLog("apiSubscriptionDelete","request_id: ".$request_id);
            $this->writeLog("apiSubscriptionDelete","decision: ".$decision);
            $this->writeLog("apiSubscriptionDelete","reason_code: ".$reason_code);
            $this->writeLog("apiSubscriptionDelete","request_token: ".$request_token);
            //$this->writeLog("apiSubscriptionDelete","subscription_id: ".$subscription_id);

            if ($decision == 'ACCEPT') {
                $this->writeLog("apiSubscriptionDelete","end (succeed)");
            } else {
                $this->writeLog("apiSubscriptionDelete","end (failed: $reason_code)");
            }

            return array(
                'request_id'      => $request_id,
                'decision'        => $decision,
                'reason_code'     => $reason_code,
                'request_token'   => $request_token,
                'subscription_id' => $datas['subscription_id'],
            );
        } else {
            $this->writeLog("apiSubscriptionDelete","end (failed: no response)");
            
            return null;
        }
    }

    /**
     * サブスクリプション更新
     *
     * @param subscription_id サブスクリプションID
     * @param price 総額（税込）
     */
    public function apiSubscriptionUpdate($datas) {
        $this->writeLog("apiSubscriptionUpdate","start");

        $config = Zend_Registry::get('config');
        
        $file = @file_get_contents(APPLICATION_PATH . '/../library/soap/sub_update.xml');
        $file = str_replace('%USER_NAME%',       $config->cs->user_name, $file);
        $file = str_replace('%MERCHANT_ID%',     $config->cs->merchant_id, $file);
        $file = str_replace('%PASSWORD%',        $config->cs->password, $file);
        $file = str_replace('%ORDER_CODE%',      $this->getUUID(), $file);
        $file = str_replace('%SUBSCRIPTION_ID%', $datas['subscription_id'], $file);
        $file = str_replace('%PRICE%',           $datas['price'], $file);
        
        $this->writeLog("apiSubscriptionUpdate","request: ".$file);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$config->cs->url_api);
        curl_setopt($curl, CURLOPT_POST,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $file);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml; charset=utf-8"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        $this->writeLog("apiSubscriptionUpdate","response: ".$result);

        if ($result) {
            $matches = array();

            @preg_match("/<c:requestID>(.+?)<\/c:requestID>/", $result, $matches);
            $request_id = $matches[1];
            @preg_match("/<c:decision>(.+?)<\/c:decision>/", $result, $matches);
            $decision = $matches[1];
            @preg_match("/<c:reasonCode>(.+?)<\/c:reasonCode>/", $result, $matches);
            $reason_code = $matches[1];
            @preg_match("/<c:requestToken>(.+?)<\/c:requestToken>/", $result, $matches);
            $request_token = $matches[1];
            @preg_match("/<c:subscriptionID>(.+?)<\/c:subscriptionID>/", $result, $matches);
            $subscription_id = $matches[1];

            if (!$decision) {
                $this->writeLog("apiSubscriptionUpdate","end (failed: no result)");
                return null;
            }

            $this->writeLog("apiSubscriptionUpdate","request_id: ".$request_id);
            $this->writeLog("apiSubscriptionUpdate","decision: ".$decision);
            $this->writeLog("apiSubscriptionUpdate","reason_code: ".$reason_code);
            $this->writeLog("apiSubscriptionUpdate","request_token: ".$request_token);
            $this->writeLog("apiSubscriptionUpdate","subscription_id: ".$subscription_id);

            if ($decision == 'ACCEPT') {
                $this->writeLog("apiSubscriptionUpdate","end (succeed)");
            } else {
                $this->writeLog("apiSubscriptionUpdate","end (failed: $reason_code)");
            }

            return array(
                'request_id'      => $request_id,
                'decision'        => $decision,
                'reason_code'     => $reason_code,
                'request_token'   => $request_token,
                'subscription_id' => $subscription_id,
            );
        } else {
            $this->writeLog("apiSubscriptionUpdate","end (failed: no response)");
            
            return null;
        }
    }

    /**
     * サブスクリプション取得
     *
     * @param subscription_id サブスクリプションID
     */
    public function apiSubscriptionRetrieve($datas) {
        $this->writeLog("apiSubscriptionRetrieve","start");

        $config = Zend_Registry::get('config');
        
        $file = @file_get_contents(APPLICATION_PATH . '/../library/soap/sub_retrieve.xml');
        $file = str_replace('%USER_NAME%',       $config->cs->user_name, $file);
        $file = str_replace('%MERCHANT_ID%',     $config->cs->merchant_id, $file);
        $file = str_replace('%PASSWORD%',        $config->cs->password, $file);
        $file = str_replace('%ORDER_CODE%',      $this->getUUID(), $file);
        $file = str_replace('%SUBSCRIPTION_ID%', $datas['subscription_id'], $file);
        
        $this->writeLog("apiSubscriptionRetrieve","request: ".$file);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$config->cs->url_api);
        curl_setopt($curl, CURLOPT_POST,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $file);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml; charset=utf-8"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        $this->writeLog("apiSubscriptionRetrieve","response: ".$result);

        if ($result) {
            $matches = array();

            @preg_match("/<c:requestID>(.+?)<\/c:requestID>/", $result, $matches);
            $request_id = $matches[1];
            @preg_match("/<c:decision>(.+?)<\/c:decision>/", $result, $matches);
            $decision = $matches[1];
            @preg_match("/<c:reasonCode>(.+?)<\/c:reasonCode>/", $result, $matches);
            $reason_code = $matches[1];
            @preg_match("/<c:requestToken>(.+?)<\/c:requestToken>/", $result, $matches);
            $request_token = $matches[1];
            @preg_match("/<c:subscriptionID>(.+?)<\/c:subscriptionID>/", $result, $matches);
            $subscription_id = $matches[1];
            @preg_match("/<c:paymentsRemaining>(.+?)<\/c:paymentsRemaining>/", $result, $matches);
            $remaining = $matches[1];
            @preg_match("/<c:status>(.+?)<\/c:status>/", $result, $matches);
            $status = $matches[1];

            if (!$decision) {
                $this->writeLog("apiSubscriptionRetrieve","end (failed: no result)");
                return null;
            }

            $this->writeLog("apiSubscriptionRetrieve","request_id: ".$request_id);
            $this->writeLog("apiSubscriptionRetrieve","decision: ".$decision);
            $this->writeLog("apiSubscriptionRetrieve","reason_code: ".$reason_code);
            $this->writeLog("apiSubscriptionRetrieve","request_token: ".$request_token);
            $this->writeLog("apiSubscriptionRetrieve","subscription_id: ".$subscription_id);
            $this->writeLog("apiSubscriptionRetrieve","remaining: ".$remaining);
            $this->writeLog("apiSubscriptionRetrieve","status: ".$status);

            if ($decision == 'ACCEPT') {
                $this->writeLog("apiSubscriptionRetrieve","end (succeed)");
            } else {
                $this->writeLog("apiSubscriptionRetrieve","end (failed: $reason_code)");
            }

            return array(
                'request_id'      => $request_id,
                'decision'        => $decision,
                'reason_code'     => $reason_code,
                'request_token'   => $request_token,
                'subscription_id' => $subscription_id,
                'remaining'       => $remaining,
                'status'          => $status,
            );
        } else {
            $this->writeLog("apiSubscriptionRetrieve","end (failed: no response)");
            
            return null;
        }
    }
}
