<?php

class Blueprint
{
    const VARCHAR_LENGTH = 255;
    const INT_LENGTH = 15;
    const DEFAULT  = 'default';
    const UNIQUE   = 'unique';
    const NULLABLE = 'NULL';
    const MIN      = 'min';
    const MAX      = 'max';
    const BETWEEN  = 'between';
    const UNSIGNED  = 'unsigned';


    private array $constrains;
    private string $tableName;
    private string $sql;
    private ?PDO $connection;
    private ?string $engine;

    // initializing the class and the connection
    public function __construct($connection){
        $this->connection = $connection;
        $this->constrains = [];
    }
    // start execute the statement of create table
    private function run(){
        $this->connection->exec($this->sql);

        foreach ($this->constrains as $constrain)
            $this->connection->exec($constrain);

    }
    // start create table schema
    public function start($tableName,$engine="InnoDB"){
        $this->tableName = $tableName;
        $this->engine = $engine;
        $this->sql="CREATE TABLE IF NOT EXISTS $tableName ( ";
    }
    // end create table schema
    public function end(){
        $this->sql=rtrim(rtrim($this->sql,' '),',');
        $this->sql.=" ) ENGINE =  $this->engine";

        $this->run();
    }

    // create column id for table primary key and auto increment
    public function id(){
        $this->sql.=" id int(".self::INT_LENGTH.") PRIMARY KEY AUTO_INCREMENT , ";
    }
    //create column with given name with some options
    public function string($columnName,$options=[]){
        $statement = $this->prepareTheStatementOptions($columnName,$options);
        $this->sql.=" $columnName varchar(".self::VARCHAR_LENGTH.") not null $statement , ";
    }
    public function integer($columnName,$options=[]){
        $statement = $this->prepareTheStatementOptions($columnName,$options);
        $this->sql.=" $columnName INT(".self::INT_LENGTH.") $statement , ";
    }
    public function float($columnName,$options=[]){
        $statement = $this->prepareTheStatementOptions($columnName,$options);
        $this->sql.=" $columnName FLOAT $statement , ";
    }
    public function decimal($columnName,$length=2,$fraction=0,$options=[]){
        $statement = $this->prepareTheStatementOptions($columnName,$options);
        $this->sql.=" $columnName DECIMAL($length, $fraction)	 $statement , ";
    }
    public function longText($columnName,$options=[]){
        $statement = $this->prepareTheStatementOptions($columnName,$options);
        $this->sql.=" $columnName LONGTEXT $statement  , ";
    }
    public function boolean($columnName,$unique=false,$default=false){
        $sql = $unique?' unique ':'';
        $default = $default === false?0:1;
        $this->sql.=" $columnName TINYINT default $default $unique , ";
    }
    public function json($columnName,$options=[]){
        $statement = $this->prepareTheStatementOptions($columnName,$options);
        $this->sql.=" $columnName JSON $statement , ";
    }
    public function enum($columnName,$values=[],$options=[]){
        if (!empty($values)){
            $values = array_map(fn($value) => "'".$value."'", $values);
            $statement = $this->prepareTheStatementOptions($columnName,$options);
            $this->sql.=" $columnName ENUM(".implode(',',$values).") $statement , ";
        }
    }
    // create timestamp column with given name
    public function timestamp($columnName){
        $this->sql.=" $columnName TIMESTAMP default CURRENT_TIMESTAMP, ";
    }
    // create timestamp column with name created_at that point to when the record inserted
    public function created_at(){
        $this->sql.=" created_at TIMESTAMP default CURRENT_TIMESTAMP, ";
    }
    // drop table with given name
    public function dropTable($tableName){
        $sql ="DROP TABLE IF EXISTS $tableName";
         $this->connection->exec($sql);
    }
    // prepare the options
    private function getStatement($columnName,$option){
        if (is_array($option)){
            if ($option[0] == self::DEFAULT)
                return " default '$option[1]' ";
            if ($option[0] == self::MIN){
//                $this->constrains[] = "ALTER TABLE $this->tableName ADD CONSTRAINT CHECK_$columnName CHECK ($columnName >= $option[1])";
//                return  '';
                return " CHECK($columnName >= $option[1]) ";
            }
            if ($option[0] == self::MAX){
//                $this->constrains[] = "ALTER TABLE $this->tableName ADD CONSTRAINT CHECK_$columnName CHECK ($columnName <= $option[1])";
//                return '';
                return " CHECK($columnName <= $option[1]) ";
            }
            if ($option[0] == self::BETWEEN){
                $min = $option[1]['min'];
                $max = $option[1]['max'];
//                $this->constrains[] = "ALTER TABLE $this->tableName ADD CONSTRAINT CHECK_$columnName CHECK ($columnName BETWEEN $min AND $max)";
//                return '';
                return " CHECK($columnName BETWEEN $min AND $max) ";
            }
        }else{
            if ($option == self::UNIQUE)
                return ' unique ';
            if ($option == self::NULLABLE)
                return ' NULL ';
            if ($option == self::UNSIGNED)
                return ' UNSIGNED ';
        }




        return '';
    }

    private function prepareTheStatementOptions($columnName,$options=[]){
        $statement='';
        foreach ($options as $option)
            $statement.= $this->getStatement($columnName,$option);
        return $statement;
    }


}
