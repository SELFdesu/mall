社区团购平台使用说明：
数据库：MySQL、Redis

参数配置文件：/.env

MySQL数据库数据导入方式：
1、使用Laravel的数据库迁移，首先创建好MySQL数据库groupon_api，然后进入接口项目目录，执行 “php artisan migrate --seed” 命令。

前台登录：前台登录路由：/login 
后台登录：后台登录路由：/admin/login

接口项目运行：首先进入接口项目目录执行 “php artisan serve --port=80” 命令启动项目，然后执行命令“php artisan queue:work”启动Laravel队列，然后执行命令“php artisan order:expire”启动订单过期监听（Redis过期键监听），最后需要启动任务调度，可以使用命令“php artisan schedule:work”将任务调度挂载在前台运行。如运行环境为Linux环境可以配置系统的cron，将下列配置加入cron配置：“* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1”。

前台、后台项目运行：启动前端项目首先需要修改本地hosts文件将127.0.0.1映射到“grouponapi.top”,然后进入前台、后台项目文件夹，执行npm run dev命令即可运行前端页面。
