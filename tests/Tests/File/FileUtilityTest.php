<?php

namespace Tests\File;

use Core\Kryn;
use Core\TempFile;
use Tests\TestCaseWithCore;

class FileUtilityTest extends TestCaseWithCore
{
    
    public function testTempFile()
    {
        $this->fileTester('Core\TempFile', Kryn::getTempFolder());
    }

    public function testSystemFile()
    {
        $this->fileTester('Core\SystemFile', './');
    }

    public function testWebFile()
    {
        $this->fileTester('Core\WebFile', 'web/');
    }

    /**
     * @param \Core\WebFile $class
     * @param string        $realPath
     */
    public function fileTester($class, $realPath)
    {
        $content = "
        asdasldm aisdh ad
        as das[odj aopsdja d
        [asj dpoashd ojadsofasdhfgat972
        3gtqohvj a-a9hg a
        sfghads
        fghasd-9gh asghasg
";

        $file = 'test_utility/test_temp_file.php';
        $class::setContent($file, $content);

        $fileObj = $class::getFile($file);
        $this->assertInstanceOf('Core\\File\\FileInfoInterface', $fileObj);

        $this->assertFileExists($realPath . $file);
        $this->assertTrue($class::exists($file));
        $this->assertEquals($content, $class::getContent($file));


        $class::remove($file);
        $this->assertFileNotExists($realPath . $file);
        $this->assertFalse($class::exists($file));


        file_put_contents($realPath . $file, $content);
        $this->assertFileExists($realPath . $file);
        $this->assertTrue($class::exists($file));
        $this->assertEquals($content, $class::getContent($file));

        $class::remove(dirname($file));
        $this->assertFalse($class::exists(dirname($file)));


        $dir = 'test_utility_folder';
        $class::createFolder($dir);
        $this->assertFileExists($realPath . $dir);
        $this->assertTrue($class::exists($dir));

        for ($i = 2; $i <= 10; $i++) {
            $class::createFile($dir . '/file' . $i, $i);
            $this->assertEquals($i, $class::getContent($dir . '/file' . $i));
        }
        $class::createFile($dir . '/file1', 1); //to have another order

        $files = $class::getFiles($dir);
        $this->assertCount(10, $files);
        $this->assertEquals(10, $class::getCount($dir));

        $this->assertInstanceOf('Core\File\FileInfoInterface', $files[0]);
        $this->assertEquals('file1', $files[0]->getName());
        $this->assertEquals('file5', $files[4]->getName());
        $this->assertEquals('file10', $files[9]->getName());

        $file1 = $class::getFile($dir . '/file1');
        $this->assertInstanceOf('Core\File\FileInfoInterface', $file1);
        $this->assertEquals('/test_utility_folder/file1', $file1->getPath());
        $this->assertEquals('file1', $file1->getName());
        $this->assertEquals('/test_utility_folder', $file1->getDir());
        $this->assertEquals('file', $file1->getType());
        $this->assertTrue($file1->isFile());
        $this->assertFalse($file1->isDir());

        $copy = 'test_utility_folder2';
        $class::copy($dir, $copy);
        $this->assertEquals(10, $class::getCount($copy));
        $file1 = $class::getFile($copy . '/file1');
        $this->assertEquals($class::getContent($copy . '/file1'), '1');
        $this->assertEquals('/test_utility_folder2/file1', $file1->getPath());
        $this->assertTrue($file1->isFile());
        $this->assertEquals('file1', $file1->getName());

        $copyDir = $class::getFile($copy);
        $this->assertTrue($copyDir->isDir());

        $class::remove($dir);
        $this->assertFileNotExists($realPath . $dir);
        $this->assertFalse($class::exists($dir));

    }

    public function tearDown()
    {
        $classes = array('Core\TempFile', 'Core\SystemFile', 'Core\WebFile');
        foreach ($classes as $class) {
            $class::remove('test_utility_folder');
            $class::remove('test_utility_folder2');
        }
    }

}
