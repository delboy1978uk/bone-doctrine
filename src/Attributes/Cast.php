<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Attributes;

use Attribute;

#[Attribute()]
class Cast {
    public function __construct(
        public readonly ?string $prefix = null,
        public readonly ?string $suffix = null,
        public readonly ?string $transformer = null,
    ) {}
}
