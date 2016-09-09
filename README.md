CakePHP 3.x AclHelper
=======================

Loading the helper
------------
in src/View/AppView.php
```php
<?php
	class AppView extends View
	{
		public function initialize()
		{
			$this->loadHelper('Acl', ['userModel' => 'Users']);
		}
	}
?>
```

Usage
------------
Create normal link
```php
<?= $this->Acl->link($title, $url, $options)); ?>
```

Create post link
```php
<?= $this->Acl->postLink($title, $url, $options)); ?>
```
