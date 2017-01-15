<?php
namespace akerbel\MailBoxBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use akerbel\MailBoxBundle\Entity\User;
use akerbel\MailBoxBundle\Entity\Message;


class loadData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = json_decode(file_get_contents(__DIR__.'/../Seed/messages_sample.json'), 1);

        foreach ($data['users'] as $uid=>$rawuser) {
            $user = new User();
            $user->setUsername($rawuser['username']);
            $user->setPassword($rawuser['password']);

            $manager->persist($user);
            $manager->flush();
        }

        foreach ($data['messages'] as $rawmesssage) {

            $user = $manager->getRepository('akerbelMailBoxBundle:User')->findOneBy(['username' => $rawmesssage['uid']]);

            if ($user) {
                $message = new Message();
                $message->setUser($user);
                $message->setSender($rawmesssage['sender']);
                $message->setSubject($rawmesssage['subject']);
                $message->setMessage($rawmesssage['message']);
                $message->setReaded(false);
                $message->setArchived(false);
                $message->setCreatedAt(\DateTime::createFromFormat('U', $rawmesssage['time_sent']));

                $manager->persist($message);
                $manager->flush();
            }

        }

    }
}