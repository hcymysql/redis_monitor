<?php

function index($arr1,$arr2,$arr3,$arr4){
    ini_set('date.timezone','Asia/Shanghai');
    /*
    $ip = $_GET['ip'];
    $tag = $_GET['tag'];
    $port = $_GET['port'];
    */
   
    $ip = $arr1;
    $tag = $arr2;
    $port = $arr3;
    $interval_time = $arr4;

    require 'conn.php';
    $get_info="select create_time,used_memory_rss_human,used_memory_peak_human from redis_status_history where host='${ip}' and tag='${tag}' and port=${port} and  
               create_time >=${interval_time} AND create_time <=NOW()"; 
    $result1 = mysqli_query($con,$get_info);
	//echo $get_info;

  $array=array();
  class UsingIndex{
    public $create_time;
    public $used_memory_rss_human;
    public $used_memory_peak_human;
  }
  while($row = mysqli_fetch_array($result1,MYSQLI_ASSOC)){
    $cons=new UsingIndex();
    $cons->create_time = $row['create_time'];
    $cons->used_memory_rss_human = $row['used_memory_rss_human'];
    $cons->used_memory_peak_human = $row['used_memory_peak_human'];
    $array[]=$cons;
  }
  $top_data=json_encode($array);
  // echo "{".'"user"'.":".$data."}";
 echo $top_data;
}

/*$fn = isset($_GET['fn']) ? $_GET['fn'] : 'main';
if (function_exists($fn)) {
  call_user_func($fn);
}
*/

    $ip = $_GET['ip'];
    $tag = $_GET['tag'];
    $port = $_GET['port'];
    $interval_time = $_GET['interval_time'];

index($ip,$tag,$port,$interval_time);


?>

