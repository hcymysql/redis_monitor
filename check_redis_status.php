<?php

//https://www.runoob.com/redis/redis-php.html

error_reporting(E_USER_WARNING | E_USER_NOTICE);
ini_set('date.timezone','Asia/Shanghai');
require 'conn.php';
include 'mail/mail.php';
include 'weixin/weixin.php';

$redis_ip_info = mysqli_query($con,"select host,tag,pwd,port,monitor,send_mail,send_mail_to_list,send_weixin,send_weixin_to_list,threshold_alarm_threads_running from redis_status_info");

while( list($ip,$tag,$pwd,$port,$monitor,$send_mail,$send_mail_to_list,$send_weixin,$send_weixin_to_list,$threshold_alarm_threads_running) = mysqli_fetch_array($redis_ip_info))
{
	
	if($monitor==0 || empty($monitor)){
		echo "\n被监控主机：$ip  【{$tag}】【端口{$port}】未开启监控，跳过不检测。"."\n";
		continue;
	}

	$redis = new Redis();

	//https://github.com/phpredis/phpredis/issues/1641
	try {
		$redis->connect($ip, $port);
		$redis->auth($pwd);
		echo "Redis " .$ip.":" .$port ." is running: " . $redis->ping() .PHP_EOL;
	}
	catch(Exception $e){
		$connect_error='down';
		echo 'ERROR: Could not connect to Redis ' .$ip .':' .$port  .'   Message: ' .$e->getMessage() .PHP_EOL;
	}
	
	if ($redis->isConnected()) {
		$is_live=isset($connect_error)?0:1;
		//Redis INFO监控状态信息
		/************************************
		https://www.jianshu.com/p/378bc1fcc7f4
		https://www.cnblogs.com/nanxiang/p/14764196.html
		************************************/
		//$is_live=isset($connect_error)?0:1;
		$redis_info = $redis->info();
		$mode = $redis_info['redis_mode'];
		$redis_version = $redis_info['redis_version'];
		$tcp_port = $redis_info['tcp_port'];
		$uptime_in_days = $redis_info['uptime_in_days'];
		$maxclients = $redis_info['maxclients'];
		$connected_clients = $redis_info['connected_clients'];
		$blocked_clients = $redis_info['blocked_clients'];
		$rejected_connections = $redis_info['rejected_connections'];
		$maxmemory_human = $redis_info['maxmemory_human'];
		$used_memory_rss_human = $redis_info['used_memory_rss_human'];
		$used_memory_peak_human = $redis_info['used_memory_peak_human'];
		$role = $redis_info['role'];
		if($role=="master"){
			$master_repl_offset = $redis_info['master_repl_offset'];
			$connected_slaves = $redis_info['connected_slaves'];
			//自动发现
			for($i=0;$i<$connected_slaves;$i++){
				$slave_info = $redis_info['slave'.$i];
				$slave_host_array = explode(',',$slave_info);
				$slave_host = str_replace('ip=','',$slave_host_array[0]);
				$slave_port = str_replace('port=','',$slave_host_array[1]);
				$host_exists = mysqli_query($con,"select * from redis_status_info where host=${slave_host} AND port=${slave_port}");
				if (mysqli_num_rows($host_exists) == 0){
					$auto_slave_info_sql = "INSERT INTO redis_status_info (host,tag,pwd,port,monitor,send_mail,send_mail_to_list,send_weixin,send_weixin_to_list) 
					VALUES('$slave_host','$tag','$pwd','$slave_port','$monitor','$send_mail','$send_mail_to_list','$send_weixin','$send_weixin_to_list')";
					mysqli_query($con, $auto_slave_info_sql);
				}
			}
			} else {
			$master_repl_offset = $redis_info['master_repl_offset'];
			$slave_repl_offset = $redis_info['slave_repl_offset'];
			$Seconds_Behind_Master = $master_repl_offset - $slave_repl_offset;
			//自动发现
			$master_host = $redis_info['master_host'];
			$master_port = $redis_info['master_port'];
			$host_exists = mysqli_query($con,"select * from redis_status_info where host=${master_host} AND port=${master_port}");
			if (mysqli_num_rows($host_exists) == 0 and $role == "slave"){
				$auto_master_info_sql = "INSERT INTO redis_status_info (host,tag,pwd,port,monitor,send_mail,send_mail_to_list,send_weixin,send_weixin_to_list) 
				VALUES('$master_host','$tag','$pwd','$master_port','$monitor','$send_mail','$send_mail_to_list','$send_weixin','$send_weixin_to_list')";
				mysqli_query($con, $auto_master_info_sql);
			}		

			}
		$aof_enabled = $redis_info['aof_enabled'];
		$qps = $redis_info['instantaneous_ops_per_sec'];
		$cluster_enabled = $redis_info['cluster_enabled'];
		
		echo "----------基本信息----------" .PHP_EOL;
		echo "Redis的角色是：" .$role .PHP_EOL;
		echo "Redis的运行模式是：" .$mode .PHP_EOL;
		echo "Redis是否开启了集群模式：" .$cluster_enabled .PHP_EOL;
		echo "Redis的版本号是：" .$redis_version .PHP_EOL;
		echo "Redis的运行时间是：" .$uptime_in_days ." 天" .PHP_EOL;
		echo "Redis是否开启了AOF：" .$aof_enabled .PHP_EOL;
		echo PHP_EOL;
		echo "----------线程/会话----------" .PHP_EOL;
		echo "Redis的最大连接数是：" .$maxclients ." 个" .PHP_EOL;
		echo "Redis的当前连接数是：" .$connected_clients ." 个" .PHP_EOL;
		echo "Redis的被阻塞的连接个数是：" .$blocked_clients ." 个" .PHP_EOL;
		echo "Redis达到maxclients限制，拒绝新的连接个数是：" .$rejected_connections ." 个" .PHP_EOL;
		echo PHP_EOL;
		echo "----------内存----------" .PHP_EOL;
		echo "Redis的最大内存是：" .$maxmemory_human .PHP_EOL;
		echo "Redis已经使用内存是：" .$used_memory_rss_human .PHP_EOL;
		echo "Redis的内存消耗峰值是：" .$used_memory_peak_human .PHP_EOL;
		echo PHP_EOL;
		echo "----------性能----------" .PHP_EOL;
		echo "Redis每秒QPS是：" .$qps .PHP_EOL;		
	} else {
		$is_live=isset($connect_error)?0:1;
		$role=NULL;
	}
	
	//主机存活报警
	if($is_live==0){
	      unset($connect_error);

	    //告警---------------------  
	    if($send_mail==0 || empty($send_mail)){
			echo "被监控主机：$ip  【{$tag}】【端口{$port}】关闭邮件监控报警。"."\n";
	    } else {
			$alarm_subject = "【告警】被监控主机：".$ip."  【{$tag}】【端口{$port}】不能连接 ".date("Y-m-d H:i:s");
			$alarm_info = "被监控主机：".$ip."  【{$tag}】【端口{$port}】不能连接，请检查!";
			$sendmail = new mail($send_mail_to_list,$alarm_subject,$alarm_info);
			$sendmail->execCommand();
	    }
		
	    if($send_weixin==0 || empty($send_weixin)){
			echo "被监控主机：$ip  【{$tag}库】【端口{$port}】关闭微信监控报警。"."\n";
	    } else {
			$alarm_subject = "【告警】被监控主机：".$ip."  【{$tag}】【端口{$port}】不能连接 ".date("Y-m-d H:i:s");
			$alarm_info = "被监控主机：".$ip."  【{$tag}】【端口{$port}】不能连接，请检查!";
			$sendweixin = new weixin($send_weixin_to_list,$alarm_subject,$alarm_info);
			$sendweixin->execCommand();
	    }
		
            //-------------------------
	    $down_sql = "INSERT INTO redis_status(host,tag,port,role,is_live,create_time)  VALUES('{$ip}','{$tag}','{$port}','{$role}',{$is_live},now())"; 
	     	
	    if (mysqli_query($con, $down_sql)) {
		echo "\n{$ip}:'{$port}' 新记录插入成功\n";
		echo "-------------------------------------------------------------\n\n\n";
		mysqli_query($con,"INSERT INTO  redis_status_history(host,tag,port,role,is_live,create_time)  VALUES('{$ip}','{$tag}','{$port}','{$role}',{$is_live},now())");
	
		mysqli_query($con,"DELETE FROM redis_status where host='{$ip}' and port='{$port}' and create_time<DATE_SUB(now(),interval 3 second)");
	    } else {
		echo "\n{$ip}:'{$port}' 新记录插入失败\n";
		echo "Error: " . $sql . "\n" . mysqli_error($con);
	    }		
	   
	    //echo $down_sql."\n";
		
	} else {
	    //恢复---------------------
			if($send_mail==0 || empty($send_mail)){
				echo "被监控主机：$ip  【{$tag}】【端口{$port}】关闭邮件监控报警。"."\n";
			} else {
				$recover_sql = "SELECT is_live FROM redis_status_history WHERE HOST='{$ip}' AND PORT='{$port}' ORDER BY create_time DESC LIMIT 1";
				$recover_result = mysqli_query($con, $recover_sql);
				$recover_row = mysqli_fetch_assoc($recover_result);
			}
		if(!empty($recover_row) && $recover_row['is_live']==0){
			$recover_subject = "【恢复】被监控主机：".$ip."  【{$tag}】【端口{$port}】已恢复 ".date("Y-m-d H:i:s");
			$recover_info = "被监控主机：".$ip."  【{$tag}】【端口{$port}】已恢复";
			$sendmail = new mail($send_mail_to_list,$recover_subject,$recover_info);
			$sendmail->execCommand();
		}

		if($send_weixin==0 || empty($send_weixin)){
			echo "被监控主机：$ip  【{$tag}】【端口{$port}】关闭微信监控报警。"."\n";
		} else {
			$recover_sql = "SELECT is_live FROM redis_status_history WHERE HOST='{$ip}' AND PORT='{$port}' ORDER BY create_time DESC LIMIT 1";              
			$recover_result =  mysqli_query($con, $recover_sql);
			$recover_row = mysqli_fetch_assoc($recover_result);
		}
		
		if(!empty($recover_row) && $recover_row['is_live']==0){
			$recover_subject = "【恢复】被监控主机：".$ip."  【{$tag}】【端口{$port}】已恢复 ".date("Y-m-d H:i:s");
			$recover_info = "被监控主机：".$ip."  【{$tag}】【端口{$port}】已恢复";
			$sendweixin = new weixin($send_weixin_to_list,$recover_subject,$recover_info);
			$sendweixin->execCommand();
		}
		
		$sql = "INSERT INTO  redis_status(host,tag,port,role,is_live,max_connections,threads_connected,blocked_connected,rejected_connected,qps,maxmemory_human,used_memory_rss_human,used_memory_peak_human,runtime,db_version,aof_enabled,mode,cluster_enabled,Seconds_Behind_Master,create_time) 
		VALUES('{$ip}','{$tag}','{$tcp_port}','{$role}','{$is_live}','${maxclients}','${connected_clients}','${blocked_clients}','${rejected_connections}','{$qps}','{$maxmemory_human}','{$used_memory_rss_human}','{$used_memory_peak_human}','{$uptime_in_days}','{$redis_version}','{$aof_enabled}','${mode}','{$cluster_enabled}','${Seconds_Behind_Master}',now())";  
		
		//echo '$sql: '.$sql.'\n\n\n';
	
	//----------------------------------------------------
	
		if (mysqli_query($con, $sql)) {
			echo "\n{$ip}:'{$port}' 新记录插入成功\n";
			echo "-------------------------------------------------------------\n\n\n";
			mysqli_query($con,"INSERT INTO  redis_status_history(host,tag,port,role,is_live,max_connections,threads_connected,blocked_connected,rejected_connected,qps,maxmemory_human,used_memory_rss_human,used_memory_peak_human,runtime,db_version,aof_enabled,mode,cluster_enabled,Seconds_Behind_Master,create_time) 
			VALUES('{$ip}','{$tag}','{$tcp_port}','{$role}','{$is_live}','${maxclients}','${connected_clients}','${blocked_clients}','${rejected_connections}','{$qps}','{$maxmemory_human}','{$used_memory_rss_human}','{$used_memory_peak_human}','{$uptime_in_days}','{$redis_version}','{$aof_enabled}','${mode}','{$cluster_enabled}','${Seconds_Behind_Master}',now())");
		
			mysqli_query($con,"DELETE FROM redis_status where host='{$ip}' and port='{$port}' and create_time<DATE_SUB(now(),interval 3 second)");
		} else {
			echo "\n{$ip}:'{$port}' 新记录插入失败\n";
			echo "Error: " . $sql . "\n" . mysqli_error($con);
		}	
	}
	
}
?>
