<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Attributes;

use Attribute;

#[Attribute()]
class Visibility {
    /**
     * rules can be
     *        | view | edit   |index |
     * all    |  x   |   x    |   x  |
     * view   |  x   |        |      |
     * edit   |      |   x    |      |
     * index  |      |        |   x  |
     * noindex|  x   |   x    |      |
     */
    public function __construct(
        public readonly string $rules = 'all'
    ) {}
}
