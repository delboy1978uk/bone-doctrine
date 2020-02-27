# bone-doctrine
Doctrine functionality for Bone MVC Framework
##Usage
Simply add the Package to Bone's module config
```php
<?php

// use statements here
use BoneMvc\Module\BoneMvcDoctrine\BoneMvcDoctrinePackage;

return [
    'packages' => [
        // packages here...,
        BoneMvc\Module\BoneMvcDoctrine\BoneMvcDoctrinePackage::class,
    ],
    // ...
];
```
You should already have a `config/bone-db.php` configuration file, as it comes by standard in the Bone Framework 
skeleton project. 
## entity manager
You can fetch and inject the `Doctrine\ORM\EntityManager` into your classes inside the package registration class:
```php
$entityManager = $c->get(EntityManager::class);
``` 
Of course from there you can check the doctrine docs here https://www.doctrine-project.org/
## database migrations
Bone Framework comes with the `vendor/bin/migrant` command, which is essentially just Doctrine Migrations configured for
Bone Framework. Not only will it scan your own entity folder for changes, but also those of any vendor packages you rely
on. 

Change your DB schema by updating the Doctrine annotations on your entity class, then run:
```php
vendor/bin/migrant diff
vendor/bin/migrant migrate
``` 
https://github.com/delboy1978uk/user is a typical example, look in the entity folder to learn more.

Migrations docs https://www.doctrine-project.org/projects/doctrine-migrations/en/2.2/reference/managing-migrations.html

