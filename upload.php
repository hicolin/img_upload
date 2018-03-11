<?php
require_once('lib/function.php');
date_default_timezone_set('PRC');
if ($_GET['act'] == 'upload') {
    $upload = $_FILES['upload'];
    $info = $_POST['info'];
    $mark = intval($_POST['mark']);
    $scale = $_POST['scale'];
    if (!$upload) {
        show(0, '请选择图片');
    }
    if (!$info) {
        show(0, '请输入图片描述');
    }
    if ($mark != 0 && $mark != 1) {
        show(0, '请选择是否添加水印');
    }
    if (!$scale) {
        show(0, '请选择图片宽度比例');
    }

    // 获取缩略图宽高
    list($width, $height) = explode('*', $scale);
    $width = intval($width);
    $height = intval($height);
    if (!preg_match('/800|600|400/', $width)) {
        show(0, '请选择正确的宽度');
    }
    if (!preg_match('/600|450|300/', $height)) {
        show(0, '请选择正确的高度');
    }
    //print_r($upload);
    //print_r($_POST);

    // 文件保存目录
    $saveDir = './upload/';
    if (!file_exists($saveDir)) {
        if (!mkdir($saveDir, 0777, true)) {
            show('0', '文件保存路径错误,路径' . $saveDir . "创建失败");
        }
    }

    if (!is_uploaded_file($upload['tmp_name'])) {
        show(0, '非法文件，文件' . $upload['name'] . '不是post获得的');
    }
    // 获取文件上传错误
    if ($upload['error'] > 0) {
        $result = upload_error($upload['error']);
        show(0, $result);
    }
    //文件后缀
    $suffix = ltrim(strrchr($upload['name'], '.'), '.');
    $allowSuffix = array('jpg', 'jpeg', 'gif', 'png');
    //文件类型
    $mimeType = ltrim(strrchr($upload['type'], '/'), '/');
    if (!in_array($suffix, $allowSuffix) || !in_array($mimeType, $allowSuffix)) {
        show(0, '文件' . $upload['name'] . '为不允许上传的文件类型');
    }

    // 生成缩略图
    $newFileName = date('YmdHis', time()) . '_' . uniqid() . '_' . 'tb.' . $suffix;
    $dstImg = './upload/' . $newFileName;
    $dstImg = upload_image_thumbnail($mimeType, $upload['tmp_name'], $width, $height, $dstImg);

    // 是否加水印
    if ($mark) {    // 加水印
        $waterMark = './upload/water_mark.png';
        if (!file_exists($waterMark)) {
            show(0, '水印图片不存在');
        }
        $newName = date('YmdHis', time()) . '_' . uniqid() . '_' . 'tb_wm.' . $suffix;
        $newPath = './upload/' . $newName;
        $pct = 50;  // 透明度
        upload_image_water_mark($dstImg, $waterMark, 4, $pct, $newPath);
        //删除原图像
        unlink($dstImg);
        $dstImg = $newPath;
    }
    // 数据写入文件
    $dataArr = array(
        'info' => $info,
        'mark' => $mark,
        'scale' => $scale,
        'src' => $dstImg,
    );
    $data = var_export($dataArr, true);
    // 拼接写入内容 并写入文件
    $res = file_put_contents('data.php', '$data[]=' . $data . ";\r\n", FILE_APPEND);
    if (!$res) {
        unlink($dstImg);
        show(0, '上传失败', '请重新上传');
    }

    show(1, '上传成功');

}