<?php
class FinalView_Doctrine_Migration_Process extends Doctrine_Migration_Process
{
    public function processCreatedTable(array $table)
    {
        $conn = Doctrine_Manager::connection();
        $conn->export->createTable($table['tableName'], $table['fields'], $table['options']);
    }         
}