<?php

class Join
{

    private string $sql = '';
    private string $where = '';
    private PDO $connection;
    public function __construct($connection){
        $this->connection = $connection;
    }

    public function setSqlStatement(string $sql){
        $this->sql = $sql;
    }
    public function on($first , $operation , $second){
        $this->sql.=" ON $first $operation $second ";
        return $this;
    }
    public function addAnotherInnerJoin($table){
        $this->sql.=" INNER JOIN $table ";
        return $this;
    }
    public function addAnotherLeftJoin($table){
        $this->sql.=" LEFT JOIN $table ";
        return $this;
    }
    public function addAnotherRightJoin($table){
        $this->sql.=" RIGHT JOIN $table ";
        return $this;
    }
    public function condition($column,$operator='',$value=''){
        if (!empty($column))
            $this->where.=$column." $operator ".(!is_numeric($value) ? $this->connection->quote($value):$value);
        return $this;
    }
    public function execute(){
        if (!empty($this->where))
            $this->where = "WHERE ".$this->where;
        $this->sql = $this->sql.$this->where;
        return (new Run($this->connection,$this->sql))->run();
    }

}
class Run{
    private string $sql;
    private PDO $connection;
    public function __construct($connection,$sql){
        $this->connection = $connection;
        $this->sql = $sql;
    }

    public function run(){
        $statement = $this->connection->prepare($this->sql);
        $statement->execute();
        return $statement->fetchAll();
    }
}