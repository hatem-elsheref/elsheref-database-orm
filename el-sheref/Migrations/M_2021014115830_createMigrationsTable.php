<?php


class M_2021014115830_createMigrationsTable{
    public function up(Blueprint $table,$tableName='migrations'){
        $table->start($tableName,'InnoDB');
        $table->id();
        $table->string('name',[Blueprint::UNIQUE]);
        $table->created_at();
        $table->end();
    }
    public function down(Blueprint $table){
        $table->dropTable('migrations');
    }
}