<?php

function index($arr1,$arr2,$arr3,$arr4){
    ini_set('date.timezone','Asia/Shanghai');
    /*
    $ip = $_GET['ip'];
    $tag = $_GET['tag'];
    $port = $_GET['port'];
    $interval_time = 'DATE_ADD(now(),interval 12 hour)';
    */
   
    $ip = $arr1;
    $tag = $arr2;
    $port = $arr3;
    $interval_time = $arr4;

    require 'conn.php';

    $get_info="select create_time,qps from redis_status_history where host='${ip}' and tag='${tag}' and port=${port} and  
               create_time >=${interval_time} AND create_time <=NOW() group by FLOOR(UNIX_TIMESTAMP(create_time)/60)"; 
    $result1 = mysqli_query($con,$get_info);
	//echo $get_info;

  $array= array();
  class Connections{
    public $create_time;
    public $qps;
  }
  while($row = mysqli_fetch_array($result1,MYSQLI_ASSOC)){
    $cons=new Connections();
    $cons->create_time = $row['create_time'];
    $cons->qps = $row['qps'];
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

