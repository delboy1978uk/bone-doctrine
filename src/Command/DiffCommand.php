<?php

namespace Bone\BoneDoctrine\Command;

use Doctrine\Migrations\Provider\SchemaProviderInterface;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand as DoctrineDiffCommand;

class DiffCommand extends DoctrineDiffCommand
{
    public function __construct(string $name, ?SchemaProviderInterface $schemaProvider = null)
    {
        $this->setName($name);
        parent::__construct($schemaProvider);
    }
}