<?php

namespace plugin\merchant\controller;

use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\admin\model\SystemAuth;
use think\admin\model\SystemBase;
use think\admin\model\SystemUser;
use think\admin\service\AdminService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 商户员工管理
 * @class User
 * @package app\merchant\controller
 */
class User extends Controller
{
    /**
     * 商户员工管理
     * @auth true
     * @menu true
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        SystemUser::mQuery()->layTable(function () {
            $this->title = '商户员工管理';
            $this->bases = SystemBase::items('身份权限');
        }, function (QueryHelper $query) {
            // 数据列表搜索过滤
            $query->equal('store,status,usertype')->dateBetween('login_at,create_time');
            $query->like('username|nickname#username,contact_phone#phone,contact_mail#mail');
        });
    }

    /**
     * 添加商户员工
     * @auth true
     */
    public function add()
    {
        SystemUser::mForm('form');
    }

    /**
     * 编辑商户员工
     * @auth true
     */
    public function edit()
    {
        SystemUser::mForm('form');
    }

    /**
     * 修改商户员工密码
     * @auth true
     */
    public function pass()
    {
        $this->_applyFormToken();
        if ($this->request->isGet()) {
            $this->verify = false;
            SystemUser::mForm('pass');
        } else {
            $data = $this->_vali([
                'id.require'                  => '用户ID不能为空！',
                'password.require'            => '登录密码不能为空！',
                'repassword.require'          => '重复密码不能为空！',
                'repassword.confirm:password' => '两次输入的密码不一致！',
            ]);
            $user = SystemUser::mk()->findOrEmpty($data['id']);
            if ($user->isExists() && $user->save(['password' => md5($data['password'])])) {
                sysoplog('系统用户管理', "修改用户[{$data['id']}]密码成功");
                $this->success('密码修改成功，请使用新密码登录！', '');
            } else {
                $this->error('密码修改失败，请稍候再试！');
            }
        }
    }

    /**
     * 表单数据处理
     * @param array $data
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    protected function _form_filter(array &$data)
    {
        if ($this->request->isPost()) {
            // 检查资料是否完整
            empty($data['username']) && $this->error('登录账号不能为空！');
            if ($data['username'] !== AdminService::getSuperName()) {
                empty($data['authorize']) && $this->error('未配置权限！');
            }
            // 处理上传的权限格式
            $data['authorize'] = arr2str($data['authorize'] ?? []);
            if (empty($data['id'])) {
                // 检查账号是否重复
                $map = ['username' => $data['username'], 'is_deleted' => 0];
                if (SystemUser::mk()->where($map)->count() > 0) {
                    $this->error("账号已经存在，请使用其它账号！");
                }
                // 新添加的用户密码与账号相同
                $data['password'] = md5($data['username']);
            } else {
                unset($data['username']);
            }
        } else {
            // 权限绑定处理
            $data['authorize'] = str2arr($data['authorize'] ?? '');
            $this->auths = SystemAuth::items();
            $this->bases = SystemBase::items('身份权限');
            $this->super = AdminService::getSuperName();
            if(empty($data['store'])) $data['store'] = $this->request->get('store');
        }
    }

    /**
     * 修改商户员工状态
     * @auth true
     */
    public function state()
    {
        SystemUser::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除商户员工
     * @auth true
     */
    public function remove()
    {
        SystemUser::mDelete();
    }
}
