<?php
include ('SqlForTest.php');
include ('../../server/app/config.php');
include ('../../server/app/libs/DbConnection.php');
include ('../../server/app/libs/GenerateParams.php');
include ('../../server/app/libs/Validator.php');
include ('../../server/app/event/Events.php');
include ('../../server/app/event/libs/EventSql.php');
include ('../../server/app/auth/libs/AuthSql.php');

class EventsTest extends PHPUnit_Framework_TestCase
{

    protected $events;
    protected $idHash;
    public function __construct()
    {
        $sqlForTest = new SqlForTest();
        $this->idHash = $sqlForTest->getAdminHashAndId();
        $this->events = new Events();
    }
    public function testGetEvent()
    {
        $result = $this->events->getEvent('wrongParams');
        $this->assertEquals(INTRUDER,$result);
    }

    public function testGetEvent1()
    {
        $_COOKIE['hash']='wrongHash';
        $_COOKIE['id']='wrongId';

        $result = $this->events->getEvent('wrongParams');
        $this->assertEquals(INTRUDER,$result);
    }

    public function testGetEvent2()
    {
        $_COOKIE['hash']= $this->idHash[0]['hash'];
        $_COOKIE['id']=$this->idHash[0]['id'];

        $result = $this->events->getEvent('wrongDate/2017-08-11/1');
        $this->assertEquals(WRONG_DATA,$result);
    }

    public function testGetEvent3()
    {
        $_COOKIE['hash']= $this->idHash[0]['hash'];
        $_COOKIE['id']= $this->idHash[0]['id'];

        $result = $this->events->getEvent('2017-01-30/2017-11-30/1');
        $this->assertTrue(is_array($result));
    }
}