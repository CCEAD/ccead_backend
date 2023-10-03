<?php

namespace App\Services\Contracts;

interface FileInterface
{
    public function getNameFile($file);

    public function getPathFile($name);

    public function checkExistsFile($name);

    public function deleteFile($name);

    public function saveFile($file, $name);
}