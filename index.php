<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>文件上传和下载</title>
    <!-- Bootstrap -->
    <link href="style/css/bootstrap.min.css" rel="stylesheet">
    <link href="style/css/site.min.css" rel="stylesheet">
    <style>
        .projects .thumbnail .caption {
            height: auto;
            max-width: 100%;
        }
        .image{
            margin:10px auto;
            border-radius:5px;
            overflow:hidden;
            border:1px solid #CCC;
        }
        .image .caption P{
            text-align: center;
        }
    </style>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="style/js/jquery.min.js"></script>
    <script src="style/js/layer/layer.js"></script>
    <script src="style/js/dialog.js"></script>
</head>
<?php
include_once('data.php');
include_once('lib/function.php');
// 数组按键逆向排序
krsort($data);
//关键词搜索
if (isset($_GET['key'])) {
    $keyword = $_GET['key'];
    if (empty($keyword)) {
        echo "<script>dialog.error('搜索内容不能为空')</script>";
    }
    $newData = array();
    foreach ($data as $value) {
        if (preg_match("/$keyword/", $value['info'])) {
            $newData[] = $value;
        }
    }
    //print_r($newData);
    $data = $newData;
    if (count($data) == 0) {
        echo "<script>dialog.error('搜索的内容不存在');</script>";
    }
}
// 分页
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$countNum = count($data);
$perPage = 9;
$totalPage = ceil($countNum / $perPage);
$page = $page > $totalPage ? $totalPage : $page;
// 按当前页码取出一定数量的数据
if ($totalPage == 0) { //空数组不报错
    $data = array();
} else {
    $data = array_chunk($data, $perPage);
    $index = $page - 1;
    $data = $data[$index];
}

?>
<body>
    <!--导航栏-->
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand hidden-sm" href="index.php" >慕课网</a>
            </div>
            <div class="navbar-collapse collapse" role="navigation">
                <ul class="nav navbar-nav">
                    <li class="hidden-sm hidden-md">
                        <a href="" target="_blank"></a>
                    </li>
                    <li>
                        <a href="" target="_blank"></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--导航栏结束-->
    <!--巨幕-->
    <div class="jumbotron masthead">
        <div class="container">
            <h1>文件上传下载</h1>
            <h2>实现文件的上传和下载功能</h2>
            <p class="masthead-button-links">
                <form class="form-inline" action="index.php" method="get">
                    <div class="form-group">
                        <input type="text" class="form-control" id="exampleInputName2" placeholder="输入搜索内容" name="key" value="">
                        <button class="btn btn-default" type="submit">搜索</button>
                        <button type="button" class="btn btn-primary btn-default" data-toggle="modal" data-target="#myModal">  上传  </button>
                    </div>
                </form>
            </p>
        </div>
    </div>
    <!--巨幕结束-->
    <!-- 模态框 -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form class="form-inline" id="modelForm"  method="post" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">上传图片</h4>
                    </div>
                    <div class="modal-body">
                        <p>选择图片：</p><input type="file" id="image" name="upload">
                        <br/>
                        <p>图片描述：</p><textarea class="form-control" cols="75" name="info" id="info"></textarea>
                        <br/><br/>
                        <p>
                          是否添加水印：
                          <select name="mark">
                            <option value="1">添加</option>
                            <option value="0">不添加</option>
                          </select>
                        </p>
                        <br/>
                        <p>
                          图片宽度比例：
                            <select name="scale">
                              <option value="800*600">800*600</option>
                              <option value="600*450">600*450</option>
                              <option value="400*300">400*300</option>
                            </select>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" name="submit" id="submit" onclick="show(this)">上传</button>
                        <button type="reset" class="btn btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--模态框结束-->

    <div class="container projects">
        <div class="projects-header page-header">
            <h2>上传图片展示</h2>
            <p>将上传的图片展示在页面中</p>
        </div>
        <div class="row">
            <?php
            if(count($data) == 0){
                echo '<h3 style="text-align: center">没有找到搜索的内容</h3>';
            }else{
                foreach ($data as $value){
            ?>
                <div class="col-sm-6 col-md-3 col-lg-4 ">
                    <div class="image">
                        <a href="#" target="_blank"><img class="img-responsive" src="<?=$value['src']?>"></a>
                        <div class="caption">
                            <p>
                                <?=$value['info']?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php
            }
              }
            ?>
        <!--分页-->
        <nav aria-label="Page navigation" style="text-align:center;clear: both">
            <ul class="pagination pagination-lg">
                <?php echo show_pages($page,$totalPage)?>
            </ul>
        </nav>
    </div>

    <footer class="footer  container">
        <div class="row footer-bottom">
            <ul class="list-inline text-center">
                <h4><a href="class.imooc.com" target="_blank">class.imooc.com</a> | 慕课网</h4>
            </ul>
        </div>
    </footer>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="style/js/bootstrap.min.js"></script>
    <script src="style/js/jquery.form.js"></script>
    <script type="text/JavaScript">
        // ajax提交表单
        function show() {
            var error = '';
            //var data = $('#modelForm').serialize();
            if (document.getElementById("image").value == '') {
                error += '请选择图片\n';
            }
            if (document.getElementById("info").value == '') {
                error += '请输入图片描述';
            }
            if (error) {
                dialog.error(error);
                return false;
            }
            //console.log(data);
            var options = {
                url: 'upload.php?act=upload',
                type: 'POST',
                dataType: 'json',
                success: function (result) {
                    if (result.status == 0) {
                        dialog.error(result.message);
                    } else if (result.status == 1) {
                        dialog.success(result.message, 'index.php');
                    }
                }
            };
            $('#modelForm').ajaxSubmit(options);
        }
    </script>
</body>

</html>
