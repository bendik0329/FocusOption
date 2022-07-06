<?php
	
	abstract class CronBase
	{
		/**
		 * Instance of Dal
		 * 
		 * @var Dal
		 */
		protected $dal;
		
		/**
		 * Instance of \DateTime
		 * 
		 * @var \DateTime
		 */
		protected $dateTime;
		
		/**
		 * Database name.
		 * 
		 * @var string
		 */
		//protected $strDbName;
		
		/**
		 * Total pages.
		 * 
		 * @var int
		 */
		protected $intTotalPages;
		
		/**
		 * Date, to scan data from.
		 * 
		 * @var string
		 */
		protected $strScanDateFrom;
		
		/**
		 * Date, to scan data to.
		 * 
		 * @var string
		 */
		protected $strScanDateTo;
		
		/**
		 * API username.
		 * 
		 * @var string
		 */
		protected $strApiUser;
		
		/**
		 * API password.
		 * 
		 * @var string
		 */
		protected $strApiPass;
		
		/**
		 * API URL.
		 * 
		 * @var string
		 */
		protected $strApiUrl;
		
		/**
		 * API whitelabel.
		 * 
		 * @var string
		 */
		protected $strApiWhiteLabel;
		
		/**
		 * API type.
		 * 
		 * @var string
		 */
		protected $strApiType;
		
		/**
		 * Site URL.
		 * 
		 * @var string
		 */
		protected $strSiteUrl;
		
		/**
		 * Define pages amount.
		 * 
		 * @param  array $arrGet
		 * @return void
		 */
		protected function defineDatesRange(array $arrGet)
		{
			$this->intTotalPages = 1;
			$arrDateTime         = explode('-', (isset($arrGet['m_date']) ? $arrGet['m_date'] : $this->dateTime->format('Y-m-d')));
			
			if (isset($arrGet['m_date']) && !empty($arrGet['m_date']) && isset($arrGet['monthly'])) {
				$this->intTotalPages   = 30;
				$this->strScanDateFrom = $this->dateTime->setDate($arrDateTime[0], $arrDateTime[1], $arrDateTime[2])->format('Y-m-1');
				$this->strScanDateTo   = $this->dateTime->setDate($arrDateTime[0], $arrDateTime[1], $arrDateTime[2])->modify('+1 month')->format('Y-m-1');
			} elseif (isset($arrGet['m_date']) && !empty($arrGet['m_date']) && !isset($arrGet['monthly'])) {
				$this->strScanDateFrom = $this->dateTime->setDate($arrDateTime[0], $arrDateTime[1], $arrDateTime[2])->format('Y-m-d');
				$this->strScanDateTo   = $this->dateTime->setDate($arrDateTime[0], $arrDateTime[1], $arrDateTime[2])->modify('+1 day')->format('Y-m-d');
			} else {
				$this->strScanDateFrom = $this->dateTime->modify('-1 day')->format('Y-m-d');
				$this->strScanDateTo   = $this->dateTime->modify('+2 day')->format('Y-m-d');
			}
		}
		
		/**
		 * Constructor.
		 * 
		 * @param array $arrConfig
		 */
		public function __construct(array $arrConfig)
		{
			//$this->strDbName  = $arrConfig['db_name'];
			$this->strSiteUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/';
			$this->dal        = new Dal('dal', '', $arrConfig['db']);
			$this->dateTime   = new \DateTime();
			$this->defineDatesRange($arrConfig['$_GET']);
		}
		
		/**
		 * Check if JSON error occurred.
		 * 
		 * @param  void
		 * @return bool
		 */
		protected function isJsonErrorOccurred()
		{
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					return false;
				case JSON_ERROR_DEPTH:
					echo '<br />JSON last error: JSON_ERROR_DEPTH<br />';
					return true;
				case JSON_ERROR_STATE_MISMATCH:
					echo '<br />JSON_ERROR_STATE_MISMATCH<br />';
					return true;
				case JSON_ERROR_CTRL_CHAR:
					echo '<br />JSON_ERROR_CTRL_CHAR<br />';
					return true;
				case JSON_ERROR_SYNTAX:
					echo '<br />JSON last error: JSON_ERROR_SYNTAX<br />';
					return true;
				case JSON_ERROR_UTF8:
					echo '<br />JSON last error: JSON_ERROR_UTF8<br />';
					return true;
				default:
					echo '<br />JSON last error: Unknown error occurred<br />';
					return true;
			}
		}
		
		/**
		 * Perform request via CURL.
		 * 
		 * @param  string $url
		 * @param  array  $arrFields
		 * @param  bool   $useGet
		 * @return mixed
		 */
		protected function curlPost($url, $arrFields = [], $useGet = false)
		{
			$fieldsString = '';
			$result       = null;
			
			foreach ($arrFields as $key => $value) { 
				$fieldsString .= $key . '=' . $value . '&'; 
			}
			
			if ($useGet) {
				$url    .= (empty($fieldsString) ? '' : '?' . $fieldsString);
				$result  = file_get_contents($url);
			} else {
				rtrim($fieldsString, '&');
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, count($arrFields));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch);
				curl_close($ch);
			}
			
			return $result;
		}
		
		/**
		 * Get campaign ids.
		 * 
		 * @param  int   $intMerchantId
		 * @param  array $arrCampaignsToExclude
		 * @return array
		 */
		protected function getCampaigns($intMerchantId, array $arrCampaignsToExclude = [])
		{
			if (isset($_GET['isTest'])) {
				return [
					[
						'campaign_id'  => $_GET['isTest'],
						'affiliate_id' => 0,
					],
				];
			}
			
			/**
			 * Override begins.
			 */
			$arrCampaignsToExclude = [
				//
			];
			/**
			 * Override ends.
			 */
			
			$arrSql = [
				'table'  => 'affiliates_campaigns_relations',
				'select' => [
					'DISTINCT campID' => 'campID', 
					'affiliateID'     => 'affiliateID',
				],
				'where'  => [
					'merchantid'  => $intMerchantId,
				],
			];
			
			$arrRetVal    = [];
			$arrCampaigns = $this->dal->select($arrSql);
			
			foreach ($arrCampaigns as $arrCamp) {
				if (!in_array($arrCamp['campID'], $arrCampaignsToExclude)) {
					$arrRetVal[] = [
						'campaign_id'  => $arrCamp['campID'],
						'affiliate_id' => $arrCamp['affiliateID'],
					];
				}
				
				unset($arrCamp);
			}
			
			unset($arrCampaigns, $arrSql);
			return $arrRetVal;
		}
		
		/**
		 * Send POST request via php socket.
		 * 
		 * @param  string $strUrl
		 * @return string
		 */
		protected function doPost($strUrl)
		{
			$parse_url = parse_url($strUrl);
			$resource  = fsockopen($parse_url['host'], 80, $errno, $errstr);
			
			if (empty($resource)) {
				echo 'Fsockopen message: ', $errstr, '   Fsockopen error-number: ', $errno, '<br />';
				return $resource . '<br />';
			} else {
				$params  = 'POST ' . $parse_url['path'] . " HTTP/1.1\r\n";
				$params .= 'Host: ' . $parse_url['host'] . "\r\n";
				$params .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$params .= "User-Agent: AB Agent\r\n";
				$params .= 'Content-Length: ' . strlen($parse_url['query']) . "\r\n";
				$params .= "Connection: close\r\n\r\n";
				$params .= $parse_url['query'];
				fputs($resource, $params);
				
				while (!feof($resource)) {
					$response .= fgets($resource);
				}
				
				fclose($resource);
				
				$result  = explode("\r\n\r\n", $response, 2);
				$content = isset($result[1]) ? $result[1] : '';
				return $content;
			}
		}
		
		/**
		 * Converts any given object to an array with lower case keys.
		 * 
		 * @param  mixed $object
		 * @return array|string
		 */
		protected function objectToArrayLowerCase($object)
		{
			$arrRetVal = [];
			
			if (!is_object($object)) {
				return $object;
			}
			
			$arrObject = get_object_vars($object);
			
			if (empty($arrObject)) {
				unset($arrObject);
				return $arrRetVal;
			}
			
			foreach ($arrObject as $k => $v) {
				$arrRetVal[strtolower($k)] = $this->objectToArrayLowerCase($v);
				unset($k, $v);
			}
			unset($arrObject);
			return $arrRetVal;
		}
		
		/**
		 * Retrieve registration data from SpotOption API.
		 * 
		 * @param  int $intCampaign
		 * @param  int $intPage
		 * @return array
		 */
		protected function getRegistrationDataFromSpot($intCampaign, $intPage = 0)
		{
			$strUrl = $this->strApiUrl . '?api_username=' 
					. $this->strApiUser . '&api_password=' 
					. $this->strApiPass . '&MODULE=Customer&COMMAND=view' 
					. '&FILTER[campaignid]=' 
					. $intCampaign . '&FILTER[regTime][min]=' 
					. $this->strScanDateFrom . '&FILTER[regTime][max]=' 
					. $this->strScanDateTo
					. (empty($intPage) ? '' : '&page=' . $intPage);
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strXmlReport = $this->doPost($strUrl);
			
			if (strlen($strXmlReport) < 120) {
				echo '<span style="color:red;">';
				var_dump($strXmlReport);
				echo '</span>';
			}
			
			/**
			 * PATCH (because of SPOT-API).
			 */
			libxml_use_internal_errors(true);
			preg_match_all('/<status>(.*?)<\/status>/', $strXmlReport, $arrXml);
			
			$objXmlReport = @simplexml_load_string('<status>' . $arrXml[1][0] . '</status>');
			
			/**
			 * Fetch all possible XML errors.
			 */
			foreach(libxml_get_errors() as $error) {
				echo '<br />XML&nbsp;ERROR:&nbsp;', $error->message, '<br />';
				unset($error);
			}
			
			return $this->objectToArrayLowerCase($objXmlReport);
		}
		
		/**
		 * Retrieve leads data from SpotOption API.
		 * 
		 * @param  int $intCampaign
		 * @param  int $intPage
		 * @return array
		 */
		protected function getLeadsDataFromSpot($intCampaign, $intPage = 0)
		{
			$strUrl = $this->strApiUrl . '?api_username=' 
					. $this->strApiUser . '&api_password=' 
					. $this->strApiPass . '&MODULE=Lead&COMMAND=view&api_whiteLabel=' 
					. $this->strApiWhiteLabel . '&FILTER[campaignid]=' 
					. $intCampaign . '&FILTER[regTime][min]=' 
					. $this->strScanDateFrom . '&FILTER[regTime][max]=' 
					. $this->strScanDateTo
					. (empty($intPage) ? '' : '&page=' . $intPage);
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strXmlReport = $this->doPost($strUrl);
			
			if (strlen($strXmlReport) < 120) {
				echo '<span style="color:red;">LEADS_XML:&nbsp;';
				var_dump($strXmlReport);
				echo '</span>';
			}
			
			/**
			 * PATCH (because of SPOT-API).
			 */
			libxml_use_internal_errors(true);
			preg_match_all('/<status>(.*?)<\/status>/', $strXmlReport, $arrXml);
			
			$objXmlReport = @simplexml_load_string('<status>' . $arrXml[1][0] . '</status>');
			
			/**
			 * Fetch all possible XML errors.
			 */
			foreach(libxml_get_errors() as $error) {
				echo '<br />XML&nbsp;ERROR:&nbsp;', $error->message, '<br />';
				unset($error);
			}
			
			return $this->objectToArrayLowerCase($objXmlReport);
		}
		
		/**
		 * Retrieve deposits data from SpotOption API.
		 * 
		 * @param  int $intCampaign
		 * @param  int $intPage
		 * @return array
		 */
		protected function getCustomersDepositsFromSpot($intCampaign, $intPage = 0)
		{
			$strUrl = $this->strApiUrl . '?api_username=' 
					. $this->strApiUser . '&api_password=' 
					. $this->strApiPass . '&MODULE=CustomerDeposits&COMMAND=view&api_whiteLabel=' 
					. $this->strApiWhiteLabel . '&FILTER[campaignid]=' 
					. $intCampaign . '&FILTER[requestTime][min]=' 
					. $this->strScanDateFrom . '&FILTER[requestTime][max]=' 
					. $this->strScanDateTo
					. (empty($intPage) ? '' : '&page=' . $intPage);
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strXmlReport = $this->doPost($strUrl);
			
			if (strlen($strXmlReport) < 120 || true) {
				echo '<span style="color:red;">';
				var_dump($strXmlReport);
				echo '</span>';
			}
			
			/**
			 * PATCH (because of SPOT-API).
			 */
			libxml_use_internal_errors(true);
			preg_match_all('/<status>(.*?)<\/status>/', $strXmlReport, $arrXml);
			
			//$objXmlReport = @simplexml_load_string('<status>' . $arrXml[1][0] . '</status>');  // Not working any more.
			$objXmlReport = @simplexml_load_string($strXmlReport);
			
			/**
			 * Fetch all possible XML errors.
			 */
			foreach(libxml_get_errors() as $error) {
				echo '<br />XML&nbsp;ERROR:&nbsp;', $error->message, '<br />';
				unset($error);
			}
			
			return $this->objectToArrayLowerCase($objXmlReport);
		}
		
		/**
		 * Retrieve stats data from SpotOption API.
		 * 
		 * @param  int $intCampaign
		 * @param  int $intPage
		 * @return array
		 */
		protected function getStatsDataFromSpot($intCampaign, $intPage = 0)
		{
			$strUrl = $this->strApiUrl . '?api_username=' 
					. $this->strApiUser . '&api_password=' 
					. $this->strApiPass . '&MODULE=Positions&COMMAND=view&api_whiteLabel=' 
					. $this->strApiWhiteLabel
					. '&FILTER[date][min]=' 
					. $this->strScanDateFrom . '&FILTER[date][max]=' 
					. $this->strScanDateTo
					. (empty($intPage) ? '' : '&page=' . $intPage);
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strXmlReport = $this->doPost($strUrl);
			
			/**
			 * PATCH (because of SPOT-API).
			 */
			$xmlStartTagPosition = strpos($strXmlReport, '<status>');
			$xmlStartTagPosition = false !== $xmlStartTagPosition ? $xmlStartTagPosition : 0;
			$xmlEndTagPosition   = strrpos($strXmlReport, '</status>');
			$xmlEndTagPosition   = false !== $xmlEndTagPosition ? $xmlEndTagPosition : strlen($strXmlReport) - 1;
			$intCharsToSubtract  = $xmlEndTagPosition - strlen($strXmlReport);
			$strXmlReport        = substr($strXmlReport, 0, $intCharsToSubtract);
			$strXmlReport        = substr($strXmlReport, $xmlStartTagPosition) . '</status>';
			
			if (strlen($strXmlReport) < 120) {
				echo '<span style="color:red;">';
				var_dump($strXmlReport);
				echo '</span>';
			}
			
			/**
			 * PATCH (because of SPOT-API).
			 */
			libxml_use_internal_errors(true);
			preg_match_all('/<status>(.*?)<\/status>/', $strXmlReport, $arrXml);
			
			//$objXmlReport = @simplexml_load_string('<status>' . $arrXml[1][0] . '</status>');  // PREVIOUS VERSION.
			$objXmlReport = @simplexml_load_string($strXmlReport);
			
			/**
			 * Fetch all possible XML errors.
			 */
			foreach(libxml_get_errors() as $error) {
				echo '<br />XML&nbsp;ERROR:&nbsp;', $error->message, '<br />';
				unset($error);
			}
			
			return $this->objectToArrayLowerCase($objXmlReport);
		}
		
		/**
		 * Retrieve customer's data from the database.
		 * 
		 * @param  int $intMerchantId
		 * @param  int $intCustomerId
		 * @return array
		 */
		protected function getTraderInfo($intMerchantId, $intCustomerId)
		{
			$arrSql = [
				'table' => 'data_reg',
				'where' => [
					'merchant_id' => $intMerchantId,
					'trader_id'   => $intCustomerId,
				],
			];
			
			$arrTraderInfo = $this->dal->select($arrSql);
			unset($arrSql);
			return empty($arrTraderInfo[0]) ? [] : $arrTraderInfo[0];
		}
		
		/**
		 * Normalize FxGlobe ActionType to data_stats.type.
		 * 
		 * @param  string $strType
		 * @return string
		 */
		protected function getFxGlobeDataStatsType($strType)
		{
			switch ($strType) {
				case 'position':
					return 'position';
				
				default:
					return $strType;
			}
		}
		
		/**
		 * Normalize FxGlobe ActionType to data_sales.type.
		 * 
		 * @param  string $strType
		 * @return string
		 */
		protected function getFxGlobeDataSalesType($strType)
		{
			switch ($strType) {
				case 'withdrawal':
					return 'withdrawal';
				
				case 'deposit':
					return 'deposit';
				
				default:
					return $strType;
			}
		}
		
		/**
		 * Check if given trader has deposited some amount at the past.
		 * In case of first deposit - set the "FTD" related info for given trader.
		 * 
		 * @param  int $intMerchantId
		 * @param  int $intTraderId
		 * @return bool
		 */
		protected function getFtdForTrader($intMerchantId, $intTraderId)
		{
			$arrSql = [
				'table'   => 'data_sales',
				'orderby' => ['rdate' => 'DESC'],
				'limit'   => ['offset' => 0, 'limit' => 1],
				'where'   => [
					'merchant_id' => $intMerchantId,
					'trader_id'   => $intTraderId,
					'type'        => 'deposit',
				],
			];
			
			$arrDataSale = $this->dal->select($arrSql);
			unset($arrSql);
			
			if (!empty($arrDataSale)) {
				$arrSql = [
					'table' 			=> 'data_reg',
					'ftdamount'         => $arrDataSale[0]['amount'],
					'initialftdtranzid' => $arrDataSale[0]['tranz_id'],
					'initialftddate'    => $arrDataSale[0]['rdate'],
					'where' 			=> [
						'merchant_id' => $intMerchantId,
						'trader_id'   => $intTraderId,
					],
				];
				
				if ($this->dal->update($arrSql)) {
					echo 'New FTD record added to Data_Reg, TraderID: ', $arrDataSale[0]['trader_id'], '<br />';
				}
				
				unset($arrSql);
			}
		}
		
		/**
		 * The "ctag" validation.
		 * 
		 * @param  string $strTag
		 * @return bool
		 */
		protected function ctagValid($strTag = '')
		{
			if (empty($strTag)) {
				return false;
			}
			
			if ('null' == strtolower($strTag)) {
				return false;
			}
			
			$arrTag = explode('-', $strTag);
		    
			return 'a' == substr($arrTag[0], 0, 1) && 'b' == substr($arrTag[1], 0, 1);
		}
		
		/**
		 * Get price, converted to chosen currency.
		 * 
		 * @param  float  $floatPrice
		 * @param  string $strFrom
		 * @return float
		 */
		protected function getUsd($floatPrice = 0, $strFrom = 'USD')
		{
			if ('usd' == strtolower($strFrom)) {
				return $floatPrice;
			} else {
				$arrSql = [
					'select' => ['val' => 'val'],
					'table'  => 'exchange_rates',
					'where'  => ['LOWER(fromCurr)' => strtolower($strFrom)],
				];
				
				$arrVal = $this->dal->select($arrSql);
				unset($arrSql);
				return round(($floatPrice * $arrVal[0]['val']), 2);
			}
		}
		
		/**
		 * Check if the record exists at "data_sales" table.
		 * 
		 * @param  int    $intMerchantId
		 * @param  int    $intTraderId
		 * @param  string $strType
		 * @param  string $strTransactionId
		 * @return bool
		 */
		protected function dataSaleExists($intMerchantId, $intTraderId, $strType, $strTransactionId)
		{
			$arrSql = [
				'table' => 'data_sales',
				'where' => [
					'merchant_id' => $intMerchantId,
					'trader_id'   => $intTraderId,
					'type'        => $strType,
					'tranz_id'    => $strTransactionId,
				],
			];
			
			$intCnt = $this->dal->count($arrSql);
			unset($arrSql);
			return !empty($intCnt);
		}
		
		/**
		 * Get "group_id" by "affiliate_id".
		 * 
		 * @param  int $intAffId
		 * @param  int $intDefaultAffiliateID
		 * @return int
		 */
		protected function getGroupId($intAffId, $intDefaultAffiliateID)
		{
			$arrSql  = [
				'select' => [
					'group_id' => 'group_id',
				], 
				'table' => 'affiliates', 
				'where' => [
					'id'    => $intAffId,
					'valid' => 1,
				],
			];
			
			$arrGroupId = $this->dal->select($arrSql);
			unset($arrSql);
			
			if (empty($arrGroupId)) {
				$arrSql  = [
					'select' => [
						'group_id' => 'group_id',
					], 
					'table' => 'affiliates', 
					'where' => [
						'id'    => $intDefaultAffiliateID,
						'valid' => 1,
					],
				];
				
				$arrGroupId = $this->dal->select($arrSql);
				unset($arrSql);
				return empty($arrGroupId[0]['group_id']) ? 0 : $arrGroupId[0]['group_id'];
				
			} else {
				return $arrGroupId[0]['group_id'];
			}
		}
		
		/**
		 * Get all valid merchants with a set of corresponding API credentials.
		 * 
		 * @param  void
		 * @return array
		 */
		protected function getMerchants()
		{
			$arrSql = [
				'select' => [
					'api.user'     			   => 'user',
					'api.password'   		   => 'password',
					'api.url' 			       => 'url',
					'api.type'			       => 'api_type',
					'mer.name'			       => 'name',
					'mer.id'  			       => 'id',
					'mer.campaignid'           => 'campaignid',
					'mer.defaultAffiliateID'   => 'defaultAffiliateID',
					'mer.lastSaleStatusUpdate' => 'lastSaleStatusUpdate',
				],
				'table' => 'merchants AS mer',
				'where' => ['mer.valid' => 1],
				'join'  => [
					'apiCredentials AS api' => [
						'jointype'  => 'INNER',
						'mer.id'    => 'api.merchant_id',
						'api.valid' => 1,
					],
				],
			];
			
			$arrMerchants = $this->dal->select($arrSql);
			unset($arrSql);
			return $arrMerchants;
		}
		
		/**
		 * Retrieve registration data from FxGlobe API.
		 * 
		 * @param  int $intPage
		 * @return \SimpleXMLElement|bool
		 */
		protected function getTradersDataFromFxGlobe($intPage = 0)
		{
			$strUrl = $this->strApiUrl . '/Registration' 
			        . '?FromDate=' . $this->strScanDateFrom 
					. '&ToDate=' . $this->strScanDateTo;
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strXmlReport = $this->curlPost($strUrl, [], true);  // via GET.
			
			if (strlen($strXmlReport) < 120) {
				echo '<span style="color:red;">';
				var_dump($strXmlReport);
				echo '</span>';
			}
			
			return @simplexml_load_string($strXmlReport);
		}
		
		/**
		 * Retrieve transactions data from FxGlobe API.
		 * 
		 * @param  int $intPage
		 * @return \SimpleXMLElement|bool
		 */
		protected function getTransactionsDataFromFxGlobe($intPage = 0)
		{
			$strUrl = $this->strApiUrl . '/Transaction'
			        . '?FromDate=' . $this->strScanDateFrom 
					. '&ToDate=' . $this->strScanDateTo;
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strXmlReport = $this->curlPost($strUrl, [], true); // via GET.
			
			if (strlen($strXmlReport) < 120) {
				echo '<span style="color:red;">';
				var_dump($strXmlReport);
				echo '</span>';
			}
			
			return @simplexml_load_string($strXmlReport);
		}
		
		/**
		 * Retrieve stats data from FxGlobe API.
		 * 
		 * @param  int $intPage
		 * @return \SimpleXMLElement|bool
		 */
		protected function getStatsDataFromFxGlobe($intPage = 0)
		{
			$strUrl = $this->strApiUrl . '/Stats'
			        . '?FromDate=' . $this->strScanDateFrom 
					. '&ToDate=' . $this->strScanDateTo;
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strXmlReport = $this->curlPost($strUrl, [], true);  // via GET.
			
			if (strlen($strXmlReport) < 120) {
				echo '<span style="color:red;">';
				var_dump($strXmlReport);
				echo '</span>';
			}
			
			return @simplexml_load_string($strXmlReport);
		}
		
		/**
		 * Check if given amount of hours has past.
		 * 
		 * @param  string $strDateTimeGiven
		 * @param  int    $intHours
		 * @return bool
		 */
		protected function isTimeHasPast($strDateTimeGiven, $intHours)
		{
			$arrDateTime   = explode(' ', $strDateTimeGiven);
			$arrDate       = explode('-', $arrDateTime[0]);
			$arrTime       = explode('-', $arrDateTime[1]);
			$dateTimeGiven = new \DateTime($arrDate[0] . '-' . $arrDate[1] . '-' . $arrDate[2]);
			$dateTimeGiven->setTime($arrTime[0], $arrTime[1], $arrTime[2]);
			$dateTimeGiven->modify('+' . $intHours . ' hour' . ($intHours > 1 ? 's' : ''));
			return $this->dateTime > $dateTimeGiven;
		}
		
		/**
		 * Update currency rates.
		 * 
		 * @param  void
		 * @return void
		 */
		protected function updateCurrencyRates()
		{
			$strUrl = $this->strSiteUrl . 'getCurrency.php';
			echo $this->curlPost($strUrl, [], true), '<br />'; // 'true' means "send GET request".
			unset($strUrl);
		}
		
		/**
		 * Normalize FxGlobe TraderStatus to data_reg.type.
		 * 
		 * @param  string $strStatus
		 * @return string
		 */
		protected function getFxGlobeTraderStatus($strStatus)
		{
			switch ($strStatus) {
				case 'Live':
					return 'real';
					
				default:
					return $strStatus;
			}
		}
		
		/**
		 * Normalize FxGlobe TraderStatus to data_reg.type.
		 * 
		 * @param  array $objXmlTrader
		 * @return string
		 */
		protected function getSpotTraderStatus(array $objXmlTrader)
		{
			if (!empty($objXmlTrader['islead'])) {
				return 'lead';
			}
			
			return empty($objXmlTrader['isdemo']) ? 'real' : 'demo';
		}
		
		/**
		 * Normalize FairTrade TraderStatus to data_reg.type.
		 * 
		 * @param  string $strStatus
		 * @return string
		 */
		protected function getFairTradeTraderStatus($strStatus)
		{
			switch ($strStatus) {
				case 'active':
					return 'real';
				default:
					return $strStatus;
			}
		}
		
		/**
		 * Extract the "ctag" contents into an array.
		 * 
		 * @param  string $strBtag
		 * @return array
		 */
		protected function extractCtag($strBtag)
		{
			$strBtag = str_replace('--', '-', $strBtag);
			$arrExp  = explode('-', $strBtag);
			$arrBtag = [];
			
			for ($i = 0; $i < count($arrExp); $i++) {
				$strTag = '';
				
				switch (substr($arrExp[$i], 0, 1)) {
					case 'a':	
						$strTag = 'affiliate_id';	
						break;
					
					case 'b':	
						$strTag = 'banner_id';		
						break;
					
					case 'p':	
						$strTag = 'profile_id';	
						break;
					
					case 'f':	
						$strTag = 'freeParam';		
						break;
					
					case 'c':	
						$strTag = 'country';		
						break;
					
					case 'u':	
						$strTag = 'uid';		
						break;
				}
				
				$arrBtag[$strTag] = substr($arrExp[$i], 1);
			}
			
			$arrBtag['p'] = isset($arrBtag['p']) ? $arrBtag['p'] : '';
			$arrBtag['f'] = isset($arrBtag['f']) ? $arrBtag['f'] : '';
			$arrBtag['c'] = isset($arrBtag['c']) ? $arrBtag['c'] : '';
			$arrBtag['u'] = isset($arrBtag['u']) ? $arrBtag['u'] : '';
			
			return $arrBtag;
		}
		
		/**
		 * Override dynamic parameter.
		 * 
		 * @param  string $strFreeTextParam
		 * @return string
		 */
		protected function overrideDynamicParameter($strFreeTextParam)
		{
			if (isset($_GET['comment']) && !empty($_GET['comment'])) {
				return $_GET['comment'];
			} else {
				return $strFreeTextParam;
			}
		}
		
		/**
		 * Dynamic tracker.
		 * 
		 * @param  int    $intAffiliateId
		 * @param  string $strUid
		 * @return string|null
		 */
		protected function getOverrideDynamicTracker($intAffiliateId = 0, $strUid = '')
		{
			if (!empty($intAffiliateId) && !empty($strUid)) {
				$arrSql = [
					'select' => ['DynamicTracker' => 'DynamicTracker'],
					'table'  => 'TrackerConversion',
					'where'  => [
						'uid'          => $strUid,
						'affiliate_id' => $intAffiliateId,
					],
				];
				
				$arrTracker = $this->dal->select($arrSql);
				return empty($arrTracker) ? null : $arrTracker[0]['DynamicTracker'];
				
			} else {
				return null;
			}
		}
		
		/**
		 * Check if given "data_stats" record exists.
		 * 
		 * @param  int    $intMerchantId
		 * @param  int    $intTraderId
		 * @param  string $strType
		 * @param  string $strTransactionId
		 * @return bool
		 */
		protected function dataStatExists($intMerchantId, $intTraderId, $strType, $strTransactionId)
		{
			$arrSql = [
				'table' => 'data_stats',
				'where' => [
					'merchant_id' => $intMerchantId,
					'trader_id'   => $intTraderId,
					'type'        => $strType,
					'tranz_id'    => $strTransactionId,
				],
			];
			
			$intCnt = $this->dal->count($arrSql);
			unset($arrSql);
			return !empty($intCnt);
		}
		
		/**
		 * Insert data into the "data_reg" table.
		 *
		 * @param  array $arrData
		 * @param  bool  $boolDebug
		 * @return bool
		 */
		protected function insertIntoDataReg(array $arrData, $boolDebug = false)
		{
			$arrSql = ['table' => 'data_reg'];
			
			if (isset($arrData['campaign_id']) && !empty($arrData['campaign_id'])) {
				$arrSql['campaign_id'] = $arrData['campaign_id'];
			}
			
			if (isset($arrData['ctag']) && !empty($arrData['ctag'])) {
				$arrSql['ctag'] = $arrData['ctag'];
			}
			
			if (isset($arrData['affiliate_id']) && !empty($arrData['affiliate_id'])) {
				$arrSql['affiliate_id'] = $arrData['affiliate_id'];
			}
			
			if (isset($arrData['rdate']) && !empty($arrData['rdate'])) {
				$arrSql['rdate'] = $arrData['rdate'];
			}
			
			if (isset($arrData['group_id']) && !empty($arrData['group_id'])) {
				$arrSql['group_id'] = $arrData['group_id'];
			}
			
			if (isset($arrData['banner_id']) && !empty($arrData['banner_id'])) {
				$arrSql['banner_id'] = $arrData['banner_id'];
			}
			
			if (isset($arrData['profile_id']) && !empty($arrData['profile_id'])) {
				$arrSql['profile_id'] = $arrData['profile_id'];
			}
			
			if (isset($arrData['market_id']) && !empty($arrData['market_id'])) {
				$arrSql['market_id'] = $arrData['market_id'];
			}
			
			if (isset($arrData['country']) && !empty($arrData['country'])) {
				$arrSql['country'] = $arrData['country'];
			}
			
			if (isset($arrData['trader_id']) && !empty($arrData['trader_id'])) {
				$arrSql['trader_id'] = $arrData['trader_id'];
			}
			
			if (isset($arrData['trader_alias'])) {
				$arrSql['trader_alias'] = $arrData['trader_alias'];
			}
			
			if (isset($arrData['type']) && !empty($arrData['type'])) {
				$arrSql['type'] = $arrData['type'];
			}
			
			if (isset($arrData['freeParam']) && !empty($arrData['freeParam'])) {
				$arrSql['freeParam'] = $arrData['freeParam'];
			}
			
			if (isset($arrData['merchant_id']) && !empty($arrData['merchant_id'])) {
				$arrSql['merchant_id'] = $arrData['merchant_id'];
			}
			
			if (isset($arrData['status'])) {
				$arrSql['status'] = $arrData['status'];
			}
			
			if (isset($arrData['lastUpdate']) && !empty($arrData['lastUpdate'])) {
				$arrSql['lastUpdate'] = $arrData['lastUpdate'];
			}
			
			if (isset($arrData['platform'])) {
				$arrSql['platform'] = $arrData['platform'];
			}
			
			if (isset($arrData['uid']) && !empty($arrData['uid'])) {
				$arrSql['uid'] = $arrData['uid'];
			}
			
			if (isset($arrData['saleStatus'])) {
				$arrSql['saleStatus'] = $arrData['saleStatus'];
			}
			
			if (isset($arrData['initialftddate'])) {
				$arrSql['initialftddate'] = $arrData['initialftddate'];
			}
			
			if (isset($arrData['initialftdtranzid'])) {
				$arrSql['initialftdtranzid'] = $arrData['initialftdtranzid'];
			}
			
			if (isset($arrData['ftdamount']) && !empty($arrData['ftdamount'])) {
				$arrSql['ftdamount'] = $arrData['ftdamount'];
			}
			
			if (
				isset($arrData['email']) && 
				!empty($arrData['email']) && 
				false !== filter_var($arrData['email'], FILTER_VALIDATE_EMAIL)
			) {
				$arrSql['email'] = $arrData['email'];
			}
			
			if (isset($arrData['couponName'])) {
				$arrSql['couponName'] = $arrData['couponName'];
			}
			
			$boolRetVal = $this->dal->insertSingle($arrSql, $boolDebug);
			
			if ($boolDebug) {
				echo '<hr>', $boolRetVal, '<hr>';
			}
			
			return true === $boolRetVal ?: false;
		}
		
		/**
		 * Insert data into the "data_sales" table.
		 *
		 * @param  array $arrData
		 * @param  bool  $boolDebug
		 * @return bool
		 */
		protected function insertIntoDataSales(array $arrData, $boolDebug = false)
		{
			$arrSql = ['table' => 'data_sales'];
			
			if (isset($arrData['campaign_id']) && !empty($arrData['campaign_id'])) {
				$arrSql['campaign_id'] = $arrData['campaign_id'];
			}
			
			if (isset($arrData['rdate']) && !empty($arrData['rdate'])) {
				$arrSql['rdate'] = $arrData['rdate'];
			}
			
			if (isset($arrData['affiliate_id']) && !empty($arrData['affiliate_id'])) {
				$arrSql['affiliate_id'] = $arrData['affiliate_id'];
			}
			
			if (isset($arrData['currency']) && !empty($arrData['currency'])) {
				$arrSql['currency'] = $arrData['currency'];
			}
			
			if (isset($arrData['ctag']) && !empty($arrData['ctag'])) {
				$arrSql['ctag'] = $arrData['ctag'];
			}
			
			if (isset($arrData['group_id']) && !empty($arrData['group_id'])) {
				$arrSql['group_id'] = $arrData['group_id'];
			}
			
			if (isset($arrData['banner_id']) && !empty($arrData['banner_id'])) {
				$arrSql['banner_id'] = $arrData['banner_id'];
			}
			
			if (isset($arrData['profile_id']) && !empty($arrData['profile_id'])) {
				$arrSql['profile_id'] = $arrData['profile_id'];
			}
			
			if (isset($arrData['market_id']) && !empty($arrData['market_id'])) {
				$arrSql['market_id'] = $arrData['market_id'];
			}
			
			if (isset($arrData['country']) && !empty($arrData['country'])) {
				$arrSql['country'] = $arrData['country'];
			}
			
			if (isset($arrData['trader_id']) && !empty($arrData['trader_id'])) {
				$arrSql['trader_id'] = $arrData['trader_id'];
			}
			
			if (isset($arrData['tranz_id']) && !empty($arrData['tranz_id'])) {
				$arrSql['tranz_id'] = $arrData['tranz_id'];
			}
			
			if (
				isset($arrData['type']) && !empty($arrData['type']) && 
				in_array($arrData['type'], ['deposit', 'positions', 'revenue', 'bonus', 'withdrawal', 'volume', 'chargeback'])
			) {
				$arrSql['type'] = $arrData['type'];
			}
			
			if (isset($arrData['trader_alias'])) {
				$arrSql['trader_alias'] = $arrData['trader_alias'];
			}
			
			if (isset($arrData['merchant_id']) && !empty($arrData['merchant_id'])) {
				$arrSql['merchant_id'] = $arrData['merchant_id'];
			}
			
			if (isset($arrData['freeParam']) && !empty($arrData['freeParam'])) {
				$arrSql['freeParam'] = $arrData['freeParam'];
			}
			
			if (isset($arrData['amount'])) {
				$arrSql['amount'] = $arrData['amount'];
			}
			
			if (isset($arrData['uid']) && !empty($arrData['uid'])) {
				$arrSql['uid'] = $arrData['uid'];
			}
			
			$boolRetVal = $this->dal->insertSingle($arrSql, $boolDebug);
			
			if ($boolDebug) {
				echo '<hr>', $boolRetVal, '<hr>';
			}
			
			return true === $boolRetVal ?: false;
		}
		
		/**
		 * Insert data into the "data_stats" table.
		 *
		 * @param  array $arrData
		 * @param  bool  $boolDebug
		 * @return bool
		 */
		protected function insertIntoDataStats(array $arrData, $boolDebug = false)
		{
			$arrSql = ['table' => 'data_stats'];
			
			if (isset($arrData['campaign_id']) && !empty($arrData['campaign_id'])) {
				$arrSql['campaign_id'] = $arrData['campaign_id'];
			}
			
			if (isset($arrData['rdate']) && !empty($arrData['rdate'])) {
				$arrSql['rdate'] = $arrData['rdate'];
			}
			
			if (isset($arrData['affiliate_id']) && !empty($arrData['affiliate_id'])) {
				$arrSql['affiliate_id'] = $arrData['affiliate_id'];
			}
			
			if (isset($arrData['ctag']) && !empty($arrData['ctag'])) {
				$arrSql['ctag'] = $arrData['ctag'];
			}
			
			if (isset($arrData['group_id']) && !empty($arrData['group_id'])) {
				$arrSql['group_id'] = $arrData['group_id'];
			}
			
			if (isset($arrData['banner_id']) && !empty($arrData['banner_id'])) {
				$arrSql['banner_id'] = $arrData['banner_id'];
			}
			
			if (isset($arrData['profile_id']) && !empty($arrData['profile_id'])) {
				$arrSql['profile_id'] = $arrData['profile_id'];
			}
			
			if (isset($arrData['market_id']) && !empty($arrData['market_id'])) {
				$arrSql['market_id'] = $arrData['market_id'];
			}
			
			if (isset($arrData['country']) && !empty($arrData['country'])) {
				$arrSql['country'] = $arrData['country'];
			}
			
			if (isset($arrData['trader_id']) && !empty($arrData['trader_id'])) {
				$arrSql['trader_id'] = $arrData['trader_id'];
			}
			
			if (isset($arrData['tranz_id']) && !empty($arrData['tranz_id'])) {
				$arrSql['tranz_id'] = $arrData['tranz_id'];
			}
			
			if (isset($arrData['spread'])) {
				$arrSql['spread'] = $arrData['spread'];
			}
			
			if (isset($arrData['pnl'])) {
				$arrSql['pnl'] = $arrData['pnl'];
			}
			
			if (isset($arrData['turnover'])) {
				$arrSql['turnover'] = $arrData['turnover'];
			}
			
			$arrTypes = [
				'bets', 'wins', 'jackpot', 'bonuses', 'removed_bonuses', 'bonuses_count', 'removed_bonuses_count', 
				'Player_loss', 'Player_profit', 'Gross_income', 'Redeemed_bonuses', 'House_correction_revenue', 
				'House_correction_loss', 'sportsbookcancelledbets', 'static', 'sport_bonuses', 'position',
			];
			
			if (
				isset($arrData['type']) && !empty($arrData['type']) 
				&& in_array($arrData['type'], $arrTypes)
			) {
				$arrSql['type'] = $arrData['type'];
			}
			
			if (isset($arrData['trader_alias'])) {
				$arrSql['trader_alias'] = $arrData['trader_alias'];
			}
			
			if (isset($arrData['merchant_id']) && !empty($arrData['merchant_id'])) {
				$arrSql['merchant_id'] = $arrData['merchant_id'];
			}
			
			if (isset($arrData['freeParam']) && !empty($arrData['freeParam'])) {
				$arrSql['freeParam'] = $arrData['freeParam'];
			}
			
			if (isset($arrData['amount'])) {
				$arrSql['amount'] = $arrData['amount'];
			}
			
			if (isset($arrData['uid']) && !empty($arrData['uid'])) {
				$arrSql['uid'] = $arrData['uid'];
			}
			
			$boolRetVal = $this->dal->insertSingle($arrSql, $boolDebug);
			
			if ($boolDebug) {
				echo '<hr>', $boolRetVal, '<hr>';
			}
			
			return true === $boolRetVal ?: false;
		}
		
		/**
		 * Insert data into the "cron_logs" table.
		 *
		 * @param  array $arrData
		 * @param  bool  $boolDebug
		 * @return bool
		 */
		protected function insertIntoCronLogs(array $arrData, $boolDebug = false)
		{
			$arrSql = ['table' => 'cron_logs'];
			
			if (isset($arrData['type']) && !empty($arrData['type'])) {
				$arrSql['type'] = $arrData['type'];
			}
			
			if (isset($arrData['lastscan']) && !empty($arrData['lastscan'])) {
				$arrSql['lastscan'] = $arrData['lastscan'];
			}
			
			if (isset($arrData['month']) && !empty($arrData['month'])) {
				$arrSql['month'] = $arrData['month'];
			}
			
			if (isset($arrData['year']) && !empty($arrData['year'])) {
				$arrSql['year'] = $arrData['year'];
			}
			
			if (isset($arrData['merchant_id']) && !empty($arrData['merchant_id'])) {
				$arrSql['merchant_id'] = $arrData['merchant_id'];
			}
			
			if (isset($arrData['merchant_name'])) {
				$arrSql['merchant_name'] = $arrData['merchant_name'];
			}
			
			if (isset($arrData['success'])) {
				$arrSql['success'] = $arrData['success'];
			}
			
			if (isset($arrData['reg_total'])) {
				$arrSql['reg_total'] = $arrData['reg_total'];
			}
			
			if (isset($arrData['sales_total'])) {
				$arrSql['sales_total'] = $arrData['sales_total'];
			}
			
			$boolRetVal = $this->dal->insertSingle($arrSql, $boolDebug);
			
			if ($boolDebug) {
				echo '<hr>', $boolRetVal, '<hr>';
			}
			
			return true === $boolRetVal ?: false;
		}
		
		/**
		 * Extracts date-time from "FairTrade" "Rdate".
		 * 
		 * @param  string $strDate
		 * @return string
		 */
		protected function extractDateFromFairTradeRdate($strDate)
		{
			$strRdate        = '';
			$firstBraceIndex = strpos($strDate, '(');
			$lastBraceIndex  = strpos($strDate, ')');
			
			if (false === $firstBraceIndex || false === $lastBraceIndex) {
				return $strDate;
			} else {
				$strStamp      = substr($strDate, ($firstBraceIndex + 1), ($lastBraceIndex - 1 - $firstBraceIndex));
				$dateTimeRdate = new \DateTime();
				return $dateTimeRdate->setTimestamp($strStamp)->format('Y-m-d H:i:s');
			}
			
			/*$strRdate        = '';
			$firstBraceIndex = strpos($strDate, '(');
			$lastBraceIndex  = strpos($strDate, ')');
			
			if (false === $firstBraceIndex || false === $lastBraceIndex) {
				return $strDate;
			} else {
				$strStamp = substr($strDate, ($firstBraceIndex + 1), ($lastBraceIndex - 1 - $firstBraceIndex));
				$arrStamp = explode('+', $strStamp);
				
				if (is_numeric($arrStamp[0])) {
					$dateTimeRdate = new \DateTime();
					$strRdate      = $dateTimeRdate->setTimestamp($arrStamp[0])->format('Y-m-d H:i:s');
					unset($dateTimeRdate, $strStamp, $arrStamp);
				} else {
					return $strDate;
				}
			}
			
			return $strRdate;*/
		}
		
		/**
		 * Get registration data from "FairTrade".
		 * 
		 * @param  int $campaign
		 * @param  int $page
		 * @return array
		 */
		protected function getRegistrationDataFromFairTrade($campaign = 0, $page = 0)
		{
			$strUrl = 'http://stagingwsab.fairtradex.com/AffiliateService.svc/'
			        . 'Affiliate_Buddies_Registration'
					. '?Btag=qwertyu2131iasdfghjk'
					. '&UserName=AB_user_1'
					. '&Password=ABFTDX2014';
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strJsonReport = $this->curlPost($strUrl, [], true);
			$arrJsonReport = json_decode($strJsonReport, true);
			
			if (is_array($arrJsonReport)) {
				return $arrJsonReport;
			} else {
				echo '<br />', ($this->isJsonErrorOccurred() ? '' : 'No JSON errors found, check your (AffiliateBuddies) source code.'), '<br />';
				return [];
			}
		}
		
		/**
		 * Get sales data from "FairTrade".
		 * 
		 * @param  int $campaign
		 * @param  int $page
		 * @return array
		 */
		protected function getSalesDataFromFairTrade($campaign = 0, $page = 0)
		{
			$strUrl = 'http://stagingwsab.fairtradex.com/AffiliateService.svc/'
			        . 'Affiliate_Buddies_Sale'
					. '?Btag=qwertyu2131iasdfghjk'
					. '&UserName=AB_user_1'
					. '&Password=ABFTDX2014';
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strJsonReport = $this->curlPost($strUrl, [], true);
			$arrJsonReport = json_decode($strJsonReport, true);
			
			if (is_array($arrJsonReport)) {
				return $arrJsonReport;
			} else {
				echo '<br />', ($this->isJsonErrorOccurred() ? '' : 'No JSON errors found, check your (AffiliateBuddies) source code.'), '<br />';
				return [];
			}
		}
		
		/**
		 * Update the "pixel_monitor" table.
		 * 
		 * @param  int   $intId
		 * @return bool
		 */
		protected function updatePixelMonitor($intId)
		{
			if (!empty($intId) && is_numeric($intId)) {
				$strSql = 'UPDATE `pixel_monitor` 
						   SET `totalFired` = totalFired + 1 
						   WHERE `id` = ' . $this->dal->paramsMapInsert($intId) . ';';
				
				$boolRetVal = $this->dal->query($strSql);
				return true === $boolRetVal ?: false;
			} else {
				return false;
			}
		}
		
		/**
		 * Override ctag by campaign (Spot API).
		 * 
		 * @param  array  $arrAffCampaignsRelations
		 * @param  int    $intDefaultAffId
		 * @param  string $strOriginalCtag
		 * @param  int    $campaign
		 * @return string
		 */
		protected function overrideCtag(array $arrAffCampaignsRelations, $intDefaultAffId, $strOriginalCtag = '', $intCampaign = 0)
		{
			$strCtag = '';
			
			if (isset($arrAffCampaignsRelations[$intCampaign])) {
				$strCtag .= 'a' . $arrAffCampaignsRelations[$intCampaign];
				
				if ($this->ctagValid($strOriginalCtag)) {
					$arrOriginalCtag  = explode('-', $strOriginalCtag);
					$strCtag         .= implode('-', array_slice($arrOriginalCtag, 1));
				} else {
					$strCtag .= '-b-p';
				}
				
			} else {
				$strCtag .= $this->ctagValid($strOriginalCtag) ? $strOriginalCtag : 'a' . $intDefaultAffId . '-b-p'; 
			}
			
			return $strCtag;
		}
		
		/**
		 * Gets campaign ids, that related to specific affiliate.
		 * 
		 * @param  int   $intMerchantId
		 * @param  array $arrCampaignsToExclude
		 * @return array
		 */
		protected function getAffCampaignsRelations($intMerchantId, array $arrCampaignsToExclude = [])
		{
			$arrSql = [
				'table'  => 'affiliates_campaigns_relations',
				'select' => [
					'DISTINCT campID' => 'campID', 
					'affiliateID'     => 'affiliateID',
				],
				'where'  => [
					'merchantid'  => $intMerchantId,
					'affiliateID' => [
						'operator' => '>', 
						'value'    => 0,
					],
				],
			];
			
			$arrRetVal    = [];
			$arrCampaigns = $this->dal->select($arrSql);
			
			foreach ($arrCampaigns as $arrCamp) {
				if (!in_array($arrCamp['campID'], $arrCampaignsToExclude)) {
					$arrRetVal[] = [
						$arrCamp['campID'] => $arrCamp['affiliateID'],
					];
				}
				unset($arrCamp);
			}
			
			unset($arrCampaigns, $arrSql);
			return $arrRetVal;
		}
		
		/**
		 * Retrieve registration data from TF API.
		 * 
		 * @param  int $intPage
		 * @return array
		 */
		protected function getTradersDataFromTf($intPage = 0)
		{
			$strUrl = $this->strApiUrl . '/marketeer/customer/findAccounts'
			        . '?affiliateUserName=' . $this->strApiUser 
					. '&affiliatePassword=' . $this->strApiPass 
					. '&fromDate=' . $this->strScanDateFrom
					. '&toDate=' . $this->strScanDateTo
					. '&pageIndex=' . $intPage;
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strJsonReport = $this->curlPost($strUrl);
			$arrJsonReport = json_decode($strJsonReport, true);
			
			if (is_array($arrJsonReport)) {
				return $arrJsonReport;
			} else {
				echo '<br />', ($this->isJsonErrorOccurred() ? '' : 'No JSON errors found, check your (AffiliateBuddies) source code.'), '<br />';
				return [];
			}
		}
		
		/**
		 * Retrieve sales data from TF API.
		 * 
		 * @param  int $intPage
		 * @return array
		 */
		protected function getSalesDataFromTf($intPage = 0)
		{
			$strUrl = $this->strApiUrl . '/marketeer/banking/deposit' 
			        . '?affiliateUserName=' . $this->strApiUser 
					. '&affiliatePassword=' . $this->strApiPass 
					. '&fromDate=' . $this->strScanDateFrom
					. '&toDate=' . $this->strScanDateTo
					. '&pageIndex=' . $intPage;
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strJsonReport = $this->curlPost($strUrl);
			$arrJsonReport = json_decode($strJsonReport, true);
			
			if (is_array($arrJsonReport)) {
				return $arrJsonReport;
			} else {
				echo '<br />', ($this->isJsonErrorOccurred() ? '' : 'No JSON errors found, check your (AffiliateBuddies) source code.'), '<br />';
				return [];
			}
		}
		
		/**
		 * Retrieve stats data from TF API.
		 * 
		 * @param  int $intPage
		 * @return array
		 */
		protected function getStatsDataFromTf($intPage = 0)
		{
			$strUrl = $this->strApiUrl . '/marketeer/trading/getTrades' 
			        . '?affiliateUserName=' . $this->strApiUser 
					. '&affiliatePassword=' . $this->strApiPass 
					. '&fromDate=' . $this->strScanDateFrom
					. '&toDate=' . $this->strScanDateTo
					. '&pageIndex=' . $intPage;
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$strJsonReport = $this->curlPost($strUrl);
			$arrJsonReport = json_decode($strJsonReport, true);
			
			if (is_array($arrJsonReport)) {
				return $arrJsonReport;
			} else {
				echo '<br />', ($this->isJsonErrorOccurred() ? '' : 'No JSON errors found, check your (AffiliateBuddies) source code.'), '<br />';
				return [];
			}
		}
		
		//
	}
	
	