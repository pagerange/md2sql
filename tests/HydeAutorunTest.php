<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);

use Pagerange\Markdown\Metaparsedown;
use Pagerange\Hyde;
use Pagerange\HydeException;

// Setup Environment
$_ENV['HYDE_DOCPATH'] =realpath(__DIR__ . '/test_docs/docs');
$_ENV['HYDE_DOCTABLE'] = 'docs';

final class HydeAutorunTest extends PHPUnit\Framework\TestCase
{

    public $mp;

    public $dbh;

    protected function setup() :void
    {

        // Create Hyde Instance
        $this->mp = new Metaparsedown;
        $this->dbh = new \PDO('sqlite::memory:');
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, 
            PDO::ERRMODE_EXCEPTION);
        $this->hyde = new Hyde($this->mp, $this->dbh);
    }

    /**
     * Test that Hyde Class can be instantiated
     * @return void
     */
    public function testHydeCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            Hyde::class,
            $this->hyde
        );
    }

    /**
     * Test that Hyde Env Doc Path is Set
     * @return void
     */
    public function testHydeEnvDocPathIsSet() :void
    {
        $this->assertEquals($_ENV['HYDE_DOCPATH'], 
            $this->hyde->getDocpath());
    }

    /**
     * Test that Hyde Env Doc Table is Set
     * @return void
     */
    public function testHydeEnvDocTableIsSet() :void 
    {
        $this->assertEquals('docs', 
            $this->hyde->getDoctable());
    }


    /**
     * Test That Hyde Database Handle is Set
     * @return void
     */
    public function testHydeDatabaseHandleIsSet()
    {
        $this->assertInstanceOf(\PDO::class, 
            $this->hyde->getDbh());
    }

    public function testHydeCanReadMarkdownFilesFromPath()
    {
        $this->assertCount(3, $this->hyde->getDocfiles());
    }

    public function testHydeCanInsertAllDocsIntoDatabase()
    {
        $query = "SELECT * FROM {$this->hyde->getDoctable()}";
        $stmt = $this->hyde->getDbh()->query($query);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $count = count($this->hyde->getDocfiles());
        $this->assertCount($count, $results);
    }

}