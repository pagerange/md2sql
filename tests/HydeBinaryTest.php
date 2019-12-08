<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);

define('BASE_PATH', _DIR__ . '/..');

// Setup Environment
$_ENV['HYDE_DOCPATH'] =realpath(__DIR__ . '/test_docs/docs');
$_ENV['HYDE_DOCTABLE'] = 'docs';

final class HydeBinaryTest extends PHPUnit\Framework\TestCase
{

    protected $output;

    protected function setup() :void
    {   

        exec('php ./bin/hyde tests/test_env runtests ' . BASE_PATH, $output);
        $this->output = $output;
    }

    public function testHydeBinaryReturnsSuccessMessages()
    {
        $this->assertEquals('Database Connection Created!',
            $this->output[0]);

        $this->assertEquals('sqlite',
            $this->output[1]);

        $this->assertEquals(':memory:',
            $this->output[2]);
        
        $this->assertEquals("Index generated!", 
            $this->output[3]);

        $this->assertEquals("Done!", 
            $this->output[4]);
    }

    public function testBinaryHydeCreatesHydeObject()
    {
        $this->assertEquals('Hyde::class', $this->output[5]);
    }

    public function testBinaryHydeSetsCorrectDocPath()
    {
        $this->assertEquals('./test_docs/docs', $this->output[6]);
    }

    public function testBinaryHydeSetsCorrectTableName()
    {
        $this->assertEquals($_ENV['HYDE_DOCTABLE'], $this->output[7]);
    }

    public function testBinaryHydeCreatesPdoObject()
    {
        $this->assertEquals('PDO::class', $this->output[8]);
    }

    public function testBinaryHydeFindsCorrectNumberOfFiles()
    {
        $files = glob(__DIR__ . '/tests/test_docs/docs/*.md');
        $this->assertEquals(count($files), $this->output[9]);
    }

    public function testBinaryHydeInsertsCorrectNumberOfRecords()
    {
        $files = glob(__DIR__ . '/tests/test_docs/docs/*.md');
        $this->assertEquals(count($files), $this->output[10]);
    }

}