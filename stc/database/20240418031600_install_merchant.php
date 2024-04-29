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
        $this->_createStoreUser();
        $this->_createMerchantStore();
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

    /**
     * 增加管理员管理商户
     * @return void
     */
    private function _createStoreUser()
    {
        // 当前数据表
        $table = 'system_user';
        // 检查与更新数据表
        $this->table($table)->hasColumn('store') || $this->table($table)
            ->addColumn('store', 'biginteger', ['default' => 0,'after' => 'describe', 'comment' => '管理商户'])
            ->update();
    }


    /**
     * 商户数据
     * @return void
     */
    private function _createMerchantStore()
    {
        // 当前数据表
        $table = 'merchant_store';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '商户-店铺',
        ])
            ->addColumn('cover', 'string', ['limit' => 180, 'default' => '', 'null' => true, 'comment' => '店铺封面'])
            ->addColumn('name', 'string', ['limit' => 180, 'default' => '', 'null' => true, 'comment' => '店铺名称'])
            ->addColumn('banner', 'text', ['default' => '', 'null' => true, 'comment' => '展示图片'])
            ->addColumn('remark', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '简介描述'])
            ->addColumn('region_prov', 'string', ['limit' => 180, 'default' => '', 'null' => true, 'comment' => '所在地区'])
            ->addColumn('region_city', 'string', ['limit' => 180, 'default' => '', 'null' => true, 'comment' => '所在城市'])
            ->addColumn('region_area', 'string', ['limit' => 180, 'default' => '', 'null' => true, 'comment' => '所在区域'])
            ->addColumn('region_addr', 'string', ['limit' => 180, 'default' => '', 'null' => true, 'comment' => '详细地址'])
            ->addColumn('lat', 'string', ['limit' => 30, 'default' => '', 'null' => true, 'comment' => '店铺经度'])
            ->addColumn('lng', 'string', ['limit' => 30, 'default' => '', 'null' => true, 'comment' => '店铺维度'])
            ->addColumn('phone', 'string', ['limit' => 180, 'default' => '', 'null' => true, 'comment' => '客服电话'])
            ->addColumn('sort', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '排序权重'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '使用状态'])
            ->addColumn('deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态'])
            ->addColumn('create_time', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true, 'comment' => '更新时间'])
            ->addIndex('sort', ['name' => 'idx_merchant_store_sort'])
            ->addIndex('status', ['name' => 'idx_merchant_store_status'])
            ->addIndex('deleted', ['name' => 'idx_merchant_store_deleted'])
            ->create();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 11, 'identity' => true]);
    }
}
