<?php
namespace app\common\model;
use think\Model;

class Student extends Model
{
    protected $pk = 'id';
    protected $table = 'students';   //模型绑定的数据表
//    protected $autoWriteTimestamp = true;   //开机自动记录时间戳（10位，1970.1.1开始,秒数）
//    protected $createTime = 'create_time';
//    protected $updateTime = 'update_time';
}