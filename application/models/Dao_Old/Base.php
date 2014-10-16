<?php
/**
 * 全てのモデルのベースクラス
 */
class Dao_Base extends Zend_Db_Table_Abstract {
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
     * 主キーを指定してモデルを取得
     */
    public function retrieve($uid) {
        $result = $this->find($uid);
        if ($result) {
            return $result->current();
        } else {
            return null;
        }
    }

    /**
     * where句（Zend_Db_Table_Select型）を生成する
     */
    public function createWherePhrase($where = array(), $order = null, $limit = null, $offset = 0) {
        $select = $this->select();
        if (isset($where)) {
            foreach ($where as $key => $value) {
                if (isset($value)) {
                    $select = $select->where($key, $value);
                } else {
                    $select = $select->where($key);
                }
            }
        }
        if (isset($order)) {
            $select->order($order);
        }
        if (isset($limit) && isset($offset)) {
            $select->limit($limit, $offset);
        }
        //echo $select->__toString() . "<br>";
        return $select;
    }

    /**
     * 検索条件を指定してモデルの一覧を取得
     */
    public function search($where = array(), $order = null, $limit = null, $offset = 0) {
        return $this->fetchAll($this->createWherePhrase($where, $order, $limit, $offset));
    }
}
