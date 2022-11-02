<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use App\Model\XiaofeiModel;
use Log;
use Illuminate\Support\Facades\DB;
/**
 * 
 */
class XiaofeiModel extends BaseModel
{

    protected $table = 'xiaofei';
    public $timestamps = false;
    public function getList($where,$field=['*'],$page='1',$limit='10',$order='id',$desc='desc'){
        $data = $this->where($where)->orderBy($order,$desc)->paginate($limit,$field,'page',$page)->toArray();
        return $data;
    }
    public function addxf($data){
        $res = $this->insert($data);
        return $res;
    }
}
