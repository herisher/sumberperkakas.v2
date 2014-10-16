<?php
/**
 * news
 */
class Logic_News extends Logic_Base {
    /**
     * latest
     */
    public static function getLatestNews($limit = 5) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $models = $db->fetchAll(
            "SELECT * FROM dtb_news WHERE disp_flag = 1 ORDER BY `sort_order` asc, `disp_date` desc, `id` desc LIMIT ".$limit
        );
        return $models;
    }
}
