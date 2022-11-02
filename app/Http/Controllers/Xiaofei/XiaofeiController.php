<?php
namespace App\Http\Controllers\Xiaofei;

use App\Model\XiaofeiModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\TypeModel;
use App\Model\NameModel;
class XiaofeiController extends Controller
{
    public function __construct()
    {
        $this->XiaofeiModel = new XiaofeiModel();
        $this->TypeModel = new TypeModel();
        $this->NameModel = new NameModel();
    }
    /**
     * @param Request $request
     */
    public function index_view(Request $request){

        
        $list = $this->TypeModel->getList([],['*']);
        $name = $this->NameModel->getList([],['*']);
        $res['type'] = $list['data'];
        $res['name'] = $name['data'];

        $data = $this->XiaofeiModel->selData([]);

        $gong = 0 ;
        foreach ($data as $key => $value) {
            $gong += $value['price'];
        }
        $res['gong'] =$gong;
        return view("Xiaofei.index",$res);
    }

    public function index_data(Request $request){
        $data = $request->input();
        $list_where = [];
        if(!empty($data['project'])){
            $list_where[] = ['project','like','%'.$data['project'].'%'];
        }
        if(!empty($data['type'])){
            $list_where['type'] = $data['type'];
        }
        if(!empty($data['endtime']) && !empty($data['strtime'])){
            $list_where[]=['time','>=',$data['strtime']];
            $list_where[]=['time','<=',$data['endtime']];
        }
        $data = $this->XiaofeiModel->selData($list_where);
        foreach ($data as $key => $value) {
            $where['id'] = $value['type'];

            $type = $this->TypeModel->selData($where);
            if($type){
                $data[$key]['type'] = $type[0]['type_name'];
            }
            switch ($value['name']) {
                case 0:
                    $data[$key]['name'] = '刘义';
                    break;
                case 1:
                    $data[$key]['name'] = '朵朵';
                    break;
                default:
                    # code...
                    break;
            }
        }
        ajax_cms($data);
    }

    public function addDisoplay(Request $request){
        $list = $this->TypeModel->getList([],['*']);
        $name = $this->NameModel->getList([],['*']);
        $res['type'] = $list['data'];
        $res['name'] = $name['data'];
        return view("Xiaofei.addDisoplay",$res);
    }

    public function ajaxaddxf(Request $request){
        $data = $request->input();
        unset($data['_token']);
        $data['addtime'] = time();
        $list = $this->XiaofeiModel->addxf($data);
        if($list){
            $code['code'] = 200;
        }else{
            $code['code'] = 199;
        }
        ajaxreturn($code);
    }
    public function constat(Request $request){
        echo 111;exit;
    }


    
}
