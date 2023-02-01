<?php

    date_default_timezone_set(PRC);
    ini_set('date.timezone','Asia/Shanghai');
    /*
    session_start();
    //检测是否登录，若没登录则转向登录界面
    if(!isset($_SESSION['userid'])){
        header("Location:../index.html");
        exit("你还没登录呢。");
    }*/
  
	$ip = $_GET['ip'];
	$tag  = $_GET['tag'];
	$port = $_GET['port'];
?>

<!doctype html>
<html class="x-admin-sm">
<head>
    <meta http-equiv="Content-Type"  content="text/html;  charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="refresh" content="60" />
    <title>列出当前所有慢查询</title>

<style type="text/css">
a:link { text-decoration: none;color: #3366FF}
a:active { text-decoration:blink;color: green}
a:hover { text-decoration:underline;color: #6600FF}
a:visited { text-decoration: none;color: green}
</style>

    <script type="text/javascript" src="xadmin/js/jquery-3.3.1.min.js"></script>
    <script src="xadmin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="xadmin/js/xadmin.js"></script>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="./css/font-awesome/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>

<div class="col-md-05">
<div class="card">
<div class="card-body">
<div class="table-responsive">

<?php
    require 'conn.php';
    $get_info="select pwd from redis_status_info where host='${ip}' and port=${port}";
    $result1 = mysqli_query($con,$get_info);
    list($pwd) = mysqli_fetch_array($result1);
?>

<h3>列出<?php echo $tag;?>当前所有慢查询</h3>
<table border='0' width='100%'>
<table style='width:100%;font-size:14px;' class='table table-hover table-condensed'>
<thead>
<tr>
<th>命令执行时的时间</th>
<th>命令消耗的时间</th>
<th>执行的命令</th>
<th>客户端地址和端口</th>
</tr>
</thead>

<tbody>
<?php

        $redis = new Redis();

        //https://github.com/phpredis/phpredis/issues/1641
        try {
                $redis->connect($ip, $port);
                $redis->auth($pwd);
                echo "Redis " .$ip.":" .$port ." is running: " . $redis->ping() .PHP_EOL;
        }
        catch(Exception $e){
                die('ERROR: Could not connect to Redis ' .$ip .':' .$port  .'   Message: ' .$e->getMessage() .PHP_EOL);
        }

	   if ($redis->isConnected()) {
			$slowlog_info = $redis->slowlog('get',-1);
			foreach ($slowlog_info as $value){
				echo "<tr>";
				$exec_time = date('Y-m-d H:i:s', $value[1]);
				$slow_time = $value[2]/1000;
				$slow_cmd = $value[3];
				$client_info = $value[4];
				echo "<td>".$exec_time."</td>";
				echo "<td>".$slow_time."（单位毫秒）"."</td>";
				echo "<td>";
				foreach ($slow_cmd as $cmd){
					echo $cmd."<br>";
				}
				echo "</td>";
				echo "<td>".$client_info."</td>";
			      echo "</tr>";
			}
	   }
			
echo "</tbody>";
echo "</table>";

?>

<!----------------------------------------------------------------------------------------->

</div>
</div>
</div>
</div>
</body>
</html>
