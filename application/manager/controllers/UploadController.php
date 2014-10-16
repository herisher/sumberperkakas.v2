<?php

class Manager_UploadController extends BaseController {
    public function indexAction()
    {
        $form = $this->view->form;
		
		$fileDestination = realpath(APPLICATION_PATH.'/upload/product/');
		$elem = new Zend_Form_Element_File("image_url");
		$elem->setDestination($fileDestination);
		$elem->addValidator('Count', false, 1);
		$elem->addValidator('Size', false, array(1048576 * 1000));
		$elem->addValidator('Extension', false, 'jpg,png,gif');
		$elem->setRequired(false);
		$form->addElement($elem);
		
        if ( $this->getRequest()->isPost() ) {
            if ( $form->isValid($_POST) ) {
				if ($form->image_url->isUploaded()) {
					$originalFilename = pathinfo($form->image_url->getFileName());
					
					echo '<br/>';
					foreach ($originalFilename as $key => $value)
					{
						echo $key . ' ---- ';
						echo $value . '<br/>';
						
					}
					
					$newName = strtolower($originalFilename['filename'] . '_'. rand(1,100) . time() . '.' . $originalFilename['extension']);
					$form->image_url->addFilter('Rename', $newName);
					$form->image_url->addFilter(new Skoch_Filter_File_Resize(array(
								'width' => 1024,
								'height' => 800,
								'keepRatio' => true,
							)));
				}
				
				$data = $form->getValues();

				$file = /*$originalFilename['dirname']*/ APPLICATION_PATH . "\\" . $newName;
				$newFile = /*$originalFilename['dirname']*/ APPLICATION_PATH . "\\" . 't_' . $newName;
				print_r($file."+++++".$newFile);
				if( !copy($file, $newFile) ) {
					print_r("ZZZZZZZZZZZZZZZZZZZZZZZZZZZZ~");
				}
				
				$resize = new Skoch_Filter_File_Resize(array(
								'width' => 200,
								'height' => 300,
								'keepRatio' => true,
							));
				$resize->filter(/*$originalFilename['dirname']*/ APPLICATION_PATH . '/' . 't_' . $newName);
				echo '<br/>';
				foreach ($data as $key => $value)
				{
					echo $key . ' ---- ';
					echo $value . '<br/>';
					
				}
            }
        }
    }
}
