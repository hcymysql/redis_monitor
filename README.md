# redis_monitor

轻便式Redis Monitor面向研发人员图形可视化监控工具，借鉴了LEPUS(天兔)监控平台以及redis-cli info命令输出的监控指标项，去掉了一些不必要看不懂的监控项，目前采集了数据库连接数、QPS、内存使用率统计和同步复制延迟时长。

采用远程连接方式获取数据，所以无需要在Redis服务器端部署相关agent或计划任务，可实现微信和邮件报警。

注：监控环境为Redis 6.2以上版本。

1）Redis状态监控 
![image](https://raw.githubusercontent.com/hcymysql/redis_monitor/main/image/redis_monitor.jpg)

2）点击图表，可以查看历史曲线图
