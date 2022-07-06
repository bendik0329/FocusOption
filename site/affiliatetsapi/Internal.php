<?php

class Internal {

    private $merchant;
    private $db;
    private $transactionTypes = ['deposit', 'positions', 'revenue', 'bonus', 'withdrawal', 'volume', 'chargeback', 'PNL', 'purchase'];
    private $systemSettings;
    private $pendingDepositExcludeAffiliatesArray;
    private $pendingDepositIncludeAffiliatesArray;

    function __construct($merchant, $affiliateId, $db) {
        $this->merchant = $merchant;
        $this->db = $db;
        $this->affiliateId = $affiliateId;
        self::setSystemSettings();
        self::pendingDepositAffiliateArrays();
    }

    private function setSystemSettings() {
        $result_affiliate = $this->db->query("SELECT * FROM settings WHERE id='1'");
        $this->systemSettings = $result_affiliate->fetch_object();
    }

    private function pendingDepositAffiliateArrays() {

        $result_affiliates_exclude = $this->db->query("select id,username  from affiliates where pendingDepositExclude=1 and valid=1");
        while ($row = $result_affiliates_exclude->fetch_assoc()) {
            $this->pendingDepositExcludeAffiliatesArray[$row['id']] = $row['username'];
        }

        $result_affiliates_include = $this->db->query("select id,username  from affiliates where pendingDepositInclude=1 and valid=1");
        while ($row = $result_affiliates_include->fetch_assoc()) {
            $this->pendingDepositIncludeAffiliatesArray[$row['id']] = $row['username'];
        }
    }

    /**
     * 
     * @param type $country
     * @param type $ctagCountry
     * @return type
     */
    private function getCountry($country = '', $ctagCountry = '') {

        $db['country'] = '';

        $countries2isoJson = '{"Bangladesh":"BD","Belgium":"BE","Burkina Faso":"BF","Bulgaria":"BG","Bosnia and Herzegovina":"BA","Barbados":"BB","Wallis and Futuna":"WF","Saint Barthelemy":"BL","Bermuda":"BM","Brunei":"BN","Bolivia":"BO","Bahrain":"BH","Burundi":"BI","Benin":"BJ","Bhutan":"BT","Jamaica":"JM","Bouvet Island":"BV","Botswana":"BW","Samoa":"WS","Bonaire, Saint Eustatius and Saba ":"BQ","Brazil":"BR","Bahamas":"BS","Jersey":"JE","Belarus":"BY","Belize":"BZ","Russia":"RU","Rwanda":"RW","Serbia":"RS","East Timor":"TL","Reunion":"RE","Turkmenistan":"TM","Tajikistan":"TJ","Romania":"RO","Tokelau":"TK","Guinea-Bissau":"GW","Guam":"GU","Guatemala":"GT","South Georgia and the South Sandwich Islands":"GS","Greece":"GR","Equatorial Guinea":"GQ","Guadeloupe":"GP","Japan":"JP","Guyana":"GY","Guernsey":"GG","French Guiana":"GF","Georgia":"GE","Grenada":"GD","United Kingdom":"GB","Gabon":"GA","El Salvador":"SV","Guinea":"GN","Gambia":"GM","Greenland":"GL","Gibraltar":"GI","Ghana":"GH","Oman":"OM","Tunisia":"TN","Jordan":"JO","Croatia":"HR","Haiti":"HT","Hungary":"HU","Hong Kong":"HK","Honduras":"HN","Heard Island and McDonald Islands":"HM","Venezuela":"VE","Puerto Rico":"PR","Palestinian Territory":"PS","Palau":"PW","Portugal":"PT","Svalbard and Jan Mayen":"SJ","Paraguay":"PY","Iraq":"IQ","Panama":"PA","French Polynesia":"PF","Papua New Guinea":"PG","Peru":"PE","Pakistan":"PK","Philippines":"PH","Pitcairn":"PN","Poland":"PL","Saint Pierre and Miquelon":"PM","Zambia":"ZM","Western Sahara":"EH","Estonia":"EE","Egypt":"EG","South Africa":"ZA","Ecuador":"EC","Italy":"IT","Vietnam":"VN","Solomon Islands":"SB","Ethiopia":"ET","Somalia":"SO","Zimbabwe":"ZW","Saudi Arabia":"SA","Spain":"ES","Eritrea":"ER","Montenegro":"ME","Moldova":"MD","Madagascar":"MG","Saint Martin":"MF","Morocco":"MA","Monaco":"MC","Uzbekistan":"UZ","Myanmar":"MM","Mali":"ML","Macao":"MO","Mongolia":"MN","Marshall Islands":"MH","Macedonia":"MK","Mauritius":"MU","Malta":"MT","Malawi":"MW","Maldives":"MV","Martinique":"MQ","Northern Mariana Islands":"MP","Montserrat":"MS","Mauritania":"MR","Isle of Man":"IM","Uganda":"UG","Tanzania":"TZ","Malaysia":"MY","Mexico":"MX","Israel":"IL","France":"FR","British Indian Ocean Territory":"IO","Saint Helena":"SH","Finland":"FI","Fiji":"FJ","Falkland Islands":"FK","Micronesia":"FM","Faroe Islands":"FO","Nicaragua":"NI","Netherlands":"NL","Norway":"NO","Namibia":"NA","Vanuatu":"VU","New Caledonia":"NC","Niger":"NE","Norfolk Island":"NF","Nigeria":"NG","New Zealand":"NZ","Nepal":"NP","Nauru":"NR","Niue":"NU","Cook Islands":"CK","Kosovo":"XK","Ivory Coast":"CI","Switzerland":"CH","Colombia":"CO","China":"CN","Cameroon":"CM","Chile":"CL","Cocos Islands":"CC","Canada":"CA","Republic of the Congo":"CG","Central African Republic":"CF","Democratic Republic of the Congo":"CD","Czech Republic":"CZ","Cyprus":"CY","Christmas Island":"CX","Costa Rica":"CR","Curacao":"CW","Cape Verde":"CV","Cuba":"CU","Swaziland":"SZ","Syria":"SY","Sint Maarten":"SX","Kyrgyzstan":"KG","Kenya":"KE","South Sudan":"SS","Suriname":"SR","Kiribati":"KI","Cambodia":"KH","Saint Kitts and Nevis":"KN","Comoros":"KM","Sao Tome and Principe":"ST","Slovakia":"SK","South Korea":"KR","Slovenia":"SI","North Korea":"KP","Kuwait":"KW","Senegal":"SN","San Marino":"SM","Sierra Leone":"SL","Seychelles":"SC","Kazakhstan":"KZ","Cayman Islands":"KY","Singapore":"SG","Sweden":"SE","Sudan":"SD","Dominican Republic":"DO","Dominica":"DM","Djibouti":"DJ","Denmark":"DK","British Virgin Islands":"VG","Germany":"DE","Yemen":"YE","Algeria":"DZ","United States":"US","Uruguay":"UY","Mayotte":"YT","United States Minor Outlying Islands":"UM","Lebanon":"LB","Saint Lucia":"LC","Laos":"LA","Tuvalu":"TV","Taiwan":"TW","Trinidad and Tobago":"TT","Turkey":"TR","Sri Lanka":"LK","Liechtenstein":"LI","Latvia":"LV","Tonga":"TO","Lithuania":"LT","Luxembourg":"LU","Liberia":"LR","Lesotho":"LS","Thailand":"TH","French Southern Territories":"TF","Togo":"TG","Chad":"TD","Turks and Caicos Islands":"TC","Libya":"LY","Vatican":"VA","Saint Vincent and the Grenadines":"VC","United Arab Emirates":"AE","Andorra":"AD","Antigua and Barbuda":"AG","Afghanistan":"AF","Anguilla":"AI","U.S. Virgin Islands":"VI","Iceland":"IS","Iran":"IR","Armenia":"AM","Albania":"AL","Angola":"AO","Antarctica":"AQ","American Samoa":"AS","Argentina":"AR","Australia":"AU","Austria":"AT","Aruba":"AW","India":"IN","Aland Islands":"AX","Azerbaijan":"AZ","Ireland":"IE","Indonesia":"ID","Ukraine":"UA","Qatar":"QA","Mozambique":"MZ"}';
        $countriesArr = json_decode($countries2isoJson);

        $country = strtoupper($country);
        if (!empty($country)) {
            if (strlen($country) > 2) {
                $db['country'] = $countriesArr->$country;
            } else {
                $db['country'] = $country;
            }
        }

        if (strtolower($db['country']) == 'any' OR empty($db['country'])) {
            $gc = str_replace(Array("\\", "'", "`"), Array("", "", ""), strtoupper($ctagCountry));
            $db['country'] = str_replace(Array("\\", "'", "`"), Array("", "", ""), $gc);
            if (!empty($db['country']) && strlen($db['country']) > 2)
                $db['country'] = $countriesArr->$db['country'];
        }

        return $db['country'];
    }

    /**
     * 
     * @return string
     */
    private function getDefaultBtag() {
        $btag = empty($this->affiliateId) ? 500 : $this->affiliateId;

        return 'a' . $btag . '-b1-p0';
    }

    /**
     * 
     * @param type $btag
     * @return boolean
     */
    private function validateBtag($btag = '') {
        if (!$btag)
            return false;
        $exp = explode("-", $btag);
        if (substr($exp[0], 0, 1) == "a" AND substr($exp[0], 1, 1) != 0 AND substr($exp[1], 0, 1) == "b")
            return true;
        return false;
    }

    /**
     * 
     * @param type $btag
     * @return string
     */
    private function getBtagData($btag) {

        $btag = str_replace("--", "-", $btag);
        $btag = str_replace(" ", "%20", $btag);
        $exp = explode("-", $btag);

        $bt = Array();

        for ($i = 0; $i < count($exp); $i++) {
            $additional = "";
            $preParam = (substr($exp[$i], 0, 1));

            if ($preParam == 'a' && empty($bt['affiliate_id'])) {
                $tag = 'affiliate_id'; //break;
            } elseif ($preParam == 'b' && empty($bt['banner_id'])) {
                $tag = 'banner_id';  //break;
            } elseif ($preParam == 'p' && empty($bt['profile_id'])) {
                $tag = 'profile_id'; //break;
			} elseif ($preParam == 'g' && empty($bt['product_id'])) {
                $tag = 'product_id'; //break;
            } elseif ($preParam == 'c' && empty($bt['country'])) {
                $tag = 'country';  //break;
            } elseif ($preParam == 'u' && empty($bt['uid'])) {
                $tag = 'uid'; //    break;
            } elseif ($preParam == 'f') {
                $tag = 'freeParam';  //break;
            } else {
                $tag = 'freeParam';
                $additional = "-" . $preParam;
            }

            $thevalue = substr($exp[$i], 1);
            $bt[$tag] .= $additional . $thevalue;
        }

        foreach ($bt as $name => $value) {
            if (empty($bt[$name])) {
                $bt[$name] = "0";
            }
        }

        return $bt;
    }

    /**
     * 
     * @return type
     */
    private function getAffiliateData() {
        $result_affiliate = $this->db->query("SELECT * FROM affiliates WHERE id='" . $this->affiliateId . "'");
        $getAffiliate = $result_affiliate->fetch_object();
        return $getAffiliate;
    }

    private function getTraderData($traderID) {
        $result_chkDouble = $this->db->query("SELECT * FROM data_reg WHERE merchant_id = '" . $this->merchant->id . "' AND trader_id='" . $traderID . "' LIMIT 1");
        return $result_chkDouble->fetch_object();
    }

    private function getTransactionData($TransactionID, $traderID, $Type) {

        $result_chkDouble = $this->db->query("SELECT id,type,tranz_id FROM data_sales WHERE  merchant_id = " . $this->merchant->id . " and trader_id='" . $traderID . "' AND type='" . $Type . "' AND tranz_id='" . $TransactionID . "' LIMIT 1");
        $chkDouble = $result_chkDouble->fetch_object();
        if (empty($chkDouble->id)) {
            $result_chkDouble = $this->db->query("SELECT id,type,tranz_id FROM data_sales_pending WHERE  merchant_id = " . $this->merchant->id . " and trader_id='" . $traderID . "' AND type='" . $Type . "' AND tranz_id='" . $TransactionID . "' LIMIT 1");
            $chkDouble = $result_chkDouble->fetch_object();
        }

        return $chkDouble;
    }

    private function isDepositPending($affiliate_id, $amount) {
        
        if (
                $this->systemSettings->hidePendingProcessHighAmountDeposit == 0 &&
                (
                    (
                        $amount >= $this->systemSettings->pendingDepositsAmountLimit &&
                        empty($this->pendingDepositExcludeAffiliatesArray[$affiliate_id])
                    ) ||
                    !empty($this->pendingDepositIncludeAffiliatesArray[$affiliate_id])
                )
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function getUSD($price = '0', $from = 'USD') {
        if (strtolower($from) == "usd") {
            return $price;
        } else {
            $result_currency = $this->db->query("SELECT val,rate FROM exchange_rates WHERE lower(fromCurr)='" . strtolower($from) . "' LIMIT 1");
            $currency = $result_currency->fetch_object();
            if (!empty($currency)) {
                if ($currency->rate > 0) {
                    return round($price * ($currency->val * (1 - $currency->rate / 100)), 2);
                } else {
                    return round($price * $currency->val, 2);
                }
            } else {
                return false;
            }
        }
    }

    /**
     * 
     * @param type $rdate
     * @param type $ctag
     * @param type $group_id
     * @param type $product_id
     * @param type $banner_id
     * @param type $profile_id
     * @param type $saleStatus
     * @param type $country
     * @param type $trader_id
     * @param type $type
     * @param type $email
     * @param type $name
     * @param type $phone
     * @return boolean
     */
    function createLead($rdate, $ctag, $saleStatus, $country, $trader_id, $type, $email, $name = '', $phone = '') {
        try {

            $db = [];
            $errors = [];
            $btag_data = [];

            $db['trader_alias'] = '';

            // MerchantID
            $db['merchant_id'] = $this->merchant->id;

            // Product ID
            $db['product_id'] = 0;

            // Type
            $db['type'] = $type;

            // Email
            if (empty($email)) {
                $db['email'] = '';
            } else {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $db['email'] = $email;
                } else {
                    $errors['email'] = 'Invalid email format';
                }
            }

            // Name
            $db['name'] = $this->db->real_escape_string($name);

            // Phone
            $db['phone'] = $this->db->real_escape_string($phone);

            // Trader ID
            if (empty($trader_id)) {
                $errors['trader_id'] = 'trader_id is empty';
            } else {
                $db['trader_id'] = $this->db->real_escape_string($trader_id);
            }

            // Sale status
            $db['saleStatus'] = $this->db->real_escape_string($saleStatus);

            // Date
            if (strtotime($rdate) === FALSE) {
                $errors['data'] = 'Invalid date format';
            } else {
                $db['rdate'] = date("Y-m-d H:i:s", strtotime($rdate));
            }

            if (empty($ctag)) {
                $ctag = self::getDefaultBtag();
            }

            if (!self::validateBtag($ctag)) {
                $ctag = self::getDefaultBtag();
            }

            $db['ctag'] = $ctag;

            $btag_data = self::getBtagData($ctag);

            if (!empty($errors)) {
                return $errors;
            }

            // Check for dublicate
            $chkDouble = self::getTraderData($db['trader_id']);

            if ($chkDouble->id) {
                if ($chkDouble->saleStatus == $db['saleStatus']) {
                $errors['global'] = 'trader_id already exists';
                return $errors;
                } else {
                    $qry = "UPDATE data_reg SET saleStatus='" . $db['saleStatus'] . "' WHERE  id=" . (int) $chkDouble->id;
                    $result = $this->db->query($qry);

                    if ($result) {

                        return true;
                    }
                    return false;
                }
            }

            $db['affiliate_id'] = $this->db->real_escape_string($btag_data['affiliate_id']);
            $db['banner_id'] = $this->db->real_escape_string($btag_data['banner_id']);
            $db['profile_id'] = $this->db->real_escape_string($btag_data['profile_id']);
			$db['product_id'] = $this->db->real_escape_string($btag_data['product_id']);
            $db['uid'] = $this->db->real_escape_string($btag_data['uid']);
            $db['freeParam']=$this->db->real_escape_string($btag_data['freeParam']);

            $db['country'] = $this->db->real_escape_string(self::getCountry($country, $btag_data['country']));

            $getAffiliate = self::getAffiliateData();
            $db['group_id'] = $getAffiliate->group_id;


            $qry = "INSERT INTO data_reg (
                    merchant_id,
                    rdate,
                    ctag,
                    affiliate_id,
                    group_id,
                    product_id,
                    banner_id,
                    profile_id,
                    saleStatus,
                    country,
                    trader_id,
                    type,
                    uid,
                    email,
                    trader_alias,
                    phone,
                    freeParam
                ) VALUES (
                    " . $db['merchant_id'] . ",
                    '" . $db['rdate'] . "',
                    '" . $db['ctag'] . "',
                    '" . $db['affiliate_id'] . "',
                    '" . $db['group_id'] . "',
                    '" . $db['product_id'] . "',
                    '" . $db['banner_id'] . "',
                    '" . $db['profile_id'] . "',
                    '" . $db['saleStatus'] . "',
                    '" . $db['country'] . "',
                    '" . $db['trader_id'] . "',
                    '" . $db['type'] . "',
                    '" . $db['uid'] . "', 
                    '" . $db['email'] . "',
                    '" . $db['name'] . "',
                    '" . $db['phone'] . "',
                    '" . $db['freeParam'] . "'
                )";

            $result = $this->db->query($qry);

            if ($result) {
                $this->sendPixelAccount($db['ctag'], $db['merchant_id'], $db['trader_id'], '', '');
                return [
                    'status' => 'ok',
                    'trader_id' => $db['trader_id']
                ];
            }
            return false;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * 
     * @param type $rdate
     * @param type $ctag
     * @param type $saleStatus
     * @param type $country
     * @param type $trader_id
     * @param type $type
     * @param type $email
     * @param type $name
     * @param type $phone
     * @return type
     */
    function createCustomer($rdate, $ctag, $saleStatus, $country, $trader_id, $type, $email, $name = '', $phone = '') {
        try {
            return self::createLead($rdate, $ctag, $saleStatus, $country, $trader_id, $type, $email, $name, $phone);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    /**
     * 
     * @param type $rdate
     * @param type $trader_id
     * @param type $tranz_id
     * @param type $type
     * @param type $currency
     * @param type $amount
     * @return type
     */
    function createTransaction($rdate, $trader_id, $tranz_id, $type, $currency, $amount) {
        try {

            $db = [];
            $errors = [];
            $btag_data = [];

            // MerchantID
            $db['merchant_id'] = $this->merchant->id;

            // Date
            if (strtotime($rdate) === FALSE) {
                $errors['data'] = 'Invalid date format';
            } else {
                $db['rdate'] = date("Y-m-d H:i:s", strtotime($rdate));
            }

            // Trader ID
            if (empty($trader_id)) {
                $errors['trader_id'] = 'trader_id is empty';
            } else {
                $db['trader_id'] = $this->db->real_escape_string($trader_id);
            }

            // Transaction ID
            if (empty($tranz_id)) {
                $errors['tranz_id'] = 'tranz_id is empty';
            } else {
                $db['tranz_id'] = $this->db->real_escape_string($tranz_id);
            }

            $coin = strtoupper($currency);

            if ($type == 'PNL') {
                $amount_usd = self::getUSD($amount, $coin);
            } else {
            $amount_usd = abs(self::getUSD($amount, $coin));
            }

            if ($amount_usd == false) {
                $errors['amount'] = 'Invalid amount';
            }
            $db['amount'] = $amount_usd;

            if (empty($type) || !in_array($type, $this->transactionTypes)) {
                $errors['type'] = 'Invalid transaction type';
            }

            $db['type'] = $type;

            // Check for dublicate
            $traderData = self::getTraderData($db['trader_id']);

            if (empty($traderData)) {
                $errors['global'] = 'trader_id not exists';
                return $errors;
            }

            $db['trader_alias'] = $this->db->real_escape_string($traderData->trader_alias);
            $db['freeParam'] = $this->db->real_escape_string($traderData->freeParam);
            $db['freeParam2'] = $this->db->real_escape_string($traderData->freeParam2);

            if (!empty($errors)) {
                return $errors;
            }

            // Check for dublicate
            $chkDouble = self::getTransactionData($db['tranz_id'], $db['trader_id'], $db['type']);
            if ($chkDouble->id) {
                $errors['global'] = 'tranz_id already exists';
                return $errors;
            }

            $db['ctag'] = $traderData->ctag;
            $btag_data = self::getBtagData($db['ctag']);

            $db['affiliate_id'] = $this->db->real_escape_string($traderData->affiliate_id);
            $db['banner_id'] = $this->db->real_escape_string($btag_data['banner_id']);
            $db['profile_id'] = $this->db->real_escape_string($btag_data['profile_id']);
			$db['product_id'] = $this->db->real_escape_string($btag_data['product_id']);
            $db['uid'] = $this->db->real_escape_string($btag_data['uid']);

            $db['country'] = $this->db->real_escape_string(self::getCountry($country, $btag_data['country']));

            $getAffiliate = self::getAffiliateData();
            $db['group_id'] = $getAffiliate->group_id;

            $db_transaction_table_name = "data_sales";
            
            if ($type == 'deposit' && self::isDepositPending($db['affiliate_id'], $db['amount'])) {
                $db_transaction_table_name = "data_sales_pending";
            }

            $qry = "INSERT INTO " . $db_transaction_table_name . " (
                    `dummySource`,
                    `currentDate`,
                    `merchant_id`,
                    `rdate`,
                    `ctag`,
                    `affiliate_id`,
                    `group_id`,
                    `banner_id`,
                    `profile_id`,
					`product_id`,
                    `country`,
                    `tranz_id`,
                    `trader_id`,
                    `trader_alias`,
                    `type`,
                    `amount`,
                    `freeParam`,
                    `freeParam2`
                ) VALUES (
                    33, 
                    '" . date('Y-m-d H:i:s') . "', 
                    " . $db['merchant_id'] . ", 
                    '" . $db['rdate'] . "',
                    '" . $db['ctag'] . "',
                    '" . $db['affiliate_id'] . "',
                    '" . $db['group_id'] . "',
                    '" . $db['banner_id'] . "',
                    '" . $db['profile_id'] . "',
					'" . $db['product_id'] . "',
                    '" . $db['country'] . "',
                    '" . $db['tranz_id'] . "',
                    '" . $db['trader_id'] . "',
                    '" . $db['trader_alias'] . "',
                    '" . $db['type'] . "',
                    '" . $db['amount'] . "',
                    '" . $db['freeParam'] . "',
                    '" . $db['freeParam2'] . "'
                )";
                    
            if ($type == 'deposit') {

                if ($traderData->ftdamount == 0 && $db['type'] == 'deposit' && $traderData->initialftdtranzid == '') {
                    $GetFTDforTraderQuery = "select id, rdate, tranz_id,amount from data_sales where trader_id = " . $traderData->trader_id . " and merchant_id = " . $db['merchant_id'] . " and type='deposit' order by rdate limit 1";
                    $GetFTDforTraderQuery_result = $this->db->query($GetFTDforTraderQuery);
        
                    $GetFTDforTrader = $GetFTDforTraderQuery_result->fetch_object();
                    if (!empty($GetFTDforTrader)) {
                        $UpdateFTDforTrader = "update data_reg set  ftdamount = " . $GetFTDforTrader->amount . " , initialftdtranzid = '". $GetFTDforTrader->tranz_id . "' , initialftddate = '" . $GetFTDforTrader->rdate . "' where trader_id= " . $traderData->trader_id . " and merchant_id = " . $db['merchant_id'];
                        $this->db->query($UpdateFTDforTrader);
                    }
                }
            }

            $result = $this->db->query($qry);

            if ($result) {
                if($db_transaction_table_name != "data_sales_pending"){
                $this->sendPixelDeposit($db['ctag'], $db['merchant_id'], $db['trader_id'], $db['tranz_id'], $db['type'], 'USD', $db['amount'], '');
                }
                return true;
            }
            return false;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    function sendPixelAccount($ctag, $merchant_id, $trader_id, $trader_alias, $subid){
        
        include_once ('../pixel.php');
        
        // account
        $pixelurl = 'http://'.$_SERVER['HTTP_HOST']. 'pixel.php?act=account&ctag='.$ctag.'&merchant_id='.$merchant_id.'&trader_id='.$trader_id.'&trader_alias='. str_replace(' ','%20',$trader_alias) . '&subid=' . $subid;
        
        $pixelContent = firePixel($pixelurl);

        //var_dump($pixelContent);
    }
    
    function sendPixelDeposit($ctag, $merchant_id, $trader_id, $tranz_id, $type, $currency, $amount, $subid){
        
        include_once ('../pixel.php');
        
        // transaction
        $pixelurl = 'http://'.$_SERVER['HTTP_HOST']. '/pixel.php?act=deposit&ctag='.$ctag.'&trader_id='.$trader_id.'&merchant_id='.$merchant_id.'&tranz='.$tranz_id.'&type='.$type.'&currency='.$currency.'&amount='.$amount.'&subid='.$subid;
        
        $pixelContent = firePixel($pixelurl);

        //var_dump($pixelContent);
    }
    
    
    function updateEventCount($merchant_id,$affiliate_id, $date, $event, $count){

        if($event == 'click'){
            $updateImpressionStat = "INSERT INTO merchants_creative_stats(Date,AffiliateID,MerchantID,BannerID,Impressions, Clicks, CountryID) VALUES ('".date('Y-m-d',strtotime($date))."','".$affiliate_id."','".$merchant_id."', '0', '0','".$count."', '') ON DUPLICATE KEY UPDATE Clicks='".$count."'";
        }else{
            $updateImpressionStat = "INSERT INTO merchants_creative_stats(Date,AffiliateID,MerchantID,BannerID,Impressions, Clicks, CountryID) VALUES ('".date('Y-m-d',strtotime($date))."','".$affiliate_id."','".$merchant_id."', '0', '".$count."','0', '') ON DUPLICATE KEY UPDATE Impressions='".$count."'";
        }
        
        $result = $this->db->query($updateImpressionStat);
       
        return $result;
    }
    
}
