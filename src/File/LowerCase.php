<?php

/**
 * @see       https://github.com/laminas/laminas-filter for the canonical source repository
 * @copyright https://github.com/laminas/laminas-filter/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-filter/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Filter\File;

use Laminas\Filter\Exception;
use Laminas\Filter\StringToLower;

class LowerCase extends StringToLower
{
    /**
     * Defined by Laminas\Filter\Filter
     *
     * Does a lowercase on the content of the given file
     *
     * @param  string|array $value Full path of file to change or $_FILES data array
     * @return string|array The given $value
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function filter($value)
    {
        // An uploaded file? Retrieve the 'tmp_name'
        $isFileUpload = (is_array($value) && isset($value['tmp_name']));
        if ($isFileUpload) {
            $uploadData = $value;
            $value      = $value['tmp_name'];
        }

        if (!file_exists($value)) {
            throw new Exception\InvalidArgumentException("File '$value' not found");
        }

        if (!is_writable($value)) {
            throw new Exception\RuntimeException("File '$value' is not writable");
        }

        $content = file_get_contents($value);
        if (!$content) {
            throw new Exception\RuntimeException("Problem while reading file '$value'");
        }

        $content = parent::filter($content);
        $result  = file_put_contents($value, $content);

        if (!$result) {
            throw new Exception\RuntimeException("Problem while writing file '$value'");
        }

        if ($isFileUpload) {
            return $uploadData;
        }
        return $value;
    }
}
