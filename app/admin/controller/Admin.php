<?php 
namespace app\admin\controller;

use app\model\Admin as AdminModel;

class Admin extends Auth
{
    /**
     * 管理员列表
     *
     * @return void
     */
    public function adminList()
    {   // 搜索条件
        $id        = input('id'); // 管理员id
        $real_name = input('real_name'); // 管理员真实姓名
        $mobile    = input('mobile'); // 管理员手机号

        $where[] = ['delete_time', '=', null];
        if ($id) {
            $where[] = ['id', '=', $id];
        }
        if ($real_name) {
            $where[] = ['real_name', 'like', '%'.$real_name.'%'];
        }
        if ($mobile) {
            $where[] = ['mobile', 'like', '%'.$mobile.'%'];
        }
        $list = AdminModel::getList($where);
        success('管理员列表', $list);
    }

    /**
     * 添加账号
     *
     * @return void
     */
    public function create()
    {
        $admin_data = [
            'username'         => input('username'),
            'password'         => trim(input('password')),
            'mobile'           => input('mobile'),
            'email'            => input('email'),
            'real_name'        => input('real_name'),
            'personal_profile' => input('personal_profile')
        ];
        $admin_data = array_filter($admin_data);

        $res = AdminModel::createAdminAccount($admin_data);
        if ($res === true) {
            success('添加成功');
        } else {
            error('添加失败');
        }
    }

    /**
     * 删除账号
     *
     * @return void
     */
    public function delete()
    {
        $admin_id = input('id');
        $res = AdminModel::deleteAdminAccount($admin_id);
        if ($res === true) {
            success('删除成功');
        } else {
            error('删除失败');
        }
    }
}