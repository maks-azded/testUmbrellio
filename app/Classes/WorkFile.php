<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 26.02.2019
 * Time: 20:37
 */

namespace App\Classes;

use Exception;
use finfo;
use  ZipArchive;
use  Storage;

class WorkFile
{
    private $config;
    private $file_info;

    public function __construct()
    {
        $this->config = yaml_parse_file(Storage::path("config.yaml"));
        $this->file_info = new finfo(FILEINFO_MIME_TYPE);
    }

    public function convertToArray($path)
    {
        $this->check($path);
        $mime_type = $this->file_info->buffer(file_get_contents($path));
        switch ($mime_type){
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return $this->read_docx($path);
                break;
            case 'application/msword' :
                return $this->read_doc_file($path);
                break;
            case 'text/plain' :
                return $this->read_txt($path);
                break;
        }
    }

    public function get_type($path){
        $mime_type = $this->file_info->buffer(file_get_contents($path));
        return $mime_type;
    }

    private function check($path){
        $mime_type = $this->file_info->buffer(file_get_contents($path));
        if(!in_array($mime_type,$this->config["type"])){
            throw new Exception('File type not allowed ('.$mime_type.')');
        }
        if(filter_var($path, FILTER_VALIDATE_URL)) {
            $data = get_headers($path, true);
            $size = isset($data['Content-Length']) ? (int)$data['Content-Length'] : 0;
        }
        else
            $size = filesize($path);
        if($size > $this->config["size"]){
            throw new Exception('File size must not exceed '.$size. ' bytes');
        }
    }

    private function get_url($path)
    {
        $host = $path;
        if(filter_var($path, FILTER_VALIDATE_URL)) {
            $path = str_replace("&amp;", "&", urldecode(trim($path)));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $path);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->config["timeout"]);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->config["timeout"]);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            $content = curl_exec($ch);
            curl_close($ch);
            Storage::disk('local')->put("new.docx",$content);
            $host = Storage::path("new.docx");
        }
        return $host;
    }

    private function read_docx($path)
    {
        $content = '';
        $filename = $this->get_url($path);
        $zip = zip_open($filename);
        if (!$zip || is_numeric($zip)) return false;
        while ($zip_entry = zip_read($zip)) {
            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;
            if (zip_entry_name($zip_entry) != "word/document.xml") continue;
            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
            zip_entry_close($zip_entry);
        }
        zip_close($zip);
        $content = str_replace('</w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);
        $striped_content = explode("\r\n",$striped_content);
        array_splice($striped_content,0,1);
        return $striped_content;
    }

    private function read_txt($path){
        $filename = $this->get_url($path);
        $f = file($filename);
        $f = mb_convert_encoding($f, 'utf-8', 'cp1251');
        return $f;
    }




}