<?php
namespace App\Http\Controllers;

use StrFile;
use Illuminate\Container\Container;
use Illuminate\Http\Request;

class FileController extends Controller
{

    private $strFile;

    public function __construct(StrFile $strFile)
    {
        $this->strFile = $strFile;
    }

    public function show(Request $request)
    {
        $path = $request->input('path');
        $str = $request->input('str');

//        return $this->strFile->str_pos('https://vk.com/doc134792948_493221240?hash=74f98c33eba01a79bb&dl=52b47d86f274e5a6e3','GAV' );
        return $this->strFile->str_pos($path, $str);
    }

}