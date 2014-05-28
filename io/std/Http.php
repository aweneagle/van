<?php
namespace std;

class Http implements IBlock{
    public $host = null;
    public $port = "80";
    public $protocol = "http";
    public $heads = array();
    public $curl_options = array();

    /* http request
     *
     * @param   $uri,  format like "/someone/do/something.php" , use the self::$host as the default domain
     *
     * @param   $params array, from which the query string is built 
     * @param   $method,  "POST" | "GET",  not case sensitive
     */
    public function query($uri, array $params=array(), $method=null){
        $method = strtoupper($method);
    
        $ch = @curl_init();
        if (!$ch) {
            trigger_error("errno=".curl_errno().",error=".curl_error(), E_USER_ERROR);
            return false;
        }
        $uri = $this->protocol . "://" . $this->host  . ":" . $this->port . "/" . trim($uri, "/") ;
        
        curl_setopt(CURLOPT_URL, $uri);
        switch ($method) {
            case 'GET':
                    $url = $uri . "?" . http_build_query($params);
                    break;
            case 'POST':
                    curl_setopt(CURLOPT_POSTFIELDS, http_build_query($params));
                    break;
        }

        $res = @curl_exec();
        if ($res === false) {
            trigger_error("errno=".curl_errno().",error=".curl_error(), E_USER_ERROR);
            return false;
        }
        return $res;
    }

}

