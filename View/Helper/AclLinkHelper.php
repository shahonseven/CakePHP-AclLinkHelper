<?php

/**
 * CakePHP ACL Link Helper
 *
 * Based on Joel Stein AclLinkHelper
 * http://bakery.cakephp.org/articles/joel.stein/2010/06/26/acllinkhelper
 *
 * @author      Shahril Abdullah - shahonseven
 * @link        https://github.com/kolorafa/CakePHP-Acl-Link-Helper
 * @package     Helper
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('FormHelper', 'View/Helper');
App::uses('AclComponent', 'Controller/Component');

class AclLinkHelper extends FormHelper {

    public $userModel = 'User';
    public $primaryKey = 'id';

    public function __construct(View $View, $settings = array()) {
        parent::__construct($View, $settings);

        if (is_array($settings) && isset($settings['userModel'])) {
            $this->userModel = $settings['userModel'];
        }

        if (is_array($settings) && isset($settings['primaryKey'])) {
            $this->primaryKey = $settings['primaryKey'];
        }
    }

    protected function _aclCheck($url, $appendCurrent = true) {
        if ($appendCurrent) {
            $url = array_merge($this->request->params, $url);
        }

        $plugin = '';
        if (isset($url['plugin'])) {
            $plugin = Inflector::camelize($url['plugin']) . '/';
        }

        $controller = '';
        if (isset($url['controller'])) {
            $controller = Inflector::camelize($url['controller']) . '/';
        }

        $action = 'index';
        if (isset($url['action'])) {
            $action = $url['action'];
        }

        $collection = new ComponentCollection();
        $acl = new AclComponent($collection);
        $aro = array(
            $this->userModel => array(
                $this->primaryKey => AuthComponent::user($this->primaryKey)
            )
        );
        $aco = $plugin . $controller . $action;
        return $acl->check($aro, $aco);
    }

    public function link($title, $url = null, $options = array(), $confirmMessage = null) {
        if ($this->_aclCheck($url)) {
            return $this->Html->link($title, $url, $options, $confirmMessage);
        }
        return '';
    }

    public function postLink($title, $url = null, $options = array(), $confirmMessage = false) {
        if ($this->_aclCheck($url)) {
            return parent::postLink($title, $url, $options, $confirmMessage);
        }
        return '';
    }

    /*
     * check if you have access by array url
     */

    public function aclCheck($url, $appendCurrent = true) {
        $this->_aclCheck($url, $appendCurrent);
    }

}
