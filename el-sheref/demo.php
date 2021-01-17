<?php

// call the shefoo database files
require_once 'database.php';

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
//$instanceOfYourTable = DB::table('users','id');
#####################################
# to get all records from table use this method
    $data = DB::table('users','id')->all(); // ->first()/->get()
# or
    $data = DB::table('users','id')->get(); // ->first()/->get()
# to select some columns not all  use this method select('id','name',....)
    $data = DB::table('users','id')->select('id','name')->get(); // ->first()/->get()
# to get all columns in this table
    $data = DB::table('users','id')->select('*')->get(); // ->first()/->get()
# to get the first row/record use first method
    $data = DB::table('users','id')->select('*')->first(); // ->first()/->get()
# to search by the primary key use find method and pass the id
    $data = DB::table('users','id')->select('*')->find(1);
#or
    $data = DB::table('users','id')->find(1);
# to search by the any column use findBy method and pass the column name and the value
    $data = DB::table('users','id')->select('*')->findBy('name','hatem');
#or
    $data = DB::table('users','id')->findBy('name','hatem');
# to get the rows count use the count method
    $data = DB::table('users','id')->select('*')->count();
#or
    $data = DB::table('users','id')->count();
# to delete records use delete method and pass the first array of identifiers and second the identifier column
    $data = DB::table('users','id')->delete([1,2,3],'id');
# to create/ add data in table  use create method and pass the first array of values as associated array
    $data = DB::table('users','id')->create(['name'=>'Hatem Mohamed']);
# to update the data in table  use update method and pass the first the identifier value (pk) and the  second array of values as associated array
    $data = DB::table('users','id')->update(10,['name'=>'Hatem Mohamed']);
# to write your custom sql use sql static method
    $data = DB::sql('select * from users');
# to search or write some where statements use where method
    $data = DB::table('users','id')->where('name','LIKE','hat%')->get(); // ->first()/->get()
//    $data = DB::table('users','id')->where('age','>',30)->get(); // ->first()/->get()
    $data = DB::table('users','id')->where('address','=','tanta')->get(); // ->first()/->get()
    $data = DB::table('users','id')->where('degree','>=',50)->get(); // ->first()/->get()
    $data = DB::table('users','id')->where('degree','<=',49)->get(); // ->first()/->get()
    $data = DB::table('users','id')->where('degree','=',100)->first(); // ->first()/->get()

# to search between two values use whereBetween  method
    $data = DB::table('users','id')->whereBetween('birthdate','2010-05-01','2021-01-03')->get(); // ->first()/->get()
    $data = DB::table('users','id')->whereBetween('age',10,30)->first(); // ->first()/->get()
# the negation use whereNotBetween method
    $data = DB::table('users','id')->whereNotBetween('birthdate','2010-05-01','2021-01-03')->get(); // ->first()/->get()
    $data = DB::table('users','id')->whereNotBetween('age',10,30)->first(); // ->first()/->get()

# to search in some values use whereIn method
    $data = DB::table('users','id')->whereIn('age',[1,2,3,4,5])->get();// ->first()/->get()
# the negation use whereNotIn method
    $data = DB::table('users','id')->whereNotIn('age',[1,2,3,4,5])->get();// ->first()/->get()

# use all where method with or instead of and by
# to search or write some where statements use where method
$data = DB::table('users','id')->orWhere('name','LIKE','hat%')->get(); // ->first()/->get()
$data = DB::table('users','id')->orWhere('age','>',30)->get(); // ->first()/->get()
$data = DB::table('users','id')->orWhere('address','=','tanta')->get(); // ->first()/->get()
$data = DB::table('users','id')->orWhere('degree','>=',50)->get(); // ->first()/->get()
$data = DB::table('users','id')->orWhere('degree','<=',49)->get(); // ->first()/->get()
$data = DB::table('users','id')->orWhere('degree','=',100)->first(); // ->first()/->get()

# to search between two values use whereBetween  method
$data = DB::table('users','id')->orWhereBetween('birthdate','2010-05-01','2021-01-03')->get(); // ->first()/->get()
$data = DB::table('users','id')->orWhereBetween('age',10,30)->first(); // ->first()/->get()
# the negation use whereNotBetween method
$data = DB::table('users','id')->orWhereNotBetween('birthdate','2010-05-01','2021-01-03')->get(); // ->first()/->get()
$data = DB::table('users','id')->orWhereNotBetween('age',10,30)->first(); // ->first()/->get()

# to search in some values use whereIn method
$data = DB::table('users','id')->orWhereIn('age',[1,2,3,4,5])->get();// ->first()/->get()
# the negation use whereNotIn method
$data = DB::table('users','id')->orWhereIn('age',[1,2,3,4,5])->get();// ->first()/->get()

# make some joins in more chains

$data = DB::innerJoin('users','posts','*')->on('users.id','=','posts.user_id')
    ->addAnotherInnerJoin('comments')->on('users.id','=','posts.id')->condition('name','=','hatem')
    ->execute();

