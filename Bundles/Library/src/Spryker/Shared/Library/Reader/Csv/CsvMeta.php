<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Library\Reader\Csv;

use SplFileObject;

class CsvMeta implements CsvMetaInterface
{

    /**
     * @var \SplFileObject
     */
    protected $csvFile;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var string
     */
    protected $enclosure;

    /**
     * @var string
     */
    protected $lineSeparator;

    /**
     * @var int
     */
    protected $total;

    /**
     * @param \SplFileObject $csvFile
     */
    public function __construct(\SplFileObject $csvFile, $lineSeparator = "\n")
    {
        $this->csvFile = $csvFile;
        $this->lineSeparator = $lineSeparator;
    }

    /**
     * @return \SplFileObject
     */
    public function getCsvFile()
    {
        return $this->csvFile;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        if ($this->columns === null) {
            $this->csvFile->fseek(0);
            $this->columns = $this->csvFile->fgetcsv();
        }

        return $this->columns;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        if ($this->total === null) {
            $this->csvFile->fseek(0);

            $lines = 0;
            while (!$this->csvFile->eof()) {
                $lines += substr_count($this->csvFile->fread(8192), $this->lineSeparator);
            }

            $this->total = $lines;
        }

        return $this->total;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        if ($this->delimiter === null) {
            list($this->delimiter, $enclosure) = $this->csvFile->getCsvControl();
        }

        return $this->delimiter;
    }

    /**
     * @return string
     */
    public function getEnclosure()
    {
        if ($this->enclosure === null) {
            list($delimiter, $this->enclosure) = $this->csvFile->getCsvControl();
        }

        return $this->enclosure;
    }

}
