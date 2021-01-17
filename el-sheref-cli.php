<?php

require_once __DIR__ . DIRECTORY_SEPARATOR .'el-sheref'.DIRECTORY_SEPARATOR. 'Core' . DIRECTORY_SEPARATOR . 'configurations.php';

require DATABASE_PATH.DIRECTORY_SEPARATOR.'migration.php';
//$migrationModule = new migration(DB_HOST,DB_USER,DB_PASS,DB_NAME,DB_PORT);
//$migrationModule->fresh();

start();

function start(){

    echo PHP_EOL.'Enter The Number Of Process .....'.PHP_EOL;
    echo PHP_EOL.'[1] Make New Migration File .....'.PHP_EOL;
    echo PHP_EOL.'[2] Run Migrations .....'.PHP_EOL;
    echo PHP_EOL.'[3] Reset Migrations .....'.PHP_EOL;
    echo PHP_EOL.'[4] Refresh Migrations .....'.PHP_EOL;
    echo "=>  ";
    $handle = fopen("php://stdin", "r");
    $process = trim(fgets($handle));
    if ($process == 1){
        echo "Enter The Migration File Name .. ".PHP_EOL;
        echo "=>  ";
        $name = trim(fgets($handle));
        if (strlen($name) > 3) {
            $content = file_get_contents(DATABASE_PATH.DIRECTORY_SEPARATOR.'migration_stub.stub');
            $fileTime=date('YmdHis',time());
            $className = lcfirst(str_replace(' ','',ucwords(strtolower(str_replace('_',' ',$name)))));
            $fileName = 'M_'.$fileTime.'_'.$className;
            $content = str_replace('_temp_',$fileName,$content);
            $content = str_replace('__#__','table_'.time().rand(0,99999),$content);
            file_put_contents(MIGRATIONS_PATH.DIRECTORY_SEPARATOR.$fileName.'.php',$content);
            echo "the migration file $fileName.php created successfully ".PHP_EOL;
        }else{
            echo "the filename must be at least  3 characters".PHP_EOL;
        }
    }elseif ($process == 2){
        $migrationModule = new migration(DB_HOST,DB_USER,DB_PASS,DB_NAME,DB_PORT);
        $migrationModule->migrate();
    }elseif ($process == 3){
        $migrationModule = new migration(DB_HOST,DB_USER,DB_PASS,DB_NAME,DB_PORT);
        $migrationModule->reset();
    }elseif ($process == 4){
        $migrationModule = new migration(DB_HOST,DB_USER,DB_PASS,DB_NAME,DB_PORT);
        $migrationModule->fresh();
    }else{
        echo "Please Enter A Valid Process ".PHP_EOL;
        start();
    }
}

