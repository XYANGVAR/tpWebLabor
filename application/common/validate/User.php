<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2019/4/8
 * Time: 21:15
 */

namespace app\common\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'num|学号' =>'require|length:8,11|number|unique:students',
        'name|姓名'  => 'require|length:2,3|chsAlphaNum',
        'email|邮箱'  => 'require|email|unique:students',
        'password|密码'  => 'require|length:6,20|alphaNum',
    ];
}