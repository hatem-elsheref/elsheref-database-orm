<?php

class Database implements DatabaseInterface
{

    public PDO $connection;
    public ?PDOStatement $statement = null;
    private ?string $table = null;
    private ?string $PK;
    private string $sql = '';
    private array $where = [];
    private array $select = [];

    private const ALLOWED_OPERATORS=['=','!=','<>','>','>=','<','<=','LIKE'];
    private const OPERATOR_IN='IN';
    private const OPERATOR_NOT_IN='NOT IN';
    private const OPERATOR_BETWEEN='BETWEEN';
    private const OPERATOR_NOT_BETWEEN='NOT BETWEEN';
    private const OPERATOR_AND='AND';
    private const OPERATOR_OR='OR';

    public function __construct(PDO $connection){
        $this->connection = $connection;
    }

    protected function setTable($table){
        $this->table = $table;
    }

    protected function setPrimaryKey($primaryKey){
        $this->PK = $primaryKey;
    }

    private function prepareSelectCommand(){
        $this->select = array_unique($this->select);
        if (empty($this->select)){
            $this->sql.= ' * ';
            return;
        }

        if (in_array('*',$this->select) && (end($this->select) == '*')){
            $this->sql.= ' * ';
            return;
        }

        if (in_array('*',$this->select) && !(end($this->select) == '*')){
            $this->sql.= str_replace('*,','',implode(',',$this->select));
            return;
        }

        $this->sql.= implode(',',$this->select);
    }

    private function prepareWhereCommand(){
        $where='';
        if (count($this->where) == 0)
            return $where;



        foreach ($this->where as $condition){
            $column = $condition[0];
            $operator = $condition[1];
            $value = $condition[2];
            $next = $condition[3];

            if (!empty($where))
                $where.=" ".$next;
            if (in_array($operator,self::ALLOWED_OPERATORS)){
                $where.=" ( $column $operator ".$this->quote($value)." ) ";
            }elseif ($operator == self::OPERATOR_IN){
                $where.=" ( $column ".self::OPERATOR_IN." ( ".implode(',',array_map(fn($val)=>$this->quote($val),$value))." ) ) ";
            }elseif ($operator == self::OPERATOR_NOT_IN){
                $where.=" ( $column ".self::OPERATOR_NOT_IN." ( ".implode(',',array_map(fn($val)=>$this->quote($val),$value))." ) ) ";
            }elseif ($operator == self::OPERATOR_BETWEEN){
                $where.=" ( $column ".self::OPERATOR_BETWEEN." ".$this->quote($value[0])." AND ".$this->quote($value[1])." ) ";
            }elseif ($operator == self::OPERATOR_NOT_BETWEEN){
                $where.=" ( $column ".self::OPERATOR_NOT_BETWEEN." ".$this->quote($value[0])." AND ".$this->quote($value[1])." ) ";
            }else{
                return $where;
            }
        }
        return " WHERE ".$where;
    }

    private function quote($value){
        if (!is_numeric($value))
            return $this->connection->quote($value);

        return $value;
    }

    private function endingTheSqlStatement(){
        $this->prepareSelectCommand();

        $where = $this->prepareWhereCommand();

        $this->sql = 'SELECT ' . $this->sql .' FROM ' . $this->table . ' '.$where;
    }

    private function execute(){
        $this->endingTheSqlStatement();
        $this->statement = $this->connection->prepare($this->sql);
        $this->statement->execute();
    }

    public function where($column,$operation='=',$value='',$andOr=self::OPERATOR_AND){
        if (!in_array($andOr,[self::OPERATOR_AND,self::OPERATOR_OR]))
            return $this;

        if (!empty($column) && (in_array(strtoupper($operation),self::ALLOWED_OPERATORS)))
            $this->where[]=[$column,strtoupper($operation),$value,$andOr];
        return $this;
    }

    public function whereIn($column,$values=[],$andOr=self::OPERATOR_AND){
        if (!in_array($andOr,[self::OPERATOR_AND,self::OPERATOR_OR]))
            return $this;
        if (!empty($column))
            $this->where[]=[$column,self::OPERATOR_IN,$values,$andOr];
        return $this;
    }

    public function whereNotIn($column,$values=[],$andOr=self::OPERATOR_AND){
        if (!empty($column))
            $this->where[]=[$column,self::OPERATOR_NOT_IN,$values,$andOr];
        return $this;
    }

    public function whereBetween($column,$min,$max,$andOr=self::OPERATOR_AND){
        if (!empty($column))
            $this->where[]=[$column,self::OPERATOR_BETWEEN,[$min,$max],$andOr];
        return $this;
    }

    public function whereNotBetween($column,$min,$max,$andOr=self::OPERATOR_AND){
        if (!empty($column))
            $this->where[]=[$column,self::OPERATOR_NOT_BETWEEN,[$min,$max],$andOr];
        return $this;
    }

    public function orWhere($column,$operation='=',$value=''){
       return $this->where($column,$operation,$value,self::OPERATOR_OR);
    }

    public function orWhereIn($column,$values=[],$andOr=self::OPERATOR_AND){
        return $this->whereIn($column,$values,self::OPERATOR_OR);
    }

    public function orWhereNotIn($column,$values=[],$andOr=self::OPERATOR_AND){
        return $this->whereNotIn($column,$values,self::OPERATOR_OR);
    }

    public function orWhereBetween($column,$min,$max,$andOr=self::OPERATOR_AND){
        return $this->whereBetween($column,$max,$max,self::OPERATOR_OR);
    }

    public function orWhereNotBetween($column,$min,$max,$andOr=self::OPERATOR_AND){
        return $this->whereNotBetween($column,$max,$max,self::OPERATOR_OR);
    }

    public function select(...$columns){
        foreach ($columns as $column)
            if (is_string($column))
                $this->select [] = $column;
        return $this;
    }

    public function get(){
        if (empty($this->sql))
             $this->select('*');

        $this->execute();
        return $this->statement->fetchAll();
    }

    public function all()
    {
        return $this->get();
    }

    public function first()
    {
        if (empty($this->sql))
           $this->select('*');

        $this->execute();
        return $this->statement->fetchObject();
    }

    public function count()
    {
        if (empty($this->sql))
             $this->select($this->PK);

        $this->execute();
        return $this->statement->rowCount();
    }

    public function find(int $primaryKey){
        return $this->findBy($this->PK,$primaryKey);
    }

    public function findBy(string $column,$value){
        $this->select('*')->where($column,'=',$value);
        return $this->first();
    }

    public function create(array $data){
        $attributes = array_map(fn($attribute) => "$attribute = :$attribute",array_keys($data));
        $this->sql = "INSERT INTO ".$this->table." SET ".implode(',',$attributes);
        $this->statement = $this->connection->prepare($this->sql);

        foreach ($data as $attributeName => $attributeValue)
            $this->statement->bindValue(":$attributeName",$attributeValue);

        return $this->statement->execute();
    }

    public function update(int $id,array $data){
        $attributes = array_map(fn($attribute) => "$attribute = :$attribute",array_keys($data));
        $this->sql = "UPDATE ".$this->table." SET ".implode(',',$attributes) . " WHERE $this->PK = :__pk__";
        $this->statement = $this->connection->prepare($this->sql);

        $this->statement->bindValue(":__pk__",$id);

        foreach ($data as $attributeName => $attributeValue)
            $this->statement->bindValue(":$attributeName",$attributeValue);

        return $this->statement->execute();
    }

    public function prepareForDelete(){
        $this->select=[$this->PK];
        $this->endingTheSqlStatement();
        return $this;
    }

    public function delete(array $ids=[],$identifierColumn=null){
        if (empty($ids)) if (empty($this->sql))
            return false;
        else{
            $this->statement = $this->connection->prepare($this->sql);
            $this->statement->execute();
            $data = $this->statement->fetchAll(PDO::FETCH_COLUMN,'id');
            if (!empty($data))
                $SQL = "DELETE FROM ".$this->table." WHERE $this->PK IN (".implode(',',array_map(fn($id)=>$this->quote($id),$data)).")";
            else
                return false;
        } else{
            if (is_null($identifierColumn))
                $identifierColumn = $this->PK;
            $SQL = "DELETE FROM ".$this->table." WHERE $identifierColumn IN (".implode(',',array_map(fn($id)=>$this->quote($id),$ids)).")";
        }

        return $this->sql($SQL);

    }

    public static function sql(string $sql){
        $sql = trim($sql);
        $statement = DB::connection()->prepare($sql);
        $result = $statement->execute();
        if (strtoupper(substr($sql,0,6)) == strtoupper('SELECT'))
            return $statement->fetchAll();
        return $result;
    }

}