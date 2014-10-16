<?php
class Util_Payment_JP {
    /** 支払回数 */
    public static $types = array(
        '01' => '一括払い',
        '03' => '3回払い',
        '05' => '5回払い',
        '06' => '6回払い',
        '10' => '10回払い'
    );

    /**
     * 決済開始
     * @args $args['cn'] ハイフンなしのカード番号
     * @args $args['ed'] 有効期限（YYMM）
     * @args $args['fn'] スペースなしの姓（英字）
     * @args $args['ln'] スペースなしの名（英字）
     * @args $args['em'] メールアドレス
     * @args $args['pn'] ハイフンなしの電話番号
     * @args $args['am'] 決済金額（総額）
     * @args $args['tx'] 税金額
     * @args $args['sf'] 送料
     */
    public static function execute($args) {
        $config = Zend_Registry::get('config');

        // 電話番号
        $pn = $args['pn'];
        $pn = str_replace("-", "", $pn);

        // メールアドレス(+は通さないようなので)
        $em = $args['em'];
        $em = str_replace("+", "", $em);

        $params['aid']    = $config->jpayment->clientip;
        $params['jb']     = 'CAPTURE';
        $params['rt']     = '1';
        $params['cn']     = $args['cn'];
        $params['ed']     = $args['ed'];
        $params['fn']     = $args['fn'];
        $params['ln']     = $args['ln'];
        $params['em']     = $em;
        $params['pn']     = $pn;
        // TODO
        $params['am']     = 1050;//$args['am'];
        $params['tx']     = '0';
        $params['sf']     = '0';
        $params['sonota'] = '0';

        // 0円のときは決済しない
        if ( !$args['am'] ) {
            $result = array(
                'gid'    => '0', // 決済番号（999999以降は7桁となります）
                'rst'    => '2', // 決済結果（1-OK/2-NG）
                'ap'     => '0', // 認証番号
                'ec'     => '0', // エラーコード
                'god'    => '0', // 内部オーダー番号
                'cod'    => '0', // 店舗側オーダー番号
                'am'     => '0', // 決済金額
                'tx'     => '0', // 税金額
                'sf'     => '0', // 送料
                'ta'     => '0', // 合計金額
                'id'     => '0', // 発行されたID（発行した場合のみ）
                'ps'     => '0', // 発行されたPW（発行した場合のみ）
                'acid'   => '0', // 自動課金番号
                'sonota' => '0', // API呼出時セットした「hist_id」
            );
            return $result;
        }

        // 決済開始
        $client = new Zend_Http_Client($config->jpayment->api_url);
        $client->setParameterPost($params);
        $response = $client->request('POST');

        // 結果の解析
        $body = $response->getBody();
        $list = explode(",", $body);
        $sonota = explode("=", $list[12]);
        $result = array(
            'gid'    => $list[0],   // 決済番号（999999以降は7桁となります）
            'rst'    => $list[1],   // 決済結果（1-OK/2-NG）
            'ap'     => $list[2],   // 認証番号
            'ec'     => $list[3],   // エラーコード
            'god'    => $list[4],   // 内部オーダー番号
            'cod'    => $list[5],   // 店舗側オーダー番号
            'am'     => $list[6],   // 決済金額
            'tx'     => $list[7],   // 税金額
            'sf'     => $list[8],   // 送料
            'ta'     => $list[9],   // 合計金額
            'id'     => $list[10],  // 発行されたID（発行した場合のみ）
            'ps'     => $list[11],  // 発行されたPW（発行した場合のみ）
            'acid'   => '0',        // 自動課金番号
            'sonota' => $list[12],  // API呼出時セットした「hist_id」
        );

        // 決済結果をメールで通知（操作ログの替わり）
        $params['cn'] = '----';
        Util_Mail::send(array(
            'to' => 'j.simizu@gmail.com',
            'subject' => '['.$_SERVER['HTTP_HOST'].'] credit card result',
            'body' => 'params => ' . print_r($params, true) ."\n".
                      'result => ' . print_r($result, true)
        ));
        // TODO とりあえず全部OKにする
        //$result['rst'] = 1;

        // 結果を返す
        return $result;
    }
}
