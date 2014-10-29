<?php
/**
 * 商品
 */
class Dao_ImageSlider extends Dao_Base {
    protected $_name    = 'dtb_image_slider';
    protected $_primary = 'id';

    public static $statics = array(
        'disp_flag' => array(
            '1' => ' Ya',
            '0' => ' Tidak',
        ),
    );
}
