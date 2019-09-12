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
