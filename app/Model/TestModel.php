<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use App\Model\TestModel as TES;
use Log;
use DB;
/**
 * 
 */
class TestModel extends BaseModel
{
	
	public $timestamps = false;
    protected $table = 'user_info';
    public static function getInfoByUid($uid)
    {
        return TES::where(['id' => $uid])->pluck('username');
    }
    public static function sendMsg($data)
    {
        return TES::insert($data);

    }
}
