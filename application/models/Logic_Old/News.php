<?php
/**
 * お知らせ
 */
class Logic_News extends Logic_Base {
    /**
     * 最新○件を取得（消費者サイト）
     */
    public static function getLatestNewsConsumer($limit = 3) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $models = $db->fetchAll(
            "SELECT * FROM dtb_news WHERE disp_flag = 1 AND disp_type != 2 ORDER BY `sort_order` asc, `disp_date` desc, `id` desc LIMIT ".$limit
        );
        return $models;
    }

    public static function getTodayNews($date) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $models = $db->fetchAll(
            "SELECT * FROM dtb_news WHERE disp_date = ?", $date
        );
        return $models;
    }

    /**
     * 最新○件を取得（栄養士サイト）
     */
    public static function getLatestNewsDietitian($limit = 3) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $models = $db->fetchAll(
            "SELECT * FROM dtb_news WHERE disp_flag = 1 AND disp_type != 1 ORDER BY `sort_order` asc, `disp_date` desc, `id` desc LIMIT ".$limit
        );
        return $models;
    }

    /**
     * 最新○件を取得（栄養士サイト）（非ログイン）
     */
	//stub
	public static function getLatestNewsNonDietitian($limit = 3) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $models = $db->fetchAll(
            "SELECT * FROM dtb_news WHERE disp_flag = 1 AND disp_type = 0 ORDER BY disp_date DESC, id DESC LIMIT ".$limit
        );
        return $models;
	}

	
	public static function getLatestNewsPublic($limit = 3) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $models = $db->fetchAll(
            "SELECT * FROM dtb_news WHERE disp_flag = 1 AND disp_type = 0 ORDER BY disp_date DESC, id DESC LIMIT ".$limit
        );
        return $models;
    }
}
