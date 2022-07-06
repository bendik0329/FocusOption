<?php
class ShortUrl
{
    public static $chars = "123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";
    public static $table = "short_urls";
    public static $checkUrlExists = true;
	
    
    
 /*
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->timestamp = $_SERVER["REQUEST_TIME"];
    }
 */
    public function urlToShortCode($url) {
        if (empty($url)) {
            throw new Exception("No URL was supplied.");
        }
 
        if ($this->validateUrlFormat($url) == false) {
            die(
                "URL does not have a valid format.");
        }
 
        if (self::$checkUrlExists) {
			// die ($url);
            if (false && !$this->verifyUrlExists($url)) {
                throw new Exception(
                    $url. "  URL does not appear to exist.");
            }
        }
 
        $shortCode = $this->urlExistsInDb($url);
        if ($shortCode == false) {
            $shortCode = $this->createShortCode($url);
        }
 
        return $shortCode;
    }
 
    public function validateUrlFormat($url) {
        return filter_var($url, FILTER_VALIDATE_URL,
            FILTER_FLAG_HOST_REQUIRED);
    }
 
    public function verifyUrlExists($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
 
        return (!empty($response) && $response != 404);
    }
 
    public function urlExistsInDb($url) {
        $url2= $url;
		if (strpos($url,'&pta=')>0) {
		$url2 = explode('&pta=',$url);
		$url2 = $url2[0];
		}
		$query = "SELECT short_code FROM  short_urls WHERE long_url like '".$url2. "%' LIMIT 1";
		//die ($query);
        $stmt = function_mysql_query($query,__FILE__,__FUNCTION__);
        $result = mysql_fetch_assoc($stmt);
        return (empty($result)) ? false : $result['short_code'];
    }
 
    public function createShortCode($url) {
        $id = $this->insertUrlInDb($url);
        $shortCode = $this->convertIntToShortCode($id);
        $this->insertShortCodeInDb($id, $shortCode);
        return $shortCode;
    }
	
	  private function getLastIDf($url,$timestamp) {
        
		$query = "SELECT max(id) as id  FROM  short_urls WHERE long_url = '".$url. "' and date_created= ".$timestamp . " LIMIT 1";
        $stmt = function_mysql_query($query,__FILE__,__FUNCTION__);
        $result = mysql_fetch_assoc($stmt);
        return (empty($result)) ? false : $result['id'];
    }
 
    public function insertUrlInDb($url) {
        //$query = "INSERT INTO  short_urls  (long_url, date_created) " .     " VALUES ('".$url."')"; //,".$timestamp. ")";
        $query = "INSERT INTO  short_urls  (long_url) " .     " VALUES ('".$url."')"; //,".$timestamp. ")";
			
			//die ($query);
        $stmnt = function_mysql_query($query,__FILE__,__FUNCTION__);
		// $lastid = getLastIDf($url,$timestamp);
		// die ($lastid);
        //return $this->pdo->lastInsertId();
		$url2= $url;
		if (strpos($url,'&pta=')>0) {
		$url2 = explode('&pta=',$url);
		$url2 = $url2[0];
		}
		
		$query = "SELECT max(id) as id  FROM  short_urls WHERE long_url like '".$url2. "%'  LIMIT 1";
        $stmt = function_mysql_query($query,__FILE__,__FUNCTION__);
        $result = mysql_fetch_assoc($stmt);
		//var_dump($result);
		
        return (empty($result)) ? false : $result['id'];
		
		
    }
 
    public function convertIntToShortCode($id) {
        $id = intval($id);
        if ($id < 1) {
            die (       "The ID is not a valid integer.");
        }
 
        $length = strlen(self::$chars);
        // make sure length of available characters is at
        // least a reasonable minimum - there should be at
        // least 10 characters
        if ($length < 10) {
            throw new Exception("Length of chars is too small");
        }
 
        $code = "";
        while ($id > $length - 1) {
            // determine the value of the next higher character
            // in the short code should be and prepend
            $code = self::$chars[fmod($id, $length)] .
                $code;
            // reset $id to remaining value to be converted
            $id = floor($id / $length);
        }
 
        // remaining value of $id is less than the length of
        // self::$chars
        $code = self::$chars[$id] . $code;
 
        return $code;
    }
 
    public function insertShortCodeInDb($id, $code) {
        if ($id == null || $code == null) {
            throw new Exception("Input parameter(s) invalid.");
        }
        $query = "UPDATE  short_urls  SET short_code = '" .$code ."' WHERE id = '" .$id ."'";
        $stmnt = function_mysql_query($query,__FILE__,__FUNCTION__);
    }
 
 public function shortCodeToUrl($code, $increment = true) {
        if (empty($code)) {
            die("No short code was supplied.");
        }
 
        if ($this->validateShortCode($code) == false) {
            die(
                "Short code does not have a valid format.");
        }
 
        $urlRow = $this->getUrlFromDb($code);
		if (empty($urlRow)) {
            die(
                "Short code does not appear to exist.");
        }
 
        if ($increment == true) {
            $this->incrementCounter($urlRow["id"]);
        }
        return $urlRow["long_url"];
    }
 
    public function validateShortCode($code) {
        return preg_match("|[" . self::$chars . "]+|", $code);
    }
 
    public function getUrlFromDb($code) {
        $query = "SELECT id, long_url FROM  short_urls  WHERE short_code = '".$code."' LIMIT 1";
        $stmt = function_mysql_query($query,__FILE__,__FUNCTION__);
        $result = mysql_fetch_assoc($stmt);
		return (empty($result)) ? false : $result;
    }
 
    public function incrementCounter($id) {
        //$query = "UPDATE  short_urls  SET counter = counter + 1 WHERE id =". $id;
		$query = "UPDATE `short_urls` SET counter = counter + 1 WHERE `short_urls`.id = " .$id;
		//die ($query);
        $stmt = function_mysql_query($query,__FILE__,__FUNCTION__);
    }
}

?>