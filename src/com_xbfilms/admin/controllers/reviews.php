<?php
/*******
 * @package xbFilms
 * @filesource admin/controlers/reviews.php
 * @version 0.3.0 5th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

class XbfilmsControllerReviews extends JControllerAdmin {
    
    public function getModel($name = 'Review', $prefix = 'XbfilmsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}