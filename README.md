
## 环境要求

- PHP 最低 8.2
- mysql 最低 5.7

## 部署

- 开启OneBot的正向WebSocket服务，获取端口
- 增加或者修改环境配置文件ZEROBOT_WS的ws连接
- 增加守护进程执行命令：php artisan app:one-bot-ws-command
- 增加或者修改环境配置文件中的数据库配置

