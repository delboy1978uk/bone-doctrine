# bone-doctrine
Doctrine functionality for Bone MVC Framework
## installation
Install via composer
```
composer require delboy1978uk/bone-doctrine
```
##Usage
Simply add the Package to Bone's module config
```php
<?php

// use statements here
use Bone\BoneDoctrine\BoneDoctrinePackage;

return [
    'packages' => [
        // packages here...,
        Bone\BoneDoctrine\BoneDoctrinePackage::class,
    ],
    // ...
];
```
You should already have a `config/bone-db.php` configuration file, as it comes by standard in the Bone Framework 
skeleton project. 
```php
<?php

return [
    'db' => [
        'driver' => 'pdo_mysql',
        'host' => 'mariadb',
        'dbname' => 'awesome',
        'user' => 'dbuser',
        'password' => '[123456]',
    ],
];
```
Also you must set paths for your proxy, cache and entity directories. 
```php
<?php

return [
    // other paths here....
    'proxy_dir' => 'data/proxies/',
    'cache_dir' => 'data/cache/',
    'entity_paths' => [],
];
```
## entity manager
You can fetch and inject the `Doctrine\ORM\EntityManager` into your classes inside the package registration class:
```php
$entityManager = $c->get(EntityManager::class);
``` 
Of course from there you can check the doctrine docs here https://www.doctrine-project.org/
## database migrations
Bone Framework comes with the `vendor/bin/bone` command, which is essentially just Doctrine Migrations configured for
Bone Framework. Not only will it scan your own entity folder for changes, but also those of any vendor packages you rely
on. 

Change your DB schema by updating the Doctrine annotations on your entity class, then run:
```php
vendor/bin/bone migrant:diff
vendor/bin/bone migrant:migrate
``` 
https://github.com/delboy1978uk/user is a typical example, look in the entity folder to learn more.

Migrations docs https://www.doctrine-project.org/projects/doctrine-migrations/en/2.2/reference/managing-migrations.html

