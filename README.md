# cjsRabbitmq

```
Broker：  消息队列服务器实体。
Exchange：消息交换机，它指定消息按什么规则，路由到哪个队列。
Queue：   消息队列载体，每个消息都会被投入到一个或多个队列。
Binding： 绑定，它的作用就是把exchange和queue按照路由规则绑定起来。
Routing Key：路由关键字，exchange交换机根据这个关键字进行消息投递。
vhost：   虚拟主机，一个broker里可以开设多个vhost，用作不同用户的权限分离，权限最小粒度。
producer：消息生产者，就是投递消息的程序，产生消息。
consumer：消息消费者，就是接受消息的程序，消费消息。
channel： 消息通道，在客户端的每个连接里，可建立多个channel，每个channel代表一个会话任务。

消息队列的发送过程大概如下：
（1）客户端连接到消息队列服务器，并打开一个channel通道。
（2）客户端声明一个exchange，并设置相关属性。
（3）客户端声明一个queue，并设置相关属性。
（4）客户端使用routing key，在exchange和queue之间建立好绑定关系。
（5）客户端投递（发送）消息到exchange。
备注： exchange接收到消息后，就根据消息的key和已经设置的binding，进行消息路由，将消息投递到一个或多个队列里

消息队列持久化包括3个部分：
　　（1）exchange持久化，在声明时指定durable => 1
　　（2）queue持久化，   在声明时指定durable => 1
　　（3）消息持久化，    在投递时指定delivery_mode => 2（1是非持久化）
    如果exchange和queue都是持久化的，那么它们之间的binding也是持久化的。
    如果exchange和queue两者之间有一个持久化，一个非持久化，就不允许建立绑定


编写代码前期准备：
1. 启动rabbitmq-server服务，=》ip和端口5672
2. 创建一个vhost =》可解决各项目冲突问题
    rabbitmqctl add_vhost /vhost名  如 rabbitmqctl add_vhost /user_queue
3. 新增加一个账户&密码
    rabbitmqctl add_user 帐号 密码  如 rabbitmqctl add_user cjs 888888
4. 为账户设置权限
    rabbitmqctl set_permissions -p /vhost名 账户 “配置权限” “写权限” “读权限”  如 rabbitmqctl set_permissions -p /user_queue cjs “.*” “.*” “.*”


```
