<?php
/**
 *  rabbitmq 消息队列
 */
namespace App\Http\Controllers\Api;

class RabbitmqController
{
    private static $mqobject = null;
    private static $channel = null;

    /**
     * @param $channelName string 交换机名称
     * @param $queueName  string 使用的队列名称
     */
    private static function connect($channelName)
    {
        $connect_list = ['host'=>env('MQ_HOST'), 'port'=>env('MQ_PORT'), 'login'=>env('MQ_USER'), 'password'=>env('MQ_PWD'),'vhost'=>env('MQ_VHOST')];
        $conn = new \AMQPConnection($connect_list);
        if (!$conn->connect()) {
            die("Cannot connect to the broker \n ");
        }
        $channel = new \AMQPChannel($conn);

        //创建交换机
        $ex = new \AMQPExchange($channel);
        $ex->setName($channelName);//交换机名
        $ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
        $ex->setFlags(AMQP_DURABLE); //持久化

        self::$channel = $channel;
        self::$mqobject = $ex;
        return $ex;
    }

    /**
     * 生产消息
     * @param $data 参数数组 [交换机名，队列名称，队列路由KEY，发送的消息（json）]
     */
    public static function publishMsg($data)
    {
        list($channelName,$queueName, $routeKey,$msg)  = $data;
        $ex = self::$mqobject ? self::$mqobject : self::connect($channelName);
        //创建队列
        $queue = new \AMQPQueue(self::$channel);
        $queue->setName($queueName);
        $queue->setFlags(AMQP_DURABLE); //持久化
        $queue->bind($channelName, $routeKey);

        $res = $ex->publish($msg,$routeKey);//进行消息推送操作
        return $res;
    }

    /**
     * 消费消息
     * @param $data 参数数组 [交换机名，队列名称]
     * @param $callback func
     */
    public static function getMsg($data,$callback)
    {
        list($channelName,$queueName)  = $data;
        $ex = self::$mqobject ? self::$mqobject : self::connect($channelName);
        //创建队列
        $queue = new \AMQPQueue(self::$channel);
        $queue->setName($queueName);
        $queue->setFlags(AMQP_DURABLE); //持久化
//        $queue->bind($channelName, $routeKey);
        //阻塞模式接收消息,只有消费端成功执行完成任务后，告诉MQ可以释放该消息
        $queue->consume($callback, AMQP_AUTOACK); //自动ACK应答
    }
}