<?php
/**
 * admin
 */
class Manager_AdminController extends BaseController {
    const NAMESPACE_LIST = '/manager/admin/list';

    /**
     * Search for creating conditions
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Admin');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // restore the search criteria from the session
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // Search conditions set
                if ( $key === 'account' && $value ) {
                    $where['account = ?'] = $value;
                }
            }
        }
        return $table->createWherePhrase($where, $order_by);
    }

    /**
     * Search restoration
     */
    private function restoreSearchForm($form) {
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        if ($session->post) {
            $form->setDefaults($session->post);
        }
    }

    /**
     * list
     */
    public function listAction() {
        // Form settings read
        $form = $this->view->form;

        // Search Clear
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('clear') ) {
                // clear
                Zend_Session::namespaceUnset(self::NAMESPACE_LIST);
            } elseif ( $this->getRequest()->getParam('search') ) {
                // Start Search
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
                $this->_redirect(self::NAMESPACE_LIST);
            } else {
                // Search restoration
                $this->restoreSearchForm($form);
            }
        } else {
            // Search restoration
            $this->restoreSearchForm($form);
        }

        // List acquisition
        $this->createNavigator(
            $this->createWherePhrase()
        );

        // Display customization
        $models = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            array_push($models, $model);
        }
        $this->view->models = $models;
    }

    /**
     * New registration check
     */
    private function createValid($form) {
        // Check form
        if (! $form->isValid($_POST) ) {
            $error_str = array();
            $this->checkForm($form, $this->view->config, $error_str);
            $this->view->error_str = $error_str;
            return false;
        }
        
        return true;
    }

    /**
     * New registration
     */
    public function createAction() {
        // Form settings read
        $form = $this->view->form;

        // Error checking
        if ( $this->getRequest()->isPost() ) {
            if ( $this->createValid($form) ) {
                $this->doCreate($form);
            }
        }
    }

    /**
     * New registration start
     */
    private function doCreate($form) {
        $table = $this->model('Dao_Admin');
        $model_id = $table->insert(
            array(
                'account'     => $form->getValue('account'),
                'password'    => $form->getValue('password'),
                'type'        => 0,
                'create_date' => new Zend_Db_Expr('now()'),
            )
        );
        $this->gobackList();
    }

    /**
     * Edit check
     */
    private function editValid($form) {
        // Check form
        if (! $form->isValid($_POST) ) {
            $error_str = array();
            $this->checkForm($form, $this->view->config, $error_str);
            $this->view->error_str = $error_str;
            return false;
        }
        
        return true;
    }

    /**
     * edit
     */
    public function editAction() {
        // Form settings read
        $form = $this->view->form;

        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Admin')->retrieve($id);

            if (!$model) {
                $this->view->error_str = 'Pengguna tidak ada atau telah dihapus.';
                $this->_forward('error', 'Error');
                return;
            }

            // Initial value setting
            $admin = $model->toArray();
            $form->setDefaults($admin);
            $this->view->model = $admin;

            // Error checking
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form) ) {
                    $this->doUpdate($admin['id'], $form);
                }
            }
        }
        else {
            $this->view->error_str = 'Pengguna tidak ada atau telah dihapus.';
            $this->_forward('error', 'Error');
            return;
        }
    }

    /**
     * Start editing
     */
    private function doUpdate($id, $form) {
        $table = $this->model('Dao_Admin');
        $model_id = $table->update(
            array(
                'password' => $form->getValue('password'),
                'update_date' => new Zend_Db_Expr('now()'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $id
            )
        );
        $this->gobackList();
    }

    /**
     * Delete
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $table = $this->model('Dao_Admin');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
            $this->gobackList();
        }
        else {
            $this->view->error_str = 'Ini adalah URL ilegal.';
            $this->_forward('error', 'Error');
        }
    }
}
