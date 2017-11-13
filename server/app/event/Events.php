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

    /**
     * @param string $params
     * @return string Return error
     * @return array Return array of event(s)
     * @return boolean Return false on error or failure.
     * get all event or event info
     */
    public function getEvent($params)
    {
        if($params == false)
        {
            return false;
        }
        $params = explode('/', $params);
        $paramsCount = count($params);
        if (!$this->checkHash($_COOKIE[COOKIE_HASH], $_COOKIE[COOKIE_ID]))
        {
            return INTRUDER;
        }
        if ($paramsCount == 3 && $this->validator->validateDate($params[0])
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

        return $result;
    }

    /**
     * @param string $params
     * @return string Return error
     * @return boolean Return true is deleted is successful, false on error or failure.
     * delete event(s)
     */
    public function deleteEvent($params)
    {
        if ($params != false)
        {
            return false;
        }
        if (!$this->checkHash($_COOKIE[COOKIE_HASH], $_COOKIE[COOKIE_ID]))
        {
            return INTRUDER;
        }
        $putStr = file_get_contents('php://input');
        $generateParams = new GenerateParams();
        $deleteData = $generateParams->generatePutData($putStr);
        if (!$this->validator->validateInt($deleteData[INPUT_EVENT_ID])
            || !$this->validator->validateDate($deleteData[INPUT_EVENT_DATE]))
        {
            return WRONG_DATA;
        }

        if ($deleteData[INPUT_EVENT_RECURSIVE] != 1)
        {
            $result = $this->eventSql->deleteEvent($deleteData[INPUT_EVENT_DATE], $deleteData[INPUT_EVENT_ID]);
        } else
        {
            $recursiveOrNot = $this->eventSql->checkRecurrence($deleteData[INPUT_EVENT_DATE], $deleteData[INPUT_EVENT_ID]);
            if ($recursiveOrNot == 0)
            {
                if ($this->eventSql->deleteEvent($deleteData[INPUT_EVENT_DATE], $deleteData[INPUT_EVENT_ID]))
                {
                    $result = $this->eventSql->recurrenceDeleteEvent($deleteData[INPUT_EVENT_DATE], $deleteData[INPUT_EVENT_ID]);
                }
            } else
            {
                $result = $this->eventSql->recurrenceDeleteEvent($deleteData[INPUT_EVENT_DATE], $recursiveOrNot);
            }
        }

        return $result;
    }

    /**
     * @param string $params
     * @return string Return error
     * @return array Return array of busy date(s)
     * @return boolean Return false on error or failure.
     * update event recursive or once
     */
    public function putEvent($params)
    {
        if ($params != false)
        {
            return false;
        }
        if (!$this->checkHash($_COOKIE[COOKIE_HASH], $_COOKIE[COOKIE_ID]))
        {
            return INTRUDER;
        }
        $putStr = file_get_contents('php://input');
        $generateParams = new GenerateParams();
        $putData = $generateParams->generatePutData($putStr);
        $checkDates = [];
        array_push($checkDates, $putData[INPUT_EVENT_DATE]);

        if (!$this->validator->validateTime($putData[INPUT_EVENT_START_TIME], 'H:i')
            || !$this->validator->validateTime($putData[INPUT_EVENT_END_TIME], 'H:i')
            || !$this->validator->validateInt($putData[INPUT_EVENT_ID])
            || !$this->validator->validateInt($putData[INPUT_USER_ID])
            || !is_string($putData[INPUT_EVENT_DESCRIPTION])
            || !$this->validator->validateDate($putData[INPUT_EVENT_DATE]))
        {
            return WRONG_DATA;
        }
        if ($putData[INPUT_EVENT_RECURSIVE] == 0)
        {
            $busyDates = $this->eventSql->checkEventTime($checkDates, $putData[INPUT_EVENT_START_TIME],
                $putData[INPUT_EVENT_END_TIME], $putData[INPUT_EVENT_ID],$putData[INPUT_EVENT_DATE]);
            $result = $this->noRecursiveUpdate($busyDates, $putData[INPUT_EVENT_ID],
                $putData[INPUT_USER_ID], $putData[INPUT_EVENT_DESCRIPTION],
                $putData[INPUT_EVENT_START_TIME], $putData[INPUT_EVENT_END_TIME],
                $putData[INPUT_EVENT_DATE]);
        } else
        {
            $busyDatesUpdateDates = $this->formRecursiveBusyAndUpdateDates($putData[INPUT_EVENT_DATE],
                $putData[INPUT_EVENT_ID], $putData[INPUT_EVENT_START_TIME],
                $putData[INPUT_EVENT_END_TIME],$putData[INPUT_ROOM_ID]);
            $busyDates = $busyDatesUpdateDates['busyDates'];
            $updateDates = $busyDatesUpdateDates['updateDates'];
            $updateDates = $this->diffDatesIdUpdateDates($busyDates, $updateDates);
            $result = $this->recursiveUpdate($updateDates, $putData[INPUT_EVENT_ID],
                $putData[INPUT_USER_ID], $putData[INPUT_EVENT_DESCRIPTION],
                $putData[INPUT_EVENT_START_TIME], $putData[INPUT_EVENT_END_TIME]);
        }

        if (!empty($busyDates))
        {
            $result['busyDates'] = $busyDates;
        }

        return $result;
    }

    /**
     * @param string $params
     * @return string Return error
     * @return array Return array of busy or weekend date(s)
     * @return boolean Return false on error or failure.
     * create one event or recursive
     */

    public function postEvent($params)
    {
        if ($params != false)
        {
            return false;
        }
        if (!$this->checkHash($_COOKIE[COOKIE_HASH], $_COOKIE[COOKIE_ID]))
        {
            return INTRUDER;
        }
        $userId = json_decode($_POST[POST_USER_ID]);
        $boardRoom = json_decode($_POST[POST_ROOM_ID]);
        $description = json_decode($_POST[POST_DESCRIPTION]);
        $date = json_decode($_POST[POST_EVENT_DATE]);
        $timeOfCreate = json_decode($_POST[POST_TIME_CREATE]);
        $recursive = json_decode($_POST[POST_RECURSIVE]);
        $timeStart = json_decode($_POST[POST_TIME_START]);
        $timeEnd = json_decode($_POST[POST_TIME_END]);
        $repetitionCount = json_decode($_POST[POST_REPETITION_COUNT]);
        $timeZone = json_decode($_POST[POST_TIME_ZONE]);
        $timeZone = timezone_name_from_abbr('', ($timeZone * 60) * -1, 0);
        date_default_timezone_set($timeZone);

        if (!$this->validator->validateInt($userId)
            || !$this->validator->validateInt($boardRoom)
            || !is_string($description)
            || !$this->validator->validateDate($date)
            || !$this->validator->validateTime($timeOfCreate, 'Y-m-d H:i:s')
            || !$this->validator->validateTime($timeStart)
            || !$this->validator->validateTime($timeEnd))
        {
            return WRONG_DATA;
        }

        $checkDates = $this->formCheckDates($recursive, $repetitionCount, $date);
        $busyDates = $this->eventSql->checkEventDateTimeInterval($checkDates, $timeStart, $timeEnd,$boardRoom);

        if ($busyDates != null)
        {
            $busyDates = array_unique($busyDates);
        }
        $checkDates = $this->diffCheckDatesBusyDates($busyDates, $checkDates);

        $checkResult = $this->checkWeekendDays($checkDates);
        $weekendDays = $checkResult['weekendDates'];
        $insertDates = $checkResult['insertDates'];

        if (!empty($insertDates))
        {
            $insertIds = $this->eventSql->addNewEvent($userId, $boardRoom, $description, $insertDates, $timeOfCreate);
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

        return $result;
    }

    /**
     * @param string $date event date
     * @param integer $eventId event id
     * @param string $startTime time when event start
     * @param string $endTime time when event end
     * @return array Return array of date(s) for update and array of busy date(s)
     * Generates busy dates and dates for updates
     */
    protected function formRecursiveBusyAndUpdateDates($date, $eventId, $startTime, $endTime,$roomId)
    {
        $recursiveOrNot = $this->eventSql->checkRecurrence($date, $eventId);
        if ($recursiveOrNot == 0)
        {
            $dates = $this->eventSql->selectEventDates($eventId);
            $busyDates = $this->eventSql->checkRecurrenceEventTime($dates, $startTime, $endTime,$roomId);

        } else
        {
            $recurrenceId = $this->eventSql->selectRecurrenceId($eventId);
            $dates = $this->eventSql->selectEventDatesRecurrence($recurrenceId, $date);
            $busyDates = $this->eventSql->checkRecurrenceEventTime($dates, $startTime, $endTime,$roomId);
        }

        return [
            'busyDates' => $busyDates,
            'updateDates' => $dates
        ];
    }

    /**
     * @param array $dates date(s) for update
     * @param array $busyDates busy date(s)
     * @return array Return array of date(s) for update
     * calculates the discrepancy between free and busy dates
     */
    protected function diffDatesIdUpdateDates($busyDates, $dates)
    {
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

        return $dates;
    }
    /**
     * @param array $busyDates busy date(s)
     * @param integer $eventId event id
     * @param integer $userId user id
     * @param string $desc description of event
     * @param string $startTime time when event start
     * @param string $endTime time when event end
     * @param string $date event date
     * @return boolean Return true is update is successful, false on error or failure.
     * update one event
     */
    protected function noRecursiveUpdate($busyDate, $eventId, $userId, $desc, $startTime, $endTime, $date)
    {
        if (empty($busyDate))
        {
            $eventUserId = $this->eventSql->selectEventUser($eventId);
            if ($eventUserId == $userId)
            {
                $result = $this->eventSql->updateEvent($userId, $desc, $startTime, $endTime, $eventId, $date, $eventId);
            } else
            {
                $result = $this->eventSql->updateEvent($userId, $desc, $startTime, $endTime, $eventId, $date, 0);
            }
        }

        return $result;
    }

    /**
     * @param array $dates dates for update
     * @param integer $eventId event id
     * @param integer $userId user
     * @param string $desc description of event
     * @param string $startTime time when event start
     * @param string $endTime time when event end
     * @return boolean Return true is update is successful, false on error or failure.
     * recursive update event
     */
    protected function recursiveUpdate($dates, $eventId, $userId, $desc, $startTime, $endTime)
    {
        if (!empty($dates))
        {
            $eventUserId = $this->eventSql->selectEventUser($eventId);
            if ($eventUserId == $userId)
            {
                $result = $this->eventSql->recurrenceUpdateEventNoChangeRecurrence($userId, $desc, $startTime, $endTime, $dates);
            } else
            {
                $result = $this->eventSql->recurrenceUpdateEventChangeRecurrence($userId, $desc, $startTime, $endTime, $dates, $eventId);
            }
        }

        return $result;
    }

    /**
     * @param string $hash user hash
     * @param integer $id user id
     * @return boolean Return true if check is successful and otherwise false
     * checks if the cookie data of the admin is correct
     * and checks if there is admin
     */
    protected function checkHash($hash, $id)
    {
        if (is_string($hash) && is_numeric($id))
        {
            $checkResult = $this->authSql->checkUserOrAdmin($hash, $id);
            if ($checkResult == 0)
            {
                $result = false;
            } else
            {
                $result = true;
            }
        } else
        {
            $result = false;
        }

        return $result;
    }

    /**
     * @param array $checkDates date(s) need to be checked
     * @return array Return array of weekend(s) date(s) and date(s) for insert
     * Check if the dates are weekend
     */
    protected function checkWeekendDays($checkDates)
    {
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

        return [
            'weekendDates' => $weekendDays,
            'insertDates' => $checkDates
        ];
    }

    /**
     * @param array $busyDates busy date(s)
     * @param array $checkDates date(s) what need checked
     * @return array Return not busy date(s)
     * Calculates the discrepancy between free and busy dates
     */
    protected function diffCheckDatesBusyDates($busyDates, $checkDates)
    {
        if ($busyDates != null)
        {
            foreach ($busyDates as $key => $val)
            {
                $busyDate = array_search($val, $checkDates);
                if ($busyDate !== false)
                {
                    unset($checkDates[$busyDate]);
                }
            }
        }

        return $checkDates;
    }

    /**
     * @param string $recursive how often to repeat event
     * @param integer $repetitionCount count of event repeat
     * @param string $date first event date
     * @return array
     * Calculates the discrepancy between free and busy dates
     */
    protected function formCheckDates($recursive, $repetitionCount, $date)
    {
        $dates = [];
        array_push($dates, $date);
        if ($recursive == 'weekly')
        {
            $date = date_create($date);
            for ($i = 1; $i <= $repetitionCount; $i++)
            {
                date_modify($date, '1 week');
                $newDate = date_format($date, 'Y-m-d');
                array_push($dates, $newDate);
            }
        } elseif ($recursive == 'bi-weekly')
        {
            $date = date_create($date);
            for ($i = 1; $i <= $repetitionCount; $i++)
            {
                date_modify($date, '2 week');
                array_push($dates, date_format($date, 'Y-m-d'));
            }
        } elseif ($recursive == 'monthly')
        {
            $date = date_create($date);
            date_modify($date, '1 month');
            array_push($dates, date_format($date, 'Y-m-d'));
        }

        return $dates;
    }
}
