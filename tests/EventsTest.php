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
    protected $sqlForTest;
    public function __construct()
    {
        $this->sqlForTest = new SqlForTest();
        $this->idHash = $this->sqlForTest->getAdminHashAndId();
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

    public function testGetEvent4()
    {
        $result = $this->events->getEvent(false);
        $this->assertFalse($result);
    }

    public function testPostEvent()
    {
        $result = $this->events->postEvent(true);
        $this->assertFalse($result);
    }

    public function testPostEvent1()
    {
        $_COOKIE['hash']='wrongHash';
        $_COOKIE['id']='wrongId';

        $result = $this->events->postEvent(false);
        $this->assertEquals(INTRUDER,$result);
    }

    public function testPostEvent2()
    {
        $_COOKIE['hash']= $this->idHash[0]['hash'];
        $_COOKIE['id']= $this->idHash[0]['id'];
        $_POST[POST_EVENT_DATE] = json_encode('wrongDate');

        $result = $this->events->postEvent(false);
        $this->assertEquals(WRONG_DATA,$result);
    }

    public function testPostEvent3()
    {
        $_COOKIE['hash']= $this->idHash[0]['hash'];
        $_COOKIE['id']= $this->idHash[0]['id'];

        $_POST[POST_USER_ID] = json_encode(1);
        $_POST[POST_ROOM_ID] = json_encode(1);
        $_POST[POST_DESCRIPTION] = json_encode('test desc');
        $_POST[POST_EVENT_DATE] = json_encode('2017-10-07');
        $_POST[POST_TIME_CREATE] = json_encode('2017-10-02 17:00:00');
        $_POST[POST_RECURSIVE] = json_encode(null);
        $_POST[POST_TIME_START] = json_encode('18:00:00');
        $_POST[POST_TIME_END] = json_encode('19:00:00');
        $_POST[POST_REPETITION_COUNT] = json_encode(null);
        $_POST[POST_TIME_ZONE] = json_encode(-120);

        $result = $this->events->postEvent(false);
        $this->assertArrayHasKey('busyDates',$result);
        $this->assertArrayHasKey('weekendDays',$result);
        $this->assertEquals('2017-10-07',$result['weekendDays'][0]);
    }

    public function testPostEvent4()
    {
        $_COOKIE['hash']= $this->idHash[0]['hash'];
        $_COOKIE['id']= $this->idHash[0]['id'];

        $_POST[POST_USER_ID] = json_encode(1);
        $_POST[POST_ROOM_ID] = json_encode(1);
        $_POST[POST_DESCRIPTION] = json_encode('test desc');
        $_POST[POST_EVENT_DATE] = json_encode('2017-10-04');
        $_POST[POST_TIME_CREATE] = json_encode('2017-10-02 17:00:00');
        $_POST[POST_RECURSIVE] = json_encode(null);
        $_POST[POST_TIME_START] = json_encode('18:00:00');
        $_POST[POST_TIME_END] = json_encode('19:00:00');
        $_POST[POST_REPETITION_COUNT] = json_encode(null);
        $_POST[POST_TIME_ZONE] = json_encode(-120);

        $result = $this->events->postEvent(false);
        $this->assertEquals(true,$result);
    }

    public function testPutEvent()
    {
        $result = $this->events->putEvent(true);
        $this->assertFalse($result);
    }

    public function testPutEvent1()
    {
        $_COOKIE['hash']='wrongHash';
        $_COOKIE['id']='wrongId';

        $result = $this->events->putEvent(false);
        $this->assertEquals(INTRUDER,$result);
    }

    public function testPutEvent2()
    {
        $_COOKIE['hash']= $this->idHash[0]['hash'];
        $_COOKIE['id']= $this->idHash[0]['id'];
        $_POST[POST_EVENT_DATE] = json_encode('wrongDate');

        $result = $this->events->putEvent(false);
        $this->assertEquals(WRONG_DATA,$result);
    }

    public function testPutEvent3()
    {
        $updateId = $this->sqlForTest->getUpdateId('2017-10-04',1,1,'test desc','2017-10-02 17:00:00');
        $putStr = INPUT_EVENT_DATE .'='.json_encode('2017-10-04').'&'.INPUT_EVENT_START_TIME.'='.json_encode('18:00')
            .'&'.INPUT_EVENT_END_TIME.'='.json_encode('20:00').'&'.INPUT_EVENT_ID.'='.json_encode($updateId)
            .'&'.INPUT_USER_ID.'='.json_encode(1).'&'.INPUT_EVENT_DESCRIPTION.'='.json_encode('test desc');
        $cookie = 'Cookie: hash='.$this->idHash[0]['hash'].'; id='.$this->idHash[0]['id'].';';
        $ch = curl_init('http://booker/user14/booker/client/api/event/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($cookie));
        curl_setopt($ch, CURLOPT_POSTFIELDS,$putStr);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD,true);

        $result = curl_exec($ch);
        $this->assertEquals(true,json_decode($result));
    }

    public function testDeleteEvent()
    {
        $result = $this->events->deleteEvent(true);
        $this->assertFalse($result);
    }

    public function testDeleteEvent1()
    {
        $_COOKIE['hash']='wrongHash';
        $_COOKIE['id']='wrongId';

        $result = $this->events->deleteEvent(false);
        $this->assertEquals(INTRUDER,$result);
    }

    public function testDeleteEvent2()
    {
        $_COOKIE['hash']= $this->idHash[0]['hash'];
        $_COOKIE['id']= $this->idHash[0]['id'];
        $_POST[POST_EVENT_DATE] = json_encode('wrongDate');

        $result = $this->events->deleteEvent(false);
        $this->assertEquals(WRONG_DATA,$result);
    }

    public function testDeleteEvent3()
    {
        $updateId = $this->sqlForTest->getUpdateId('2017-10-04',1,1,'test desc','2017-10-02 17:00:00');
        $deleteStr = INPUT_EVENT_DATE .'='.json_encode('2017-10-04').'&'.INPUT_EVENT_ID.'='.json_encode($updateId)
            .'&'.INPUT_EVENT_RECURSIVE.'='.json_encode(0);
        $cookie = 'Cookie: hash='.$this->idHash[0]['hash'].'; id='.$this->idHash[0]['id'].';';
        $ch = curl_init('http://booker/user14/booker/client/api/event/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($cookie));
        curl_setopt($ch, CURLOPT_POSTFIELDS,$deleteStr);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD,true);

        $result = curl_exec($ch);
        $this->assertEquals(true,json_decode($result));
    }
}