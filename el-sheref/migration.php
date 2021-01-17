<?php


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'DatabaseInterface.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Join.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Database.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'DB.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Blueprint.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Migrations' . DIRECTORY_SEPARATOR . 'M_2021014115830_createMigrationsTable.php';

class migration{

    private $database;
    const MAIN_TABLE='migrations';
    const MIGRATION_CLASS='M_2021014115830_createMigrationsTable.php';
    private Blueprint $table;
    public function __construct($host,$user,$pass,$name,$port){
        \DB::open($host,$user,$pass,$name,$port);
        $this->database = \DB::connection();
        $this->table = new Blueprint($this->database);
        $this->migrateMainTable();
    }
    private function add($name){
        $SQL = "INSERT INTO ".self::MAIN_TABLE ."(name) VALUES (:name)";
        $statement = $this->database->prepare($SQL);
        $statement->bindValue(':name',$name,\PDO::PARAM_STR);
        $statement->execute();
    }

    private function getInstalledTables(){
        $SQL = "SELECT * FROM ".self::MAIN_TABLE ." WHERE name != :table_name";
        $statement = $this->database->prepare($SQL);
        $statement->bindValue(":table_name",self::MAIN_TABLE,\PDO::PARAM_STR);
        $statement->execute();
        return array_column($statement->fetchAll(\PDO::FETCH_ASSOC),'name');
    }
    // install new migrations
    public function migrate(){
        $migrationsFiles = scandir(MIGRATIONS_PATH);
        $migrationsFiles = array_diff($migrationsFiles,$this->getInstalledTables());
        foreach ($migrationsFiles as $file){
            if (!($file == '.' || $file == '..')){
                $className = pathinfo($file,PATHINFO_FILENAME);
                $this->log("Start Processing $className Migration");
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'Migrations' . DIRECTORY_SEPARATOR . $className . '.php';
                $classObject = new $className();
                $classObject->up($this->table);
                $this->add($file);
                $this->log("Finished Processing $className Migration");
            }
        }
        if (empty($migrationsFiles) || count($migrationsFiles) ==2){
           $this->log('No New Migrations Founded To Run ..');
        }else{
            $this->log('Migrations Ended Successfully ..');
        }
    }

    // drop and install tables without migration table
    public function fresh(){
        $this->reset();
        $this->migrateMainTable();
        $this->migrate();
        $this->log('All Migrations Regenerated Successfully ..');
    }

    // drop tables
    public function reset(){
        $migrations = $this->database->prepare("show tables");
        $migrations->execute();
        $migrations = $migrations->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($migrations as $migration){
            $this->database->exec("drop table $migration");
        }
        $this->log('All Migrations Has Been Reset Successfully .. ');
    }
    private function migrateMainTable(){
        $mainMigration = new M_2021014115830_createMigrationsTable();
        $mainMigration->up($this->table,self::MAIN_TABLE);
        try {
            $this->add(self::MIGRATION_CLASS);
        }catch (\PDOException $exception){

        }
    }
    private function log($message){
        echo "[".date('Y-m-d H i s',time())."] $message ".PHP_EOL;
    }
}

