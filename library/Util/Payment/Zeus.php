<?php
class Util_Payment_Zeus {
    /** �x���� */
    public static $types = array(
        '01' => '�ꊇ����',
        '03' => '3�񕥂�',
        '05' => '5�񕥂�',
        '06' => '6�񕥂�',
        '10' => '10�񕥂�'
    );

    /**
     * ���ϊJ�n
     * @args $args['cardnumber'] �n�C�t���Ȃ��̃J�[�h�ԍ�
     * @args $args['expyy']  �N�i2���j
     * @args $args['expmm']  ���i2���j
     * @args $args['telno']  �n�C�t���Ȃ��̓d�b�ԍ�
     * @args $args['email']  ���[���A�h���X
     * @args $args['sendid'] ����ID
     * @args $args['username'] �X�y�[�X�Ȃ��̃��[�U�[���i�p���j
     * @args $args['money']  ���ϋ��z�i���z�j
     * @args $args['div']    �������i01/03/05/06/10�j
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

        // 0�~�̂Ƃ��͌��ς��Ȃ�
        if ( !$args['money'] ) {
            return false;
        }

        // ���ϊJ�n
        $client = new Zend_Http_Client($config->zeus->api_url);
        $client->setParameterPost($params);
        $response = $client->request('POST');

        // ���ʂ̔���
        if ( $response->getBody() === 'Success_order' ) {
            return true;
        }
        else {
            return false;
        }
    }
}
