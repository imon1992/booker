<?php

class validator
{
    public function validateDate($date,$format = 'Y-m-d')
{
    $dateFormat = DateTime::createFromFormat($format, $date);
    return $dateFormat && $dateFormat->format($format) == $date;
}

    public function validateEmail($email)
    {
        $validEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
        if($validEmail !=false)
        {
            $result = true;
        }else{
            $result = false;
        }

        return $result;
    }

    public function validateInt($number)
    {
        return is_numeric($number);
    }

    public function validateTime($time,$format = 'H:i:s')
    {
        $dateFormat = DateTime::createFromFormat($format, $time);
        return $dateFormat && $dateFormat->format($format) == $time;
    }
}