<?php
class Util_Image {
    public $image  = null;
    public $resize = false;
    public $width  = 0;
    public $height = 0;
    public $anigif = false;

    /**
     * イメージ生成
     */
    public function createImage($image_data) {
        $this->image = new Imagick();
        $this->image->readImageBlob($image_data);

        $mobile = Zend_Registry::get('mobile');
        if ( $mobile->career && $mobile->width && $mobile->height ) {
            $mw = $mobile->width;
            $mh = $mobile->height;
            $sw = $this->image->getImageWidth();
            $sh = $this->image->getImageHeight();
            $this->calcSize($mw, $mh, $sw, $sh);
        } else {
            $this->width  = $this->image->getImageWidth();
            $this->height = $this->image->getImageHeight();
        }
    }

    /**
     * JPEG -> PNG -> GIF の順にチェック
     */
    private function checkJPEG() {
        $mobile = Zend_Registry::get('mobile');
        if ($mobile->jpeg) {
            // 無変換
            header('Content-Type: image/jpeg');
        }
        elseif ($mobile->png) {
            header('Content-Type: image/png');
            $this->image->setImageFormat('png');
        }
        elseif ($mobile->gif) {
            header('Content-Type: image/gif');
            $this->image->setImageFormat('gif');
        }
    }

    /**
     * PNG -> JPEG -> GIF の順にチェック
     */
    private function checkPNG() {
        $mobile = Zend_Registry::get('mobile');
        if ($mobile->png) {
            // 無変換
            header('Content-Type: image/png');
        }
        elseif ($mobile->jpeg) {
            header('Content-Type: image/jpeg');
            $this->image->setImageFormat('jpg');
        }
        elseif ($mobile->gif) {
            header('Content-Type: image/gif');
            $this->image->setImageFormat('gif');
        }
    }

    /**
     * GIF -> PNG -> JPEG の順にチェック
     */
    private function checkGIF() {
        $mobile = Zend_Registry::get('mobile');
        if ($mobile->gif) {
            // 無変換
            header('Content-Type: image/gif');

            // アニメーションGIF対応
            if ( $mobile->gif && $this->image->getNumberImages() > 1 && !$this->resize ) {
                $this->anigif = true;
            }
        }
        elseif ($mobile->png) {
            header('Content-Type: image/png');
            $this->image->setImageFormat('png');
        }
        elseif ($mobile->jpeg) {
            header('Content-Type: image/jpeg');
            $this->image->setImageFormat('jpg');
        }
    }

    /**
     * イメージ書き出し
     */
    public function render($copy_disallow = true) {
        $mobile = Zend_Registry::get('mobile');
        if ( $mobile->career && $mobile->width && $mobile->height ) {
            // 画像種別設定
            if ($this->image->getImageFormat() === 'JPEG') {
                $this->checkJPEG();
            }
            elseif ($this->image->getImageFormat() === 'PNG') {
                $this->checkPNG();
            }
            elseif ($this->image->getImageFormat() === 'GIF') {
                $this->checkGIF();
            }
            // リサイズ
            if ($this->resize) {
                $this->image->scaleImage($this->width, $this->height);
                //$this->image->resizeImage($this->width, $this->height, Imagick::FILTER_LANCZOS, 1);
            }
            // コピー禁止フラグ
            /*
            if ($copy_disallow) {
                if ($mobile->career === Util_Mobile::$DOCOMO) {
                    $this->image->commentImage('copy="NO"');
                } elseif ($mobile->career === Util_Mobile::$AU) {
                    $this->image->commentImage('kddi_copyright=on');
                } elseif ($mobile->career === Util_Mobile::$SOFTBANK) {
                    header('x-jphone-copyright: no-transfer,no-peripheral');
                }
            }
            */
            // ソフトバンクではこれをつけないとファイルを保存できない
            if ($mobile->career === Util_Mobile::$SOFTBANK) {
                header('Cache-Control: private');
            }
            // 画像書き出し
            $data = $this->image->getImagesBlob();
            header('Content-Length: ' . strlen($data));
            echo $data;
        }
        else {
            // 画像ヘッダ設定
            if ($this->image->getImageFormat() === 'JPEG') {
                header('Content-Type: image/jpeg');
            } elseif ($this->image->getImageFormat() === 'PNG') {
                header('Content-Type: image/png');
            } elseif ($this->image->getImageFormat() === 'GIF') {
                header('Content-Type: image/gif');
            }
            // 画像書き出し
            $data = $this->image->getImagesBlob();
            header('Content-Length: ' . strlen($data));
            echo $data;
        }
        
    }

    /**
     * サイズ調整
     */
    private function calcSize($mw, $mh, $sw, $sh) {
        $result = array();
        // リサイズなし
        if ($mw > $sw && $mh > $sh) {
            $this->width  = $sw;
            $this->height = $sh;
            $this->resize = false;
        }
        // 幅縮小
        elseif ( ( $mw / $sw ) > ( $mh / $sh ) ) {
            $this->width  = (int) ( $sw * $mh / $sh );
            $this->height = $mh;
            $this->resize = true;
        }
        // 高さ縮小
        else {
            $this->height = (int) ( $sh * $mw / $sw );
            $this->width  = $mw;
            $this->resize = true;
        }
    }
}
