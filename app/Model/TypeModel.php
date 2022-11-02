<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use App\Model\TypeModel;
use Log;
use Illuminate\Support\Facades\DB;
/**
 * 
 */
class TypeModel extends BaseModel
{

    protected $table = 'type';
    public $timestamps = false;

    public function getList($where,$field=['*'],$page='1',$limit='99',$order='id',$desc='desc'){
        $data = $this->where($where)->orderBy($order,$desc)->paginate($limit,$field,'page',$page)->toArray();
        return $data;
    }
}
