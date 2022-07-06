<?php
include 'DB.php';
include 'AbstractAPI.php';
include 'Archer.php';
include 'Mgflex.php';
include 'Intense.php';
include 'Panda.php';
include 'Internal.php';
include 'CrazyBit.php';
include 'Octagon.php';



class AffiliateTS {

    const ARCHER = 'archer';
    const PANDA = 'panda';
    const OCTAGON = 'octagon';
    const CRAZYBIT = 'crazybit';
    const MGFLEX = 'mgflex';
    const INTERNAL = 'internal';
    const INTENSE = 'intense';

    private $mode;
    private $merchant;
    private $db;
    private $db_username;
    private $db_password;
    private $db_host;
    private $db_name;
    private $affiliateId;
    private $affiliateKey;
    private $apiToken;

    /**
     * 
     * @param int $merchantId
     * @param type $db_host
     * @param type $db_username
     * @param type $db_password
     * @param type $db_name
     */
    function __construct($merchantId, $db_host, $db_username, $db_password, $db_name) {
        // DB credentials
        $this->db_host = $db_host;
        $this->db_username = $db_username;
        $this->db_password = $db_password;
        $this->db_name = $db_name;

        // connect to DB
        $this->db = $this->connectToDb();

        //Set merchant
        $this->merchant = $this->setMerachant($merchantId);

        // Set mode
        $this->mode = $this->merchant->apiType;
    }

    /**
     * 
     * @return \Connection
     */
    private function connectToDb() {
        return new Connection($this->db_host, $this->db_username, $this->db_password, $this->db_name);
    }

    private function setMerachant($merchantId) {
        $merchantId = (int) $merchantId;
        try {
            if (!is_integer($merchantId)) {
                throw new \Exception('Merchant ID must be a integer!');
            }


            $result = $this->db->query("SELECT * FROM merchants WHERE id = '" . (int) $merchantId . "' LIMIT 1;");

            $merchant = $result->fetch_object();

            if (empty($merchant)) {
                throw new \Exception('Merchant not found!');
            }

            $this->merchant = $merchant;

            return $this->merchant;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    function getMerachant() {
        return $this->merchant;
    }

    /**
     * 
     * @param type $affiliateId
     * @param type $affiliateKey
     */
    public function setAffiliateCredentials($affiliateId, $affiliateKey) {
        $this->affiliateId = $affiliateId;
        $this->affiliateKey = $affiliateKey;
    }

    public function setSystemCredentials($affiliateId, $apiToken) {
        $this->apiToken = $apiToken;
        $this->affiliateId = $affiliateId;
    }

    /**
     * Check Affiliate Credentials
     * @return boolean
     */
    public function checkAffiliateCredentials() {

        if (empty($this->affiliateId || $this->affiliateKey)) {
            throw new \Exception('Affiliate ID is required!');
        }

        $result = $this->db->query("SELECT * FROM affiliates WHERE id = " . (int) $this->affiliateId . " LIMIT 1;");
        $affiliate_data = $result->fetch_object();

        if (empty($affiliate_data->id) || empty($affiliate_data->apiToken) || $affiliate_data->apiToken != $this->affiliateKey) {
            throw new \Exception('Invalid affiliate credentials!');
            return false;
        }

        return true;
    }

    public function checkSystemCredentials() {

        if (empty($this->apiToken)) {
            throw new \Exception('apiToken is required!');
        }

        $result = $this->db->query("SELECT * FROM config_api_n_feeds WHERE apiToken = " . (int) $this->apiToken . " LIMIT 1;");
        $data = $result->fetch_object();

        if (empty($data->id) || empty($data->apiToken) || $data->apiToken != $this->apiToken) {
            throw new \Exception('Invalid apiToken credentials!');
            return false;
        }

        return true;
    }

    function integrations() {
        try {
            switch ($this->mode) {
                case self::ARCHER:
                    return new Archer($this->merchant);
                    break;
                case self::PANDA:
                    return new Panda($this->merchant);
                    break;
                case self::OCTAGON:
                    return new Octagon($this->merchant);
                    break;
                case self::CRAZYBIT:
                    return new CrazyBit($this->merchant);
                    break;
		case self::MGFLEX:
                    return new Mgflex($this->merchant);
                    break;
                case self::INTENSE:
                    return new Intense($this->merchant);
                    break;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    function internal(){
        return new Internal($this->merchant, $this->affiliateId, $this->db);
    }

    function getFTDs($users, $dateFrom, $dateTo) {
        $max_period = 7;
        try {

            if ($this->checkAffiliateCredentials() === false) {
                throw new \Exception('Affiliate credentials is wrong!');
            }

            if (empty($dateFrom) || !strtotime($dateFrom)) {
                throw new \Exception('FromDate is required!');
            }

            if (empty($dateTo) || !strtotime($dateTo)) {
                throw new \Exception('ToDate is required!');
            }

            $datetime1 = new DateTime($dateFrom);
            $datetime2 = new DateTime($dateTo);
            $date_interval = $datetime1->diff($datetime2);
            $date_diff = $date_interval->format('%a');

            if (empty($date_diff) || $date_diff > $max_period) {
                throw new \Exception('Max periood ' . $max_period . ' day(s)!');
            }

            $result = [];

            $query_ftds = $this->db->query("SELECT  `trader_id`, `trader_id`, `initialftddate`, `ftdamount` FROM `data_reg` WHERE merchant_id = " . $this->merchant->id . " AND affiliate_id = " . $this->affiliateId . " AND initialftddate between '" . $dateFrom . "' and '" . $dateTo . "' ");

            $ftds_data = $query_ftds->fetch_all(MYSQLI_ASSOC);

            if(!empty($ftds_data)) {
                foreach ($ftds_data as $ftd) {
                    $result[] = [
                        'trader_id' => $ftd['trader_id'],
                        'date' => $ftd['initialftddate'],
                        'amount' => $ftd['ftdamount'],
                    ];
                }
            }

            return json_encode($result);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

}
