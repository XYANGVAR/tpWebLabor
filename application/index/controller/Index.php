<?php
namespace app\index\controller;

use app\common\controller\Base;
use app\common\model\Labs;
use think\facade\Request;
use app\common\model\Student;

class Index extends Base
{
    public function index()
    {
        $this->isLogin();

//      // 检测所有预约超时的实验室，并更新相应字段
        $today = date('Y-m-d', time());
        $Lablist = Labs::where('end_time', '<',$today)->all();
        foreach($Lablist as $key=>$lab){
            // 得到申请该实验室的用户,并更新其使用的实验室字段
            $userNum = $lab->applicant;
            $labNum = $lab->num;
            $user = Student::get(function ($query) use ($userNum) {
                $query->where('num', $userNum);
            });
            if ($user->lab_one == $labNum) {
                $user->lab_one = null;
            }
            if ($user->lab_two == $labNum) {
                $user->lab_two = null;
            }
            $user->replace()->save();
            //更新所有的实验室字段
            $lab->applicant  = null;
            $lab->begin_time = null;
            $lab->end_time   = null;
            $lab->is_order   = 0;
            $lab->replace()->save();
        }

        return $this->fetch();
    }

    public function stuspace()
    {
        $this->isLogin();
        return $this->fetch();
    }

    public function showlabs()
    {
        $this->isLogin();
        $labList = Labs::all();
        $this->assign('labList', $labList);
        return $this->fetch();
    }

    public function details()
    {
        $this->isLogin();
        $labNum = Request::param('num');
        $lab   = Labs::get(function ($query) use ($labNum) {
            $query->where('num', $labNum);
        });
//        与上述使用闭包有相同效果
//        $lab = Article::where('num', $labNum);
//        $lab = Article::get($lab);
        $this->assign('lab', $lab);
        return $this->fetch();
    }
}
