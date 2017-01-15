<?php

namespace akerbel\MailBoxBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    const SUCCESS = ['result' => 'success'];
    const PHP_AUTH_USER = 'testUser';
    const PHP_AUTH_PW = 'testUser';
    const TEST_MESSAGE = [
        'addressee' => 'testUser',
        'sender' => 'testSender',
        'subject' => 'testSubject',
        'message' => 'testMessage',
    ];

    public function testIndex()
    {
        /*$client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains('Hello World', $client->getResponse()->getContent());*/
    }

    public function testAdd()
    {
        $client = static::createClient();

        // Wrong addressee test
        $client->request('POST', '/mailbox/add',
            [
                'addressee' => 'wrongUser',
                'sender' => 'testSender',
                'subject' => 'testSubject', 
                'message' => 'testMessage',
            ]
        );
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertEquals(['result' => 'error', 'error_code' => 404, 'error_text' => 'The addressee \'wrongUser\' cannot be found'], $response);

        // Correct request test
        $client->request('POST', '/mailbox/add', self::TEST_MESSAGE);
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertEquals(self::SUCCESS, $response);

    }

    public function testList()
    {
        $client = $this->getClient();

        $client->request('GET', '/mailbox/list');
        $response = json_decode($client->getResponse()->getContent(), 1);

        // Check our message is saved
        $this->assertCount(1, $response['messages']);
        $message = $response['messages'][0];
        $this->assertEquals(self::TEST_MESSAGE['sender'], $message['sender']);
        $this->assertEquals(self::TEST_MESSAGE['subject'], $message['subject']);
        $this->assertEquals(self::TEST_MESSAGE['message'], $message['message']);
        $this->assertNotEmpty($message['time_sent']);
        $this->assertFalse($message['read']);
        $this->assertFalse($message['archived']);

        return $message['id'];
    }

    /**
     * @depends testList
     */
    public function testShow($id)
    {
        $client = $this->getClient();

        $client->request('GET', '/mailbox/show/'.$id);
        $response = json_decode($client->getResponse()->getContent(), 1);

        $this->assertEquals(self::TEST_MESSAGE['sender'], $response['sender']);
        $this->assertEquals(self::TEST_MESSAGE['subject'], $response['subject']);
        $this->assertEquals(self::TEST_MESSAGE['message'], $response['message']);
        $this->assertNotEmpty($response['time_sent']);
        $this->assertFalse($response['read']);
        $this->assertFalse($response['archived']);
    }

    /**
     * @depends testList
     */
    public function testArchive($id)
    {
        $client = $this->getClient();

        // Check archiving
        $client->request('PATCH', '/mailbox/archive/'.$id);
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertEquals(self::SUCCESS, $response);

        // Check the message was archived
        $client->request('GET', '/mailbox/show/'.$id);
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertNotFalse($response['archived']);
    }

    public function testListArchived()
    {
        $client = $this->getClient();

        $client->request('GET', '/mailbox/list/archived');
        $response = json_decode($client->getResponse()->getContent(), 1);

        // Chech our message is in archived list
        $this->assertCount(1, $response['messages']);
        $this->assertNotFalse($response['messages'][0]['archived']);
    }

    /**
     * @depends testList
     */
    public function testUnarchive($id)
    {
        $client = $this->getClient();

        // Check unarchiving
        $client->request('PATCH', '/mailbox/unarchive/'.$id);
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertEquals(self::SUCCESS, $response);

        // Check the message was unarchived
        $client->request('GET', '/mailbox/show/'.$id);
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertFalse($response['archived']);
    }

    /**
     * @depends testList
     */
    public function testRead($id)
    {
        $client = $this->getClient();

        // Check archiving
        $client->request('PATCH', '/mailbox/read/'.$id);
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertEquals(self::SUCCESS, $response);

        // Check the message was archived
        $client->request('GET', '/mailbox/show/'.$id);
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertNotFalse($response['read']);
    }

    /**
     * @depends testList
     */
    public function testUnread($id)
    {
        $client = $this->getClient();

        // Check unarchiving
        $client->request('PATCH', '/mailbox/unread/'.$id);
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertEquals(self::SUCCESS, $response);

        // Check the message was unarchived
        $client->request('GET', '/mailbox/show/'.$id);
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertFalse($response['read']);
    }

    /**
     * @depends testList
     */
    public function testDelete($id)
    {
        $client = $this->getClient();

        // Check deleting a message
        $client->request('DELETE', '/mailbox/delete/'.$id);
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertEquals(self::SUCCESS, $response);

        // Check the message was deleted
        $client->request('GET', '/mailbox/show/'.$id);
        $response = json_decode($client->getResponse()->getContent(), 1);
        $this->assertEquals(['result' => 'error', 'error_code' => 404, 'error_text' => "Message #$id cannot be found"], $response);
    }

    private function getClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => self::PHP_AUTH_USER,
            'PHP_AUTH_PW'   => self::PHP_AUTH_PW,
        ));
    }

}
