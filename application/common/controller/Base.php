<?php

namespace app\common\controller;
use think\Controller;
use think\facade\Session;

class Base extends Controller
{
    public function initialize()
    {
        ;
    }

    //每个控制器都要用到的方法，检查用户是否登陆
    public function stuHaveLogin()
    {
        if (Session::has('num')) {
            $this->error('您已经登陆了', 'index/index');
        }
    }
    public function teachHaveLogin()
    {
        if (Session::has('teach_num')) {
            $this->error('您已经登陆了', 'teacher/teachspace');
        }
    }

    //虽然重复度很高,但是与上述方法不能写在一起，不然会陷入死循环\

    public function isLogin()
    {
        if (!Session::has('num')) {
            //由于这个错误跳转到login,而每次login时会检查是否重复登陆，故与上述函数不能写在一起
            $this->error('您还没有登陆，请登陆后操作', 'user/stulogin');
        }
    }

    public function isTeachLogin()
    {
        if (!Session::has('teach_num')) {
            //由于这个错误跳转到login,而每次login时会检查是否重复登陆，故与上述函数不能写在一起
            $this->error('您还没有登陆，请登陆后操作', 'user/teachlogin');
        }
    }

}