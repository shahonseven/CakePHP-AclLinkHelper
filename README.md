CakePHP AclLinkHelper
=======================

Installation
------------
For CakePHP 3.x please use [3.x branch](https://github.com/shahonseven/CakePHP-AclLinkHelper/tree/3.x)


Loading the helper
------------
```php
<?php
	class AppController extends Contoller{
		public $helpers = array('AclLink' => array
		  'userModel' => 'Customer', //overide default userModel "User"
		  'primaryKey' => 'customer_id' //overide default primaryKey "id"
		);
	}
?>
```

Usage
------------
Create normal link
```php
<?php echo $this->AclLink->link($title, $url, $options, $confirmMsg)); ?>
```

Create post link
```php
<?php echo $this->AclLink->postLink($title, $url, $options, $confirmMsg)); ?>
```
