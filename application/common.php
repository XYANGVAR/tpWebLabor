<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use app\common\model\Teacher;
use app\common\model\Student;

if (!function_exists('getTeacherName')) {
    function getTeacherName($charge_person)
    {
        return Teacher::where('num', $charge_person)->value('name');
    }
}

if (!function_exists('getTeacherMail')) {
    function getTeacherMail($charge_person)
    {
        return Teacher::where('num', $charge_person)->value('email');
    }
}

if (!function_exists('getStudentName')) {
    function getStudentName($applicant)
    {
        return Student::where('num', $applicant)->value('name');
    }
}