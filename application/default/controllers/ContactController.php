<?php
/**
 * contact
 */
class ContactController extends BaseController {
    const NS_CHANGE = "/contact";
    
    /**
     * form validation
     */
    private function formValid($form) {
        $error_str = array();

        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
        }
        
        if (count($error_str)) {
            $this->view->error_str = $error_str;
            return false;
        }

        return true;
    }

    /**
     * form
     */
    public function formAction() {
        $form = $this->view->form;

        if ( $this->getRequest()->isPost() ) {
            if ( $this->formValid($form) ) {
                $this->doCreate($form);
                $this->_redirect('/contact/comp');
            }
        }
    }
    
    public function doCreate($form) {
        // insert
        $model_id = $this->model('Dao_Contact')->insert(array(
            'fullname'          => $form->getValue('fullname'),
            'email'             => $form->getValue('email'),
            'phone'             => $form->getValue('phone'),
            'title'             => $form->getValue('title'),
            'content'           => $form->getValue('content'),
            'create_date'       => new Zend_Db_Expr('now()'),
            'update_date'       => new Zend_Db_Expr('now()'),
        ));
        $session = new Zend_Session_Namespace(self::NS_CHANGE);
        $session->id = $model_id;
    }
    
    /**
     * complete
     */
    public function compAction() {
        // check session
        $session = new Zend_Session_Namespace(self::NS_CHANGE);
        if (!$session->id) {
            $this->view->error_str = 'Ini adalah operasi ilegal.';
            $this->_forward('error', 'Error');
            return;
        }

        /*
        // 問い合わせ者にメール送信
        $this->model('Dao_Mailtemplate')->doSend(10, $session->email, array(
            '%USERNAME%'    => $session->name1.' '.$session->name2,
            '%NAME1%'       => $session->name1,
            '%NAME2%'       => $session->name2,
            '%KANA1%'       => $session->name1_kana,
            '%KANA2%'       => $session->name2_kana,
            '%EMAIL%'       => $session->email,
            '%TITLE%'       => $title,
            '%CONTENT%'     => $session->content,
        ));

        // 管理者にメール送信
        $send_to = 'info@titian.jp';
        $this->model('Dao_Mailtemplate')->doSend(11, $send_to, array(
            '%USERNAME%'    => $session->name1.' '.$session->name2,
            '%NAME1%'       => $session->name1,
            '%NAME2%'       => $session->name2,
            '%KANA1%'       => $session->name1_kana,
            '%KANA2%'       => $session->name2_kana,
            '%USERID%'      => $user,
            '%PERSON_TYPE%' => $person_type,
            '%EMAIL%'       => $session->email,
            '%TITLE%'       => $title,
            '%CONTENT%'     => $session->content,
        ));*/

        Zend_Session::namespaceUnset(self::NS_CHANGE);
    }
}
