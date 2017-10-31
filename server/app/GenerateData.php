<?php

class GenerateData{

    public function generatePutData($putStr)
    {
        $result =[];
        $putArr = explode('&',$putStr);

        foreach($putArr as $value)
        {
            $params = explode('=',$value);
            $result[$params[0]] = json_decode($params[1],true);
        }

        return $result;
    }
}