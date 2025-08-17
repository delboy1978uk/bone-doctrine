# bone-doctrine
[![Latest Stable Version](https://poser.pugx.org/delboy1978uk/bone-doctrine/v/stable)](https://packagist.org/packages/delboy1978uk/bone-doctrine) [![Total Downloads](https://poser.pugx.org/delboy1978uk/bone-doctrine/downloads)](https://packagist.org/packages/delboy1978uk/bone-doctrine) [![License](https://poser.pugx.org/delboy1978uk/bone/license)](https://packagist.org/packages/delboy1978uk/bone)<br />
![build status](https://github.com/delboy1978uk/bone-doctrine/actions/workflows/master.yml/badge.svg) [![Code Coverage](https://scrutinizer-ci.com/g/delboy1978uk/bone-doctrine/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/delboy1978uk/bone-doctrine/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/delboy1978uk/bone-doctrine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/delboy1978uk/bone-doctrine/?branch=master)<br />

Doctrine functionality for Bone Framework
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
See Doctrine Fixtures documentation for more details.
## fixtures
You can run data fixtures using the following command
```
vendor/bin/bone migrant:fixtures
```
In your Bone Framework config, add a fixtures.php and return classes that run your fixtures. 
```php
<?php
/**
 * Returns a list of fixtures by classname, in the order of their execution
 */

use Fixtures\LoadUsers;

return [
    'fixtures' => [
        LoadUsers::class,
    ],
];
```
See Doctrine Fixtures documentation for more details.
## admin panel
You can create a quick CRUD admin panel for your entities. Example:
```php

use Bone\BoneDoctrine\Attributes\Cast;
use Bone\BoneDoctrine\Attributes\Visibility;
use Bone\BoneDoctrine\Traits\HasId;
use Del\Form\Field\Attributes\Field;
use Del\Form\Traits\HasFormFields;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class SomeEntity
{
    use HasFormFields;
    use HasId;

    #[ORM\Column(type: 'float')]
    #[Field('float|required|max:6')]    // form field validator rules
    #[Visibility('all')                 // visible on index,create,edit,delete
    #[Cast(prefix: '€')]                // prefix or suffix  table values
    private ?float $price = null;

    // etc
}
```
Now that the entity is annotated, create a service, and an admin controller as follows:
```php
<?php

declare(strict_types=1);

namespace Bone\App\Service;

use Bone\App\Entity\Test;
use Bone\BoneDoctrine\Service\RestService;

class SomeEntityService extends RestService
{
    public function getEntityClass(): string
    {
        return SomeEntity::class;
    }
}
```
The Admin controller looks like  this:
```php
<?php

declare(strict_types=1);

namespace Bone\App\Http\Controller;

use Bone\App\Entity\SomeEntity;
use Bone\App\Service\SomeEntityService;
use Bone\BoneDoctrine\Http\Controller\AdminController;

class TestAdminController extends AdminController
{
    public function getEntityClass(): string
    {
        return SomeEntity::class;
    }

    public function getServiceClass(): string
    {
        return SomeEntityService::class;
    }
}
```
Finally add the routes in your package class:
```php
public function addRoutes(Container $c, Router $router): Router
{
    // other routes here....
    $router->adminResource('some-entities', TestAdminController::class, $c);
}
```
You can now browse to `/admin/some-entities` and perform CRUD actions.
## api controller
You can do the same to get an instant API. Here's an example for a Perdson class.
Controller:
```php
<?php

declare(strict_types=1);

namespace Bone\App\Http\Controller\Api;

use Bone\App\Service\PersonService;
use Bone\Http\Controller\ApiController;

class PersonController extends ApiController
{
    public function getServiceClass(): string
    {
        return PersonService::class;
    }
}
```
Service:
```php
<?php

declare(strict_types=1);

namespace Bone\App\Service;

use Bone\App\Entity\Person;
use Bone\BoneDoctrine\Service\RestService;

class PersonService extends RestService
{
    public function getEntityClass(): string
    {
        return Person::class;
    }
}
```
Route:
```php
public function addRoutes(Container $c, Router $router): Router
{
    $router->apiResource('people', PersonController::class, $c);

    return $router;
}
```
You now have API REST endpoints at `/api/people`.

You can see all configured routes using the `bone router:list` command.
