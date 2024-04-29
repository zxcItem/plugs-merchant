<?php

declare (strict_types=1);

namespace plugin\merchant;

use think\admin\Plugin;

/**
 * 组件注册服务
 * @class Service
 * @package app\merchant
 */
class Service extends Plugin
{
    /**
     * 定义插件名称
     * @var string
     */
    protected $appName = '商户数据管理';

    /**
     * 定义安装包名
     * @var string
     */
    protected $package = 'xiaochao/plugs-merchant';

    /**
     * 插件服务注册
     * @return void
     */
    public function register(): void
    {

    }

    /**
     * 菜单配置
     * @return array[]
     */
    public static function menu(): array
    {
        // 设置插件菜单
        $code = app(static::class)->appCode;
        // 设置插件菜单
        return [
            [
                'name' => '商户管理',
                'subs' => [
                    ['name' => '商户管理', 'icon' => 'layui-icon layui-icon-home', 'node' => "{$code}/store/index"]
                ],
            ],
        ];
    }
}