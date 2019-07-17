<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2019/7/1
 * Time: 11:05
 */

namespace app\index\controller;

use app\common\controller\Base;
use app\common\model\Teacher;
use function PHPSTORM_META\type;
use think\facade\Request;

use app\common\model\Student;
use think\facade\Session;

use app\common\model\Labs;

class User extends Base
{
    public function login()
    {
        return $this->fetch();
    }

    public function stulogin()
    {
        $this->stuHaveLogin();
        return $this->fetch();
    }

    public function teachlogin()
    {
        $this->teachHaveLogin();
        return $this->fetch();
    }

    public function register()
    {
        return $this->fetch();
    }

    public function insert()
    {
        if (Request::isAjax()) {
            $data = Request::post();    //得到要验证的数据
            $rule = 'app\common\validate\User'; //自定义的验证规则
            //开始验证
            $res = $this->validate($data, $rule);
            if ($res !== true) {
                return ['success' => -1, 'message' => $res];
            } else {
                if ($user = Student::create($data)) {
                    Session::set('num', $user->num);
                    Session::set('name', $user->name);
                    return ['success' => 1, 'message' => '注册成功'];
                } else {
                    return ['success' => 0, 'message' => '注册失败'];
                }
            }
        } else {
            $this->error('请求错误', 'register');
        }
    }

    public function loginCheck()
    {
        if (Request::isAjax()) {
            //验证客户端提交的数据
            $data = Request::post();    //得到要验证的数据
            $rule = [
                'num|学号'      => 'require|length:8,11|number',
                'password|密码' => 'require|length:6,20|alphaNum',
            ]; //自定义的验证规则
            //开始验证
            $res = $this->validate($data, $rule);
            if ($res !== true) {
                return ['success' => -1, 'message' => $res];
            } else {
                //查询操作
                $result = Student::get(function ($query) use ($data) {
                    //$query为当前变量；在闭包中使用外部变量用use
                    $query->where('num', $data['num'])
                        ->where('password', $data['password']);
                });

                //模型无论使用 get 还是 find 方法查询，返回的是都当前模型的对象实例，如果查询数据不存在，则返回Null ，
                if ($result === null) {
                    return ['success' => 0, 'message' => '学号或密码不正确，请检查'];
                } else {
                    Session::set('num', $result['num']);
                    Session::set('name', $result['name']);
                    return ['success' => 1, 'message' => '登陆成功'];
                }
            }
        } else {
            $this->error('请求错误', 'login');
        }
    }

    public function Passwd()
    {
        $this->isLogin();
        return $this->fetch();
    }

    public function doPasswd()
    {
        if (Request::isAjax()) {
            //验证客户端提交的数据
            $data = Request::post();    //得到要验证的数据
            $rule = [
                'prepd|原密码'      => 'require|length:6,20|alphaNum',
                'newpdagain|新密码' => 'require|length:6,20|alphaNum|confirm:newpd',
            ]; //自定义的验证规则
            //开始验证
            $res = $this->validate($data, $rule);
            if ($res !== true) {
                return ['success' => -1, 'message' => $res];
            } else {
                //查询操作
                $result = Student::get(function ($query) use ($data) {
                    //$query为当前变量；在闭包中使用外部变量用use
                    $query->where('password', $data['prepd']);
                });

                //模型无论使用 get 还是 find 方法查询，返回的是都当前模型的对象实例，如果查询数据不存在，则返回Null ，
                if ($result === null) {
                    return ['success' => 0, 'message' => '原密码不正确，请检查后输入'];
                } else {
                    $num = Session::get('num');
                    if (Student::where('num', $num)->update(['password' => $data['newpd']])) {
//                        Session::delete('num');
//                        Session::delete('name');
                        return ['success' => 1, 'message' => '修改成功!'];
                    } else {
                        $this->error('修改失败！');
                    }

                }
            }
        }
    }

    public function Logout()
    {
        Session::delete('num');
        Session::delete('name');
        $this->success('退出成功', 'user/stulogin');
    }

    public function reserve()
    {
        // 预约
        if (Request::isAjax()) {
//            //验证客户端提交的数据
            $data = Request::post();    //得到要验证的数据
            $today = date('Y-m-d', time());
            if (empty($data['begin_time']) || empty($data['end_time'])) {
                return ['success' => 0, 'message' => '请先选择预约时间'];
            }
            if ($today > $data['begin_time']) {
                return ['success' => 0, 'message' => '请预约今天之后的日期'];
            }
            // 学生表判断并记录记录预约信息
            $userNum = Session::get('num');
            $labNum  = Request::param('num');

            $user = Student::get(function ($query) use ($userNum) {
                $query->where('num', $userNum);
            });
            if (is_null($user->lab_one)) {
                $user->lab_one = $labNum;
                $user->replace()->save();
            } elseif (is_null($user->lab_two)) {
                $user->lab_two = $labNum;
                $user->replace()->save();
            } else {
                return ['success' => 1, 'message' => '您已经预约两个实验室啦！'];
            }

            // 隐藏域 post 发送实验室编号
            // 根据编号查询实验室
            // 插入数据
            $lab             = Labs::get(function ($query) use ($labNum) {
                $query->where('num', $labNum);
            });
            $lab->applicant  = Session::get('num');
            $lab->begin_time = $data['begin_time'];
            $lab->end_time   = $data['end_time'];
            $lab->is_order   = 1;
            $lab->replace()->save();
            $TeacherNum = $lab->charge_person;
            $teacher= Teacher::get(function ($query) use ($TeacherNum) {
                $query->where('num', $TeacherNum);
            });
            $to = $teacher->email;
            //$to = get('email');
            $subject = "Lapoi-实验室待处理通知";
            $message = "您有一个待处理的实验室审核请求，请及时进入管理页面处理！(系统自动发送请勿回复)";
            $from = "Lapoi@163.com";
            $headers = "From: $from";
            mail($to, $subject,$message,$headers);
            return ['success' => 1, 'message' => '预定成功！待老师审核通过后即可使用'];

        } else {
            $this->error('请求错误', 'login');
        }
    }

    public function Order()
    {
        // 预约列表页面
        $this->isLogin();
        $userNum = Session::get('num');

        $user = Student::get(function ($query) use ($userNum) {
            $query->where('num', $userNum);
        });

        $labOne = $user->lab_one;
        $labTwo = $user->lab_two;

        if (!is_null($labOne)) {
            $lab_one = Labs::get(function ($query) use ($labOne) {
                $query->where('num', $labOne);
            });
            $this->assign('lab_one', $lab_one);
        } else {
            $this->assign('lab_one', null);
        }
        if (!is_null($labTwo)) {
            $lab_two = Labs::get(function ($query) use ($labTwo) {
                $query->where('num', $labTwo);
            });
            $this->assign('lab_two', $lab_two);
        } else {
            $this->assign('lab_two', null);

        }
        return $this->fetch();
    }

    public function doCancel()
    {
        $lab_one = Request::param('one_num');
        $lab_two = Request::param('two_num');

        $userNum = Session::get('num');
        $user    = Student::get(function ($query) use ($userNum) {
            $query->where('num', $userNum);
        });
        if (!is_null($lab_one)) {
            $lab             = Labs::get(function ($query) use ($lab_one) {
                $query->where('num', $lab_one);
            });
            // 清空实验室预约信息
            $lab->applicant  = null;
            $lab->begin_time = null;
            $lab->end_time   = null;
            $lab->is_order   = 0;
            $lab->replace()->save();
            // 清空个人预约信息
            $user->lab_one = null;
            $user->replace()->save();
            $this->success('取消成功');
        }
        if (!is_null($lab_two)) {
            $lab             = Labs::get(function ($query) use ($lab_two) {
                $query->where('num', $lab_two);
            });
            // 清空实验室预约信息
            $lab->applicant  = null;
            $lab->begin_time = null;
            $lab->end_time   = null;
            $lab->is_order   = 0;
            $lab->replace()->save();
            // 清空个人预约信息
            $user->lab_two = null;
            $user->replace()->save();
            $this->success('取消成功');
        }

    }


}