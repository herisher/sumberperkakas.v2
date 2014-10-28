<?php
/**
 * category
 */
class Logic_Category extends Logic_Base {
    /**
     *
     */
    public function findById($id) {
        return $this->db()->fetchRow("SELECT * FROM dtb_category WHERE `id` = ?", $id);
    }

    /**
     *
     */
    public function getAll() {
        $models = $this->db()->fetchAll("SELECT * FROM dtb_category ORDER BY `disp_order`");
        
        $datas = array();
        foreach( $models as $model ) {
            $datas[$model['id']] = $model;
            $sub_category = $this->model('Logic_SubCategory')->findByCategoryId($model['id'], 1);
            if( count($sub_category) ) {
                $datas[$model['id']]['sub_category'] = $sub_category;
            }
        }
        return $datas;
    }

    /**
     *
     */
    public function getName($id) {
        return $this->db()->fetchOne("SELECT name FROM dtb_category WHERE id = ?", $id);
    }
	
    /**
     *
     */
    public function getLatest() {
        return $this->db()->fetchRow("SELECT * FROM dtb_category ORDER BY `disp_order` DESC LIMIT 1");
    }
	
    /**
     *
     */
    public function getAllByHash() {
        $models = $this->db()->fetchAll("SELECT * FROM dtb_category ORDER BY `disp_order`");
        $datas = array();
        foreach ($models as $model) {
            $datas[$model['id']] = $model['name'];
        }
        return $datas;
    }
	
    /**
     * do up row
     */
    public function doUp($id) {
        $model = $this->db()->fetchRow(
            "SELECT * FROM dtb_category WHERE id = ? LIMIT 1",
            array($id)
        );
        $target = $this->db()->fetchRow(
            "SELECT * FROM dtb_category WHERE disp_order < ? ORDER BY disp_order DESC LIMIT 1",
            array($model['disp_order'])
        );
        if (!$target) exit();
        
        $this->db()->update("dtb_category", array(
            'disp_order'  => $target['disp_order'],
            'update_date' => date("Y-m-d H:i:s")
        ),'id = '.$model['id']);
        $this->db()->update("dtb_category", array(
            'disp_order'  => $model['disp_order'],
            'update_date' => date("Y-m-d H:i:s")
        ),'id = '.$target['id']);
    }

    /**
     * do down row
     */
    public function doDown($id) {
        $model = $this->db()->fetchRow(
            "SELECT * FROM dtb_category WHERE id = ? LIMIT 1",
            array($id)
        );
        $target = $this->db()->fetchRow(
            "SELECT * FROM dtb_category WHERE disp_order > ? ORDER BY disp_order LIMIT 1",
            array($model['disp_order'])
        );
        if (!$target) exit();
        
        $this->db()->update("dtb_category", array(
            'disp_order'  => $target['disp_order'],
            'update_date' => date("Y-m-d H:i:s"),
        ),'id = '.$model['id']);
        $this->db()->update("dtb_category", array(
            'disp_order'  => $model['disp_order'],
            'update_date' => date("Y-m-d H:i:s"),
        ),'id = '.$target['id']);
    }

    /**
     * insert a row
     */
    public function doInsert($id) {
        $model = $this->db()->fetchRow(
            "SELECT * FROM dtb_category WHERE id = ? LIMIT 1",
            array($id)
        );
        if (!$model) {
            return;
        }
        
        $this->db()->query(
            "UPDATE dtb_category SET disp_order = disp_order + 1 WHERE disp_order > ?",
            array($model['disp_order'])
        );
        
		$category_id = $this->model('Dao_Category')->insert(
			array(
				'disp_order'   => intval($model['disp_order'])+1,
				'create_date'  => date("Y-m-d H:i:s"),
				'update_date'  => date("Y-m-d H:i:s")
			)
		);
		
		return $category_id;
    }

    /**
     * add row
     */
    public function doNewRow() {
        $i = $this->db()->fetchOne("SELECT MAX(disp_order) FROM dtb_category");

		$category_id = $this->model('Dao_Category')->insert(
			array(
				'disp_order'  => intval($i) + 1,
				'create_date' => date("Y-m-d H:i:s"),
				'update_date' => date("Y-m-d H:i:s")
			)
		);
		
		return $category_id;
    }
	
    /**
     * add row
     */
    public function doNewTop() {
		if( $this->getLatest() ) {
			$this->db()->query(
				"UPDATE dtb_category SET disp_order = disp_order + 1 WHERE disp_order >= 1"
			);
		}
		
		$category_id = $this->model('Dao_Category')->insert(
			array(
				'disp_order'  => 1,
				'create_date' => date("Y-m-d H:i:s"),
				'update_date' => date("Y-m-d H:i:s")
            )
        );
		
		return $category_id;
    }
}
