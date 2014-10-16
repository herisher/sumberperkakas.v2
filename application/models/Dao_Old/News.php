<?php
/**
 * dao_news
 */
class Dao_News extends Dao_Base {
    protected $_name    = 'dtb_news';
    protected $_primary = 'id';
    
    /**
     * statis
     */
    public static $statics = array(
        'disp_flag' => array(
            '0' => 'Tidak',
            '1' => 'Ya'
        ),
    );
}
