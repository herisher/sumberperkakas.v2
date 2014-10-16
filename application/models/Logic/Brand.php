<?php
/**
 * brand
 */
class Logic_Brand extends Logic_Base {
    /**
     *
     */
    public function findById($id) {
        return $this->db()->fetchRow("SELECT * FROM dtb_brand WHERE `id` = ?", $id);
    }

    /**
     *
     */
    public function getAll() {
        return $this->db()->fetchAll("SELECT * FROM dtb_brand ORDER BY `name`");
    }

    /**
     *
     */
    public function getName($id) {
        return $this->db()->fetchOne("SELECT name FROM dtb_brand WHERE id = ?", $id);
    }
	
    /**
     *
     */
    public function getAllByHash() {
        $models = $this->db()->fetchAll("SELECT * FROM dtb_brand ORDER BY `id`");
        $datas = array();
        foreach ($models as $model) {
            $datas[$model['id']] = $model['name'];
        }
        return $datas;
    }
}
