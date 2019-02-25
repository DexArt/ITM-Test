<?php
/**
 * Created by PhpStorm.
 * User: desig
 * Date: 21-Feb-19
 * Time: 21:27
 */

namespace App\Mailer;
use App\Consumer\MailSenderConsumer;
use App\Producer\MailSenderProducer;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Container\ContainerInterface;
use Swift_Mime_SimpleMessage;
use Swift_Transport;

class RabbitMqSpool extends \Swift_ConfigurableSpool
{
    protected $container;
    private $consumer;
    private $producer;

    public function __construct(ContainerInterface $container, ProducerInterface $producer, ConsumerInterface $consumer)
    {
        $this->container = $container;
        $this->consumer = $consumer;
        $this->producer = $producer;
    }

    public function start()
    {
        // TODO: Implement start() method.
    }

    public function stop()
    {
        // TODO: Implement stop() method.
    }

    public function isStarted()
    {
        return true;
    }

    public function flushQueue(Swift_Transport $transport, &$failedRecipients = null)
    {
        return $this->getConsumer()->consume($this->getMessageLimit());
    }

    public function queueMessage(Swift_Mime_SimpleMessage $message)
    {
        $serialized = serialize($message);
        $this->getMailProducer()->publish($serialized);
    }

    protected function getConsumer() {
        return $this->consumer;
    }


    protected function getMailProducer() {
        return $this->producer;
    }

}