<?php
/**
 * 商品
 */
class Dao_Product extends Dao_Base {
    protected $_name    = 'dtb_product';
    protected $_primary = 'id';

    public static $statics = array(
        'status' => array(
            '1' => 'Tersedia',
            '2' => 'Persediaan Habis',
        ),
        'disp_flag' => array(
            '1' => ' Ya',
            '0' => ' Tidak',
        ),
    );
}
