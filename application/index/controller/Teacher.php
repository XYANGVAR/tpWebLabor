<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2019/7/1
 * Time: 11:05
 */

namespace app\index\controller;

use app\common\controller\Base;
use think\facade\Request;
use app\common\model\Teacher as TeacherModel;
use think\facade\Session;
use app\common\model\Labs;
use app\common\model\History;
use app\common\model\Student;

class Teacher extends Base
{
    public function loginCheck()
    {
        if (Request::isAjax()) {
            //验证客户端提交的数据
            $data = Request::post();    //得到要验证的数据
            $rule = [
                'num|工号'      => 'require|length:8,11|number',
                'password|密码' => 'require|length:6,20|alphaNum',
            ]; //自定义的验证规则
            //开始验证
            $res = $this->validate($data, $rule);
            if ($res !== true) {
                return ['success' => -1, 'message' => $res];
            } else {
                //查询操作
                $result = TeacherModel::get(function ($query) use ($data) {
                    //$query为当前变量；在闭包中使用外部变量用use
                    $query->where('num', $data['num'])
                        ->where('password', $data['password']);
                });

                //模型无论使用 get 还是 find 方法查询，返回的是都当前模型的对象实例，如果查询数据不存在，则返回Null ，
                if ($result === null) {
                    return ['success' => 0, 'message' => '工号或密码不正确，请检查'];
                } else {
                    Session::set('teach_num', $result['num']);
                    Session::set('teach_name', $result['name']);
                    return ['success' => 1, 'message' => '登陆成功'];
                }
            }
        } else {
            $this->error('请求错误', 'login');
        }
    }

    public function teachspace()
    {
        $this->isTeachLogin();
        return $this->fetch();
    }

    public function addlab()
    {
        $this->isTeachLogin();
        $num = Session::get('teach_num');
        //获取当前用户的实验室
        $lab = Labs::where('charge_person', $num)->find();
        $this->assign('lab', $lab);
        return $this->fetch();
    }

    public function droplab()
    {
        $this->isTeachLogin();
        $num = Session::get('teach_num');
        //获取当前用户的实验室
        $lab = Labs::where('charge_person', $num)->find();
        $this->assign('lab', $lab);
        return $this->fetch();
    }

    public function insertlab()
    {
        if (Request::isPost()) {
            $data = Request::post();
            if ($lab = Labs::create($data)) {
                $lab->charge_person = Session::get('teach_num');
                $lab->replace()->save();

                // 教师更新所属实验室
                $teachNum     = Session::get('teach_num');
                $teacher      = TeacherModel::get(function ($query) use ($teachNum) {
                    $query->where('num', $teachNum);
                });
                $teacher->lab = $data['num'];
                $teacher->replace()->save();

                $this->success('实验室添加成功');
            } else {
                $this->error('添加失败！');
            }
        }
    }

    public function doDelete()
    {
        //获取要删除的实验室编号
        $num = Request::param('num'); //不指明则返回数组
        $lab = Labs::get(function ($query) use ($num) {
            $query->where('num', $num);
        });
        //执行删除操作
        if ($lab) {
            if (!is_null($lab->applicant)) {
                $stuNum = $lab->applicant;
                $student = Student::get(function ($query) use ($stuNum) {
                    $query->where('num', $stuNum);
                });
                // 学生预约记录清除
                $lab_one = $student->lab_one;
                $lab_two = $student->lab_two;
                if ($lab_one == $num) {
                    $student->lab_one = null;
                }
                if ($lab_two == $num) {
                    $student->lab_two = null;
                }
                $student->save();
                // 教师所属记录清除
            }
            $teachNum = $lab->charge_person;
            $teacher = TeacherModel::get(function ($query) use ($teachNum) {
                $query->where('num', $teachNum);
            });
                $teacher->lab = null;
                $teacher -> save();
                Labs::where('num', $num)->delete();
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
    }


    public function Logout()
    {
        Session::delete('teach_num');
        Session::delete('teach_name');
        $this->success('退出成功', 'user/teachlogin');
    }

    public function Passwd()
    {
        $this->isTeachLogin();
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
                $result = TeacherModel::get(function ($query) use ($data) {
                    //$query为当前变量；在闭包中使用外部变量用use
                    $query->where('password', $data['prepd']);
                });

                //模型无论使用 get 还是 find 方法查询，返回的是都当前模型的对象实例，如果查询数据不存在，则返回Null ，
                if ($result === null) {
                    return ['success' => 0, 'message' => '原密码不正确，请检查后输入'];
                } else {
                    $num = Session::get('teach_num');
                    if (TeacherModel::where('num', $num)->update(['password' => $data['newpd']])) {
//                        Session::delete('teach_num');
//                        Session::delete('teach_name');
                        return ['success' => 1, 'message' => '修改成功'];
                    } else {
                        $this->error('修改失败！');
                    }

                }
            }
        }
    }

    public function applylist()
    {
        $this->isTeachLogin();
        $num = Session::get('teach_num');
        //获取当前用户的实验室
        $lab = Labs::where('charge_person', $num)->find();
        $this->assign('lab', $lab);
        return $this->fetch();
    }

    public function doReply()
    {
        //查询实验室
        //老师同意则置为2，否则变为0
        $resultId = Request::param('result');
        $labNum   = Request::param('num');
        $lab = Labs::get(function ($query) use ($labNum) {
            $query->where('num', $labNum);
        });
        // 插入审核记录
        $data = new History([
            'operator' => $lab->charge_person,
            'lab_name'=>$lab->name,
            'begin_time'=>$lab->begin_time,
            'end_time'=>$lab->end_time,
            'result'=>$resultId,
            'applicant'=>$lab->applicant
        ]);
        $data->save();

        $stuNum = $lab->applicant;
        $stuNum= Student::get(function ($query) use ($stuNum) {
            $query->where('num', $stuNum);
        });
        $to = $stuNum->email;

        if ($resultId == 1) {
            $lab->is_order = 2;
            $subject = "Lapoi-实验审核结果";
            $message = "您的实验室申请已通过！(系统自动发送请勿回复)";
            $from = "Lapoi@163.com";
            $headers = "From: $from";
            mail($to, $subject,$message,$headers);
        }
        if ($resultId == 0) {
            //拒绝则清空实验室和学生记录
            $stuNum  = $lab->applicant;
            $student = Student::get(function ($query) use ($stuNum) {
                $query->where('num', $stuNum);
            });
            // 学生预约记录清除
            $lab_one = $student->lab_one;
            $lab_two = $student->lab_two;
            if ($lab_one == $labNum) {
                $student->lab_one = null;
            }
            if ($lab_two == $labNum) {
                $student->lab_two = null;
            }
            $student->save();

            $subject = "Lapoi-实验室审核结果";
            $message = "您的实验室申请已被拒绝！(系统自动发送请勿回复)";
            $from = "Lapoi@163.com";
            $headers = "From: $from";
            mail($to, $subject,$message,$headers);
            // 实验室预约记录清除
            $lab->applicant  = null;
            $lab->begin_time = null;
            $lab->end_time   = null;
            $lab->is_order   = 0;
        }
        $lab->replace()->save();
        $this->success('操作成功！');

    }

    public function replylist()
    {
        $this->isTeachLogin();
        $num = Session::get('teach_num');
        $replyList = History::where('operator', $num)->select();
        $this->assign('replyList', $replyList);
        return $this->fetch();
    }


}