<?php
/**
 * images
 */
class Logic_Images extends Logic_Base {
    public function getImageForm($form, $folder) {
		$fileDestination = realpath(APPLICATION_PATH . '/upload/' . $folder);
		
		$elem1 = new Zend_Form_Element_File("image_url1");
		$elem1->setDestination($fileDestination);
		$elem1->addValidator('Count', false, 3);
		$elem1->addValidator('Size', false, array(1048576 * 1000));
		$elem1->addValidator('Extension', false, 'jpg,png,gif');
		$elem1->setRequired(false);
		$form->addElement($elem1);
		
		if( $folder == 'product' ) {
			$elem2 = new Zend_Form_Element_File("image_url2");
			$elem2->setDestination($fileDestination);
			$elem2->addValidator('Count', false, 3);
			$elem2->addValidator('Size', false, array(1048576 * 1000));
			$elem2->addValidator('Extension', false, 'jpg,png,gif');
			$elem2->setRequired(false);
			$form->addElement($elem2);
			
			$elem3 = new Zend_Form_Element_File("image_url3");
			$elem3->setDestination($fileDestination);
			$elem3->addValidator('Count', false, 3);
			$elem3->addValidator('Size', false, array(1048576 * 1000));
			$elem3->addValidator('Extension', false, 'jpg,png,gif');
			$elem3->setRequired(false);
			$form->addElement($elem3);
		}
		
        return $form;
	}
	
    public function doUpload($form, $key, $folder) {
		if ($form->$key->isUploaded()) {
			$originalFilename = pathinfo($form->$key->getFileName());
			
			$image_name = strtolower($originalFilename['filename'] . '_'. rand(1,100) . time() . '.' . $originalFilename['extension']);
			$thumb_name = 't_' . $image_name;
			
			$form->$key->addFilter('Rename', $image_name);
			$form->$key->addFilter(new Skoch_Filter_File_Resize(array(
						'width' => 1024,
						'height' => 800,
						'keepRatio' => true,
					)));
		} else {
			return;
		}
		
		$data = $form->getValue($key);

		$temp_path = APPLICATION_PATH . "/" . $image_name;
		$image_path = $originalFilename['dirname'] . "/" . $image_name;
		$thumb_path = $originalFilename['dirname'] . "/thumb/" . $thumb_name; 
		$image_url =  '/upload/' . $folder . "/" . $image_name;
		$thumb_url = '/upload/' . $folder . "/thumb/" . $thumb_name; 
		
		if( !copy($temp_path, $image_path) ) {
			return;
		}
		
		if( !copy($image_path, $thumb_path) ) {
			return;
		}
		
		$resize = new Skoch_Filter_File_Resize(array(
						'width' => 300,
						'height' => 200,
						'keepRatio' => true,
					));
		$resize->filter($thumb_path);
		
		$image = array('image_url' => $image_url, 'thumb_url' => $thumb_url);
		return $image;
	}
	
    public function doUploadProduct($form, $key, $folder) {
		if ($form->$key->isUploaded()) {
			$originalFilename = pathinfo($form->$key->getFileName());
			
			$image_name = strtolower($originalFilename['filename'] . '_'. rand(1,100) . time() . '.' . $originalFilename['extension']);
			$th082_name = 'th082_' . $image_name;
			$th155_name = 'th155_' . $image_name;
			$th270_name = 'th270_' . $image_name;
			
			$form->$key->addFilter('Rename', $image_name);
			$form->$key->addFilter(new Skoch_Filter_File_Resize(array(
						'width' => 800,
						'height' => 800,
						'keepRatio' => false,
					)));
		} else {
			return;
		}
		
		$data = $form->getValue($key);

		$temp_path = APPLICATION_PATH . "/" . $image_name;
		$image_path = $originalFilename['dirname'] . "/" . $image_name;
		$th082_path = $originalFilename['dirname'] . "/th082/" . $th082_name;
		$th155_path = $originalFilename['dirname'] . "/th155/" . $th155_name;
		$th270_path = $originalFilename['dirname'] . "/th270/" . $th270_name;
		$image_url =  '/upload/' . $folder . "/" . $image_name;
		$th082_url = '/upload/' . $folder . "/th082/" . $th082_name;
		$th155_url = '/upload/' . $folder . "/th155/" . $th155_name;
		$th270_url = '/upload/' . $folder . "/th270/" . $th270_name;
		
		if( !copy($temp_path, $image_path) ) {
			return;
		}
		
		if( !copy($image_path, $th082_path) ) {
			return;
		}
		
		if( !copy($image_path, $th155_path) ) {
			return;
		}
		
		if( !copy($image_path, $th270_path) ) {
			return;
		}

		//added :
		$resize082 = new Skoch_Filter_File_Resize(array(
						'width' => 82,
						'height' => 82,
						'keepRatio' => false,
					));
		$resize082->filter($th082_path);
		
		$resize155 = new Skoch_Filter_File_Resize(array(
						'width' => 155,
						'height' => 155,
						'keepRatio' => false,
					));
		$resize155->filter($th155_path);
		
		$resize270 = new Skoch_Filter_File_Resize(array(
						'width' => 270,
						'height' => 270,
						'keepRatio' => false,
					));
		$resize270->filter($th270_path);
		
		$image = array('image_url' => $image_url, 'th082_url' => $th082_url, 'th155_url' => $th155_url, 'th270_url' => $th270_url);
		return $image;
	}
}