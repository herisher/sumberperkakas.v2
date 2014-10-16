<?php
class Util_Image {
    public $image  = null;
    public $resize = false;
    public $width  = 0;
    public $height = 0;
    public $anigif = false;

    /**
     * �C���[�W����
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
     * JPEG -> PNG -> GIF �̏��Ƀ`�F�b�N
     */
    private function checkJPEG() {
        $mobile = Zend_Registry::get('mobile');
        if ($mobile->jpeg) {
            // ���ϊ�
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
     * PNG -> JPEG -> GIF �̏��Ƀ`�F�b�N
     */
    private function checkPNG() {
        $mobile = Zend_Registry::get('mobile');
        if ($mobile->png) {
            // ���ϊ�
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
     * GIF -> PNG -> JPEG �̏��Ƀ`�F�b�N
     */
    private function checkGIF() {
        $mobile = Zend_Registry::get('mobile');
        if ($mobile->gif) {
            // ���ϊ�
            header('Content-Type: image/gif');

            // �A�j���[�V����GIF�Ή�
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
     * �C���[�W�����o��
     */
    public function render($copy_disallow = true) {
        $mobile = Zend_Registry::get('mobile');
        if ( $mobile->career && $mobile->width && $mobile->height ) {
            // �摜��ʐݒ�
            if ($this->image->getImageFormat() === 'JPEG') {
                $this->checkJPEG();
            }
            elseif ($this->image->getImageFormat() === 'PNG') {
                $this->checkPNG();
            }
            elseif ($this->image->getImageFormat() === 'GIF') {
                $this->checkGIF();
            }
            // ���T�C�Y
            if ($this->resize) {
                $this->image->scaleImage($this->width, $this->height);
                //$this->image->resizeImage($this->width, $this->height, Imagick::FILTER_LANCZOS, 1);
            }
            // �R�s�[�֎~�t���O
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
            // �\�t�g�o���N�ł͂�������Ȃ��ƃt�@�C����ۑ��ł��Ȃ�
            if ($mobile->career === Util_Mobile::$SOFTBANK) {
                header('Cache-Control: private');
            }
            // �摜�����o��
            $data = $this->image->getImagesBlob();
            header('Content-Length: ' . strlen($data));
            echo $data;
        }
        else {
            // �摜�w�b�_�ݒ�
            if ($this->image->getImageFormat() === 'JPEG') {
                header('Content-Type: image/jpeg');
            } elseif ($this->image->getImageFormat() === 'PNG') {
                header('Content-Type: image/png');
            } elseif ($this->image->getImageFormat() === 'GIF') {
                header('Content-Type: image/gif');
            }
            // �摜�����o��
            $data = $this->image->getImagesBlob();
            header('Content-Length: ' . strlen($data));
            echo $data;
        }
        
    }

    /**
     * �T�C�Y����
     */
    private function calcSize($mw, $mh, $sw, $sh) {
        $result = array();
        // ���T�C�Y�Ȃ�
        if ($mw > $sw && $mh > $sh) {
            $this->width  = $sw;
            $this->height = $sh;
            $this->resize = false;
        }
        // ���k��
        elseif ( ( $mw / $sw ) > ( $mh / $sh ) ) {
            $this->width  = (int) ( $sw * $mh / $sh );
            $this->height = $mh;
            $this->resize = true;
        }
        // �����k��
        else {
            $this->height = (int) ( $sh * $mw / $sw );
            $this->width  = $mw;
            $this->resize = true;
        }
    }
}
