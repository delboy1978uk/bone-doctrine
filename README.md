# bone-doctrine
[![Build Status](https://travis-ci.org/delboy1978uk/bone-doctrine.png?branch=master)](https://travis-ci.org/delboy1978uk/bone-doctrine) [![Code Coverage](https://scrutinizer-ci.com/g/delboy1978uk/bone-doctrine/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/delboy1978uk/bone-doctrine/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/delboy1978uk/bone-doctrine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/delboy1978uk/bone-doctrine/?branch=master) <br />
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
