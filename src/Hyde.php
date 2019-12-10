<?php

namespace Pagerange;
use \Pagerange\Markdown\MetaParsedown;
use \Carbon\Carbon;

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

class Hyde
{
    
    protected $mp;

    protected $env;

    protected $dbh;

    protected $docfiles;

    public function __construct(MetaParsedown $mp, \PDO $dbh)
    {
        $this->mp = $mp;
        $this->dbh = $dbh;
        $this->setDocpath();
        $this->setDoctable();
        $this->createDoctable();
        $this->setDocfiles();
        $this->docsToSql();
    }

    public function setDocPath()
    {
        if(empty($_ENV['HYDE_DOCPATH'])) {
            throw new HydeException('Count not find HYDE_DOCPATH in .env');
        } else {
            $this->env['DOCPATH'] = $_ENV['HYDE_DOCPATH'];
        }
    }

    public function setDoctable()
    {
        $this->env['DOCTABLE'] = $_ENV['HYDE_DOCTABLE'] ?? 'hyde_docs';
    }

    public function getMp()
    {
        return $this->mp;
    }

    public function getDocpath()
    {
        return $this->env['DOCPATH'];
    }

    public function getDoctable()
    {
        return $this->env['DOCTABLE'];
    }

    public function getDbh()
    {
        return $this->dbh;
    }

    public function createDoctable()
    {

        if($this->dbh->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'sqlite') {
            $create =  "CREATE TABLE {$this->env['DOCTABLE']} (
                id INTEGER NOT NULL PRIMARY KEY,
                file VARCHAR(255),
                meta TEXT,
                html TEXT,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                status TINYINT NOT NULL DEFAULT 0 
            )";
        } else {
            $create =  "CREATE TABLE {$this->env['DOCTABLE']} (
                id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
                file VARCHAR(255),
                meta TEXT,
                html TEXT,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                status TINYINT NOT NULL DEFAULT 0 
            )";
        }

        try {
            $drop = "DROP TABLE IF EXISTS {$this->env['DOCTABLE']}";
            $stmt = $this->dbh->prepare($drop);
            $stmt->execute();
            $stmt = $this->dbh->prepare($create);
            $stmt->execute();
        } catch(\PDOException $e) {
            throw new HydeException($e->getMessage());
        }
    }

    public function setDocfiles()
    {
        $this->docfiles = glob($this->getDocpath() . '/*.md');
    }

    public function getDocfiles()
    {
        return $this->docfiles;
    }

    public function docsToSql()
    {
        try{
            foreach($this->getDocfiles() as $file) {
                $md = file_get_contents($file);
                $html = $this->mp->text($md);
                $meta = $this->mp->meta($md);
                $base = basename($file);
                $meta['uri'] = str_replace('_', '-', substr($base, 0, strrpos($base, '.')));
                $data = json_encode($meta);
                $query = "INSERT INTO {$this->getDoctable()}
                    (file, meta, html, status, created_at)
                    VALUES
                    (:file, :meta, :html, :status, :created_at)";
                $params = array(
                    ":file" => $meta['uri'],
                    ":meta" => $data,
                    ":html" => $html,
                    ":status" => ($meta['status'] == 'public' ? 1 : 0),
                    ":created_at" => $meta['created_at'] ?? Carbon::now()
                );
                $stmt = $this->dbh->prepare($query);
                $stmt->execute($params);
            }
        } catch(\PDOException $e) {
            throw new HydeException($e->getMessage());
        }
    }

}