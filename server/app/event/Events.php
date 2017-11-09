<?php

class Events
{

    protected $eventSql;
    protected $authSql;
    protected $validator;

    public function __construct()
    {
        $this->eventSql = new EventSql();
        $this->authSql = new AuthSql();
        $this->validator = new Validator();
    }

    public function getEvent($params)
    {
        $params = explode('/', $params);
        $paramsCount = count($params);
//        return $_COOKIE;
        if (is_string($_COOKIE['hash']) && is_string($_COOKIE['id']))
        {
            $checkResult = $this->authSql->checkUserOrAdmin($_COOKIE['hash'], $_COOKIE['id']);
            if ($checkResult != 0)
            {
                if ($paramsCount == 3
                    && $this->validator->validateDate($params[0])
                    && $this->validator->validateDate($params[1])
                    && $this->validator->validateInt($params[2]))
                {
                    $result = $this->eventSql->getEventsByMonth($params[0], $params[1], $params[2]);
                } elseif ($paramsCount == 4
                    && $this->validator->validateInt($params[0])
                    && $this->validator->validateInt($params[1])
                    && $this->validator->validateInt($params[2])
                    && $this->validator->validateTime($params[3]))
                {
                    $result = $this->eventSql->getEventInfo($params[0], $params[1], $params[2], $params[3]);
                } else
                {
                    $result = WRONG_DATA;
                }
            } else
            {
                $result = INTRUDER;
            }
        } else
        {
            $result = INTRUDER;
        }

        return $result;
    }

    public function deleteEvent($params)
    {
        if ($params == false)
        {

            $putStr = file_get_contents('php://input');
            $generateParams = new GenerateParams();
            $putData = $generateParams->generatePutData($putStr);
            if (is_string($_COOKIE['hash']) && is_string($_COOKIE['id']))
            {
                $checkResult = $this->authSql->checkUserOrAdmin($_COOKIE['hash'], $_COOKIE['id']);
                if ($checkResult != 0)
                {
                    if(!$this->validator->validateInt($putData['eventId'])
                        || !$this->validator->validateDate($putData['date']))
                    {
                        return WRONG_DATA;
                    }

                    if ($putData['recurrence'] != 1)
                    {
                        $result = $this->eventSql->deleteEvent($putData['date'], $putData['eventId']);
                    } else
                    {
                        $recursiveOrNot = $this->eventSql->checkRecurrence($putData['date'], $putData['eventId']);
                        if ($recursiveOrNot == 0)
                        {
                            if ($this->eventSql->deleteEvent($putData['date'], $putData['eventId']))
                            {
                                $result = $this->eventSql->recurrenceDeleteEvent($putData['date'], $putData['eventId']);
                            }
                        } else
                        {
                            $result = $this->eventSql->recurrenceDeleteEvent($putData['date'], $recursiveOrNot);
                        }
                    }
                } else
                {
                    $result = INTRUDER;
                }
            } else
            {
                $result = INTRUDER;
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function putEvent($params)
    {
        if ($params == false)
        {
            if (is_string($_COOKIE['hash']) && is_string($_COOKIE['id']))
            {
                $checkResult = $this->authSql->checkUserOrAdmin($_COOKIE['hash'], $_COOKIE['id']);
                if ($checkResult != 0)
                {
                    $putStr = file_get_contents('php://input');
                    $generateParams = new GenerateParams();
                    $putData = $generateParams->generatePutData($putStr);
                    $checkDates = [];
                    array_push($checkDates, $putData['date']);

                    if(!$this->validator->validateTime($putData['startTime'],'H:i')
                        || !$this->validator->validateTime($putData['endTime'],'H:i')
                        || !$this->validator->validateInt($putData['eventId'])
                        || !$this->validator->validateInt($putData['userId'])
                        || !is_string($putData['desc'])
                        || !$this->validator->validateDate($putData['date']))
                    {
                        return WRONG_DATA;
                    }
                    if ($putData['recurrence'] == 0)
                    {
                        $busyDates = $this->eventSql->checkEventTime($checkDates, $putData['startTime'],
                            $putData['endTime'], $putData['eventId']);
                        if (empty($busyDates))
                        {
                            $eventUserId = $this->eventSql->selectEventUser($putData['eventId']);
                            if ($eventUserId == $putData['userId'])
                            {
                                $result = $this->eventSql->updateEvent($putData['userId'], $putData['desc'],
                                    $putData['startTime'], $putData['endTime'], $putData['eventId'],
                                    $putData['date'], $putData['eventId']);
                            } else
                            {
                                $result = $this->eventSql->updateEvent($putData['userId'], $putData['desc'],
                                    $putData['startTime'], $putData['endTime'], $putData['eventId'], $putData['date'], 0);
                            }
                        }
                    } else
                    {
                        $recursiveOrNot = $this->eventSql->checkRecurrence($putData['date'], $putData['eventId']);
                        if ($recursiveOrNot == 0)
                        {
                            $dates = $this->eventSql->selectEventDates($putData['eventId']);
                            $busyDates = $this->eventSql->checkRecurrenceEventTime($dates, $putData['startTime'], $putData['endTime']);

                        } else
                        {
                            $recurrenceId = $this->eventSql->selectRecurrenceId($putData['eventId']);
                            $dates = $this->eventSql->selectEventDatesRecurrence($recurrenceId);
                            $busyDates = $this->eventSql->checkRecurrenceEventTime($dates, $putData['startTime'], $putData['endTime']);
                        }
                        if ($busyDates != null)
                        {
                            foreach ($dates as $key => $val)
                            {
                                foreach ($busyDates as $date)
                                {
                                    if ($val['date'] == $date)
                                    {
                                        unset($dates[$key]);
                                    }
                                }
                            }
                        }
                        if (!empty($dates))
                        {
                            $eventUserId = $this->eventSql->selectEventUser($putData['eventId']);
                            if ($eventUserId == $putData['userId'])
                            {
                                $result = $this->eventSql->recurrenceUpdateEventNoChangeRecurrence($putData['userId'],
                                    $putData['desc'], $putData['startTime'], $putData['endTime'], $dates);
                            } else
                            {
                                $result = $this->eventSql->recurrenceUpdateEventChangeRecurrence($putData['userId'],
                                    $putData['desc'], $putData['startTime'], $putData['endTime'], $dates, $putData['eventId']);
                            }
                        }
                    }

                    if (empty($busyDates))
                    {
                        $result = true;
                    } else
                    {
                        $result['busyDates'] = $busyDates;
                    }
                } else
                {
                    $result = INTRUDER;
                }
            } else
            {
                $result = INTRUDER;
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    public function postEvent($params)
    {
        if ($params == false)
        {
            if (is_string($_COOKIE['hash']) && is_string($_COOKIE['id']))
            {
                $checkResult = $this->authSql->checkUserOrAdmin($_COOKIE['hash'], $_COOKIE['id']);
                if ($checkResult != 0)
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
                    array_push($checkDates, $date);
                    $timeZone = timezone_name_from_abbr('', ($timeZone * 60) * -1, 0);
                    date_default_timezone_set($timeZone);

                    if(!$this->validator->validateInt($userId)
                        || !$this->validator->validateInt($boardRoom)
                        || !is_string($description)
                        || !$this->validator->validateDate($date)
                        || !$this->validator->validateTime($timeOfCreate,'Y-m-d H:i:s')
                        || !$this->validator->validateTime($timeStart)
                        || !$this->validator->validateTime($timeEnd)
                    )
                    {
                        return WRONG_DATA;
                    }
                    if ($recursive == 'weekly')
                    {
                        $date = date_create($date);
                        $recursive = 0;
                        for ($i = 1; $i <= $repetitionCount; $i++)
                        {
                            date_modify($date, '1 week');
                            $newDate = date_format($date, 'Y-m-d');
                            array_push($checkDates, $newDate);
                        }
                    } elseif ($recursive == 'bi-weekly')
                    {
                        $date = date_create($date);
                        $recursive = 0;
                        for ($i = 1; $i <= $repetitionCount; $i++)
                        {
                            date_modify($date, '2 week');
                            array_push($checkDates, date_format($date, 'Y-m-d'));
                        }
                    } elseif ($recursive == 'monthly')
                    {
                        $date = date_create($date);
                        $recursive = 0;
                        date_modify($date, '1 month');
                        array_push($checkDates, date_format($date, 'Y-m-d'));
                    } else
                    {
                        $recursive = 0;
                    }

                    $busyDates = $this->eventSql->checkEventDateTimeInterval($checkDates, $timeStart, $timeEnd);

                    if ($busyDates != null)
                    {
                        $busyDates = array_unique($busyDates);
                        foreach ($busyDates as $key => $val)
                        {
                            $busyDate = array_search($val, $checkDates);
                            if ($busyDate !== false)
                            {
                                unset($checkDates[$busyDate]);
                            } elseif ($busyDate === false)
                            {
                                unset($busyDates[$key]);
                            }
                        }
                    }
                    $weekendDays = [];
                    if (!empty($checkDates))
                    {
                        foreach ($checkDates as $key => $val)
                        {
                            $date = date_create($val);
                            $day = date_format($date, 'l');
                            if ($day == 'Saturday' || $day == 'Sunday')
                            {
                                unset($checkDates[$key]);
                                array_push($weekendDays, $val);
                            }
                        }
                    }
                    if (!empty($checkDates))
                    {
                        $insertIds = $this->eventSql->addNewEvent($userId, $boardRoom, $description, $checkDates, $timeOfCreate, $recursive);
                        $this->eventSql->addEventTime($insertIds, $timeStart, $timeEnd);

                    }
                    if (empty($busyDates) && empty($weekendDays))
                    {
                        $result = true;
                    } else
                    {
                        $result['busyDates'] = $busyDates;
                        $result['weekendDays'] = $weekendDays;
                    }
                } else
                {
                    $result = INTRUDER;
                }
            } else
            {
                $result = INTRUDER;
            }

        } else
        {
            $result = false;
        }

        return $result;
    }
}
