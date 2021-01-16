<?php

class DB extends Database {

    public static ?PDO $databaseConnectionLink = null;
    public static ?Database $databaseClass = null;

    public static function open($hostName,$userName,$password,$databaseName,$portNumber){
        $dataSourceName = 'mysql:host='.$hostName.';dbname='.$databaseName.';port='.$portNumber;
        try {
            if (is_null(self::$databaseConnectionLink)){
                self::$databaseConnectionLink = new PDO($dataSourceName,$userName,$password);
                self::$databaseConnectionLink->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                self::$databaseConnectionLink->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
                self::$databaseClass = new Database(self::$databaseConnectionLink);

                if (is_null(self::$databaseConnectionLink))
                    throw new Exception('Invalid Credentials');
            }
        }catch (\PDOException $exception){
            echo $exception->getMessage();
            exit();
        }
    }

    public static function connection(){
       return self::$databaseConnectionLink;
    }

    public static function table($tableName,$primaryKey='id'){
        $database = (is_null(self::$databaseClass))?new Database(self::$databaseConnectionLink):self::$databaseClass;
        $database->setTable($tableName);
        $database->setPrimaryKey($primaryKey);
        return $database;
    }

}
