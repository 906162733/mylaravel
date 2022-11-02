<?php
/**
 * [create_id 生成唯一id]
 * @return [type] [description]
 */
function create_id()
{
    return md5(time().mt_rand(10000,9999999).mt_rand(10000,9999999).mt_rand(10000,9999999));
}
/**
 * [@param $len 长度]
 * [@param $pre 单号前缀]
 * @return [type] [description]
 */
function create_sn($pre = '',$len = 8)
{
    return $pre . date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, $len);
}
function ajaxreturn($res, $message = '', $code='',$url = '',$callback = '')
{
    //默认提示消息
    $messages = [
        199 => trans('common.operation_fail'),
        200=> trans('common.operation_ok'),
        500=> trans('common.operation_exception'),
        600=> trans('common.group_examine'),
    ];
    $status = 200;  //默认状态
    if(empty($res)) $status = 199;
    if(strlen($code)>0) $status = $code;
    if(!empty($message) && is_array($message)) {
        $message = !empty($res) ? current($message) : end($message);
    }
    if(empty($message)) $message = $messages[$status];
    $data['code']       = $status;
    $data['data']       = !empty($res) ? $res : array();
    $data['message']    = $message;
    !empty($url) ? $data['url'] = $url : '';
    if(empty($callback)){
        exit(json_encode($data));
    }else{
        exit($callback.'('.json_encode($data).')');
    }
}

function ajax_cms($res, $message = '',$count = '0')
{
    //默认提示消息
    $messages = [
        1 => trans('common.operation_fail'),
        0=> trans('common.operation_ok'),
    ];
    $status = 0;  //默认状态
    if(empty($res)) $status = 1;
    if(!empty($message) && is_array($message)) {
        $message = !empty($res) ? current($message) : end($message);
    }
    if(empty($message)) $message = $messages[$status];
    $data['code']       = $status;
    $data['data']       = !empty($res) ? $res : array();
    $data['msg']    = $message;
    $data['count']    = $count;
    exit(json_encode($data));
}

function ajax_web($res, $message = '', $code='',$url = '',$callback = '')
{
    //默认提示消息
    $messages = [
        199 => trans('common.operation_fail'),
        200=> trans('common.operation_ok'),
        500=> trans('common.operation_exception'),
    ];
    $status = 200;  //默认状态
    if(empty($res)) $status = 199;
    if(strlen($code)>0) $status = $code;
    if(!empty($message) && is_array($message)) {
        $message = !empty($res) ? current($message) : end($message);
    }
    if(empty($message)) $message = $messages[$status];
    $data['code']       = $status;
    $data['data']       = !empty($res) ? $res : array();
    $data['message']    = $message;
    !empty($url) ? $data['url'] = $url : '';
    if(empty($callback)){
        exit(json_encode($data));
    }else{
        exit($callback.'('.json_encode($data).')');
    }
}
/**
 * [curl_get curl远程获取方法]
 * @param  [type] $url    [description]
 * @param  [type] $method [description]
 * @param  [type] $args   [description]
 * @return [type]         [description]
 */
function curl_get($url,$header,$method,$args)
{
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 120 );
    curl_setopt( $ch, CURLOPT_TIMEOUT , 120);
    if(!empty($header)) curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    if($method == 'post')
    {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
    }
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res,true);
}

/**
 * [curl_get curl远程获取方法]
 * @param  [type] $url    [description]
 * @param  [type] $method [description]
 * @param  [type] $args   [description]
 * @return [type]         [description]
 */
function curl_web($url,$args=[],$method='post',$header=[])
{
    $base_url = 'http://192.168.0.6:5001';//'192.168.0.6';
    $url = $base_url.$url;
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 120 );
    curl_setopt( $ch, CURLOPT_TIMEOUT , 120);
    if(!empty($header)) curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    if($method == 'post')
    {
        //生成sign
        if(isset($args['sign'])) unset($args['sign']);
        $str = '';
        ksort($args);
        foreach($args as $key=>$v)
        {
            $str .= $key.$v;
        }
        $args['sign'] = strtoupper(base64_encode(md5($str.config('app.publickey'))));

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
    }

    $res = curl_exec($ch);
//        var_dump($res);
    return json_decode($res,true);
}



/**
 * [card_curl_get   curl远程获取方法(身份证识别 系列...)]
 * @param  [type] $url    [description]
 * @param  [type] $method [description]
 * @param  [type] $args   [description]
 * @return [type]         [description]
 */
function card_curl_get($host,$path,$header,$method,$data)
{
    $url = $host . $path;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    return curl_exec($curl);
}

/**
 * [curl_nation   (全国建筑工人管理服务信息平台标准化服务接口)]
 * @param  [type] $url    [description]
 * @param  [type] $method [description]
 * @param  [type] $args   [description]
 * @return [type]         [description]
 */
function curl_nation($args,$method)
{
    if(!isset($args['projectCode'])) return false;
    $up_base = new \App\Logic\UpPlat\UpBase();
    $up_nation = $up_base->getProjectInfo($args['projectCode']);
    if(!$up_nation) return false;
//    $data['appid'] = config('config.nation_appid');
    $data['appid'] = isset($up_nation['AppID']) ? $up_nation['AppID'] : '';
    $data['format'] = config('config.nation_format');
    $data['method'] = $method;
    $data['version'] = config('config.nation_version');
    $data['data'] = json_encode($args);
    $data['timestamp'] = date('YmdHis');
    $data['nonce'] = md5('hangchen'.time());
    $SecretKey = isset($up_nation['SecretKey']) ? $up_nation['SecretKey'] : '';
    $data['sign'] = createNationSign($data,$SecretKey);

    $url = config('config.nation_url');
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 120 );
    curl_setopt( $ch, CURLOPT_TIMEOUT , 120);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res,true);
}

function createNationSign($args,$SecretKey)
{
    if (isset($args['sign'])) unset($args['sign']);
    $str = '';
    ksort($args);
    foreach ($args as $key => $v) {
        if ($key != 'sign') $str .= $key . '=' . $v . '&';
    }
//    $str .= 'appsecret=' . config('config.nation_appsecret');
    $str .= 'appsecret=' . $SecretKey;
    $str = strtolower($str);//全部转小写
    return bin2hex(hash('sha256',$str,true));
}

/* 获取文件后缀名 */
function get_file_end($filename)
{
    $a = explode('.',$filename);
    return $a[1];
}

/* excel文件导入 */
function uploads_excel($file)
{
    $file_info = $file->getInfo();
    $exts = get_file_end($file_info['name']);       // 获取文件后缀
    $filename = $file->getPathname();               // 生成文件路径名
    require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel.php');
    if ($exts == 'xls')                             // 如果excel文件后缀名为.xls，导入这个类
    {
        require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel/Writer/Excel5.php');
        $PHPReader = new \PHPExcel_Reader_Excel5();
    } else {
        require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel/Writer/Excel2007.php');
        $PHPReader = new \PHPExcel_Reader_Excel2007();
    }
    require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel/IOFactory.php');
    $PHPExcel = $PHPReader->load($filename);// 载入文件
    $currentSheet = $PHPExcel->getSheet(0);                      // 获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
    $allColumn = $currentSheet->getHighestColumn();              // 获取总列数
    $allRow = $currentSheet->getHighestRow();                    // 获取总行数
    for ($currentRow = 0; $currentRow <= $allRow; $currentRow++) // 循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
    {
        for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) // 从哪列开始，A表示第一列
        {
            $address = $currentColumn . $currentRow;             // 数据坐标
            $ExlData[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();// 读取到的数据，保存到数组$arr中
        }
    }
    return $ExlData;
}
/**
根据给定的数字生成至多两位对应EXCEL文件列的字母
 */
function num_to_en($pColumnIndex = 0)
{
    static $_indexCache = array();

    if (!isset($_indexCache[$pColumnIndex])) {
        if ($pColumnIndex < 26) {
            $_indexCache[$pColumnIndex] = chr(65 + $pColumnIndex);
        } elseif ($pColumnIndex < 702) {
            $_indexCache[$pColumnIndex] = chr(64 + ($pColumnIndex / 26)) . chr(65 + $pColumnIndex % 26);
        } else {
            $_indexCache[$pColumnIndex] = chr(64 + (($pColumnIndex - 26) / 676)) . chr(65 + ((($pColumnIndex - 26) % 676) / 26)) . chr(65 + $pColumnIndex % 26);
        }
    }
    return $_indexCache[$pColumnIndex];
}
/**
根据给定的数字生成至多两位对应EXCEL文件列的字母
*/
function num_to_ena($num){
    $asc = 0;
    $en = "";
    $num =(int)$num+1;
    if($num<26){                      //判断指定的数字是否需要用两个字母表示{
        if((int)$num<10){
            $asc = ord($num);
            $en =chr($asc+16);
        }
        else{
            $num_g = substr($num,1,1);
            $num_s = substr($num,0,1);
            $asc = ord($num_g);
            $en =chr($asc+16+10*$num_s);
        }
    }
    else{
        $num_complementation = floor($num/26);
        $en_q = num_to_en($num_complementation-1);
        $en_h = $num%26 != 0 ? num_to_en($num-$num_complementation*26):"A";
        $en = $en_q.$en_h;
    }
    return $en;
}

/**
 * 加载路由模块
 * @param $dir_name 目录地址
 */
function load_routes($dir_name)
{
    //目录路径
    $dir_path = rtrim($dir_name, '/') . '/';
    //打开文件句柄
    $handle = opendir($dir_path);
    //获得目录中的文件夹列表
    $dir_list = [$dir_path];
    while(false !== ($file_name = readdir($handle)))
    {
        if(is_dir($dir_path . $file_name) && $file_name != '.' && $file_name != '..')
        {
            $dir_list[] = $dir_path . $file_name . '/';
        }
    }
    foreach($dir_list as $key => $value)
    {
        foreach(glob($value . '*.php') as $files)
        {
            require $files;
        }
    }
}

/*
**  人民币金额 数字转中文大写
 */
function num_to_rmb($num){
    $c1 = "零壹贰叁肆伍陆柒捌玖";
    $c2 = "分角元拾佰仟万拾佰仟亿";
    //精确到分后面就不要了，所以只留两个小数位
    $num = round($num, 2);
    //将数字转化为整数
    $num = $num * 100;
    if (strlen($num) > 10) {
        return "金额太大，请检查";
    }
    $i = 0;
    $c = "";
    while (1) {
        if ($i == 0) {
            //获取最后一位数字
            $n = substr($num, strlen($num)-1, 1);
        } else {
            $n = $num % 10;
        }
        //每次将最后一位数字转化为中文
        $p1 = substr($c1, 3 * $n, 3);
        $p2 = substr($c2, 3 * $i, 3);
        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
            $c = $p1 . $p2 . $c;
        } else {
            $c = $p1 . $c;
        }
        $i = $i + 1;
        //去掉数字最后一位了
        $num = $num / 10;
        $num = (int)$num;
        //结束循环
        if ($num == 0) {
            break;
        }
    }
    $j = 0;
    $slen = strlen($c);
    while ($j < $slen) {
        //utf8一个汉字相当3个字符
        $m = substr($c, $j, 6);
        //处理数字中很多0的情况,每次循环去掉一个汉字“零”
        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
            $left = substr($c, 0, $j);
            $right = substr($c, $j + 3);
            $c = $left . $right;
            $j = $j-3;
            $slen = $slen-3;
        }
        $j = $j + 3;
    }
    //这个是为了去掉类似23.0中最后一个“零”字
    if (substr($c, strlen($c)-3, 3) == '零') {
        $c = substr($c, 0, strlen($c)-3);
    }
    //将处理的汉字加上“整”
    if (empty($c)) {
        return "零元整";
    }else{
        return $c . "整";
    }
}
/**
 * [get_url 根据网址获取路由]
 * @author xingwenzhi
 * @param $url 网址
 * @return
*/
function get_url($url)
{
    if(!$url) return false;
    $search = '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i';
    $url = trim($url);
    preg_match_all($search, $url ,$rr);
    return $rr;
}

function get_cat_tree($arr,$pid='0',$step=0){
    static $tree;
    foreach($arr as $key=>$val) {
        if($val['parent_id'] == $pid) {
            $flg = str_repeat('└―',$step);
            $val['type_name'] = $flg.$val['type_name'];
            $tree[] = $val;
            unset($arr[$key]);
            get_cat_tree($arr , $val['type_id'] ,$step+1);
        }
    }
    return $tree;
}
/**
 * 最新导出函数
 * @param title 导出文件名称
 */
function export_excel_new($title,$data){
    require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel.php');
   /*
    require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel/Writer/Excel5.php');
    require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel/Writer/Excel2007.php');
    $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
    $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
    */
    $objPHPExcel = new \PHPExcel();
    // 设置表头信息
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $letter = [];
    foreach ($data as $key => $value) {
        $en = num_to_en($key);
        foreach($value as $k=>$r){
            $letter[] = $en = num_to_en($k);
            $sheet->setCellValue($en . ($key+1), $r);
        }
    }
    if(!empty($letter)) {
        foreach($letter as $k=>$r) {
            $sheet->getRowDimension($k+1)->setRowHeight(20);  //单元格行高
            $sheet->getColumnDimension($r)->setWidth(20); //单元格宽度
        }
    }
//    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($key))->setWidth(20); //单元格宽度
    //水平居中
//    $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    /*--------------下面是设置其他信息------------------*/
//  $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
    $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,"Excel2007");
    //$time = $sql['time'];
    $name = $title .date('Y-m-d').".xlsx";
    header('Content-Disposition: attachment;filename="'.$name.'"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $PHPWriter->save("php://output");
    exit;
}
/**
 * 导出excel函数
 */
function export_excel($file_name='',$data) {
    $strTable ='<style>td{text-align:center;height:40px;line-height:40px;font-size:14px;font-family:"宋体";}';
    $strTable .='</style><table border="1" bordercolor="#e6e6e6">';
    if(!empty($data)) {
        foreach($data as $val) {
            $strTable .= '<tr>';
            foreach($val as $r) {
                $strTable .= '<td width="*">'.$r.'</td>';
            }
            $strTable .= '</tr>';
        }
    }
    $strTable .= '</table>';
    down_excel($strTable, $file_name);
}
/**
 * 导出excel
 * @param $strTable	表格内容
 * @param $filename 文件名
 */
function down_excel($strTable,$filename)
{
//    header("Content-type: application/vnd.ms-excel");
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Type: application/force-download");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header("Content-Disposition: attachment; filename=".$filename.date('Y-m-d').".xls");
    header('Expires:0');
    header('Pragma:public');
    echo $strTable;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}
/**
 * [check 数据验证方法]
 * @param  [type] $check [description]
 * @return [type]        [description]
 */
function check($check)
{
    if(empty($check)) return;
    else
    {
        foreach($check as $key=>$v)
        {
            switch($v['action'])
            {
                case 'phone':
                    phone_check($v['value']);
                    break;
                case 'email':
                    email_check($v['value']);
                    break;
                case 'string':
                    string_check($v['value']);
                    break;
                case 'number':
                    number_check($v['value']);
                    break;
                case 'cardid':
                    card_check($v['value']);
                    break;
                case 'carnum':
                    car_number_check($v['value']);
                    break;
                case 'cjnum':
                    cjnumber_check($v['value']);
                    break;
                case 'owner':
                    owner_check($v['value']);
                    break;
                case 'ch':
                    ch_check($v['value']);
                    break;
            }
        }
    }
}
function ch_check($val)
{
    if(preg_match("/[\x7f-\xff]/", $val)) error_report('内容包含中文');
}
function error_report($message)
{
    $data['code'] = 500;
    $data['data'] = array();
    $data['message'] = $message;
    exit(json_encode($data));
}
/**
 * [手机号码验证]
 * @param $val
 * @return bool
 */
function phone_check($val)
{
    if (!is_int($val)) return false;
    return preg_match('^1(3|4|5|6|7|8|9)\d{9}$', $val) ? true : false;
}

function email_check($val)
{
    $res = filter_var($val,FILTER_VALIDATE_EMAIL);
    if($res) return true;
    else error_report('邮箱验证失败');
}

function string_check($val)
{
    $val = trim($val);
    if($val == '' || empty($val) || is_null($val)) error_report('数据不完整,验证失败');
    else return true;
}

function number_check($val)
{
    if(is_numeric($val)) return true;
    else error_report('数字验证失败');
}
/**
 * [card_check 身份证验证]
 * @param  [type] $val [description]
 * @return [type]      [description]
 */
function card_check($val)
{
    /*$ch = curl_init();
    $url = 'http://apis.baidu.com/chazhao/idcard/idcard?idcard=' . $val;
    $header = array(
        "apikey:".config('apistore_apikey'),
    );
   	$res = curl_get($url,$header,'get','');*/
    $res = isCreditNo($val);
    if(!$res) error_report($val.'身份证验证失败');
    else return true;
}

/**
 * [car_number_check 车牌号验证]
 * @param  [type] $val [description]
 * @return [type]      [description]
 */
function car_number_check($val)
{
    if ($val==null || empty($val) || !preg_match('^[\x80-\xff][A-Z][A-Z_0-9]{5}$^', $val)) error_report($val.'车牌号验证失败');
    else return true;
}

/**
 * [cjnumber_check 车架号验证]
 * @param  [type] $val [description]
 * @return [type]      [description]
 */
function cjnumber_check($val)
{
    if((int)$val == $val && strlen($val) == 4) return true;
    else error_report($val.'车架号验证失败');
}

/**
 * [owner_check 车主姓名验证]
 * @param  [type] $val [description]
 * @return [type]      [description]
 */
function owner_check($val)
{
    if(strlen($val) == 4) return true;
    else error_report($val.'车主姓名验证失败');
}
function password($password)
{
    return substr(md5($password.config('config.salt')),0,28);
}
/*
** 钱 分-元转换
** @param $multiply true 转为分;false 转为元
*/
function format_money($price, $multiply=true){
    $price = floatval($price);
    if($multiply) return $price * 100;
    return number_format($price/100,2,'.','');
}

/**
 * 二维数组根据字段进行排序
 * @params array $array 需要排序的数组
 * @params string $field 排序的字段
 * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
 */
function arraySequence($array, $field, $sort = 'SORT_ASC')
{
    $arrSort = array();
    foreach ($array as $uniqid => $row)
    {
        foreach ($row as $key => $value)
        {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort[$field], constant($sort), $array);
    return $array;
}

/**
 * 随机生成十六进制颜色
 */
function randomColor() {
  $str = '#';
  for($i = 0 ; $i < 6 ; $i++) {
    $randNum = rand(0 , 15);
    switch ($randNum) {
      case 10: $randNum = 'A'; break;
      case 11: $randNum = 'B'; break;
      case 12: $randNum = 'C'; break;
      case 13: $randNum = 'D'; break;
      case 14: $randNum = 'E'; break;
      case 15: $randNum = 'F'; break;
    }
    $str .= $randNum;
  }
  return $str;
}


/**
 * [send_message 发送短信]
 * @param  [type] $phone   [description]
 * @param  [type] $message [description]
 * @return [type]          [description]
 */
function send_message($phone,$tpl,$code,$company)
{
    $url = "http://v.juhe.cn/sms/send";
    $args = [
        'key'=>config('sendmsg.send_message_key'),
        'mobile'=>$phone,
        'tpl_id'=>$tpl,
        'tpl_value'=>"#code#=$code&#company#=$company"
    ];
    $res = curl_get($url,'','post',$args);
    if($res['error_code'] == 0) return true;
    else return false;
}

//阿里市场 发送短信
function send_message_new($phone,$code)
{
    $host = "https://feginesms.market.alicloudapi.com";
    $path = "/codeNotice";
    $method = "GET";
    $appcode = config('sendmsg.ali_appcode');
    $sign = config('sendmsg.ali_sign');//公司标题
    $skin = config('sendmsg.ali_skin');//模板号
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $querys = "param=".$code."&phone=".$phone."&sign=".$sign."&skin=".$skin;
    $url = $host . $path . "?" . $querys;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
//    curl_setopt($curl, CURLOPT_HEADER, true); //如不输出json, 请打开这行代码，打印调试头部状态码。
    //状态码: 200 正常；400 URL无效；401 appCode错误； 403 次数用完； 500 API网管错误
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    $out_put = curl_exec($curl);
    $res = json_decode($out_put,true);
    if(isset($res['Message']) && $res['Message'] == 'OK') return true;
    else return false;
}


/*
 * 身份证识别验证
 * @param $code_img  身份证照片url
 * @param $type  1 正面 2反面
 * */
function sendCardOrcLogic($code_img,$type='1'){
    $id_card_host = config('config.id_card_host');//身份证验证地址
    $id_card_appcode = config('config.id_card_appcode'); //身份证验证appcode
    $path = "/id_card_ocr";
    $method = "POST";
    //定义请求头
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $id_card_appcode);
    //获取base64位的图片
    $img = urlencode(base64_encode(file_get_contents($code_img)));
    $bodys = "imgData={$img}&type={$type}";
    //进行发送请求
    $res = card_curl_get($id_card_host,$path,$headers,$method,$bodys);
    return json_decode($res,true);
}

/**
 * 姓名和身份证号码，判断是否正确
 * @param $code_img
 * @param string $type
 * @return mixed
 */
function sendIdCardAndName($id_card,$name){
    $id_card_host = config('config.id_card_and_name_host');//身份证验证地址
    $id_card_appcode = config('config.id_card_appcode'); //身份证验证appcode
    $path = "/IDCard";
    $method = "GET";
    //定义请求头
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $id_card_appcode);
    //获取base64位的图片
    $bodys = "idCard={$id_card}&name={$name}";
    $path = $path . '?' . "idCard={$id_card}&name={$name}";
    //进行发送请求
    $res = card_curl_get($id_card_host,$path,$headers,$method,$bodys);
    return json_decode($res,true);
}

//人脸识别调用
function sendFaceAndIdCard($id_card_img_url,$img_url){
    $host =  config('config.id_card_and_face_host');//身份证和人脸验证
    $path = "/MatchFace";
    $method = "POST";
    $appcode = config('config.id_card_appcode'); //身份证验证appcode

    //定义请求头
    $headers = array();
    array_push($headers, "Expect:");
    array_push($headers, "Authorization:APPCODE " . $appcode);
    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
    //定义请求内容 身份证图片
//    $id_card_img_url = base_path().'/public/images/a.png';
    $Image1Base64= base64_encode(file_get_contents($id_card_img_url));
    //脸
//    $img_url = base_path().'/public/images/c.jpg';
    $Image2Base64= base64_encode(file_get_contents($img_url));
    $bodys ="{\"Image1Base64\":\"{$Image1Base64}\",\"Image2Base64\":\"{$Image2Base64}\"}";

    //进行发送请求
    $res = card_curl_get($host,$path,$headers,$method,$bodys);
    return json_decode($res,true);
}


/*
 *阿里 发送短信
 * */
function aliSendMsg($phone,$msg_code,$tpl_id="TP1711063")
{
    $send_message_host = config('sendmsg.send_message_host');//短信地址
    $send_message_path = config('sendmsg.send_message_path');//路径
    $send_message_appcode = config('sendmsg.send_message_appcode'); //短信appcode
    $method = "POST";
    //定义请求头
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $send_message_appcode);
    //发送短信
    $querys = "mobile={$phone}&param=code%3A{$msg_code}&tpl_id={$tpl_id}";
    $url = $send_message_path . "?" . $querys;
    $bodys = "";
    //进行发送请求
    $res = card_curl_get($send_message_host,$url,$headers,$method,$bodys);
    var_dump($res);
//    $res =json_decode($res,true);
//    if($res['return_code']=='00000' && isset($res['order_id'])){
//        return $res['order_id'];
//    }else{
//        return false;
//    }

}



/*
 * 计算每个月有多少天
 * @param 月份格式 2019-02
 * @return 20190201 数组
 * */
function reckonMonthDays($month){
    $j = date("t",strtotime($month));
    $month = date('Ym',strtotime($month));
    $month_input =[];
    for($i=1;$i<=$j;$i++){
        if($i<10){
            $month_input[] = $month.'0'.$i;
        }else{
            $month_input[] = $month.$i;
        }

    }
   return $month_input;
}

/*
 * 计算一天 两个区间
 * @param 月份格式 2019-02
 * @return 20190201 数组
 * */
function strDaystime($day,$is_date=false){
    $day =strtotime($day);
    if($is_date){
        $date['start_time']= strtotime(date('Y-m-d 00:00:00', $day)); //2016-11-01 00:00:00
        $date['end_time']= strtotime(date('Y-m-d 23:59:59', $day)); //2016-11-01 23:59:59
    }else{
        $date['start_time']= date('Y-m-d 00:00:00', $day); //2016-11-01 00:00:00
        $date['end_time']= date('Y-m-d 23:59:59', $day); //2016-11-01 23:59:59
    }


    return $date;
}

/*获取当前域名*/
function getHostUrl(){
   $host = 'http://'.$_SERVER['HTTP_HOST'];
   return  $host;

}


/*
 * 根据日期获取 该日期的当月第一天 和 最后一天 时间戳
 * start_time
 * */
function getMonthStartEndTime($time)
{
    $data['end_time'] = strtotime(date('Ymt',$time))+60*60*24;
    $data['start_time'] = strtotime(date('Ym01',$time));
    return $data;
}

//加密
function aes_en($data,$appsecret='2950e8f1bff14e7cd218be8dc1885acb'){
    if(!$appsecret) return '';
    $privateKey = $appsecret;
    $iv = substr($privateKey,0,16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $privateKey, 1, $iv);
    $encrypt_msg = base64_encode($encrypted);
    return $encrypt_msg;
}
//解密
function aes_de($encrypt_msg,$appsecret='2950e8f1bff14e7cd218be8dc1885acb'){
    if(!$appsecret) return '';
    $privateKey = $appsecret;
    $iv = substr($privateKey,0,16);
    $data = openssl_decrypt(base64_decode($encrypt_msg),  'AES-256-CBC', $privateKey, 1, $iv);
    return $data;
}

//异步请求，不会返回结果
function asyn_curl($url,$data=[]){
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_TIMEOUT , 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch , CURLOPT_URL , $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_exec($ch);
    curl_close($ch);
}

function ios_or_android()
{
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) return 'ios';
    else return 'android';
}

 /**
  * 计算时间差
  * @param int $timestamp1 时间戳开始
  * @param int $timestamp2 时间戳结束
  * @return array
  */
function time_diff($timestamp1, $timestamp2)
{
    if ($timestamp2 <= $timestamp1)
    {
        return ['hours'=>0, 'minutes'=>0, 'seconds'=>0];
    }
    $timediff = $timestamp2 - $timestamp1;
    // 时
    $remain = $timediff%86400;
    $hours = intval($remain/3600);

    // 分
    $remain = $timediff%3600;
    $mins = intval($remain/60);
    // 秒
    $secs = $remain%60;

    $time = ['hours'=>$hours, 'minutes'=>$mins, 'seconds'=>$secs];

    return $time;
}

/**
 * [获取首字母的值和排序]
 * @param $str
 */
function getFirstCharter($str){
    $info['num'] = '0';
    $info['letter'] = '';
    if(empty($str)){return $info;}
    $fchar=ord($str{0});
    if($fchar>=ord('A')&&$fchar<=ord('z')){
        $info['letter'] = strtoupper($str{0});
        return $info;
    }
    $s1=iconv('UTF-8','gb2312',$str);
    $s2=iconv('gb2312','UTF-8',$s1);
    $s=$s2==$str?$s1:$str;
    $asc=ord($s{0})*256+ord($s{1})-65536;
    $info['num'] = $asc;
    if($asc>=-20319&&$asc<=-20284) $info['letter'] = 'A';
    if($asc>=-20283&&$asc<=-19776) $info['letter'] = 'B';
    if($asc>=-19775&&$asc<=-19219) $info['letter'] = 'C';
    if($asc>=-19218&&$asc<=-18711) $info['letter'] = 'D';
    if($asc>=-18710&&$asc<=-18527) $info['letter'] = 'E';
    if($asc>=-18526&&$asc<=-18240) $info['letter'] = 'F';
    if($asc>=-18239&&$asc<=-17923) $info['letter'] = 'G';
    if($asc>=-17922&&$asc<=-17418) $info['letter'] = 'H';
    if($asc>=-17417&&$asc<=-16475) $info['letter'] = 'J';
    if($asc>=-16474&&$asc<=-16213) $info['letter'] = 'K';
    if($asc>=-16212&&$asc<=-15641) $info['letter'] = 'L';
    if($asc>=-15640&&$asc<=-15166) $info['letter'] = 'M';
    if($asc>=-15165&&$asc<=-14923) $info['letter'] = 'N';
    if($asc>=-14922&&$asc<=-14915) $info['letter'] = 'O';
    if($asc>=-14914&&$asc<=-14631) $info['letter'] = 'P';
    if($asc>=-14630&&$asc<=-14150) $info['letter'] = 'Q';
    if($asc>=-14149&&$asc<=-14091) $info['letter'] = 'R';
    if($asc>=-14090&&$asc<=-13319) $info['letter'] = 'S';
    if($asc>=-13318&&$asc<=-12839) $info['letter'] = 'T';
    if($asc>=-12838&&$asc<=-12557) $info['letter'] = 'W';
    if($asc>=-12556&&$asc<=-11848) $info['letter'] = 'X';
    if($asc>=-11847&&$asc<=-11056) $info['letter'] = 'Y';
    if($asc>=-11055&&$asc<=-10247) $info['letter'] = 'Z';
    return $info;
}

//根据身份证计算年龄
function getAgeByID($id){

//过了这年的生日才算多了1周岁
    if(empty($id)) return '';
    $date=strtotime(substr($id,6,8));
//获得出生年月日的时间戳
    $today=strtotime('today');
//获得今日的时间戳
    $diff=floor(($today-$date)/86400/365);
//strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
    $age=strtotime(substr($id,6,8).' +'.$diff.'years')>$today?($diff+1):$diff;

    return $age;
}

/*
 *计算时间戳格式
 * @param date 时间
 * @param format 规则
 * @param is_date true 时间戳转时间 false 时间格式转指定时间格式 例如 20180606 转为 2018-06-06
 * */
function getDateFormat($date,$format='Y-m-d H:i:s',$is_date=true){
    if(!$is_date) $date = strtotime($date);
    $time = date($format,$date);
    return $time;
}

/**
 * 生成二维码
 * @param $content//内容
 * @param $path//路径
 * @param $name//图片名称
 * @param string $size//大小
 * @param string $margin//边距
 * @return string //路径
 */
function createQrcode($content,$path,$name,$size='500',$margin='1')
{
    if (!file_exists($path)) mkdir ($path,0755,true);
    $path = $path . $name . '.png';
    \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size($size)->margin($margin)->generate($content, public_path($path));//内容和路径
//        merge('/public/qrcodes/laravel.png',.15)->//水印
    return $path;
}


/*
 *默认返回格式
 * */
function getReturnData($code=true,$message='',$data=[]){
    if($code){
        $info['code'] = '200';
        $info['message'] = $message ? $message :'调用成功';
    }else{
        $info['code'] = '199';
        $info['message'] = $message? $message :'调用失败';
    }
    $info['data'] = $data;
    return $info;
}


/*
 *
 * 获取数组的所有值 组成新数组
 * */
function getArrValues($data){
   return array_values(array_unique(explode(',',implode(',',$data))));
}




/**
 * [log 日志]
 * @return [type][description]
 */
function dolog($parm,$mkdir='htmllog')
{
    $path = public_path() . "/logs/{$mkdir}/";
//		$path = "./logs/htmllog/";  //第二种写法
    if (!file_exists($path)) mkdir ($path,0755,true);
    $url = $path.date('Y-m-d',time()).'.log';
    $data = json_encode($parm,JSON_UNESCAPED_UNICODE);//参数
    $interface = '('.$_SERVER['REQUEST_URI'].')';//url地址
    $address = '['.get_client_ip().']';//地址
    $agent = isset($_SERVER['HTTP_USER_AGENT'])?strtolower($_SERVER['HTTP_USER_AGENT']):'';
    $content = date('Y-m-d H:i:s',time()) . $agent . $interface .$address. $data."\r\n";
    file_put_contents($url, $content,FILE_APPEND);
}



/**
 * 导出人员花名册
 * @param title 导出文件名称
 */
function export_excel_member($title,$input){
    require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel.php');
    /*
     require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel/Writer/Excel5.php');
     require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel/Writer/Excel2007.php');
     $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
     $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
     */
    $objPHPExcel = new \PHPExcel();
    // 设置表头信息
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $letter = [];
    //给第一行赋值
    $sheet->setCellValue('A1',$input['title']);

    //给第二行赋值
    foreach($input['top2'] as $k => $v){
        $sheet->setCellValue($k, $v);
    }

    foreach ($input['data'] as $key => $value) {
        $en = num_to_en($key);
        foreach($value as $k=>$r){
            $letter[] = $en = num_to_en($k);
            $sheet->setCellValue($en . ($key+3), $r);
        }
    }
    if(!empty($letter)) {
        foreach($letter as $k=>$r) {
            $sheet->getRowDimension($k+3)->setRowHeight(20);  //单元格行高
            $sheet->getColumnDimension($r)->setWidth(20); //单元格宽度
        }
    }
    //合并单元格 - 首行
    $objPHPExcel->getActiveSheet()->mergeCells('A1:T1');
    //边框加黑
    $styleThinBlackBorderOutline = array(
        'borders' => array(
            'allborders' => array( //设置全部边框
                'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
            ),
        ),
    );
    //计算有多少条数据
    $coumath = count($input['data'])+2;
    //加边框
    $objPHPExcel->getActiveSheet()->getStyle( 'A1:T'.$coumath)->applyFromArray($styleThinBlackBorderOutline);
    //合并最后一行
    $objPHPExcel->getActiveSheet()->mergeCells('A'.($coumath).':T'.($coumath));
    //设置字体颜色 -- 设置第一行字体大小
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(22);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
    $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
    $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
    $objPHPExcel->getActiveSheet()->mergeCells('D2:I2');
    $objPHPExcel->getActiveSheet()->mergeCells('K2:L2');
    $objPHPExcel->getActiveSheet()->mergeCells('M2:N2');
    $objPHPExcel->getActiveSheet()->mergeCells('O2:T2');
    //设置指定单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(0))->setWidth(7); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(1))->setWidth(7); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(2))->setWidth(7); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(3))->setWidth(7); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(4))->setWidth(7); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(5))->setWidth(7); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(6))->setWidth(10); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(7))->setWidth(15); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(8))->setWidth(7); //单元格宽度

    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(9))->setWidth(40); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(10))->setWidth(22); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(11))->setWidth(50); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(12))->setWidth(11); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(13))->setWidth(11); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(14))->setWidth(11); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(15))->setWidth(11); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(16))->setWidth(11); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(17))->setWidth(13); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(18))->setWidth(15); //单元格宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(19))->setWidth(11); //单元格宽度
//    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($key))->setWidth(20); //单元格宽度
    //水平居中
//    $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    /*--------------下面是设置其他信息------------------*/
//  $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
    $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,"Excel2007");
    //$time = $sql['time'];
    $name = $title .date('Y-m-d').".xlsx";
    header('Content-Disposition: attachment;filename="'.$name.'"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $PHPWriter->save("php://output");
    exit;
}


/**
 * 导出人员花名册
 * @param title 导出文件名称
 */
function export_excel_attendance($title,$input){
    require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel.php');
    /*
     require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel/Writer/Excel5.php');
     require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel/Writer/Excel2007.php');
     $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
     $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
     */
    $objPHPExcel = new \PHPExcel();
    // 设置表头信息
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    //第一行居中
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $letter = [];
    //给第一行赋值
    $sheet->setCellValue('A1',$input['top1']);
    //设置第一行行高
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(35);
    //合并单元格 - 第一行
    $objPHPExcel->getActiveSheet()->mergeCells('A1:AN1');
    //设置字体颜色 -- 设置第一行字体大小
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(22);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    //给第二行赋值
    foreach($input['top2'] as $k => $v){
        $sheet->setCellValue($k, $v);
    }
    //设置第二行行高
    $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
    //合并第二行单元格
    $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
    $objPHPExcel->getActiveSheet()->mergeCells('D2:G2');
    $objPHPExcel->getActiveSheet()->mergeCells('H2:J2');
    $objPHPExcel->getActiveSheet()->mergeCells('K2:Q2');
    $objPHPExcel->getActiveSheet()->mergeCells('R2:U2');
    $objPHPExcel->getActiveSheet()->mergeCells('V2:AL2');
    $objPHPExcel->getActiveSheet()->mergeCells('AM2:AM4');
    $objPHPExcel->getActiveSheet()->mergeCells('AN2:AN4');
    //垂直居中
    $objPHPExcel->getActiveSheet()->getStyle('AM2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('AN2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    //给第三行赋值
    foreach($input['top3'] as $k => $v){
        $letter[] = $en = num_to_en($k);
        $sheet->setCellValue($v['key'], $v['value']);
        //垂直合并前5行表格
        if($k<5){
            $objPHPExcel->getActiveSheet()->mergeCells($en.'3:'.$en.'4');
            $objPHPExcel->getActiveSheet()->getStyle($en.'3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }
    }
    //设置第三行行高
    $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
    //合并
    $objPHPExcel->getActiveSheet()->mergeCells('F3:AJ3');
    $objPHPExcel->getActiveSheet()->mergeCells('AK3:AL3');
    $objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('AK3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    //第四行赋值
    foreach($input['top4'] as $k => $v){
        $en = num_to_en($k+5);
        $sheet->setCellValue($en.'4', $v);
    }
    //边框加黑
    $styleThinBlackBorderOutline = array(
        'borders' => array(
            'allborders' => array( //设置全部边框
                'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
            ),
        ),
    );
    //计算有多少条数据 默认是3行
    $coumath = isset($input['data'])? count($input['data'])+4:4;
    //加边框
    $objPHPExcel->getActiveSheet()->getStyle( 'A1:AN'.$coumath)->applyFromArray($styleThinBlackBorderOutline);
    //合并最后一行
//    $objPHPExcel->getActiveSheet()->mergeCells('A'.($coumath).':T'.($coumath));
    //调整列宽度
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(0))->setWidth(6);
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(1))->setWidth(10);
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(2))->setWidth(6);
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(3))->setWidth(10);
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(4))->setWidth(20); //身份证号
    for($i=5;$i<38;$i++){
        $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($i))->setWidth(5);
    }
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(38))->setWidth(12);
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(39))->setWidth(12);


    //设置内容
    foreach ($input['data'] as $key => $value) {
        $en = num_to_en($key);
        foreach($value as $k=>$r){
            $letter[] = $en = num_to_en($k);
            $sheet->setCellValue($en . ($key+5), $r);
        }
    }
    if(!empty($letter)) {
        foreach($letter as $k=>$r) {
            $sheet->getRowDimension($k+5)->setRowHeight(20);  //单元格行高
        }
    }


    /*--------------下面是设置其他信息------------------*/
//  $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
    $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,"Excel2007");

    $name = $title .date('Y-m-d').".xlsx";
    header('Content-Disposition: attachment;filename="'.$name.'"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $PHPWriter->save("php://output");
    exit;
}







/**
 * 导出人员花名册
 * @param title 导出文件名称
 */
function export_excel_salary($title,$input,$tow_table){
    require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel.php');
    /*
     require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel/Writer/Excel5.php');
     require (base_path() . '/vendor/PHPExcel/PHPExcel/PHPExcel/Writer/Excel2007.php');
     $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
     $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
     */
    $objPHPExcel = new \PHPExcel();
    // 设置表头信息

    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle('工资单');
    $sheet = $objPHPExcel->getActiveSheet();
    //第一行居中
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $letter = [];
    //给第一行赋值
    $sheet->setCellValue('A1',$input['top1']);
    //设置第一行行高
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(35);
    //合并单元格 - 第一行
    $objPHPExcel->getActiveSheet()->mergeCells('A1:P1');
    //设置字体颜色 -- 设置第一行字体大小
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(22);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

    //第二行
    foreach($input['top2'] as $k => $v){
        //给第二行赋值
        $sheet->setCellValue($k, $v);
        //垂直居中
        $objPHPExcel->getActiveSheet()->getStyle($k)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }
    //设置第二行行高
    $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
    //合并第二行单元格
    $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
    $objPHPExcel->getActiveSheet()->mergeCells('D2:G2');
    $objPHPExcel->getActiveSheet()->mergeCells('H2:I2');
    $objPHPExcel->getActiveSheet()->mergeCells('J2:P2');

    //第三行
    foreach($input['top3'] as $k => $v){
        //给第三行赋值
        $sheet->setCellValue($k,$v);
        //垂直居中
        $objPHPExcel->getActiveSheet()->getStyle($k)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }
    //设置第三行行高
    $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
    //合并第三行单元格
    $mergeCells3 = ['A3:C3','D3:G3','H3:I3','J3:P3'];
    foreach($mergeCells3 as $k => $v){
        $objPHPExcel->getActiveSheet()->mergeCells($v);
    }
    //第四行
    foreach($input['top4'] as $k => $v){
        $letter[] = $en = num_to_en($k);
        $sheet->setCellValue($v['key'].'4', $v['value']);
        $objPHPExcel->getActiveSheet()->getStyle($v['key'].'4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //垂直合并制定行数
        if($k==0||$k>=2){
            $objPHPExcel->getActiveSheet()->mergeCells($v['key'].'4:'.$v['key'].'5');
        }
    }
    $mergeCells4 = ['B4:D4'];
    foreach($mergeCells4 as $k => $v){
        $objPHPExcel->getActiveSheet()->mergeCells($v);
    }
    $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(30);

    //第五行
    foreach($input['top5'] as $k => $v){
        $letter[] = $en = num_to_en($k);
        $sheet->setCellValue($v['key'].'5', $v['value']);
        $objPHPExcel->getActiveSheet()->getStyle($v['key'].'5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

    for($i=0;$i<16;$i++){
        if($i>=4 && $i<=11 ){
            $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($i))->setWidth(13);
        }
        if($i>11 && $i <=16){
            $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($i))->setWidth(15);
        }
    }
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(0))->setWidth(10);
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(1))->setWidth(14);
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(2))->setWidth(14);
    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex(3))->setWidth(22);
    $sum_hi = '5';
    if(isset($input['data'])){
        //设置内容
        foreach ($input['data'] as $key => $value) {
            $en = num_to_en($key);
            foreach($value as $k=>$r){
                $letter[] = $en = num_to_en($k);
                $sheet->setCellValue($en . ($key+6), $r);
                //垂直居中
                $objPHPExcel->getActiveSheet()->getStyle($en . ($key+6))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                //最后行高
                $sum_hi = $key +6;
            }
        }
        if(!empty($letter)) {
            foreach($letter as $k=>$r) {
                $sheet->getRowDimension($k+6)->setRowHeight(20);  //单元格行高
            }
        }
    }

    //合计
    foreach($input['salary_count'] as $k => $v){
        //给合计赋值
        $en = num_to_en($k);
        $sheet->setCellValue($en . ($sum_hi + 1), $v);
    }
    $sheet->getRowDimension($sum_hi + 1)->setRowHeight(20);  //单元格行高
    //添加表底
    $end_num = $sum_hi + 2;
    foreach($input['table_end'] as $k => $v){
        //给表底赋值
        $sheet->setCellValue($k . $end_num, $v);
        //垂直居中
        $objPHPExcel->getActiveSheet()->getStyle($k . $end_num)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }
    //设置表底行高
    $objPHPExcel->getActiveSheet()->getRowDimension($end_num)->setRowHeight(20);
    //合并表底单元格
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$end_num.':B'.$end_num);
    $objPHPExcel->getActiveSheet()->mergeCells('C'.$end_num.':E'.$end_num);
    $objPHPExcel->getActiveSheet()->mergeCells('F'.$end_num.':G'.$end_num);
    $objPHPExcel->getActiveSheet()->mergeCells('H'.$end_num.':K'.$end_num);
    $objPHPExcel->getActiveSheet()->mergeCells('L'.$end_num.':M'.$end_num);
    $objPHPExcel->getActiveSheet()->mergeCells('N'.$end_num.':P'.$end_num);


    //边框加黑
    $styleThinBlackBorderOutline = array(
        'borders' => array(
            'allborders' => array( //设置全部边框
                'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
            ),
        ),
    );
    $coumath = isset($input['data'])? count($input['data'])+7:7;
    //加边框
    $objPHPExcel->getActiveSheet()->getStyle( 'A1:P'.$coumath)->applyFromArray($styleThinBlackBorderOutline);
    $objPHPExcel->setActiveSheetIndex(0);
    //第二个表
    $objPHPExcel->createSheet();
    $objPHPExcel->setActiveSheetIndex(1);
    $objPHPExcel->getActiveSheet()->setTitle('银行工资单');
    $objPHPExcel = export_excel_salary_tow($objPHPExcel,$tow_table);
    //切换到第一个表
    $objPHPExcel->setActiveSheetIndex(0);

//    $sheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($key))->setWidth(20); //单元格宽度
    //水平居中
//    $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    /*--------------下面是设置其他信息------------------*/
//  $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
    $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,"Excel2007");
    //$time = $sql['time'];
    $name = $title .date('Y-m-d').".xlsx";
    header('Content-Disposition: attachment;filename="'.$name.'"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $PHPWriter->save("php://output");
    exit;
}

function export_excel_salary_tow($objPHPExcel,$tow_table)
{
    // 设置表头信息
    //第一行居中
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //给第一行赋值
    $objPHPExcel->getActiveSheet()->setCellValue('A1',$tow_table['top1']);
    //设置第一行行高 与列宽
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(35);
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(19);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(21);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(24);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
    //合并单元格 - 第一行
    $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
    //设置字体颜色 -- 设置第一行字体大小
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(22);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    //第二行
    foreach($tow_table['top2'] as $k => $v){
        //给第二行赋值
        $objPHPExcel->getActiveSheet()->setCellValue($k, $v);
        //垂直居中
        $objPHPExcel->getActiveSheet()->getStyle($k)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }
    //第三行
    foreach($tow_table['top3'] as $k => $v){
        //给第三行赋值
        $objPHPExcel->getActiveSheet()->setCellValue($k, $v);
        //垂直居中
        $objPHPExcel->getActiveSheet()->getStyle($k)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }
    //工资数据
    $add = 4;
    foreach($tow_table['top4'] as $k => $v){
        $add = $k + 4;
        foreach($v as $key=>$val){
            $objPHPExcel->getActiveSheet()->setCellValue($key . $add, $val);
            //垂直居中
            $objPHPExcel->getActiveSheet()->getStyle($key . $add)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }
    }
    //合计
    $count_address = $add + 1;
    foreach($tow_table['count'] as $k => $v){
        //给第三行赋值
        $objPHPExcel->getActiveSheet()->setCellValue($k . $count_address, $v);
        //垂直居中
        $objPHPExcel->getActiveSheet()->getStyle($k . $count_address)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }
    //边框加黑
    $styleThinBlackBorderOutline = array(
        'borders' => array(
            'allborders' => array( //设置全部边框
                'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
            ),
        ),
    );
    $coumath = isset($tow_table['top4'])? count($tow_table['top4'])+4:4;
    //加边框
    $objPHPExcel->getActiveSheet()->getStyle( 'A1:H'.$coumath)->applyFromArray($styleThinBlackBorderOutline);


    return $objPHPExcel;
}




/**
 * [getBankIDOrc 获取OCR识别]
 * @return [type] [description]
 * @param  $card_img_url //图片路径
 */
function getBankIDOrc($card_img_url)
{
//    $card_img_url = 'http://jyyg.dazhetech.com/images/hr.jpg';//汇融
    $host = "https://api06.aliyun.venuscn.com";
    $path = "/ocr/bank-card";
    $method = "POST";
    $appcode = config('config.id_card_appcode'); //
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    //根据API的要求，定义相对应的Content-Type
    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
    $querys = "";
    $bodys = "pic=".$card_img_url;
    //进行发送请求
    $res = card_curl_get($host,$path,$headers,$method,$bodys);
    $result = $res ? json_decode($res,true) :false;
    return $result;
}


/**
 * [getBankIDAndName 银行卡实名验证]
 * @return [type] [description]
 */
function getBankIDAndName($acct_name,$acct_pan,$id_card)
{
//    $host = "https://api06.aliyun.venuscn.com";
    $host = "https://ali-bankcard4.showapi.com";
    $path = "/bank3";
    $method = "GET";
    $appcode = config('config.id_card_appcode'); //
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    //根据API的要求，定义相对应的Content-Type
    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
    $querys = "";
    $bodys = "acct_name={$acct_name}&acct_pan={$acct_pan}&cert_id={$id_card}&cert_type=01&needBelongArea=true";

    $path =$path. '?'.$bodys;
    //进行发送请求
    $res = card_curl_get($host,$path,$headers,$method,$bodys);
    $result = $res ? json_decode($res,true) :false;
    return $result;
}


/*获取银行卡类型*/
/*
 *$cardNo 银行卡号
 *
 * */
function getBankType($cardNo)
{
    $path = 'validateAndCacheCardInfo.json?_input_charset=utf-8&cardBinCheck=true&cardNo='.$cardNo;
    $res  = card_curl_get('https://ccdcapi.alipay.com/',$path,[],'GET',[]);
    $result = $res ? json_decode($res,true) :false;
    return $result;
}

/**
 * 获取百度的OCR识别的token
 * @param $cardNo
 */
function getBaiduToken()
{
    $cache_res = cache('baidu_token');
    if($cache_res) return $cache_res;
    $host = 'https://aip.baidubce.com/oauth/2.0/token';
    $path = '?grant_type=client_credentials&client_id=yKVTLbCHyn5MRY5s5I70rrkS&client_secret=uXja0vmM8mSWAUVXVFYCgIEcfumMGA1l';
    $res = card_curl_get($host,$path,[],'POST',[]);
    $a = json_decode($res,true);;
    $token = isset($a['access_token']) ? $a['access_token'] : '';
    cache(['baidu_token'=>$token],60*24*28);
    return $token;
}

/**
 * 获取百度的OCR识别银行卡的卡号
//        $img_url = './images/zxk.jpg';
//        $img_url = './images/zxxyk.jpg';
//    $img_url = './images/hr.jpg';
 * @param $cardNo
 */
function getBaidubankNum($img_url)
{
    $token = getBaiduToken();
    $img = file_get_contents($img_url);
    $img = base64_encode($img);
    $bodys = array(
        "image" => $img
    );
    $host = 'https://aip.baidubce.com';
    $path = '/rest/2.0/ocr/v1/bankcard?access_token=' . $token;
    $res = card_curl_get($host,$path,[],'POST',$bodys);;
    $res = json_decode($res,true);
    if($res && isset($res['result']['bank_card_type']) && $res['result']['bank_card_type'] = '1' && isset($res['result']['bank_card_number'])){
        return str_replace(' ','',$res['result']['bank_card_number']);
    }else{
        return '';
    }
}

/**
 * 百度身份证识别
 * @param $img_url  图片URL
 * @param string $id_card_side  front正面  back反面
 * @return mixed
 */
function getBaiduIdCard($img_url,$id_card_side = 'front')
{
//    $cache_res = cache($img_url);
//    if($cache_res) return $cache_res;
    $token = getBaiduToken();
    $img = file_get_contents($img_url);
    $img = base64_encode($img);
    $bodys = array(
        "image" => $img,
        "id_card_side" => $id_card_side,
        "detect_risk" => 'true',
        "detect_direction" => 'true',
    );

    $host = 'https://aip.baidubce.com';
//    $path = '/rest/2.0/ocr/v1/bankcard?access_token=' . $token;
    $path = '/rest/2.0/ocr/v1/idcard?access_token=' . $token;
    $res = card_curl_get($host,$path,[],'POST',$bodys);;
    $res = json_decode($res,true);
//    cache([$img_url=>$res],'100');
    return $res;
}



/*
* base64位图片写入文件
 * @param base64_file base64位图片
 * @dir 上传路径
 * @$file_name 上传后图片名称 默认md5随机字符串
 * @return 全路径图片
* */
function filePutContent($base64_file,$dir='file',$file_name='')
{
    if (!$base64_file) return false;
    //生成图片路径
    $file_dir = "/uploads/{$dir}/" . date('Y-m') . '/';
    $path = '.' . $file_dir;
    $filename = $file_name ? $file_name . '.png' : md5(time() . mt_rand(1000, 9999)) . '.png';
    $file_path = $path . $filename;
    doDirName($path);
    file_put_contents($file_path, base64_decode($base64_file));
    return config('api.img_http_host') . $file_dir . $filename;
}

function doDirName($path){
    // 判断传过来的$path是否已是目录，若是，则直接返回true
    if(is_dir($path)) {
        return true;
    }
    // 走到这步，说明传过来的$path不是目录
    // 判断其上级是否为目录，是，则直接创建$path目录
    if(is_dir(dirname($path))) {
        return mkdir($path);
    }
    // 走到这说明其上级目录也不是目录,则继续判断其上上...级目录
    doDirName(dirname($path));

    // 走到这步，说明上级目录已创建成功，则直接接着创建当前目录，并把创建的结果返回
    return mkdir($path);
}


/**
 * [log 日志 简单版本]
 * @return [type][description]
 */
function putEasylog($parm,$dir='systeam')
{
    $path = public_path() . "/logs/{$dir}/";
//		$path = "./logs/htmllog/";  //第二种写法
    if (!file_exists($path)) mkdir($path, 0755, true);
    $url = $path . date('Y-m-d', time()) . '.log';
    $data = json_encode($parm, JSON_UNESCAPED_UNICODE);//参数
    $address = '[' . get_client_ip() . ']';//地址
    $content = date('Y-m-d H:i:s', time()) . $address . $data . "\r\n";
    file_put_contents($url, $content, FILE_APPEND);
}















