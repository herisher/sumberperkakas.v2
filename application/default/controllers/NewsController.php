<?php
/**
 * News
 */
class NewsController extends BaseController {
    /**
     * list
     */
    public function listAction() {
        $this->createNavigator(
            $this->model('Dao_News')->createWherePhrase(
                array(
                    'disp_flag = ?' => 1,
                ),
                array('sort_order asc', 'disp_date desc', 'id desc')
            ),
            10
        );
    }

    /**
     * detail
     */
    public function detailAction() {
        if ($this->_getParam('id')) {
            $news = $this->model('Dao_News')->retrieve($this->_getParam('id'));
            if ($news && $news['disp_flag']) {
                $this->view->news = $news;
            } else {
                $this->view->error_str = "Berita tidak ada.";
                $this->_forward('error', 'Error');
                return;
            }
        } else {
            $this->view->error_str = "Berita tidak ada.";
            $this->_forward('error', 'Error');
            return;
        }
    }
}
