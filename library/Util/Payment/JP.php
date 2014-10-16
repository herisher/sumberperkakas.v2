<?php
class Util_Payment_JP {
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
     * @args $args['cn'] �n�C�t���Ȃ��̃J�[�h�ԍ�
     * @args $args['ed'] �L�������iYYMM�j
     * @args $args['fn'] �X�y�[�X�Ȃ��̐��i�p���j
     * @args $args['ln'] �X�y�[�X�Ȃ��̖��i�p���j
     * @args $args['em'] ���[���A�h���X
     * @args $args['pn'] �n�C�t���Ȃ��̓d�b�ԍ�
     * @args $args['am'] ���ϋ��z�i���z�j
     * @args $args['tx'] �ŋ��z
     * @args $args['sf'] ����
     */
    public static function execute($args) {
        $config = Zend_Registry::get('config');

        // �d�b�ԍ�
        $pn = $args['pn'];
        $pn = str_replace("-", "", $pn);

        // ���[���A�h���X(+�͒ʂ��Ȃ��悤�Ȃ̂�)
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

        // 0�~�̂Ƃ��͌��ς��Ȃ�
        if ( !$args['am'] ) {
            $result = array(
                'gid'    => '0', // ���ϔԍ��i999999�ȍ~��7���ƂȂ�܂��j
                'rst'    => '2', // ���ό��ʁi1-OK/2-NG�j
                'ap'     => '0', // �F�ؔԍ�
                'ec'     => '0', // �G���[�R�[�h
                'god'    => '0', // �����I�[�_�[�ԍ�
                'cod'    => '0', // �X�ܑ��I�[�_�[�ԍ�
                'am'     => '0', // ���ϋ��z
                'tx'     => '0', // �ŋ��z
                'sf'     => '0', // ����
                'ta'     => '0', // ���v���z
                'id'     => '0', // ���s���ꂽID�i���s�����ꍇ�̂݁j
                'ps'     => '0', // ���s���ꂽPW�i���s�����ꍇ�̂݁j
                'acid'   => '0', // �����ۋ��ԍ�
                'sonota' => '0', // API�ďo���Z�b�g�����uhist_id�v
            );
            return $result;
        }

        // ���ϊJ�n
        $client = new Zend_Http_Client($config->jpayment->api_url);
        $client->setParameterPost($params);
        $response = $client->request('POST');

        // ���ʂ̉��
        $body = $response->getBody();
        $list = explode(",", $body);
        $sonota = explode("=", $list[12]);
        $result = array(
            'gid'    => $list[0],   // ���ϔԍ��i999999�ȍ~��7���ƂȂ�܂��j
            'rst'    => $list[1],   // ���ό��ʁi1-OK/2-NG�j
            'ap'     => $list[2],   // �F�ؔԍ�
            'ec'     => $list[3],   // �G���[�R�[�h
            'god'    => $list[4],   // �����I�[�_�[�ԍ�
            'cod'    => $list[5],   // �X�ܑ��I�[�_�[�ԍ�
            'am'     => $list[6],   // ���ϋ��z
            'tx'     => $list[7],   // �ŋ��z
            'sf'     => $list[8],   // ����
            'ta'     => $list[9],   // ���v���z
            'id'     => $list[10],  // ���s���ꂽID�i���s�����ꍇ�̂݁j
            'ps'     => $list[11],  // ���s���ꂽPW�i���s�����ꍇ�̂݁j
            'acid'   => '0',        // �����ۋ��ԍ�
            'sonota' => $list[12],  // API�ďo���Z�b�g�����uhist_id�v
        );

        // ���ό��ʂ����[���Œʒm�i���샍�O�̑ւ��j
        $params['cn'] = '----';
        Util_Mail::send(array(
            'to' => 'j.simizu@gmail.com',
            'subject' => '['.$_SERVER['HTTP_HOST'].'] credit card result',
            'body' => 'params => ' . print_r($params, true) ."\n".
                      'result => ' . print_r($result, true)
        ));
        // TODO �Ƃ肠�����S��OK�ɂ���
        //$result['rst'] = 1;

        // ���ʂ�Ԃ�
        return $result;
    }
}
