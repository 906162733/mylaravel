<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    public $timestamps = false;

    /**
     * 查询单条
     */
    public function findData($where,$field=['*'],$order='id',$desc = 'desc'){
        $data = $this->where($where)->orderBy($order,$desc)->first($field);
        if(!empty($data)) $data = $data->toArray();
        return $data;
    }

    /**
     * 分页
     * @param [$desc 参数已弃用]
     */
    public function selDataPage($where,$field=['*'],$page='1',$limit='10',$order='id desc',$desc=''){
        $data = $this->where($where)->orderByRaw($order)->paginate($limit,$field,'page',$page)->toArray();
        return $data;
    }

    /**
     * 查询多个
     */
    public function selData($where,$field=['*'],$order='id',$desc = 'desc'){
        $data = $this->where($where)->orderBy($order,$desc)->get($field)->toArray();
        return $data;
    }

    /**
     * 查询多个
     */
    public function selDataNew($where,$field=['*'],$order='id desc'){
        $data = $this->where($where)->orderByRaw($order)->get($field)->toArray();
        return $data;
    }

    public function selDataSum($where,$field=['*'],$order='id',$desc = 'desc',$group = ''){
        $data = $this->where($where)->orderBy($order,$desc)->groupBy($group)->get($field)->toArray();
        return $data;
    }

    public function selDataInSum($where,$column,$wherein,$field=['*'],$order='id',$desc = 'desc',$group = ''){
        $data = $this->where($where)->whereIn($column,$wherein)->orderBy($order,$desc)->groupBy($group)->get($field)->toArray();
        return $data;
    }

    public function selDatIn($where,$column,$wherein,$field=['*'],$order='id',$desc = 'desc'){
        if(!$wherein) return [];
        $data = $this->where($where)->whereIn($column,$wherein)->orderBy($order,$desc)->get($field)->toArray();
        return $data;
    }

    public function selDatNotIn($where,$column,$wherein,$field=['*'],$order='id',$desc = 'desc'){
        $data = $this->where($where)->whereNotIn($column,$wherein)->orderBy($order,$desc)->get($field)->toArray();
        return $data;
    }
    /**
     * 分页
     */
    public function selDataInPage($where,$column,$wherein,$field=['*'],$page='1',$limit='10',$order='id',$desc='desc'){
        $data = $this->where($where)->whereIn($column,$wherein)->orderBy($order,$desc)->paginate($limit,$field,'page',$page)->toArray();
        return $data;
    }

    /**
     * 添加
     */
    public function addData($data){
        $res = $this->insert($data);
        return $res;
    }
    /**
     * 添加,并且返回ID
     */
    public function addDataId($data){
        $res = $this->insertGetId($data);
        return $res;
    }
    /**
     * 编辑
     */
    public function upInfo($where,$data,$wherein=[]){
        $res = $this->where($where);
        if(!empty($wherein)) {
            foreach($wherein as $k=>$r) $res = $res->whereIn($k,$r);
        }
//        event(new \App\Events\TableEvent($this->table,$data,$where));
        $res = $res->update($data);
        return $res;
    }

    /**
     * 删除
     */
    public function delInfo($where){
        $res = $this->where($where)->delete();
        return $res;
    }

    /**
     *  编辑
     *  //$e->getMessage()
     */
    public function edit($data){
        DB::beginTransaction();
        try{
            $res = $this->insert($data);
            DB::commit();
        }catch(\Exception $e){
            $res = false;
            DB::rollBack();
        }
        return $res;
    }

    public function getValue($where,$value){
        $res = $this->where($where)->value($value);
        return $res;
    }

    public function getValueOrder($where,$value,$order='id desc'){
        $res = $this->where($where)->orderByRaw($order)->value($value);
        return $res;
    }

    public function getPageDateTian($where,$whereIn=[],$field=['*'],$page='1',$limit=15,$order='create_time')
    {
        $a = $this->where($where);
        if(!empty($whereIn)){
            foreach($whereIn as $k=>$v){
                $a = $a->whereIn($v[0],$v[1]);
            }
        }
        $a = $a->orderBy($order,'desc')->paginate($limit,$field,'page',$page)->toarray();
        return $a;
    }

    public function getPageDateTianNew($where,$whereIn=[],$field=['*'],$page='1',$limit=15,$order='create_time',$groupBy='member_id')
    {
        $a = $this->where($where);
        if(!empty($whereIn)){
            foreach($whereIn as $k=>$v){
                $a = $a->whereIn($v[0],$v[1]);
            }
        }
        $a = $a->orderBy($order,'desc')->groupBy($groupBy)->paginate($limit,$field,'page',$page)->toarray();
        return $a;
    }

    public function countData($where){
        $res = $this->where($where)->count();
        return $res;
    }

    public function countDataGroupBy($where,$groupBy='member_id'){
        $res = $this->where($where)->groupBy($groupBy)->get()->toArray();
        return $res;
    }

    public function selDataInCount($where,$column,$wherein){
        if(!$wherein) return '0';
        $data = $this->where($where)->whereIn($column,$wherein)->count();
        return $data;
    }

    public function selWhereInCount($where,$whereIn=[])
    {
        $a = $this->where($where);
        if(!empty($whereIn)){
            foreach($whereIn as $k=>$v){
                $a = $a->whereIn($v[0],$v[1]);
            }
        }
        $a = $a->count();
        return $a;
    }



    /**
     * 分页
     */
    public function upInData($where,$column,$wherein,$data){
        $data = $this->where($where)->whereIn($column,$wherein)->update($data);
        return $data;
    }


}


