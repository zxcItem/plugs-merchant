<?php

use think\admin\model\SystemBase;
use think\db\exception\DbException;
use think\migration\Migrator;
use think\migration\db\Column;

class InstallMerchant extends Migrator
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
        $this->_createStoreAuth();
    }

    /**
     * 添加商户身份
     * @return void
     */
    private function _createStoreAuth()
    {
        $data = ['type'=>'身份权限','code'=>'store','name'=>'商户','deleted'=>0];
        $model = SystemBase::mk()->where($data)->findOrEmpty();
        $model->isEmpty() && $model->insert($data);
    }
}
