<?php
class Util_Payment_Zeus {
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
     * @args $args['cardnumber'] ハイフンなしのカード番号
     * @args $args['expyy']  年（2桁）
     * @args $args['expmm']  月（2桁）
     * @args $args['telno']  ハイフンなしの電話番号
     * @args $args['email']  メールアドレス
     * @args $args['sendid'] 注文ID
     * @args $args['username'] スペースなしのユーザー名（英字）
     * @args $args['money']  決済金額（総額）
     * @args $args['div']    分割数（01/03/05/06/10）
     */
    public static function execute($args) {
        $config = Zend_Registry::get('config');

        $params['clientip']   = $config->zeus->clientip;
        $params['cardnumber'] = $args['cardnumber'];
        $params['expyy']      = $args['expyy'];
        $params['expmm']      = $args['expmm'];
        $params['telno']      = $args['telno'];
        $params['email']      = $args['email'];
        $params['sendid']     = $args['sendid'];
        $params['username']   = $args['username'];
        $params['money']      = $args['money'];
        $params['sendpoint']  = 'eccube';
        $params['send']       = 'mall';
        $params['pubsec']     = '';
        $params['div']        = $args['div'];

        // 0円のときは決済しない
        if ( !$args['money'] ) {
            return false;
        }

        // 決済開始
        $client = new Zend_Http_Client($config->zeus->api_url);
        $client->setParameterPost($params);
        $response = $client->request('POST');

        // 結果の判定
        if ( $response->getBody() === 'Success_order' ) {
            return true;
        }
        else {
            return false;
        }
    }
}
