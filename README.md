Backend Task - Mailbox API
==========================
Introduction
------------
A local firm is building a small E-mail client to manage their internal messaging.
You have been asked to provide a simple prototype for a basic mailbox API in which the provided
messages are listed. Each message can be marked as read and you can archive single messages.

Authorization
-------------
HTTP basic authorization was used.

Demonstration user: admin:test

Database
--------
Before using you need to prepare the database.
* Create tables via symfony console command ``php bin/console doctrine:schema:update``.
* Seed tables. _doctrine-fixtures-bundle_ was used for seeding. Use symfony console command ``php bin/console doctrine:fixtures:load``. The command will use special class _akerbel\MailBoxBundle\DataFixtures\ORM\loadData_ that will put json data from `akerbel\MailBoxBundle\DataFixtures\Seed\messages_sample.json` to the database.
    * `messages_sample.json` file from the task was changed. Now it also contains users. Also messages were attached to owners via usernames instead IDs because user`s IDs can be different.

API
---
 * __/mailbox/list__ - list all users messages.
    * Supported methods: GET
    * Parameters:
        * _offset_ - integer, the sequence will start at that offset in the array. Defaut = null.
        * _length_ - integer, the sequence will have up to that many elements in it. Default = null.
 * __/mailbox/list/archived__ - list archived users messages.
     * Supported methods: GET
     * Parameters:
        * _offset_ - integer, the sequence will start at that offset in the array. Defaut = null.
        * _length_ - integer, the sequence will have up to that many elements in it. Default = null.
 * __/mailbox/show/{id}__ - show a message with ID = {id}.
     * Supported methods: GET
 * __/mailbox/read/{id}__ - mark a message with ID = {id} as read.
     * Supported methods: PATCH
 * __/mailbox/unread/{id}__ - mark a message with ID = {id} as unread.
     * Supported methods: PATCH
 * __/mailbox/archive/{id}__ - mark a message with ID = {id} as archived.
     * Supported methods: PATCH
 * __/mailbox/unarchive/{id}__ - mark a message with ID = {id} as unarchived.
     * Supported methods: PATCH
 * __/mailbox/add__ - add a message to the database. Authorization is not needed.
     * Supported methods: PUT, POST
     * Parameters:
        * _addressee_ - string, addressee`s username. Required.
        * _sender_ - string, sender`s name. Required.
        * _subject_ - string, a subject of a message. Required.
        * _message_ - string, a text of a message. Required.
 * __/mailboc/delete/{id}__ - delete a message with ID = {id}.
    * Supported methods: DELETE

Tests
-----
All tests are in tests/akerbelMailBoxBundle/Controller . PHPunit was used. Use `phpunit tests/akerbelMailBoxBundle/Controller/` for runing tests.
