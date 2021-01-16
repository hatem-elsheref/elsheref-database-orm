<?php

interface DatabaseInterface{

    public function get();
    public function all();
    public function first();
    public function count();
    public function select(...$columns);
    public function find(int $primaryKey);
    public function findBy(string $column,$value);
    public function where($column,$operation,$value,$andOr);
    public function whereIn($column,$values,$andOr);
    public function whereNotIn($column,$values,$andOr);
    public function whereBetween($column,$min,$max,$andOr);
    public function whereNotBetween($column,$min,$max,$andOr);
    public function orWhere($column,$operation,$value);
    public function orWhereIn($column,$values,$andOr);
    public function orWhereNotIn($column,$values,$andOr);
    public function orWhereBetween($column,$min,$max,$andOr);
    public function orWhereNotBetween($column,$min,$max,$andOr);
    public function create(array $data);
    public function update(int $id,array $data);
    public function delete(array $ids,$identifierColumn);
    public static function sql(string $sql);
}