<?php
/**
 * 全てのロジックのベースクラス
 */
class Logic_Base {
    /**
     * データベースアダプターを返す
     */
    public function db() {
        return Zend_Db_Table_Abstract::getDefaultAdapter();
    }

    /**
     * モデルのインスタンスを返す
     */
    public function model($modelname, $option = array()) {
        $table = new $modelname($option);
        return $table;
    }

    /**
     * ナビゲータの生成
     */
    public function createNavigator($datas, $page, $limit) {
        if (!$page)  { $page  = 1;  }
        if (!$limit) { $limit = 10; }

        $paginator = null;
        if ( gettype($datas) == "object" && get_class($datas) === 'Zend_Db_Table_Select' ) {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_DbTableSelect( $datas )
            );
        }
        elseif ( gettype($datas) == "object" && get_class($datas) === 'Zend_Db_Select' ) {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_DbSelect( $datas )
            );
        } else {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_Array( $datas )
            );
        }
        
        $paginator->setItemCountPerPage($limit);
        if ( $paginator->count() && $page ) {
            $paginator->setCurrentPageNumber($page);
        }
        
        return $paginator;
    }
}
