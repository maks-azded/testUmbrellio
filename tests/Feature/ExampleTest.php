<?php

namespace Tests\Feature;

use Tests\TestCase;
use StrFile;
use Storage;
use Illuminate\Container\Container;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $container = Container::getInstance();
        $strFile = $container->make(StrFile::class);
        echo "\nTest1. local txt:\n";
        echo $strFile->str_pos(Storage::path('maksimIvanov.txt'), 'maks.azded@yandex.ru ');
        echo "\nTest2. local docx:\n";
        echo $strFile->str_pos(Storage::path('maksimIvanov.docx'), 'maks.azded@yandex.ru ');
        echo "\nTest3. local doc (not on the config.yaml):\n";
        echo $strFile->str_pos(Storage::path('maksimIvanov.doc'), 'maks.azded@yandex.ru ');
        echo "\n\nTest4. local hash (identical files):\n";
        echo $strFile->compare_hash(Storage::path('maksimIvanov.docx'), Storage::path('maksimIvanov2.docx'));
        echo "\nTest5. local hash (different files):\n";
        echo $strFile->compare_hash(Storage::path('maksimIvanov.txt'),  Storage::path('maksimIvanov2.docx'));
        echo "\nTest6. url txt:\n";
        echo $strFile->str_pos('https://vk.com/doc134792948_494271809?hash=531db089683fa21f22&dl=af3105575173b2feff', 'maks.azded@yandex.ru ');
        echo "\nTest7. url docx:\n";
        echo $strFile->str_pos('https://vk.com/doc134792948_494272368?hash=f2ee3ebccec83ad518&dl=78a6e8386731595dd3', 'maks.azded@yandex.ru ');

    }
}
