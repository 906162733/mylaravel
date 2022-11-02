<?php
namespace App\Http\Controllers\Test;

use App\Model\TestModel as TES;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\Auth\JwtAuth;
class TestController extends Controller
{

    /**
     * @param Request $request
     */
    public function index(Request $request){


        $aaa = 123;
        function hah(){
            global $aaa;
            echo $aaa;
        }
        hah();
        echo $aaa;
        exit;
        
    	//phpinfo();exit;
    	//$data = $request->input();
    	//$this->askjd();exit;
    	/*$user = $data['user'];
    	$pass = $data['pass'];*/

    	/*$JwtAuth = JwtAuth::getInstance();
    	$token = $JwtAuth->setUid(1)->encode()->getToken();
    	ajaxreturn($token);
*/




    	ini_set('memory_limit','3072M');    // 临时设置最大内存占用为3G
   		set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期
    	$time = time();
    	for ($i=0; $i <2000000 ; $i++) {
    		$data[$i]['userid'] = $this->build_order_no();
    		$data[$i]['username'] = '刘义'.$i;
    		$data[$i]['addtime'] = time();
    		if($i%100 == 0) {
    			$data = array_values($data);
    			TES::sendMsg($data);
    			$data =array();
    		}
    	}
    	//TES::sendMsg($data);
    	echo time() - $time;exit;
    	$res = TES::getInfoByUid(1);
    	foreach ($res as $title) {
    		echo $title;
		}exit;
    	var_dump($res);exit;
    }
    public function home(Request $request)
    {
    	//验证tonken
    	$token = $request['token'];
    	JwtAuth::getInstance()->decode($token)->isToken();
    	//


    }
    function build_order_no()
	{
	    /* 选择一个随机的方案 */
	    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2020] . strtoupper(dechex(date('m'))) . date('d') .
            substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $orderSn;
	}
	function getChar($num)  // $num为生成汉字的数量
    {
        $b = '';
        for ($i=0; $i<$num; $i++) {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    }
}
