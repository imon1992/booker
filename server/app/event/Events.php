<?php

class Events
{

    protected $eventSql;

    public function __construct()
    {
        $this->eventSql = new EventSql();
    }

    public function getEvent($params)
    {
        return $_COOKIE;
        $params = explode('/',$params);
        $paramsCount = count($params);
        if($paramsCount == 3)
        {
            //var_dump($params);
            $result = $this->eventSql->getEventsByMonth($params[0],$params[1],$params[2]);
        }elseif($paramsCount == 4)
        {
            $result = $this->eventSql->getEventInfo($params[0],$params[1],$params[2],$params[3]);
        }
        return $result;
    }

    public function deleteEvent($params)
    {
        if($params == false)
        {
            $putStr = file_get_contents('php://input');
            $generateParams = new GenerateParams();
            $putData = $generateParams->generatePutData($putStr);

            if($putData['recurrence'] != 1)
            {
                $result = $this->eventSql->deleteEvent($putData['date'],$putData['eventId']);
            } else 
            {
                $recursiveOrNot = $this->eventSql->checkRecurrence($putData['date'],$putData['eventId']);
                if($recursiveOrNot == 0)
                {
                    if($this->eventSql->deleteEvent($putData['date'],$putData['eventId']))
                    {
                        $result = $this->eventSql->recurrenceDeleteEvent($putData['date'],$putData['eventId']); 
                    }
                }else
                {
                    $result = $this->eventSql->recurrenceDeleteEvent($putData['date'],$recursiveOrNot);
                }
            } 
        }

        return $result;
    }

    public function putEvent($params)
    {
        if($params == false)
        {
            $putStr = file_get_contents('php://input');
            $generateParams = new GenerateParams();
            $putData = $generateParams->generatePutData($putStr);
            $checkDates = [];
            array_push($checkDates,$putData['date']);
            if($putData['recurrence']== 0)
            {
                $busyDates = $this->eventSql->checkEventTime($checkDates,$putData['startTime'],
                    $putData['endTime'], $putData['eventId']);
                if(empty($busyDates))
                {
                    $eventUserId = $this->eventSql->selectEventUser($putData['eventId']);
                    if($eventUserId == $putData['userId'])
                    {
                        $result = $this->eventSql->updateEvent($putData['userId'],$putData['desc'],
                            $putData['startTime'],$putData['endTime'],$putData['eventId'],$putData['date'],$putData['eventId']);
                    }else
                    {
                        $result = $this->eventSql->updateEvent($putData['userId'],$putData['desc'],
                            $putData['startTime'],$putData['endTime'],$putData['eventId'],$putData['date'],0);
                    }
                }
            }else 
                {
                    $recursiveOrNot = $this->eventSql->checkRecurrence($putData['date'],$putData['eventId']);
                    if($recursiveOrNot == 0)
                    {
                        $dates = $this->eventSql->selectEventDates($putData['eventId']);
                        $busyDates = $this->eventSql->checkRecurrenceEventTime($dates, $putData['startTime'],
                            $putData['endTime']);

                    }else
                    {
                        $recurrenceId = $this->eventSql->selectRecurrenceId($putData['eventId']);
                        $dates = $this->eventSql->selectEventDatesRecurrence($recurrenceId);
                        $busyDates = $this->eventSql->checkRecurrenceEventTime($dates,$putData['startTime'],
                            $putData['endTime']);
                    }
                    if ($busyDates != null) {
                        foreach ($dates as $key => $val) {
                            foreach ($busyDates as $date) {
                                if ($val['date'] == $date) {
                                    unset($dates[$key]);
                                }
                            }
                        }
                    }
                    if (!empty($dates)) {
                        $eventUserId = $this->eventSql->selectEventUser($putData['eventId']);
                        if ($eventUserId == $putData['userId']) {
                            $result = $this->eventSql->recurrenceUpdateEventNoChangeRecurrence($putData['userId'], $putData['desc'],
                                $putData['startTime'], $putData['endTime'], $dates);
                        } else {
//                            var_dump($putData['eventId']);
                            $result = $this->eventSql->recurrenceUpdateEventChangeRecurrence($putData['userId'], $putData['desc'],
                                $putData['startTime'], $putData['endTime'], $dates, $putData['eventId']);
                        }
                    }
                }

            if(empty($busyDates))
            {
                $result = true;
            } else
            {
                $result['busyDates'] = $busyDates;
            }
            } else
        {
            $result = false;
        }

        return $result;
    }

    public function postEvent($params)
    {
        if($params == false)
        {
            $userId = json_decode($_POST['userId']);
            $boardRoom = json_decode($_POST['roomId']);
            $description = json_decode($_POST['description']);
            $date = json_decode($_POST['date']);
            $timeOfCreate = json_decode($_POST['timeOfCreate']);
            $recursive = json_decode($_POST['recursive']);
            $timeStart = json_decode($_POST['timeStart']);
            $timeEnd = json_decode($_POST['timeEnd']);
            $repetitionCount = json_decode($_POST['repetitionCount']);
            $timeZone = json_decode($_POST['timeZone']);
            $checkDates = [];
            array_push($checkDates,$date);
            $timeZone =  timezone_name_from_abbr('',($timeZone*60)*-1,0);
            date_default_timezone_set($timeZone);
            if($recursive == 'weekly')
            {
                $date = date_create($date);
                $recursive = 0;
                for($i = 1; $i <=$repetitionCount;$i++)
                {
                    date_modify($date, '1 week');
                    $newDate = date_format($date, 'Y-m-d');
                    array_push($checkDates,$newDate);
                }
            } elseif ($recursive == 'bi-weekly')
            {
                $date = date_create($date);
                $recursive = 0;
                for($i = 1; $i <=$repetitionCount;$i++)
                {
                    date_modify($date, '2 week');
                    array_push($checkDates,date_format($date, 'Y-m-d'));
                }
            } elseif ($recursive == 'monthly')
            {
                $date = date_create($date);
                $recursive = 0;
                    date_modify($date, '1 month');
                    array_push($checkDates,date_format($date, 'Y-m-d'));
            } else
            {
                $recursive = 0;
            }

            $busyDates = $this->eventSql->checkEventDateTimeInterval($checkDates,$timeStart,$timeEnd);
//            $busyDates = array_unique($busyDates);
//            $freeDates = $this->eventSql->checkEventDateTimeInterval($checkDates,$timeStart,$timeEnd);
//var_dump($busyDates);
//            return $busyDates;


//            foreach($res as $ev)
//            {
//                if(((new \DateTime($ev['start']) <= $start) && (new \DateTime($ev['end']) <= $start))
//			|| ((new \DateTime($ev['start']) >= $end)   && (new \DateTime($ev['end']) >= $end)))
//			{
//                return true;
//            }
//			return false;
//		}

//            foreach($busyDates as $key=>$val)
//            {
//                if((($val['startTime'] <= $timeStart) && ($val['endTime'] <= $timeStart))
//			|| (($val['startTime'] >= $timeEnd)   && ($val['endTime'] >= $timeEnd)))
//			{
//                var_dump($val);
////                return true;
//            }
//			return false;
//		}

//            foreach($busyDates as $key=>$val)
//            {
//                if($val['startTime'] == $timeStart && ($timeStart > $val['startTime'] && $timeEnd <$val['endTime'] ))
//                {
//                    unset($busyDates[$key]);
//                }
////                var_dump($val);
//            }
//            var_dump($busyDates);
//            return;
//            $c = $this->eventSql->checkEventDateTime($busyDates,$timeStart,$timeEnd);
////var_dump($c);
//            if($c != null)
//            {
//                foreach($c as $val)
//                {
//                    $noBusyTime = array_search($val['date'],$busyDates);
////                    var_dump($noBusyTime);
//                    var_dump($busyDates);
//                    var_dump($val['date']);
//                    if($noBusyTime !== false)
//                    {
//                        unset($busyDates[$noBusyTime]);
//                    }
//                }
//            }
//return $busyDates;
//var_dump($busyDates);
            if($busyDates != null)
            {
                $busyDates = array_unique($busyDates);
//                var_dump($busyDates);
                foreach($busyDates as $key=>$val)
                {
                    $busyDate = array_search($val,$checkDates);
//                    var_dump($busyDate);
                    if($busyDate !== false)
                    {
                        unset($checkDates[$busyDate]);
                    } elseif ($busyDate === false)
                    {
//                        echo $key;
                        unset($busyDates[$key]);
                    }
    //                if($busyDates[$kay]['date'] != $val)
    //                {
    //                    $insertDates[] = $val;
    //                }
                }
            }
//            unset($busyDates,0);
//            var_dump($busyDates);
//            var_dump($checkDates);
//            return
            $weekendDays = [];
            if(!empty($checkDates))
            {
                foreach($checkDates as $key=>$val)
                {
                    $date = date_create($val);
                    $day = date_format($date, 'l');
    //                var_dump($day);
                    if($day == 'Saturday' || $day == 'Sunday')
                    {
                        unset($checkDates[$key]);
                        array_push($weekendDays,$val);
                    }
                }
            }
//            var_dump($weekendDays);
//            return $weekendDays;
//            foreach($checkDates as $kay=>$val)
//            {
////                if($busyDates[$kay]['date'] != $val)
////                {
////                    $insertDates[] = $val;
////                }
//            }
            //return $insertDates;
//            var_dump($insertDates);
//var_dump(!empty($insertDates));
            if(!empty($checkDates))
            {
                //echo '111';
                $insertIds = $this->eventSql->addNewEvent($userId,$boardRoom,$description,$checkDates,$timeOfCreate,$recursive);
                $this->eventSql->addEventTime($insertIds,$timeStart,$timeEnd);

            }
            if(empty($busyDates) && empty($weekendDays))
            {
                $result = true;
            } else
            {
                $result['busyDates'] = $busyDates;
                $result['weekendDays'] = $weekendDays;
            }
        }

        return $result;
    }

//    public function putAuth($params)
//    {
//        if($params == false)
//        {
//            $putStr = file_get_contents('php://input');
//            $generatePutData = new GenerateData();
//            $putData = $generatePutData->generatePutData($putStr);
//            $idPasswordRoleActive = $this->authSql->getIdPassRoleActiveByLogin($putData['login']);
//            if ($idPasswordRoleActive[0]['password'] === md5(md5($putData['password']))) {
//                $hash = md5($this->generateCode(10));
//
//                $result = $this->authSql->setNewHash($hash, $idPasswordRoleActive[0]['id']);
//                if ($result == true) {
//                    $result =[];
//                    $result['id'] = $idPasswordRoleActive[0]['id'];
//                    $result['hash'] = $hash;
//                    $result['role'] = $idPasswordRoleActive[0]['role'];
//
//                    if($idPasswordRoleActive[0]['isActive'] != true)
//                    {
//                        $result = 'Sorry your account is Not Active';
//                    }
//                }else
//                {
//                    $result = false;
//                }
//            } else {
//                $result = "wrong password";
//            }
//        }
//        return $result;
//
//    }
//
//    public function getAuth($params)
//    {
//        $params = explode('/',$params);
//        $paramsCount = count($params);
//        if($paramsCount > 2 || $paramsCount < 2)
//        {
//            return false;
//        }
//
//        if($params != false)
//        {
//            if ($params[0] !== 'undefined' && $params[1] !== 'undefined') {
//                $idHash = $this->authSql->getIdHashByCookieId($params[0]);
//                if (($idHash[0]['hash'] !== $params[1]) || ($idHash[0]['id'] !== $params[0]))
//                {
//                    $result = false;
//                } else {
//                    $result = true;
//                }
//            } else {
//                $result = false;
//            }
//        }
//        return $result;
//    }
//
//    private function generateCode($length = 6)
//    {
//        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
//        $code = "";
//        $clen = strlen($chars) - 1;
//        while (strlen($code) < $length) {
//            $code .= $chars[mt_rand(0, $clen)];
//        }
//
//        return $code;
//    }

}
