<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);

use Pagerange\Markdown\Metaparsedown;
use Pagerange\Hyde;
use Pagerange\HydeException;

// Setup Environment
$_ENV['HYDE_DOCPATH'] =realpath(__DIR__ . '/test_docs/docs');
$_ENV['HYDE_DOCTABLE'] = 'docs';

final class HydeTest extends PHPUnit\Framework\TestCase
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
    }

    /**
     * Test that Hyde Class can be instantiated
     * @return void
     */
    public function testHydeCanBeInstantiated(): void
    {
        $hyde = new Hyde($this->mp, $this->dbh);
        $this->assertInstanceOf(
            Hyde::class,
            $hyde
        );
    }

    /**
     * Test that Hyde Env Doc Path is Set
     * @return void
     */
    public function testHydeEnvDocPathIsSet() :void
    {
        $hyde = new Hyde($this->mp, $this->dbh);
        $this->assertEquals($_ENV['HYDE_DOCPATH'], 
            $hyde->getDocpath());
    }

    /**
     * Test that Hyde Env Doc Table is Set
     * @return void
     */
    public function testHydeEnvDocTableIsSet() :void 
    {
        $hyde = new Hyde($this->mp, $this->dbh);
        $this->assertEquals('docs', 
            $hyde->getDoctable());
    }

    /**
     * Test That Hyde Sets Default Table if Not Set In Env
     * @return void
     */
    public function testHydeDefaultTableIsSetIfNotSetInEnv() :void 
    {
        unset($_ENV['HYDE_DOCTABLE']);
        $hyde = new Hyde($this->mp, $this->dbh);
        $this->assertEquals('hyde_docs', 
            $hyde->getDoctable());
    }

    /**
     * Test That Hyde Database Handle is Set
     * @return void
     */
    public function testHydeDatabaseHandleIsSet()
    {
        $hyde = new Hyde($this->mp, $this->dbh);
        $this->assertInstanceOf(\PDO::class, 
            $hyde->getDbh());
    }


    public function testHydeCanCreateDatabaseTable()
    {
        $hyde = new Hyde($this->mp, $this->dbh);
        $hyde->createDoctable();
        $dbh = $hyde->getDbh();
        $stmt = $dbh->query("SELECT id, file, meta, html, status, created_at FROM {$hyde->getDoctable()}");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertCount(0, $result);
    }


    /**
     * Test Hyde Throws PDO Eception If Table Creation Error
    */
    public function testHydeThrowsHydeExceptionIfTableCreationError()
    {
        $this->expectException(HydeException::class);
        $this->expectExceptionMessage('SQLSTATE[HY000]: General error: 1 unrecognized token: "#"');
        $_ENV['HYDE_DOCTABLE'] = '#%--&';
        $hyde = new Hyde($this->mp, $this->dbh);
        $hyde->createDoctable();
    }


    public function testHydeCanReadMarkdownFilesFromPath()
    {
        $_ENV['HYDE_DOCTABLE'] = 'docs';
        $hyde = new Hyde($this->mp, $this->dbh);
        $hyde->setDocfiles();
        $this->assertCount(3, $hyde->getDocfiles());
    }

    public function testHydeCanInsertAllDocsIntoDatabase()
    {
        $_ENV['HYDE_DOCTABLE'] = 'docs';
        $hyde = new Hyde($this->mp, $this->dbh);
        $hyde->createDoctable();
        $hyde->setDocfiles();
        $hyde->docsToSql();
        $query = "SELECT * FROM {$hyde->getDoctable()}";
        $stmt = $hyde->getDbh()->query($query);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $count = count($hyde->getDocfiles());
        $this->assertCount($count, $results);
    }


}