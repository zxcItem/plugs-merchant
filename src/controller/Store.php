<?php

namespace plugin\merchant\controller;

use plugin\merchant\model\MerchantStore;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 商户店铺管理
 * Class Store
 * @package plugin\merchant\controller
 */
class Store extends Controller
{
    /**
     * 商户店铺管理
     * @auth true
     * @menu true
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        $this->type = $this->get['type'] ?? 'index';
        MerchantStore::mQuery()->layTable(function () {
            $this->title = '商户店铺管理';
        }, function (QueryHelper $query) {
            $query->where(['deleted'=> 0,'status' => intval($this->type === 'index')]);
            $query->like('name')->dateBetween('create_time');
        });
    }

    /**
     * 添加商户店铺
     * @auth true
     */
    public function add()
    {
        MerchantStore::mForm('form');
    }

    /**
     * 编辑商户店铺
     * @auth true
     */
    public function edit()
    {
        MerchantStore::mForm('form');
    }

    /**
     * 修改商户店铺状态
     * @auth true
     */
    public function state()
    {
        MerchantStore::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除商户店铺
     * @auth true
     */
    public function remove()
    {
        MerchantStore::mDelete();
    }
}