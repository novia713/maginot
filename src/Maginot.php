<?php

namespace Novia713\Maginot;

use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class Maginot
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
            $this->fs = new \Symfony\Component\Filesystem\Filesystem();
        }

    /**
     * @param $line
     * @param $file
     * @return bool|\Exception|IOException
     */
    public function appendLine($line, $file)
    {
        try {
            $this->fs->dumpFile(
                    $file,
                    file_get_contents($file) . PHP_EOL .$line
                );
            return true;
        } catch (IOException $e) {
            return $e;
        }
    }

    /**
     * @param $line
     * @param $file
     * @return bool|\Exception|IOException
     */
    public function prependLine($line, $file)
    {
        try {
            $this->fs->dumpFile(
                $file,
                $line . PHP_EOL . file_get_contents($file)
            );
            return true;
        } catch (IOException $e) {
            return $e;
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
                    if ($replacedLine =
                            str_replace($line, '//' . $line, $fileLine)) {
                        $tmpFile[] = $replacedLine;
                        $i++;
                    } else {
                        throw new IOException("Can't comment the line!");
                    }
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
     * Gets line by number
     * First line is #1, not #0
     * @param $file
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
            strpos($fileLine, $line) !== false
            &&
            !(substr($fileLine, 0, 2) === "//")
            &&
            !(substr($fileLine, 0, 1) === "#")
            &&
            (strlen($line) == strlen($this->deleteCarriageReturn($fileLine)))) ?
                true:
                false;
    }

    /**
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
     * @param $file
     * @return array|string
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
