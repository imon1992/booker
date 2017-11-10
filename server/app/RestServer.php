<?php
require_once('Autoloader.php');
spl_autoload_register([
    'Autoloader',
    'loadPackages'
]);

include('config.php');

class RestServer
{
    protected $url;
    protected $requestMethod;
    protected $cars;
    protected $contentType;
    protected $class;

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->url = $_SERVER['REQUEST_URI'];
    }

    /**
     * @param string $dir
     * @return object
     * creates the necessary class
     */
    private function classCreate($dir)
    {
        $class = ucfirst($dir) . 's';
        $this->class = new $class;
    }
    /**
     * @return object
     * checks the request method and starts execution
     */
    public function run()
    {
        list($s, $user, $REST, $server, $api, $dir, $params) = explode("/", $this->url, 7);
        $this->classCreate($dir);

        switch (trim($this->requestMethod))
        {
            case 'GET':
                $result = $this->setMethod('get' . ucfirst($dir), $params);
                return $this->sendResponse($result);
                break;
            case 'POST':
                $result = $this->setMethod('post' . ucfirst($dir), '');
                return $this->sendResponse($result);
                break;
            case 'PUT':
                $result = $this->setMethod('put' . ucfirst($dir), '');
                return $this->sendResponse($result);
                break;
            case 'DELETE':
                $result = $this->setMethod('delete' . ucfirst($dir), $params);
                return $this->sendResponse($result);
//                break;
        }
    }

    /**
     * @param string $classMethod
     * @param string $params
     * @return object
     * start necessary method
     */
    protected function setMethod($classMethod, $params)
    {
        if (method_exists($this->class, $classMethod))
        {
            if (is_string($params) || is_array($params))
            {
                $result = $this->class->$classMethod($params);
                if ($result === 'error')
                {
                    $this->sendHeaders(500);
                }
            } else
            {
                $this->sendHeaders(403);
            }

            return $result;
        } else
        {
            $this->sendHeaders(501);
        }
    }


    private function sendHeaders($errorCode)
    {
        header("HTTP/1.1 $errorCode " . $this->getStatusMessage($errorCode));
    }

    private function sendResponse($result)
    {
        if(RESPONSE_METHOD == 'json')
        {
            header($this->getStatusMessage('contentTypeJson'));
            echo json_encode($result);
        }
    }

    private function getStatusMessage($code)
    {
        $status = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            'contentTypeJson' => 'Content-Type: application/json'
        ];

        return ($status[$code]) ? $status[$code] : $status[500];
    }
}
