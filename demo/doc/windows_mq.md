
```
rabbitmq依赖运行环境erlang.
http://www.erlang.org/downloads
http://www.rabbitmq.com/install-windows.html
http://www.rabbitmq.com/management.html
windows下安装rabbitmq：
    1. 双击安装erlang: otp_win64_19.3.exe
        安装位置： D:\Program Files\erl8.3
        设置环境变量PATH=D:\Program Files\erl8.3\bin;
             ERLANG_HOME=D:\Program Files\erl8.3
    2. 双击安装rabbitmq: rabbitmq-server-3.6.9.exe
    	安装位置： D:\Program Files\RabbitMQ Server
    	设置环境变量： PATH=D:\Program Files\RabbitMQ Server\rabbitmq_server-3.6.9\sbin
    	以管理员身份运行：rabbitmq-service.bat 文件，则会把rabbitmq加入服务中。
    	WIN+R->services.msc 回车  查看rabbitmq服务会存在

    	cd到安装目录/sbin目录，可操作命令如下：
    	安装成服务：rabbitmq-service.bat install   或者双击安装成服务
    	停止服务： rabbitmq-service.bat stop 
		启动服务： rabbitmq-service.bat start
		从服务中移除： rabbitmq-service.bat remove

		安装web插件： rabbitmq-plugins.bat enable rabbitmq_management
        列出所有插件： rabbitmq-plugins.bat list
        浏览器中访问： 浏览器访问http://127.0.0.1:15672/ 默认账号：guest  密码：guest

```
###other
For Homebrew on OS X:  brew install erlang
For MacPorts on OS X:  port install erlang
For Ubuntu and Debian: apt-get install erlang
For Fedora:            yum install erlang
For FreeBSD:           pkg install erlang


