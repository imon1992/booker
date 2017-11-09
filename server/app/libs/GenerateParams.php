<?php

class GenerateParams
{
    public function generateCode($length = 6)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length)
        {
            $code .= $chars[mt_rand(0, $clen)];
        }

        return $code;
    }

    public function generatePutData($putStr)
    {
        $result = [];
        $putArr = explode('&', $putStr);

        foreach ($putArr as $value)
        {
            $params = explode('=', $value);
            $result[$params[0]] = json_decode($params[1], true);
        }

        return $result;
    }
}