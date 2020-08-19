<?php

namespace app\model;

use think\Model;

class InviteRecord extends Model
{
    public static function getInviteRecord($where)
    {
        $invite_list = self::where($where)->with(['toUserInfo' => function ($query){
            $query->field('id, username, attr, mobile, email, status');
        }])->select();
        return $invite_list;
    }

    /**
     * 用户表关联
     *
     * @return void
     */
    public function toUserInfo()
    {
    	return $this->hasOne(User::class, 'id' ,'to_user_id');
    }
}