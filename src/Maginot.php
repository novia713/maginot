<?php
declare(strict_types=1);

namespace Novia713\Maginot;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class Maginot
 * Maginot is a php class for managing lines into php files
 * WARNING: not intended for files simultaneously managed by several people
 *
 * @package Novia713\Maginot
 */
class Maginot
{
    private $fs;

        /**
         * Maginot constructor.
         */
        public function __construct()
        {
            if (!$this->fs) {
                $this->fs = new \Symfony\Component\Filesystem\Filesystem();
            }
        }

    /**
     * This comments out all the ocurrences
     * @param $line
     * @param $file
     * @return int|null|string
     */
    public function commentLine($line, $file)
    {
        $i = 0;
        $tmpFile = [];
        try {
            $lines = $this->getLines($file);
            foreach ($lines as $fileLine) {
                if ($this->lineMatch($line, $fileLine)) {
                    $tmpFile[] = '//' . $line . PHP_EOL;
                    $i++;
                } else {
                    $tmpFile[] = $fileLine;
                }
            }
            if ($i > 0) {
                $this->fs->dumpFile(
                    $file,
                    implode("", $tmpFile)
                );
                return $i;
            } else {
                return null;
            }
        } catch (IOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * This uncomments out all the ocurrences
     * @param $line - Provide the line uncommented
     * @param $file
     * @return int|null|string - Number of uncommentations
     */
    public function unCommentLine($line, $file)
    {
        $i = 0;
        $tmpFile = [];
        try {
            $lines = $this->getLines($file);
            foreach ($lines as $fileLine) {
                if (
                    $this->lineMatch(
                        "//" . $line,
                        $fileLine
                    )
                    ||
                    $this->lineMatch(
                        "#" . $line,
                        $fileLine
                    )
                ) {
                    $tmpFile[] = $line . PHP_EOL;
                    $i++;
                } else {
                    $tmpFile[] = $fileLine;
                }
            }
            if ($i > 0) {
                $this->fs->dumpFile(
                    $file,
                    implode("", $tmpFile)
                );
                return $i;
            } else {
                return null;
            }
        } catch (IOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $line
     * @param $file
     * @return int|mixed
     */
    public function getLineNumber($line, $file)
    {
        $i = 0;
        $res = [];
        $lines = $this->getLines($file);
        foreach ($lines as $fileLine) {
            $i++;
            if ($this->lineMatch($line, $fileLine)) {
                $res[] = $i;
            }
        }

        if (empty($res)) {
            return null;
        } elseif (count($res) == 1) {
            return $res[0];
        } else {
            return $res;
        }
    }

    /**
     * Gets first line in file
     * @param $file
     * @return mixed
     */
    public function getFirstLine($file)
    {
        return
            $this->deleteCarriageReturn(
                $this->getLines(
                    $file
                )[0]
            );
    }

    /**
     * Sets first line in file
     * @param $line
     * @param $file
     * @return mixed
     */
    public function setFirstLine($line, $file)
    {
        $newFile = [];
        $lines = $this->getLines($file);
        $newFile = array_merge([$line . PHP_EOL], $lines);


        try {
            $this->fs->dumpFile(
                $file,
                implode("", $newFile)
            );
            return true;
        } catch (IOException $e) {
            return $e->getMessage();
        }
    }


    public function deleteFirstLine($file)
    {
        $newFile = [];
        $lines = $this->getLines($file);
        $firstLine = array_shift($lines);

        try {
            $this->fs->dumpFile(
                $file,
                implode("", $lines)
            );
            return true;
        } catch (IOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $file
     * @return mixed
     */
    public function getLastLine($file)
    {
        $lines = $this->getLines(
            $file
        );
        return
            $this->deleteCarriageReturn(
                $lines[(
                    count($lines) - 1
                )]
            );
    }

    /**
     * Sets last line in file
     * @param $line
     * @param $file
     * @return mixed
     */
    public function setLastLine($line, $file)
    {
        $newFile = [];
        $lines = $this->getLines($file);
        $newFile = array_merge($lines, [$line . PHP_EOL]);


        try {
            $this->fs->dumpFile(
                $file,
                implode("", $newFile)
            );
            return true;
        } catch (IOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Gets line by number
     * First line is #1, not #0
     * @param $file
     * @param $n
     * @return integer
     */
    public function getNLine($file, $n = 1)
    {
        $lines = $this->getLines(
            $file
        );
        return (int)
            $this->deleteCarriageReturn(
                $lines[//@TODO: $n is integer and > 0
                (int)($n - 1)
                ]
            );
    }

    /**
     * @param $line
     * @param $fileLine
     * @return bool
     */
    private function lineMatch($line, $fileLine)
    {
        return (
            strpos($fileLine, $line)
            &&
            (strlen($line) == strlen($this->deleteCarriageReturn($fileLine)))) ?
                true:
                false;
    }

    /**
     * Private function for return carriage removal
     * @param $string
     * @return mixed
     */
    private function deleteCarriageReturn($string)
    {
        return str_replace(
            [ "\n\r", "\n", "\r" ],
            "",
            $string
        );
    }

    /**
     * Opens the file and execute file() on it
     * @param $file
     * @return array|\Exception
     */
    private function getLines($file)
    {
        if (!$this->fs->exists($file)) {
            throw new IOException("Can't access the file!");
        } else {
            try {
                return file($file);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }
}
