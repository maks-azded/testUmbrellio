<?php
namespace App\Classes;

use File;
use WorkFile;

class StrFile
{
    private $checkFile;

    public function __construct(WorkFile $checkFile)
    {
        $this->checkFile = $checkFile;
    }

    public function str_pos($path, $substr)
    {
        try {
            $f = $this->checkFile->convertToArray($path);
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
        foreach ($f as $num => $str){
            $pos = strpos($str, $substr);
            if($pos!==false){
                $type = $this->checkFile->get_type($path);
                return ("File type: ".$type."\nFind in line ".($num+1).", position ".($pos+1)."\n");
            }
        }
        return ("String not found\n");
    }

    public function compare_hash($path, $path_2)
    {
        if (filesize($path) == filesize($path_2)
            && md5_file($path) == md5_file($path_2))
            return ("Files are the same\n");
        else
            return ("Files are not the same\n");
    }
}
