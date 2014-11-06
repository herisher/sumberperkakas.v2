<?php
/**
 * Base
 */
class BaseController extends Zend_Controller_Action {
    /**
     * redirect
     * @override
     */
    protected function _redirect($url, array $options = array()) {
        // commit
        $this->db()->commit();

        return parent::_redirect($url, $options);
    }

    /**
     * return database adapter
     */
    public function db() {
        return Zend_Db_Table_Abstract::getDefaultAdapter();
    }

    /**
     * return an instance of the model
     */
    public function model($modelname, $option = array()) {
        $table = new $modelname($option);
        return $table;
    }

    /**
     * generate navigator
     */
    public function createNavigator($datas, $limit = 10) {
        // session to store the current page number
        $module_name = $this->getRequest()->getModuleName();
        $class_name  = $this->getRequest()->getControllerName();
        $action_name  = $this->getRequest()->getActionName();
        $list_path   = '/' . $module_name . '/' . $class_name . '/' . $action_name;
        $session     = new Zend_Session_Namespace($list_path);
        
        $paginator = null;
        if ( get_class($datas) === 'Zend_Db_Table_Select' ) {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_DbTableSelect( $datas )
            );
        } elseif ( get_class($datas) === 'Zend_Db_Select' ) {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_DbSelect( $datas )
            );
        } else {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_Array( $datas )
            );
        }
        
        // setting of the range to be displayed
        $paginator->setPageRange(5);
        
        // setting the limit
        if ($this->getRequest()->getParam('limit')) {
            $limit = $this->getRequest()->getParam('limit');
        }
        $session->limit = $limit;
        
        // paging
        $paginator->setItemCountPerPage($limit);
        if ( $paginator->count() && $this->getRequest()->getParam('page') ) {
            $page = $this->getRequest()->getParam('page');
            $session->page = $page;
            $paginator->setCurrentPageNumber($page);
            if ( $page === '1' ) {
                $this->view->assign('first_page', true);
            } else {
                $this->view->assign('first_page', false);
            }
        } else {
            $session->page = 1;
            $this->view->assign('first_page', true);
        }
        
        $this->view->assign('pages', $paginator->getPages());
        $this->view->assign('paginator', $paginator);
    }

    /**
     * check login
     */
    public function checkLogin() {
        // manager site
        if ($this->getRequest()->getModuleName() == 'manager') {
            $session = new Zend_Session_Namespace('admin');
            if (!$session->id) {
                $this->_redirect('/manager/login?return_path=' . urlencode($_SERVER['REDIRECT_URL']));
            } else {
                $model = $this->model('Dao_Admin')->retrieve($session->id);
                if (!$model) {
                    $this->_redirect('/manager/login?return_path=' . urlencode($_SERVER['REDIRECT_URL']));
                }
            }
        }
        // consumer site
        else {
            $session = new Zend_Session_Namespace('login_member');
            if (!$session->id) {
                $this->_redirect('/login?return_path=' . urlencode($_SERVER['REDIRECT_URL']));
            } else {
                $model = $this->model('Dao_Member')->retrieve($session->id);
                if (!$model) {
                    $this->_redirect('/login?return_path=' . urlencode($_SERVER['REDIRECT_URL']));
                }
            }
        }
    }

    /**
     * generate login information
     */
    public function createLoginInfo() {
        // manager site
        if ($this->getRequest()->getModuleName() == 'manager') {
            $session = new Zend_Session_Namespace('admin');
            if ($session->id) {
                $model = $this->model('Dao_Admin')->retrieve($session->id);
                if ($model) {
                    $this->view->admin = $model->toArray();
                }
//              error_log("admin_id = ".$this->view->admin['id']);
            }
        }
        // consumer site
        else {
            $session = new Zend_Session_Namespace('login_member');
            if ($session->id) {
                $model = $this->model('Dao_Member')->retrieve($session->id);
                if ($model) {
                    $this->view->login_member = $model->toArray();
                }
                error_log("member_id = ".$this->view->login_member['id']);
            }
        }
    }

    /**
     * generate form automatically
     */
    public function createForm() {
        $module_name = $this->getRequest()->getModuleName();
        $class_name = $this->getRequest()->getControllerName();
        $func_name = $this->getRequest()->getActionName();
        $config_path = APPLICATION_PATH . '/../application/' . $module_name . '/configs/' . $class_name . '.ini';
        if ( file_exists($config_path) ) {
            $config = new Zend_Config_Ini($config_path, 'form');
            $form = new Zend_Form($config->$class_name->$func_name);
            $this->view->config = $config->$class_name->$func_name;
            $this->view->form = $form;
        }
    }

    /**
     * generate error check form
     */
    public function checkForm($form, $config, &$error_str) {
        foreach ( $config->elements as $key => $element ) {
            if ( isset($element->errors) ) {
                if ( $form->getElement($key)->getErrors() ) {
                    foreach ( $form->getElement($key)->getErrors() as $error ) {
                        if ( isset($element->errors->$error) ) {
                            $message = $element->errors->$error;
                        } else {
                            $message = $element->errors->other;
                        }
                        if ( isset($element->errors->place) ) {
                            $error_str[$element->errors->place] = $message;
                        } else {
                            $error_str[$key] = $message;
                        }
                    }
                }
            }
        }
    }

    /**
     * get the page number of the list
     */
    public function createLastPage() {
        $module_name = $this->getRequest()->getModuleName();
        $class_name  = $this->getRequest()->getControllerName();
        $action_name  = $this->getRequest()->getActionName();
        $this->view->module_name = $module_name;
        $this->view->class_name  = $class_name;
        $this->view->action_name  = $action_name;
    }

    /**
     * back to list
     */
    public function gobackList() {
        $module_name = $this->getRequest()->getModuleName();
        $class_name  = $this->getRequest()->getControllerName();
        $list_path   = '/' . $module_name . '/' . $class_name . '/list';
        $session = new Zend_Session_Namespace($list_path);
        $this->_redirect($list_path . '/page/' . $session->page . '/limit/' . $session->limit);
    }

    /**
     * back to list action
     */
    public function gobackListAction() {
        $this->gobackList();
    }

    /**
     * ファイルアップロード（枠にあわせて画像をトリミング）
     * @args $key 画像ファイル名
     * @args $sw  画像幅
     * @args $sh  画像高さ
     */
    public function doUpload($key, $sw = 0, $sh = 0, $folder = 'temp') {
        // パーミッションを666にする
        umask(0111);

        if ( array_key_exists($key, $_FILES) && $_FILES[$key]['size'] ) {
            try {
                // イメージ読み込み
                $image = new Imagick();
                $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));

                $iw = $image->getImageWidth();
                $ih = $image->getImageHeight();

                $uniqid = uniqid('',true);

                if ($image->getImageFormat() == 'JPEG') {
                    $format = 'jpg';
                } elseif($image->getImageFormat() == 'PNG'){
                    $format = 'png';
                } elseif($image->getImageFormat() == 'GIF'){
                    $format = 'gif';
                } else {
                    return null;
                }

                $file_n = $uniqid . '.' . $format;
                $path_n = APPLICATION_PATH . "/upload/$folder/" . $uniqid . '.' . $format;
                $path_t = APPLICATION_PATH . "/upload/$folder/thumbnail/" . $uniqid . '.' . $format;
                $url_n  = "/upload/$folder/" . $uniqid . '.' . $format;
                $url_t  = "/upload/$folder/thumbnail/" . $uniqid . '.' . $format;
                
                // 元ファイル書き込み
                $fh = fopen($path_n, 'wb');
                fwrite($fh, $image->getImagesBlob());
                fclose($fh);
                
                // リサイズなし
                if ($sw == $iw && $sh == $ih) {
                    // サムネイルファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $image->getImagesBlob());
                    fclose($fh);
                }
                // 高さ縮小
                elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->scaleImage($sw, $hh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                // 幅縮小
                else {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->scaleImage($ww, $sh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                
                return array(
                    'image_file' => $file_n,
                    'image_path' => $path_n,
                    'thumb_path' => $path_t,
                    'image_url'  => $url_n,
                    'thumb_url'  => $url_t,
                );
            } catch(Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * 画像アップロード（高さだけを基準にしてサムネイル作成）
     * @args $key 画像ファイル名
     * @args $sw  画像幅
     * @args $sh  画像高さ
     */
    public function doUpload2($key, $sw = 0, $sh = 0, $folder = 'temp') {
        // パーミッションを666にする
        umask(0111);

        if (array_key_exists($key, $_FILES) && $_FILES[$key]['size']) {
            try {
                // イメージ読み込み
                $image = new Imagick();
                $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                $iw = $image->getImageWidth();
                $ih = $image->getImageHeight();
                
                $uniqid = uniqid('',true);

                if ($image->getImageFormat() == 'JPEG') {
                    $format = 'jpg';
                } elseif($image->getImageFormat() == 'PNG'){
                    $format = 'png';
                } elseif($image->getImageFormat() == 'GIF'){
                    $format = 'gif';
                } else {
                    return null;
                }

                // ファイル名生成
                $file_n = $uniqid . '.' . $format;
                $path_n = APPLICATION_PATH . "/upload/$folder/" . $uniqid . '.' . $format;
                $url_n  = "/upload/$folder/" . $uniqid . '.' . $format;

                // 拡大縮小
                if ($sw < $iw) {
                    // 高さ縮小
                    $image->scaleImage($sw, (int) ( $ih * $sw / $iw ));
                }

                // ファイル書き込み
                $fh = fopen($path_n, 'wb');
                fwrite($fh, $image->getImagesBlob());
                fclose($fh);
                
                return array(
                    'image_file' => $file_n,
                    'image_path' => $path_n,
                    'image_url'  => $url_n,
                );
            } catch(Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * ファイルアップロード（画像にあわせて余白をつける）
     * @args $key 画像ファイル名
     * @args $sw  画像幅
     * @args $sh  画像高さ
     */
    public function doUpload3($key, $sw = 0, $sh = 0, $folder = 'temp') {
        // パーミッションを666にする
        umask(0111);

        if ( array_key_exists($key, $_FILES) && $_FILES[$key]['size'] ) {
            try {
                // イメージ読み込み
                $image = new Imagick();
                $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));

                $iw = $image->getImageWidth();
                $ih = $image->getImageHeight();

                $uniqid = uniqid('',true);

                if ($image->getImageFormat() == 'JPEG') {
                    $format = 'jpg';
                } elseif($image->getImageFormat() == 'PNG'){
                    $format = 'png';
                } elseif($image->getImageFormat() == 'GIF'){
                    $format = 'gif';
                } else {
                    return null;
                }

                $file_n = $uniqid . '.' . $format;
                $path_n = APPLICATION_PATH . "/upload/$folder/" . $uniqid . '.' . $format;
                $path_t = APPLICATION_PATH . "/upload/$folder/thumbnail/" . $uniqid . '.' . $format;
                $url_n  = "/upload/$folder/" . $uniqid . '.' . $format;
                $url_t  = "/upload/$folder/thumbnail/" . $uniqid . '.' . $format;
                
                // 元ファイル書き込み
                $fh = fopen($path_n, 'wb');
                fwrite($fh, $image->getImagesBlob());
                fclose($fh);
                
                // リサイズなし
                if ($sw == $iw && $sh == $ih) {
                    // サムネイルファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $image->getImagesBlob());
                    fclose($fh);
                }
                // 高さ縮小
                elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->scaleImage($ww, $sh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, new ImagickPixel('white'));
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                // 幅縮小
                else {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->scaleImage($sw, $hh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, new ImagickPixel('white'));
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                
                return array(
                    'image_file' => $file_n,
                    'image_path' => $path_n,
                    'thumb_path' => $path_t,
                    'image_url'  => $url_n,
                    'thumb_url'  => $url_t,
                );
            } catch(Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * check whether maintenance mode
     */
    public function maintenanceMode() {
        // Reading of existing points
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . "/../application/configs/recipe.ini", null, array('allowModifications' => false)
        );
        
        $start_date = $config->setting->maintenance->start_date;
        $end_date   = $config->setting->maintenance->end_date;
        
        if (!$start_date || !$end_date) {
            return false;
        }
        
        if (strtotime($start_date) <= time() && time() <= strtotime($end_date)) {
            $this->view->start_date = $start_date;
            $this->view->end_date   = $end_date;
            
            return true;
        } else {
            return false;
        }
    }

    /**
     * called just before the action is performed
     */
    public function preDispatch() {
        // change to html extension of view
        $this->_helper->viewRenderer->setViewSuffix('html');

        // read config file
        $config = Zend_Registry::get('config');
        $this->view->app = $config->app;

        // login check (manager site)
        if ($this->getRequest()->getModuleName() == 'manager' || $this->getRequest()->getModuleName() == 'marketmanager') {
            $this->checkLogin();
        }
		
        $controller_name = $this->getRequest()->getControllerName();
		if( $controller_name != 'product' ) {
			if( isset($_SESSION['keyword']) ) {
				$_SESSION['keyword'] = null;
			}
			$session = new Zend_Session_Namespace('/product/list');
			if (isset($session)) {
				Zend_Session::namespaceUnset('/product/list');
			}
        }
		
/*
        // BASIC auth
        if ($this->view->app->basic_auth || $this->getRequest()->getModuleName() == 'market') {
            if (!isset($_SERVER["PHP_AUTH_USER"])) {
                header("WWW-Authenticate: Basic realm=\"Please Enter Your Password\"");
                header("HTTP/1.0 401 Unauthorized");
                //display of cancellation
                echo "Authorization Required";
                exit;
            }
            else {
                if(!($_SERVER["PHP_AUTH_USER"] == 'buzoo' && $_SERVER["PHP_AUTH_PW"] == 'test')){
                    header("WWW-Authenticate: Basic realm=\"Please Enter Your Password\"");
                    header("HTTP/1.0 401 Unauthorized");
                    //display of cancellation
                    echo "Authorization Required";
                    exit;
                }
            }
        }
*/
        // 9/17の時点では消費者サイト見せない
        /*
        if (!$this->view->app->debug && $this->getRequest()->getModuleName() == 'default') {
            $class_name  = $this->getRequest()->getControllerName();
            $func_name   = $this->getRequest()->getActionName();
            
            if ($class_name != 'Error' && $func_name != 'preview') {
                //header('HTTP/1.1 404 Not Found');
                echo '<!DOCTYPE html><html lang="ja"><head><meta charset="UTF-8"></head><body>titian 正式オープンまであと僅か!!</body></html>';
                exit;
            }
        }
        */

        // 栄養士サイトを一時的にメンテナンス画面にする
        /*
        $ip = $_SERVER['REMOTE_ADDR'];
        if ($this->getRequest()->getModuleName() == 'dietitian') {
            if ($ip != '122.216.23.236' && //BUZOO JAPAN
                $ip != '202.152.30.98'  && //PT.BUZOO
                $ip != '202.152.30.110' && //PT.BUZOO
                $ip != '153.137.39.219' && //YMS
                $ip != '218.219.209.229'   //Ring
               )
            {
                $this->_redirect("/maintenance/dietitian.html");
                return;
            }
        }
        */

        // generate login information
        $this->createLoginInfo();

        // generate form automatically
        $this->createForm();

        // get the page number of the list
        $this->createLastPage();
    }

    /**
     * called immediately after the action is executed
     */
    public function postDispatch() {
        if ($this->_helper->layout->isEnabled()) {
        }
		
		//restore brand session
		$session = new Zend_Session_Namespace('/product/list');
		if (isset($session->brand_id)) {
			$this->view->brand_id = $session->brand_id;
		}
    }
}
