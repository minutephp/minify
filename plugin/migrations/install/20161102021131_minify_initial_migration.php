<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class MinifyInitialMigration extends AbstractMigration
{
    public function change()
    {
        // Automatically created phinx migration commands for tables from database minute

        // Migration for table m_minified_data
        $table = $this->table('m_minified_data', array('id' => 'minify_data_id'));
        $table
            ->addColumn('created_at', 'datetime', array())
            ->addColumn('version', 'float', array())
            ->addColumn('name', 'string', array('limit' => 255))
            ->addColumn('content', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
            ->addIndex(array('name', 'version'), array('unique' => true))
            ->create();


    }
}