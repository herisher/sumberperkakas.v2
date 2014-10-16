<?php
/**
 * 商品
 */
class Logic_Product extends Logic_Base {
    /**
     * 現在の価格を返す
     */
    public function getCurrentPrice($product) {
        if ($product['discount_period_start'] && strtotime($product['discount_period_start']) < time() && $product['discount_period_end'] && strtotime($product['discount_period_end'] . " +1 day") > time())
        {
            $current_price = intval($product['discount_price']); //intval($product['price']) - intval($product['discount_price']);
        }
        elseif ($product['discount_period_start'] && strtotime($product['discount_period_start']) < time() && !$product['discount_period_end'])
        {
            $current_price = intval($product['discount_price']); //intval($product['price']) - intval($product['discount_price']);
        }
        else {
            $current_price = intval($product['price']);
        }
        if ($current_price < 0) {
            $current_price = 0;
        }
        return $current_price;
    }

    /**
     * 割引金額を返す
     */
    public function getDiscountPrice($product) {
        if ($product['discount_period_start'] && strtotime($product['discount_period_start']) < time() && $product['discount_period_end'] && strtotime($product['discount_period_end'] . " +1 day") > time())
        {
            $discount_price = intval($product['discount_price']);
        }
        elseif ($product['discount_period_start'] && strtotime($product['discount_period_start']) < time())
        {
            $discount_price = intval($product['discount_price']); //intval($product['price']) - intval($product['discount_price']);
        }
        else {
            $discount_price = 0;
        }
        if ($discount_price < 0) {
            $discount_price = 0;
        }
        return $discount_price;
    }

    /**
     * ピックアップ商品○件を取得
     */
    public function getPickupProduct($limit = 6) {
        $products = $this->db()->fetchAll(
            "SELECT pr.*, s.name AS shop_name FROM dtb_product_pickup AS pi, dtb_shop AS s, dtb_product AS pr ".
            "WHERE 
				pi.product_id = pr.id
				AND pr.shop_id = s.id 
				AND s.status = 3 
				AND pr.status = 2
				AND pr.disp_flag = 1
			ORDER BY 
				pi.create_date 
			DESC LIMIT {$limit}"
        );
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }

    /**
     * 最新○件を取得
     */
    public function getLatestProduct($limit = 6) {
        $products = $this->db()->fetchAll(
            "SELECT pr.*, s.name AS shop_name FROM dtb_product AS pr, dtb_shop AS s "."
            WHERE 
				pr.status = 2 
				AND pr.disp_flag = 1 
				AND pr.shop_id = s.id 
				AND s.status = 3 
			ORDER BY pr.create_date DESC LIMIT {$limit}"
        );
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }

    /**
     * カテゴリ別商品一覧の取得
     */
    public function getProductByCategory($category_id = 0) {
        if ($category_id == 0){
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE status = 2");
        } else {
            $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE status = 2 AND category_id = ? ", $category_id);
        }
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }

    /**
     * 商品名でLIKE検索
     */
    public function getProductByName($name) {
        $products = $this->db()->fetchAll("SELECT * FROM dtb_product WHERE status = 2 AND `name` LIKE ?", "%".$name."%");
        
        $models = array();
        foreach($products as $product) {
            $product['current_price'] = $this->getCurrentPrice($product);
            array_push($models, $product);
        }
        return $models;
    }
}
