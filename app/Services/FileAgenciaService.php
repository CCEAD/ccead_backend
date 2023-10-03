<?php

namespace App\Services;

use App\Services\Contracts\FileInterface;
use Illuminate\Support\Facades\Storage;

class FileAgenciaService implements FileInterface
{
    public function getNameFile($file): string
    {
    	$file_name  = uniqid(time()) . '.' . $file->getClientOriginalExtension();
    	return $file_name;
    }

    public function getPathFile($name): string
    {
        $file_path = 'pdf/documentos/'.$name;
    	return $file_path;
    }

    public function checkExistsFile($name): bool
    {
        return Storage::disk('local')->exists($this->getPathFile($name));
    }

    public function deleteFile($name): bool
    {
    	if ($this->checkExistsFile($name)) {
            return Storage::disk('local')->delete($this->getPathFile($name));
    	}
    	
    	return false;
    }

    public function saveFile($file, $name): bool
    {
    	$saved = false;

        Storage::disk('local')->putFileAs('pdf/documentos/', $file, $name);

    	if ($this->checkExistsFile($name)) {
    		$saved = true;
    	}

    	return $saved; 	
    }
}
