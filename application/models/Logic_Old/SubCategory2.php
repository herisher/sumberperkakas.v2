<?php
/**
 * sub category 2
 */
class Logic_SubCategory2 extends Logic_Base {
    /**
     *
     */
    public function findById($id) {
        return $this->db()->fetchRow("SELECT * FROM dtb_sub_category2 WHERE `id` = ?", $id);
    }
	
    /**
     *
     */
    public function findByCategoryId($category_id) {
		$models = $this->db()->fetchAll(
			"SELECT * FROM dtb_sub_category2 WHERE `category_id` = ? ORDER BY `disp_order`", 
			array($category_id)
		);
		$datas = array();
		foreach ($models as $model) {
			$datas[$model['id']] = $model['name'];
		}		
        return $datas;
    }
	
    /**
     *
     */
    public function findBySubCategoryId($sub_category_id) {
		$models = $this->db()->fetchAll(
			"SELECT * FROM dtb_sub_category2 WHERE `sub_category_id` = ? ORDER BY `disp_order`", 
			array($sub_category_id)
		);
		$datas = array();
		foreach ($models as $model) {
			$datas[$model['id']] = $model['name'];
		}		
        return $datas;
    }
	
    /**
     *
     */
    public function findBySubCategory1Id($sub_category1_id) {
		$models = $this->db()->fetchAll("SELECT * FROM dtb_sub_category2 WHERE `sub_category1_id` = ? ORDER BY `disp_order`", array($sub_category1_id));
		$datas = array();
		foreach ($models as $model) {
			$datas[$model['id']] = $model['name'];
		}		
        return $datas;
    }
	
    /**
     *
     */
    public function getAll() {
        return $this->db()->fetchAll("SELECT * FROM dtb_sub_category2 ORDER BY `id`");
    }

    /**
     *
     */
    public function getLatest($sub_category1_id) {
        return $this->db()->fetchRow("SELECT * FROM dtb_sub_category2 WHERE sub_category1_id = ? ORDER BY `disp_order` DESC LIMIT 1",
			array($sub_category1_id)
		);
    }
	
    /**
     *
     */
    public function getAllByCategory($sub_category1_id) {
		if( !$sub_category1_id ) {
			$models = $this->db()->fetchAll("SELECT * FROM dtb_sub_category2 ORDER BY `id`");
		} else {
			$models = $this->db()->fetchAll("SELECT * FROM dtb_sub_category2 WHERE `sub_category1_id` = ? ORDER BY `disp_order`", array($sub_category1_id));
			if( !count($models) ) {
				$models = array(
					array('id' => '', 'name' => 'emptyIdValue')
				);
			}
		}
		
		array_unshift($models, array('id' => '', 'name' => '▼Pilih'));
		return $models;
    }
	
    /**
     *
     */
    public function getAllByMainCategory($category_id) {
		if( !$category_id ) {
			$models = $this->db()->fetchAll("SELECT * FROM dtb_sub_category2 ORDER BY `id`");
		} else {
			$models = $this->db()->fetchAll("SELECT * FROM dtb_sub_category2 WHERE `category_id` = ? ORDER BY `disp_order`", array($category_id));
			if( !count($models) ) {
				$models = array(
					array('id' => '', 'name' => 'emptyIdValue')
				);
			}
		}
		array_unshift($models, array('id' => '', 'name' => '▼Pilih'));
		return $models;
    }
	
    /**
     *
     */
    public function getAllBySubCategory($sub_category_id) {
		if( !$sub_category_id ) {
			$models = $this->db()->fetchAll("SELECT * FROM dtb_sub_category2 ORDER BY `id`");
		} else {
			$models = $this->db()->fetchAll(
				"SELECT * FROM dtb_sub_category2 WHERE `sub_category_id` = ? ORDER BY `disp_order`", 
				array($sub_category_id)
			);
			if( !count($models) ) {
				$models = array(
					array('id' => '', 'name' => 'emptyIdValue')
				);
			}
		}
		array_unshift($models, array('id' => '', 'name' => '▼Pilih'));
		return $models;
    }
	
    /**
     *
     */
    public function getAllByHash() {
        $models = $this->db()->fetchAll("SELECT * FROM dtb_sub_category2 ORDER BY `id`");
        $datas = array();
        foreach ($models as $model) {
            $datas[$model['id']] = $model['name'];
        }
        return $datas;
    }

    /**
     * do up row
     */
    public function doUp($sub_category1_id, $id) {
        $model = $this->db()->fetchRow(
            "SELECT * FROM dtb_sub_category2 WHERE id = ? LIMIT 1",
            array($id)
        );
        $target = $this->db()->fetchRow(
            "SELECT * FROM dtb_sub_category2 WHERE sub_category1_id = ? AND disp_order < ? ORDER BY disp_order DESC LIMIT 1",
            array($sub_category1_id, $model['disp_order'])
        );
        if (!$target) exit();
        
        $this->db()->update("dtb_sub_category2", array(
            'disp_order'  => $target['disp_order'],
            'update_date' => date("Y-m-d H:i:s")
        ),'id = '.$model['id']);
        $this->db()->update("dtb_sub_category2", array(
            'disp_order'  => $model['disp_order'],
            'update_date' => date("Y-m-d H:i:s")
        ),'id = '.$target['id']);
    }

    /**
     * do down row
     */
    public function doDown($sub_category1_id, $id) {
        $model = $this->db()->fetchRow(
            "SELECT * FROM dtb_sub_category2 WHERE id = ? LIMIT 1",
            array($id)
        );
        $target = $this->db()->fetchRow(
            "SELECT * FROM dtb_sub_category2 WHERE sub_category1_id = ? AND disp_order > ? ORDER BY disp_order LIMIT 1",
            array($sub_category1_id, $model['disp_order'])
        );
        if (!$target) exit();
        
        $this->db()->update("dtb_sub_category2", array(
            'disp_order'  => $target['disp_order'],
            'update_date' => date("Y-m-d H:i:s"),
        ),'id = '.$model['id']);
        $this->db()->update("dtb_sub_category2", array(
            'disp_order'  => $model['disp_order'],
            'update_date' => date("Y-m-d H:i:s"),
        ),'id = '.$target['id']);
    }

    /**
     * insert a row
     */
    public function doInsert($sub_category1_id, $id) {
        $model = $this->db()->fetchRow(
            "SELECT * FROM dtb_sub_category2 WHERE id = ? LIMIT 1",
            array($id)
        );
        if (!$model) {
            return;
        }
        
        $this->db()->query(
            "UPDATE dtb_sub_category2 SET disp_order = disp_order + 1 WHERE sub_category1_id = ? AND disp_order > ?",
            array($sub_category1_id, $model['disp_order'])
        );
        
		$model_id = $this->model('Dao_SubCategory2')->insert(
			array(
				'disp_order'   => intval($model['disp_order'])+1,
				'create_date'  => date("Y-m-d H:i:s"),
				'update_date'  => date("Y-m-d H:i:s")
			)
		);
		
		return $model_id;
    }

    /**
     * add row
     */
    public function doNewRow($sub_category1_id) {
        $i = $this->db()->fetchOne("SELECT MAX(disp_order) FROM dtb_sub_category2 WHERE sub_category1_id = ?", 
			array($sub_category1_id)
		);

		$model_id = $this->model('Dao_SubCategory2')->insert(
			array(
				'disp_order'  => intval($i) + 1,
				'create_date' => date("Y-m-d H:i:s"),
				'update_date' => date("Y-m-d H:i:s")
			)
		);
		
		return $model_id;
    }
	
    /**
     * add row
     */
    public function doNewTop($sub_category1_id) {
		if( $this->getLatest($sub_category1_id) ) {
			$this->db()->query(
				"UPDATE dtb_sub_category2 SET disp_order = disp_order + 1 WHERE sub_category1_id = ? AND disp_order >= 1",
				array($sub_category1_id)
			);
		}
		
		$model_id = $this->model('Dao_SubCategory2')->insert(
			array(
				'disp_order'  => 1,
				'create_date' => date("Y-m-d H:i:s"),
				'update_date' => date("Y-m-d H:i:s")
            )
        );
		
		return $model_id;
    }
}
