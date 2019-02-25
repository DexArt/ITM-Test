<?php
/**
 * Created by PhpStorm.
 * User: desig
 * Date: 21-Feb-19
 * Time: 18:49
 */

namespace App\Consumer;


use App\Services\SmsService;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Container\ContainerInterface;
use Swift_Message;


class MailSenderConsumer implements ConsumerInterface
{
    private $delayedProducer;

    private $entityManager;

    private $container;

    private $transport;

    /**
     * MailSenderConsumer constructor.
     * @param ProducerInterface $delayedProducer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ProducerInterface $delayedProducer, EntityManagerInterface $entityManager, ContainerInterface $container, \Swift_Transport $transport)
    {
        $this->delayedProducer = $delayedProducer;
        $this->entityManager   = $entityManager;
        $this->container       = $container;
        $this->transport       = $transport;
        gc_enable();
    }

    public function execute(AMQPMessage $msg)
    {

        $userMessage = unserialize($msg->getBody());
        if ($userMessage->type === 'email') {

            try {
                $userMessage = $this->replaceMessageText($userMessage);
                $this->sendEmail($userMessage);

            } catch (\Exception $exception) {
                echo 'Email Error';
                $this->delayedProducer->publish(serialize($userMessage));
            }
        } elseif ($userMessage->type === 'phone') {

            try {
                $userMessage = $this->replaceMessageText($userMessage);
                if ($this->sendSMS() != 200) {
                    throw new \Exception();
                }
                echo 'SMS Success...';
            } catch (\Exception $exception) {
                echo 'SMS Error';
                $this->delayedProducer->publish(serialize($userMessage));
            }
        } else {
            echo 'ERROR';
            $this->delayedProducer->publish(serialize($userMessage));
        }
        $this->entityManager->clear();
        $this->entityManager->getConnection()->close();
        gc_collect_cycles();
    }

    private function sendEmail($userData)
    {
        $message = new Swift_Message();
        $message->setFrom('fitness.club.itm2019@gmail.com')
            ->setSubject('FitnessClub Notification')
            ->setTo($userData->email)
            ->setBody($userData->message);
        $this->processMessage($message);
    }

    public function processMessage($msg)
    {
        $transport = $this->getTransport();
        $transport->send($msg);
        $transport->stop();
        return ConsumerInterface::MSG_ACK;
    }

    public function getTransport()
    {
        $swiftTransport = $this->transport;
        if (!$swiftTransport->isStarted()) {
            $swiftTransport->start();
        }
        return $swiftTransport;
    }

    private function sendSMS()
    {
        $service = new SmsService();
        $status = $service->sendSms();
        return $status;
    }

    private function replaceMessageText($userData)
    {
        $marks             = array("%firstname%", "%lastname%", "%email%", "%birthday%", "%phone%");
        $userInfo          = array($userData->firstname, $userData->lastname, $userData->email, $userData->birthday, $userData->phone);
        $userData->message = str_replace($marks, $userInfo, $userData->message);
        return $userData;
    }

}