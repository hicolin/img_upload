<?php
/**
 * 公共函数文件
 */

/**
 * 后台向前台返回数据方法
 * @param $status
 * @param $message
 * @param array $data
 */
function show($status,$message,$data=array()){
    $result =array(
        'status'=>$status,
        'message'=>$message,
        'data'=>$data
    );
    exit(json_encode($result));
}

/**
 * 匹配文件上传的错误
 * @param $error ：int
 * @return string
 */
function upload_error($error){
    $result = '';
    switch($error){
        case 1:
            $result = '上传文件超过了php配置文件中uoload_max_filesize选项的值';
            break;
        case 2:
            $result = '超过HTML表单MAX_FILE_SIZE限制的大小';
            break;
        case 3:
            $result = '文件部分被上传';
            break;
        case 4;
            $result = '没有选择上传文件';
            break;
        case 6:
            $result = '没有找到临时目录';
            break;
        case 7:
            $result = '文件写入失败';
            break;
        case 8:
            $result = '上传的文件被PHP扩展程序中断';
            break;
    }
    return $result;
}

/**
 * 上传的图片处理为缩略图
 * @param $mimeType：上传图片的类型
 * @param $tmpImg：上传图片的临时目录
 * @param $width：缩略图宽度
 * @param $height：缩略图高度
 * @param $dstImg：缩略图文件
 * @return string: 缩略图路径
 */
function upload_image_thumbnail($mimeType,$tmpImg,$width,$height,$dstImg){
    $imgInfo = getimagesize($tmpImg);
    $imgCreateFuc = 'imagecreatefrom'.$mimeType;
    $srcSource = $imgCreateFuc($tmpImg);  // 原图像资源
    $srcW = $imgInfo[0];
    $srcH = $imgInfo[1];
    $dstSource = imagecreatetruecolor($width,$height); // 创建缩略图资源
    imagecopyresampled($dstSource,$srcSource,0,0,0,0,$width,$height,$srcW,$srcH); // 复制图像并改变大小
    $imgFuc = 'image'.$mimeType;
    $imgFuc($dstSource,$dstImg);
    imagedestroy($srcSource);
    imagedestroy($dstSource);
    return $dstImg;
}

/**
 * 图像添加水印
 * @param $srcImg：原图像路径
 * @param $waterMark：水印图像路径
 * @param $posType：水印位置参数
 * @param $pct：透明度
 * @param $newName：保存的文件路径
 * @return mixed：保存的文件路径
 */
function upload_image_water_mark($srcImg,$waterMark,$posType,$pct,$newName){
    $srcImgInfo = getimagesize($srcImg);
    $waterMarkInfo = getimagesize($waterMark);
    $srcImgSuffix = ltrim(strrchr($srcImgInfo['mime'],'/'),'/');
    $waterMarkSuffix = ltrim(strrchr($waterMarkInfo['mime'],'/'),'/');
    $srcImgcreateFunc = 'imagecreatefrom'.$srcImgSuffix;
    $waterMarkFunc = 'imagecreatefrom'.$waterMarkSuffix;
    $srcSource = $srcImgcreateFunc($srcImg);
    $waterMarkSource = $waterMarkFunc($waterMark);
    $srcW = $srcImgInfo[0];
    $srcH = $srcImgInfo[1];
    $wmW = $waterMarkInfo[0];
    $wmH = $waterMarkInfo[1];
    // 水印图片位置
    switch($posType){
        case 1:  // 左上角
            $dst['width'] = 0;
            $dst['height'] = 0;
            break;
        case 2:  // 右上角
            $dst['width'] = $srcW-$wmW;
            $dst['height'] = 0;
            break;
        case 3:  // 左下角
            $dst['width'] = 0;
            $dst['height'] = $srcH-$wmH;
            break;
        case 0:  // 中心
            $dst['width'] = ($srcW-$wmW)/2;
            $dst['height'] = ($srcH-$wmH)/2;
            break;
        case 4:  // 右下角
        default:
        $dst['width'] = $srcW-$wmW;
        $dst['height'] = $srcH-$wmH;
            break;
    }
    imagecopymerge($srcSource,$waterMarkSource,$dst['width'],$dst['height'],0,0,$wmW,$wmH,$pct);
    $srcImgFunc = 'image'.$srcImgSuffix;
    $srcImgFunc($srcSource,$newName);
    imagedestroy($srcSource);
    imagedestroy($waterMarkSource);
    return $newName;
}

/**
 * 分页
 * @param $page：当前页码
 * @param $totalPage：总页数
 * @return string：分页字符串
 */
function show_pages($page,$totalPage){
    // 获取当前路径
    if(isset($_GET['key'])){
        $url = $_SERVER['PHP_SELF']."?key=".$_GET['key'];
        $sep = '&';
    }else{
        $url = $_SERVER['PHP_SELF'];
        $sep = '?';
    }
    $index = ($page == 1) ? "<li><a style='color:black' aria-label='Previous'><span aria-hidden='true'>首页</span></a></li>" : "<li><a href='{$url}{$sep}page=1'aria-label='Previous'><span aria-hidden='true'>首页</span></a></li>";
    $last = ($page == $totalPage) ? "<li><a style='color:black' aria-label='Next'><span aria-hidden='true'>尾页</span></a></li>" : "<li><a href='{$url}{$sep}page={$totalPage}' aria-label='Next'><span aria-hidden='true'>尾页</span></a></li>";
    $p = "";
    if($totalPage<=5){
        for($i = 1;$i <= $totalPage;$i++){
            // 当前页 无链接
            if($page == $i){
                $p .= "<li><a style='color:black'>{$i}</a></li>";
            }else{
                $p .= "<li><a href='{$url}{$sep}page={$i}'>{$i}</a></li>";
            }
        }
    }else{
            for($i = $page;$i <= $page+5;$i++){
                // 当前页 无链接
                if($page == $i){
                    $p .= "<li><a style='color:black'>{$i}</a></li>";
                }else{
                    $p .= "<li><a href='{$url}{$sep}page={$i}'>{$i}</a></li>";
                }
            }
    }
    $pageStr =$index.$p.$last;
    return $pageStr;  // 返回分页字符串
}

/**
 * 获取IP地址
 * @return mixed|string
 */
function ip_address(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip_address = array_pop(explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']));
    }elseif (!empty($_SERVER['REMOTE_ADDR'])){
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }else{
        $ip_address = '';
    }
    return $ip_address;
}