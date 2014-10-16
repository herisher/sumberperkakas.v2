<?php
/**
 * 画像処理
 */
class Logic_Image extends Logic_Base {
    /**
     * 商品用ファイルアップロード（画像にあわせて余白をつける）
     * @args $key 画像ファイル名
     * @args $sw  画像幅
     * @args $sh  画像高さ
     */
    public function doUploadProduct($key, $sw = 0, $sh = 0, $folder = 'temp') {
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

                $file_orig = $uniqid . '.' . $format;
                $path_orig = APPLICATION_PATH . "/upload/market/product/" . $uniqid . '.' . $format;
                $path_t082 = APPLICATION_PATH . "/upload/market/product/th082/" . $uniqid . '.' . $format;
                $path_t155 = APPLICATION_PATH . "/upload/market/product/th155/" . $uniqid . '.' . $format;
                $path_t270 = APPLICATION_PATH . "/upload/market/product/th270/" . $uniqid . '.' . $format;
                $url_orig  = "/upload/market/product/" . $uniqid . '.' . $format;
                $url_t082  = "/upload/market/product/th082/" . $uniqid . '.' . $format;
                $url_t155  = "/upload/market/product/th155/" . $uniqid . '.' . $format;
                $url_t270  = "/upload/market/product/th270/" . $uniqid . '.' . $format;
                
                // 元ファイル書き込み
                $fh = fopen($path_orig, 'wb');
                fwrite($fh, $image->getImagesBlob());
                fclose($fh);
                
                $sw = 82;
                $sh = 82;
                
                // リサイズなし
                if ($sw == $iw && $sh == $ih) {
                    // サムネイルファイル書き込み
                    $fh = fopen($path_t082, 'wb');
                    fwrite($fh, $image->getImagesBlob());
                    fclose($fh);
                }
                // 高さ縮小
                elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                    $image->scaleImage($ww, $sh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, new ImagickPixel('white'));
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_t082, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                // 幅縮小
                else {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                    $image->scaleImage($sw, $hh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, new ImagickPixel('white'));
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_t082, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                
                $sw = 155;
                $sh = 155;
                
                // リサイズなし
                if ($sw == $iw && $sh == $ih) {
                    // サムネイルファイル書き込み
                    $fh = fopen($path_t155, 'wb');
                    fwrite($fh, $image->getImagesBlob());
                    fclose($fh);
                }
                // 高さ縮小
                elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                    $image->scaleImage($ww, $sh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, new ImagickPixel('white'));
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_t155, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                // 幅縮小
                else {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                    $image->scaleImage($sw, $hh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, new ImagickPixel('white'));
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_t155, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                
                $sw = 270;
                $sh = 270;
                
                // リサイズなし
                if ($sw == $iw && $sh == $ih) {
                    // サムネイルファイル書き込み
                    $fh = fopen($path_t270, 'wb');
                    fwrite($fh, $image->getImagesBlob());
                    fclose($fh);
                }
                // 高さ縮小
                elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                    $image->scaleImage($ww, $sh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, new ImagickPixel('white'));
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_t270, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                // 幅縮小
                else {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                    $image->scaleImage($sw, $hh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, new ImagickPixel('white'));
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_t270, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                
                return array(
                    'image_file' => $file_orig,
                    'image_path' => $path_orig,
                    'th082_path' => $path_t082,
                    'th155_path' => $path_t155,
                    'th270_path' => $path_t270,
                    'image_url'  => $url_orig,
                    'th082_url'  => $url_t082,
                    'th155_url'  => $url_t155,
                    'th270_url'  => $url_t270,
                );
            } catch(Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * レシピ用ファイルアップロード（画像にあわせて余白をつける）
     * @args $key 画像ファイル名
     * @args $sw  画像幅
     * @args $sh  画像高さ
     */
    public function doUploadRecipe($key, $sw = 0, $sh = 0, $folder = 'temp') {
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
                $path_n = APPLICATION_PATH . "/upload/recipe/" . $uniqid . '.' . $format;
                $path_t = APPLICATION_PATH . "/upload/recipe/thumbnail/" . $uniqid . '.' . $format;
                $url_n  = "/upload/recipe/" . $uniqid . '.' . $format;
                $url_t  = "/upload/recipe/thumbnail/" . $uniqid . '.' . $format;
                
                $sw = 420;
                $sh = 315;
                
                // リサイズなし
                if ($sw == $iw && $sh == $ih) {
                    // サムネイルファイル書き込み
                    $fh = fopen($path_n, 'wb');
                    fwrite($fh, $image->getImagesBlob());
                    fclose($fh);
                }
                // 高さ縮小
                elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                    $image->scaleImage($ww, $sh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, new ImagickPixel('white'));
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_n, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                // 幅縮小
                else {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                    $image->scaleImage($sw, $hh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, new ImagickPixel('white'));
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
                    $im2->setImageFormat($format);

                    // サムネイルファイル書き込み
                    $fh = fopen($path_n, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                
                $sw = 150;
                $sh = 113;
                
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

                    $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
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

                    $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
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
}
