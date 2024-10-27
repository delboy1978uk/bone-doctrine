<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Fixture;

use League\Csv\Reader;

abstract class AbstractCsvFixtureLoader
{
    protected function getCsvFixtureData(): \Iterator
    {
        $file = $this->getCsvFileName();

        if (!file_exists($file)) {
            throw new \InvalidArgumentException($file . ' does not exist.');
        }

        $csv = Reader::createFromPath($file);
        $csv->setHeaderOffset(0);
        $header = $csv->getHeader();
        $records = $csv->getRecords($header);
        $records->rewind();

        return $records;
    }

    abstract public function getCsvFileName(): string;
}

