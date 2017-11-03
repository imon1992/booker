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
            //return $_POST;
            $timeZone =  timezone_name_from_abbr('',($timeZone*60)*-1,0);
            date_default_timezone_set($timeZone);
            if($recursive == 'weekli')
            {
                $date = date_create($date);
                $recursive = 1;
                for($i = 1; $i <=$repetitionCount;$i++)
                {
                    date_modify($date, '1 week');
                    array_push($checkDates,date_format($date, 'Y-m-d'));
                }
            } elseif ($recursive == 'bi-weekli')
            {
                $date = date_create($date);
                $recursive = 1;
                for($i = 1; $i <=$repetitionCount;$i++)
                {
                    date_modify($date, '2 week');
                    array_push($checkDates,date_format($date, 'Y-m-d'));
                }
            } elseif ($recursive == 'monthly')
            {
                $date = date_create($date);
                $recursive = 1;
                    date_modify($date, '1 month');
                    array_push($checkDates,date_format($date, 'Y-m-d'));
            } else
            {
                $recursive = 0;
            }
            //return date_format($date, 'Y-m-d');
            //return $checkDates;
//var_dump($checkDates);

            $busyDates = $this->eventSql->checkEventDateTime($checkDates,$timeStart,$timeEnd);
//return $bussyDates;
            foreach($checkDates as $kay=>$val)
            {
                if($busyDates[$kay]['date'] != $val)
                {
                    $insertDates[] = $val;
                }
            }
            //return $insertDates;
//            var_dump($insertDates);
//var_dump(!empty($insertDates));
            if(!empty($insertDates))
            {
                //echo '111';
                $insertIds = $this->eventSql->addNewEvent($userId,$boardRoom,$description,$insertDates,$timeOfCreate,$recursive);
                $this->eventSql->addEventTime($insertIds,$timeStart,$timeEnd);

            }
            if(empty($busyDates))
            {
                $result = true;
            } else
            {
                $result = $busyDates;
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
