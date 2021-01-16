<?php

// call the shefoo database files
require_once 'Database/el-sheref.php';

#####################################
# how to use the package
# ex:
#   tableName  => users
#   primaryKey => id
#####################################
/*
 * first you must make id for your all tables in database while creating your schema
 * determine the table name that you want to use
 * determine the primary key of this table
 * */

#####################################
# get instance from your table
$instanceOfYourTable = DB::table('users','id');
#####################################

# to get all records from table use this method
$data = $instanceOfYourTable->all();
# or
$data = $instanceOfYourTable->get();
# to select some columns not all  use this method select('id','name',....)
$data = $instanceOfYourTable->select('id','name')->get();
# to get all columns in this table
$data = $instanceOfYourTable->select('*')->get();
# to get the first row/record use first method
$data = $instanceOfYourTable->select('*')->first();
# to search by the primary key use find method and pass the id
$data = $instanceOfYourTable->select('*')->find(1);
#or
$data = $instanceOfYourTable->find(1);
# to search by the any column use findBy method and pass the column name and the value
$data = $instanceOfYourTable->select('*')->findBy('name','hatem');
#or
$data = $instanceOfYourTable->findBy('name','hatem');
# to get the rows count use the count method
$data = $instanceOfYourTable->select('*')->count();
#or
$data = $instanceOfYourTable->count();