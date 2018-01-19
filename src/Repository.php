<?php

namespace App;

abstract class Repository
{
    /**
     * @var \PDO
     */
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
            SELECT ".$this->keys()." FROM ".$this->table()." WHERE id=?
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

    public function updateById($id, $update)
    {
        $setString = '';
        foreach ($this->columns() as $column) {
            $value = is_string($update[$column])
                ? "'".$update[$column]."'"
                : $update[$column];
            $setString .= sprintf("%s=%s,", $column, $value);
        }
        $setString = rtrim($setString, ','); // remove trailing comma

        $sql = "
            UPDATE ".$this->table()." SET $setString WHERE id=?
        ";

        $stmt = $this->dbh->prepare($sql);

        $stmt->execute([$id]);
    }

    /**
     * @return string
     */
    protected function keys()
    {
        return implode(',', $this->columns());
    }

    /**
     * @return string
     */
    abstract protected function table();

    /**
     * @return array
     */
    abstract protected function columns();
}
