<?php

namespace App;

abstract class Repository
{
    protected $dbh;

    /**
     * Repository constructor.
     * @param $dbh
     */
    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $stmt = $this->dbh->prepare("SELECT ".$this->keys()." FROM ".$this->table());

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        $stmt = $this->dbh->prepare("
            SELECT ".$this->keys()." FROM ".$this->table()." WHERE id={$id}
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param array $data
     */
    public function insert(array $data)
    {
        $stmt = $this->dbh->prepare("
            INSERT INTO ".$this->table()."(".$this->keys().") VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $data['display_name'], $data['product_type'], $data['price']
        ]);
    }

    /**
     * @param array $data
     */
    public function insertMany(array $data)
    {
        foreach ($data as $dataEntry) {
            $this->insert($dataEntry);
        }
    }

    /**
     * @return string
     */
    abstract protected function table();

    /**
     * @return string
     */
    abstract protected function keys();
}
