<?php


use Phinx\Migration\AbstractMigration;

class CreateAccessTokenTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('access_tokens', ['id' => false, 'primary_key' => ['id']]);

        $table->addColumn('id', 'integer')
            ->addColumn('token_id', 'string')
            ->addColumn('client_id', 'string')
            ->addColumn('user_id', 'string')
            ->addColumn('scope', 'string')
            ->addColumn('revoked', 'integer', ['default' => '0'])
            ->addColumn('expires', 'datetime')
            ->addColumn('created', 'datetime')
            ->create();
    }
}
