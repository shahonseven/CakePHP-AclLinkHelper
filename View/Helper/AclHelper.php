<?php
/**
 * CakePHP 3.x ACL Link Helper
 *
 * @author      Shahril Abdullah - shahonseven
 * @link        https://github.com/shahonseven/CakePHP-Acl-Link-Helper
 * @package     Helper
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace App\View\Helper;

use Acl\Controller\Component\AclComponent;
use Cake\Cache\Cache;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\View\Helper;
use Cake\View\View;

class AclHelper extends Helper
{
    public $helpers    = ['Form'];
    
    public $userModel  = 'Users';
    
    public $primaryKey = 'id';

    public function __construct(View $View, array $config = []) {
        if (Plugin::loaded('Acl') == false) {
            throw new MissingPluginException(['plugin' => 'Acl']);
        }

        if (!empty($config['userModel'])) {
            $this->userModel = $config['userModel'];
        }

        $this->primaryKey = TableRegistry::get($this->userModel)->primaryKey();
        if (!empty($config['primaryKey'])) {
            $this->primaryKey = $config['primaryKey'];
        }

        parent::__construct($View, $config);
    }

    private function _aclCheck($url) {
        $userId   = $this->request->session()->read('Auth.User.' . $this->primaryKey);
        $cacheKey = md5($userId . '-' . Router::url($url, true));

        $access = Cache::read($cacheKey);
        if ($access !== false) {
            return (bool) $access;
        }

        $registry  = new ComponentRegistry();
        $this->Acl = new AclComponent($registry, Configure::read('Acl'));

        $aro = [
            'model'       => $this->userModel,
            'foreign_key' => $userId
        ];

        $url = Router::parse(Router::normalize($url));
        $path = [
            'controllers',
            $url['plugin'],
            $url['controller'],
            $url['action']
        ];
        $path = implode('/', array_filter($path));
        $node = $this->Acl->Aco->node($path);
        if (!$node) {
            return false;
        }

        $access = $this->Acl->check($aro, $path);
        Cache::write($cacheKey, (int) $access);
        return $access;
    }

    public function link($title, $url = null, array $options = []) {
        if (!$this->_aclCheck($url)) {
            return '';
        }
        return $this->Form->Html->link($title, $url, $options);
    }

    public function postLink($title, $url = null, array $options = []) {
        if (!$this->_aclCheck($url)) {
            return '';
        }
        return $this->Form->postLink($title, $url = null, $options);
    }
}
