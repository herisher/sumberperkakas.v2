<?php
// ???
function initialize() {
    // ???????????
    define('APPLICATION_PATH', realpath(dirname(__FILE__)));
    set_include_path(APPLICATION_PATH . '/../library' . PATH_SEPARATOR . APPLICATION_PATH . '/../application/models');

    // ??????????????
    date_default_timezone_set('Asia/Jakarta');

    // ?????????
    require_once 'Zend/Loader.php';
    Zend_Loader::registerAutoload();

    // ??????????
    if ((isset($_ENV['BZ_STAGE']) && $_ENV['BZ_STAGE'] === '1') || file_exists('/etc/id_stage')) {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/../application/configs/config.ini', 'staging');
        Zend_Registry::set('config', $config);
    } else {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/../application/configs/config.ini', 'production');
        Zend_Registry::set('config', $config);
    }

    // ???????????
    $db = Zend_Db::factory('Pdo_Mysql', $config -> database);
    $db -> query("SET names utf8");
    $db -> query("SET time_zone = '+7:00'");
    Zend_Db_Table_Abstract::setDefaultAdapter($db);

    // ???????
    ini_set('memory_limit', '-1');
}

function import_product( $datas = array() ) {
    $config = Zend_Registry::get('config');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    
    $matches = array();
    preg_match_all("/<div class=\"gitems\">((.|\n)*?)<div class=\"box3\">/", $datas['html'], $matches);
    //print_r($matches[0]);
    //echo "\n";
    foreach ($matches[0] as $item) {
        $domDocument = new DOMDocument();
        @$domDocument -> loadHTML($item);
        $xmlString = $domDocument -> saveXML();
        $xmlObject = simplexml_load_string($xmlString);
        //print_r($xmlObject);
        //echo "\n";
        //$category_name = (string)$xmlObject->body->li->a;
        $url = (string)$xmlObject->body->div->div[1]->a->attributes()->href;
        //echo "url:".$url;
        $html_product = crawl_url($url);
        if( !$html_product ) {
            echo "failed to open page ($url)!";
            //exit("failed to open page ($url)!");
        }
        
        $html_product = str_replace("\r", "", $html_product);
        $html_product = str_replace("\n", "", $html_product);

        //print_r($html_product);
        //echo "\n";
        //echo "mengandung ggroup";
        $matches_product = array();
        preg_match_all("/<div class=\"product\">((.|\n)*?)<\/form>/", $html_product, $matches_product);
        //print_r($matches_product);
        //echo "\n";
        //TODO NEXT!!!!
        $domDocument2 = new DOMDocument();
        @$domDocument2 -> loadHTML($matches_product[0][0]);
        $xmlString2 = $domDocument2 -> saveXML();
        $xmlObject2 = simplexml_load_string($xmlString2);
        //print_r($xmlObject2);
        //echo "\n";
        $product_name = (string)$xmlObject2->body->div->div[0]->div[0]->div[1]->div->b;
        $product_image_url = (string)$xmlObject2->body->div->div[0]->div[0]->div[0]->a[0]->attributes()->href;
        $product_thumb_url = (string)$xmlObject2->body->div->div[0]->div[0]->div[0]->a[0]->img->attributes()->src;
        $product_url = $url;
        
        if( (string)$xmlObject2->body->div->div[0]->h2[0]->a == "Katalog Produk" ) {
            $desc = array();
            preg_match_all("/<h2>Keterangan<\/h2>((.|\n)*?)<table cellspacing=\"1\"/", $matches_product[0][0], $desc);
            //print_r($desc);
            $product_desc = $desc[1][0];
        } elseif( (string)$xmlObject2->body->div->div[0]->h2->a == "Jual" ) {
            $product_desc = (string)$xmlObject2->body->div->div[0]->div[0]->div[2];
        }
        //exit;
        
        $product = $db -> fetchRow("SELECT * FROM `dtb_product` WHERE `name` = ?", array($product_name));
        if ( !$product ) {
            $product_data = array(
                'brand_id'          => 0,
                'category_id'       => isset($datas['category_id']) ? $datas['category_id'] : '',
                'sub_category_id'   => isset($datas['sub_category_id']) ? $datas['sub_category_id'] : '',
                'name'              => $product_name,
                'description'       => $product_desc,
                'url'               => $product_url,
                'image_url1'        => $product_image_url,
                'th082_url1'        => $product_thumb_url,
                'call_price'        => 1,
                'create_date'       => date("Y-m-d H:i:s"),
                'update_date'       => date("Y-m-d H:i:s")
             );

            $table = new Dao_Product();
            $product_id = $table->insert($product_data);
        } else {
            $product_id = $product['id'];
        }
    }
}

function import_category() {
    $config = Zend_Registry::get('config');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $html = crawl_url("http://sumberperkakas.indonetwork.co.id/");
    
    if( !$html ) {
        echo "failed to open page ($html)!";
        //exit("failed to open page ($html)!");
    }
    
    $html = str_replace("\r", "", $html);
    $html = str_replace("\n", "", $html);

    $matches = array();
    preg_match_all("/<li class=\"sddmenu\">((.|\n)*?)<ul class=\"ssgrup\">/", $html, $matches);
    //print_r($matches[0]);
    //echo "\n";
    foreach ($matches[0] as $item) {

        $domDocument = new DOMDocument();
        @$domDocument -> loadHTML($item);
        $xmlString = $domDocument -> saveXML();
        $xmlObject = simplexml_load_string($xmlString);
        //print_r($xmlObject);
        //echo "\n";
        $category_name = (string)$xmlObject->body->li->a;
        $url = (string)$xmlObject->body->li->a->attributes()->href;
        //echo $category_name;
        //echo "\n";
        
        $category = $db -> fetchRow("SELECT * FROM `dtb_category` WHERE `name` = ?", array($category_name));
        if ( !$category ) {
            $category_data = array(
                'name'          => $category_name,
                'url'           => $url,
                'create_date'   => date("Y-m-d H:i:s"),
                'update_date'   => date("Y-m-d H:i:s")
             );

            $table = new Dao_Category();
            $category_id = $table->insert($category_data);
        } else {
            $category_id = $category['id'];
            //echo "\ncategory udah ada\n";
        }
        
        $datas = array();
        //echo "url ($url) : ";
        if( preg_match("/ggroup/", $url) ) {
            //echo "mengandung ggroup";
            $html_category = crawl_url($url);
            if( !$html_category ) {
                echo "failed to open page ($url)!";
                //exit("failed to open page ($url)!");
            }
            
            $html_category = str_replace("\r", "", $html_category);
            $html_category = str_replace("\n", "", $html_category);

            $matches_category = array();
            preg_match_all("/<div class=\"gitems\">((.|\n)*?)<div class=\"box3\">/", $html_category, $matches_category);
            foreach( $matches_category[0] as $item_category ) {
                $domDocument2 = new DOMDocument();
                @$domDocument2 -> loadHTML($item_category);
                $xmlString2 = $domDocument2 -> saveXML();
                $xmlObject2 = simplexml_load_string($xmlString2);
                //print_r($xmlObject2);
                //echo "\n";
                $sub_category_name = (string)$xmlObject2->body->div->div[1]->a->img->attributes()->alt;
                $sub_category_image_url = (string)$xmlObject2->body->div->div[1]->a->img->attributes()->src;
                $sub_category_url = (string)$xmlObject2->body->div->div[1]->a->attributes()->href;
                
                $sub_category = $db -> fetchRow("SELECT * FROM `dtb_sub_category` WHERE `name` = ?", array($sub_category_name));
                if ( !$sub_category ) {
                    $sub_category_data = array(
                        'category_id'   => $category_id,
                        'name'          => $sub_category_name,
                        'url'           => $sub_category_url,
                        'image_url'     => $sub_category_image_url,
                        'create_date'   => date("Y-m-d H:i:s"),
                        'update_date'   => date("Y-m-d H:i:s")
                     );

                    $table = new Dao_SubCategory();
                    $sub_category_id = $table->insert($sub_category_data);
                } else {
                    $sub_category_id = $sub_category['id'];
                }
                
                for($i=0; $i<= 1000; $i--) {
                    if( $i == 0 ) {
                        $sub_category_url2 = $sub_category_url . "/change?perPageItem=48";
                    } else {
                        $sub_category_url2 = $sub_category_url . "/" . $i . ".html";
                    }
                    
                    $html_sub_category = crawl_url($sub_category_url2);
                    if( !$html_sub_category ) {
                        echo "failed to open page ($sub_category_url2)!";
                        //exit("failed to open page ($url)!");
                    }
                    
                    $html_sub_category = str_replace("\r", "", $html_sub_category);
                    $html_sub_category = str_replace("\n", "", $html_sub_category);

                    $datas = array(
                        'html'              => $html_sub_category,
                        'category_id'       => $category_id,
                        'sub_category_id'   => $sub_category_id,
                    );
                    import_product($datas);
                    $i += 49;
                }
            }
        } else {
            for($i=0; $i<= 1000; $i--) {
                if( $i == 0 ) {
                    $url2 = $url . "/change?perPageItem=48";
                } else {
                    $url2 = $url . "/" . $i . ".html";
                }
                
                $html_category = crawl_url($url2);
                if( !$html_category ) {
                    echo "failed to open page ($url2)!";
                    //exit("failed to open page ($url)!");
                }
                
                $html_category = str_replace("\r", "", $html_category);
                $html_category = str_replace("\n", "", $html_category);

                $datas = array(
                    'html'          => $html_category,
                    'category_id'   => $category_id,
                );
                import_product($datas);
                $i += 49;
            }
        }
        //echo "\n";
    }
}

function crawl_url($get_url) {
    echo "-- start crawling (" . $get_url . ") --\n";
    fopen("/home/shimizu/svn_backup/myvillage/trunk/home/sites/myvillage/batch/cookies.txt", "w");
    $url= $get_url;
    $ch = curl_init();

    $header=array('GET /1575051 HTTP/1.1',
        'Host: sumberperkakas.indonetwork.co.id',
        'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language:en-US,en;q=0.8',
        'Cache-Control:max-age=0',
        'Connection:keep-alive',
        'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36',
        );

    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
    curl_setopt( $ch, CURLOPT_COOKIESESSION, true);

    curl_setopt($ch,CURLOPT_COOKIEFILE,'/home/shimizu/svn_backup/myvillage/trunk/home/sites/myvillage/batch/cookies.txt');
    curl_setopt($ch,CURLOPT_COOKIEJAR,'/home/shimizu/svn_backup/myvillage/trunk/home/sites/myvillage/batch/cookies.txt');
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    $html=curl_exec($ch);
    curl_close($ch);

    echo "-- end crawling --\n";
    if($html) {
        //echo "ada HTML!\n";
        return $html;
    } else {
        return;
    }
}

function main() {
    initialize();
    import_category();
}

main();
