<?php

    //date_default_timezone_set(PRC);
    ini_set('date.timezone','Asia/Shanghai');
    /*
    session_start();

    //检测是否登录，若没登录则转向登录界面
    if(!isset($_SESSION['userid'])){
        header("Location:../index.html");
        exit("你还没登录呢。");
    }*/
    
?>

<!doctype html>
<html class="x-admin-sm">
<head>
    <meta http-equiv="Content-Type"  content="text/html;  charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="refresh" content="600" />  <!-- 页面刷新时间600秒 -->
    <title>Redis 状态监控</title>

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

<script language="javascript">
function TestBlack(TagName){
 var obj = document.getElementById(TagName);
 if(obj.style.display=="block"){
  obj.style.display = "none";
 }else{
  obj.style.display = "block";
 }
}
</script>

<!--
<script>
function ss(){
var slt=document.getElementById("select");
if(slt.value==""){
        alert("请选择数据库!!!");
        return false;
}
return true;
}
</script>
-->
</head>

<body>
<div class="card">
<div class="card-header bg-light">
    <h1><a href="redis_status_monitor.php">
    <img src='https://redis.com/wp-content/themes/wpx/assets/images/logo-redis.svg?auto=webp&quality=85,75&width=120'/>  状态监控</a></h1>
</div>
      
<div class="card-body">
<div class="table-responsive">
                
<form action="" method="post" name="sql_statement" id="form1" onsubmit=" return ss()">
  <div>
    <tr>
        <td><p align='left'>输入IP地址:
 	   <input type='text' name='dbip' value=''>	

           <select id="select" name="tag">
	     <option value="">选择数据库标签</option>
	<?php
	
	require 'conn.php';
	$result = mysqli_query($con,"SELECT tag FROM redis_status_info group by tag");
	while($row = mysqli_fetch_array($result)){
		//保留下拉列表框选项
                    if(isset($_POST['tag']) || isset($_GET['tag'])){
                        if($_POST['tag'] == $row[0] || $_GET['tag'] == $row[0]){
                            echo "<option selected='selected' value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
                        } else {
                            echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
                        }
                    } else{ echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";}

		//echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
    }
	
    ?>
        </select>

	<select id="select" name="dbrole">
	    <option value="">选择角色</option>
	<?php
	    require 'conn.php';
            $result = mysqli_query($con,"SELECT DISTINCT(role) FROM redis_status");	
            while($row = mysqli_fetch_array($result)){
		//$dbrole_original=$row[0];
		//$dbrole=$row[0]==1?'主':'从';
                //保留下拉列表框选项
                    if(isset($_POST['dbrole']) || isset($_GET['dbrole'])){
			//$dbrole=$row[0]==1?'是':'否';
                        if($_POST['dbrole'] == $row[0] || $_GET['dbrole'] == $row[0]){
			    //$dbrole=$row[0]==1?'是':'否';
                            echo "<option selected='selected' value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
                        } else { 
                            echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
                        }
                    } else{ echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";}
                
                //echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
            }
	?>
        </select>
	

            &nbsp;&nbsp;输入Reis端口号:
           <input type='text' name='dbport' value=''>
<td>
    </tr>
    <input name="submit" type="submit" class="STYLE3" value="搜索" />
    </label>
  </div>
</form>


<?php
echo "<table border='0' width='100%'>";
echo "<tr>";
echo "<td>监控采集阀值是每1分钟/次</td>";
echo "<td align='right'>最新监控时间:".date('Y-m-d H:i:s')."</td>";
echo "</tr>";
echo "</table>";
echo "<p>";
	
    if(isset($_POST['submit'])){
        $tag=$_POST['tag'];
        $dbip=$_POST['dbip'];
        $dbport=$_POST['dbport'];
	$dbrole=$_POST['dbrole'];
        //session_start();
	//$_SESSION['transmit_tag']=$tag;
        //require 'show.html';
    } else {
	//require 'top.html';
    }

?>

<table border="1" width="100%" bgcolor="#B2DBFB">
        <tr>
                <td width="780" align="center">基本信息</td>
                <td width="550" align="center">线程/会话</td>
                <td width="400" align="center">内存</td>
                <td width="158" align="center">性能</td>
        </tr>
</table>

<table style='width:100%;font-size:14px;' class='table table-hover table-condensed'>                                    
<thead>                                   
<tr>                                                                         
<th>主机</th>
<th>数据库标签</th>
<th>端口</th>
<th>角色</th>
<th>状态</th>
<th>运行模式</th>
<th>版本</th>
<th>运行时间</th>
<th>开启AOF</th>
<th>最大连接数</th>
<th>当前连接数</th>
<th>被阻塞连接数</th>
<th>被拒绝连接数</th>
<th>最大内存</th>
<th>已经使用内存</th>
<th>内存消耗峰值</th>
<th>每秒QPS</th>
<th>图表</th>
</tr>
</thead>
<tbody>

<?php
    require 'conn.php';

$perNumber=500; //每页显示的记录数  
$page=$_GET['page']; //获得当前的页面值  
$count=mysqli_query($con,"select count(*) from redis_status"); //获得记录总数
$rs=mysqli_fetch_array($count);   
$totalNumber=$rs[0];  
$totalPage=ceil($totalNumber/$perNumber); //计算出总页数  

if (empty($page)) {  
 $page=1;  
} //如果没有值,则赋值1

$startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录 

    $condition.="1=1 ";	
    if(!empty($tag)){
    	$condition.=" AND tag='{$tag}'";
    }
    if(!empty($dbip)){
    	$condition.=" AND host='{$dbip}'";
    }
    if(!empty($dbport)){
    	$condition.=" AND port='{$dbport}'";
    }
    if(!empty($dbrole)){
        $condition.=" AND role='{$dbrole}'";
    }
   
	$sql = "SELECT * FROM redis_status WHERE $condition order by host ASC,id ASC LIMIT $startCount,$perNumber";
 	//echo $sql."<br>";   

$result = mysqli_query($con,$sql);

//echo "复制监控采集阀值是每1分钟/次    最新监控时间：".date('Y-m-d H:i:s')."</br>";

while($row = mysqli_fetch_array($result)) 
{
    if($row['is_live']==0){
	$role='<span class="badge badge-secondary">未知</span>';
    } else {
	$role=$row['role']=='slave'?'<span class="badge badge-warning">slave</span>':'<b><span class="badge badge-primary">master</span></b>';
    }
$status=$row['is_live']==1?'<b><span class="badge badge-success">在线</span></b>':'<span class="badge badge-danger">宕机</span>';
echo "<tr>";
echo "<td>{$row['host']}</td>";
echo "<td>{$row['tag']}</td>";
//echo "<td><a href='javascript:void(0);' onclick=\"x_admin_show('统计库里每个表的大小','table_statistic.php?ip={$row['1']}&tag={$row['2']}&port={$row['3']}')\">{$row['2']}</a></td>";
echo "<td>{$row['port']}</td>";
echo "<td>{$role}</td>";
echo "<td>{$status}</td>";
echo "<td>{$row['mode']}</td>";
echo "<td>{$row['db_version']}</td>";
echo "<td>{$row['runtime']}天</td>";
echo "<td>{$row['aof_enabled']}</td>";
echo "<td>{$row['max_connections']}</td>";
echo "<td>{$row['threads_connected']}</td>";
echo "<td>{$row['blocked_connected']}</td>";
echo "<td>{$row['rejected_connected']}</td>";
//echo "<td><a href='javascript:void(0);' onclick=\"x_admin_show('连接数详情','db_connect_statistic.php?ip={$row['1']}&tag={$row['2']}&port={$row['3']}')\">{$row['7']}</a></td>";
echo "<td>{$row['maxmemory_human']}</td>";
echo "<td>{$row['used_memory_rss_human']}</td>";
echo "<td>{$row['used_memory_peak_human']}</td>";
echo "<td>{$row['qps']}</td>";
echo "<td><a href='javascript:void(0);' onclick=\"x_admin_show('历史信息图表','show_graph.php?ip={$row['1']}&tag={$row['2']}&port={$row['3']}')\"><img src='image/chart.gif' /></a></td>";
echo "</tr>";
}
//end while
echo "</tbody>";
echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

$maxPageCount=10; 
$buffCount=2;
$startPage=1;
 
if  ($page< $buffCount){
    $startPage=1;
}else if($page>=$buffCount  and $page<$totalPage-$maxPageCount  ){
    $startPage=$page-$buffCount+1;
}else{
    $startPage=$totalPage-$maxPageCount+1;
}
 
$endPage=$startPage+$maxPageCount-1;
 
 
$htmlstr="";
 
$htmlstr.="<table class='bordered' border='1' align='center'><tr>";
    if ($page > 1){
        $htmlstr.="<td> <a href='redis_status_monitor.php?page=" . "1" . "'>第一页</a></td>";
        $htmlstr.="<td> <a href='redis_status_monitor.php?page=" . ($page-1) . "'>上一页</a></td>";
    }

    $htmlstr.="<td> 总共${totalPage}页</td>";

    for ($i=$startPage;$i<=$endPage; $i++){
         
        $htmlstr.="<td><a href='redis_status_monitor.php?page=" . $i . "'>" . $i . "</a></td>";
    }
     
    if ($page<$totalPage){
        $htmlstr.="<td><a href='redis_status_monitor.php?page=" . ($page+1) . "'>下一页</a></td>";
        $htmlstr.="<td><a href='redis_status_monitor.php?page=" . $totalPage . "'>最后页</a></td>";
 
    }
$htmlstr.="</tr></table>";
echo $htmlstr;

?>
