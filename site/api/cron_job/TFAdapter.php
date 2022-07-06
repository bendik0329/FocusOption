<?php
	
	require 'CronBase.php';
	
	class TFAdapter extends CronBase
	{
		/**
		 * Constructor.
		 * 
		 * @param array $arrConfig
		 */
		public function __construct(array $arrConfig)
		{
			parent::__construct($arrConfig);
		}
		
		/**
		 * Check sales status (FxGlobe).
		 * 
		 * @param  int $intMerchantId
		 * @param  int $intCampaign
		 * @param  int $intPage
		 * @return void
		 */
		protected function checkSalesStatus($intMerchantId, $intCampaign, $intPage = 0)
		{
			/*$dateTimePast  = new \DateTime();
			$strDateFrom   = $dateTimePast->modify('-2 months')->format('Y-m-d');
			$dateTimeToday = new \DateTime();
			$strDateToday  = $dateTimeToday->format('Y-m-d');
			$strUrl        = $this->strApiUrl . '/Registration' . '?FromDate=' . $strDateFrom . '&ToDate=' . $strDateToday;
			$strXmlReport  = $this->curlPost($strUrl, [], true);  // via GET.
			
			if (strlen($strXmlReport) < 120) {
				echo '<span style="color:red;">';
				var_dump($strXmlReport);
				echo '</span>';
			}
			
			echo '<br /><span style="color:green;">Processing data from: </span>', $strUrl, '<br />';
			$objXmlReport = @simplexml_load_string($strXmlReport);
			
			if (false !== $objXmlReport) {
				echo '--------------------------------------------------------------------------------<br />',
				     '---------&nbsp;UPDATING SaleStatus FOR CAMPAIGN:&nbsp;', $intCampaign, '&nbsp;---------<br />',
					 '----&nbsp;URL:&nbsp;', $strUrl, '&nbsp;----------------------<br />',
					 '--------------------------------------------------------------------------------<br />';
			    
				foreach ($objXmlReport as $objXmlTrader) {
					$strRdate = (string) $objXmlTrader->rdate;
					$arrSql   = [
						'table' => 'data_sales',
						'where' => [
							'merchant_id' => $intMerchantId,
							'trader_id'   => (int) $objXmlTrader->TraderID,
							'type'        => 'deposit',
						],
					];
					
					$intCnt = $this->dal->count($arrSql);
					unset($arrSql);
					
					if (empty($intCnt)) {
						$arrSql = [
							'table'      => 'data_reg',
							'saleStatus' => (string) $objXmlTrader->SalesStatus,
							'where'      => [
								'trader_id'   => (int) $objXmlTrader->TraderID,
								'merchant_id' => $intMerchantId,
							], 
						];
						
						$boolUpdateRetVal = $this->dal->update($arrSql);
						
						if ($boolUpdateRetVal) {
							echo 'TraderID: ', (int) $objXmlTrader->TraderID, 
								 ' | saleStatus: ', (string) $objXmlTrader->SalesStatus, 
								 ' | rdate: ', $strRdate, '<br />';
						}
						
						unset($boolUpdateRetVal, $arrSql);
					}
					
					unset($objXmlTrader, $intCnt);
				}
			}
			
			$arrSql = [
				'table' 			   => 'merchants',
				'lastSaleStatusUpdate' => $strDateToday,
				'where'                => ['id' => $intMerchantId],
			];
			
			$boolRetVal = $this->dal->update($arrSql);
			unset(
				$boolRetVal, $arrSql, $strUrl, $strXmlReport, $objXmlReport, 
				$strDateFrom, $strDateToday, $dateTimeToday, $dateTimePast
			);*/
		}
		
		/**
		 * Run the cron.
		 *
		 * @param  void
		 * @return void
		 */
		public function run()
		{
			$floatSecStartExec = microtime(true);
			
			/**
			 * Each 12 hours currency rates should be updated.
			 */
			$arrSql = [
				'select' => ['MAX(lastUpdate)' => 'last_update'],
				'table'  => 'exchange_rates',
			];
			
			$arrCurrencyUpdate = $this->dal->select($arrSql);
			
			if ($this->isTimeHasPast($arrCurrencyUpdate[0]['last_update'], 12)) {
				$this->updateCurrencyRates();
			}
			
			unset($arrCurrencyUpdate, $arrSql);
			
			
			/**
			 * Retrieve valid merchants and corresponding API credentials.
			 * Loop through valid merchants.
			 */
			$arrMerchants = $this->getMerchants();
			
			foreach ($arrMerchants as $arrMerchant) {
				echo '<b style="color:blue;">Processing merchant:</b>', $arrMerchant['id'], '<br />';
				$this->strApiUser         = $arrMerchant['user'];
				$this->strApiPass         = $arrMerchant['password'];
				$this->strApiUrl          = $arrMerchant['url'];
				$this->strApiWhiteLabel   = $arrMerchant['name'];
				$this->strApiType         = $arrMerchant['api_type'];
				$arrAffCampaignsRelations = $this->getAffCampaignsRelations($arrMerchant['id']);
				$arrCampaigns             = $this->getCampaigns($arrMerchant['id']);
				$intCampaignsCount        = 0;
				$arrCampaigns[]           = [
					'campaign_id'  => $arrMerchant['campaignid'],
					'affiliate_id' => 0,
				];
				
				/**
				 * Loop through campaigns.
				 */
				foreach ($arrCampaigns as $arrCampaign) {
					$campaign = $arrCampaign['campaign_id'];
					
					if (empty($campaign)) {
						continue;
					} else {
						echo '<span style="color:green">Processing campaign: ', $campaign, '</span>';
					}
					
					if ($intCampaignsCount % 4 === 0) {
						sleep(5);
					}
					
					/**
					 * Loop through pages.
					 * Begin.
					 */
					for ($page = 1; $page <= $this->intTotalPages; $page++) {
						
						// Each 8 hours a sales status should be checked.
						if ($this->isTimeHasPast($arrMerchant['lastSaleStatusUpdate'], 8)) {
							$this->checkSalesStatus($arrMerchant['id'], $campaign, $page);
						}
						
						/**
						 * Process registration data.
						 * Begin.
						 */
						echo '<hr><b>Connecting to Customers\'s & Database (Campaign ID: ', $campaign, 
						     ')<span style="color:blue">&nbsp;&nbsp;Page: <u>', $page, '</u>...</span></b><br />';
						
						$objXmlReport = $this->getTradersDataFromTf($page);
						
						////////////////////////////////////////////////////////////////////////////////////////
						exit;///////////////////////////////////////////////////////////////////////////////////
						////////////////////////////////////////////////////////////////////////////////////////
						
						if (false === $objXmlReport) {
							continue;
						} else {
							
							$arrExistingTraders    		= [];
							$arrNewTraders         		= [];
							$arrTradersIds         		= [];
							$arrInsertedTraders    		= [];
							$arrNotInsertedTraders 	    = [];
							$arrExistingTradersWithCtag = [];
							
							foreach ($objXmlReport as $objXmlTrader) {
								// ctagOverride
								$arrTradersIds[] = (int) $objXmlTrader->TraderID;
								unset($objXmlTrader);
							}
							
							// Retrieve existing traders.
							$arrSql = [
								'select' => [
									'trader_id' => 'trader_id',
									'ctag'      => 'ctag',
								],
								'table'  => 'data_reg',
								'where'  => [
									'merchant_id' => $arrMerchant['id'],
									'trader_id'   => [
										'operator' => 'IN', 
										'value'    => $arrTradersIds,
									],
								],
							];
							
							$existingTradersNonNormalized = $this->dal->select($arrSql);
							
							if (is_array($existingTradersNonNormalized)) {
								foreach ($existingTradersNonNormalized as $arrExistingTrader) {
									$arrExistingTraders[] = $arrExistingTrader['trader_id'];
									$arrExistingTradersWithCtag[$arrExistingTrader['trader_id']] = $arrExistingTrader['ctag'];
									unset($arrExistingTrader);
								}
							}
							
							// Check duplications.
							foreach ($arrTradersIds as $intTraderId) {
								if (in_array($intTraderId, $arrExistingTraders)) {
									echo 'Trader ', $intTraderId, ' | CTAG: ', $arrExistingTradersWithCtag[$intTraderId] , 
									     ' | already exists at merchant ', $arrMerchant['id'], '<br />';
										
								} else {
									$arrNewTraders[] = $intTraderId;
								}
								unset($intTraderId);
							}
							
							// Run through XML report, and insert a relevant data.
							foreach ($objXmlReport as $objXmlTrader) {
								if (in_array((int) $objXmlTrader->TraderID, $arrNewTraders)) {
									$strCtag = $this->overrideCtag($arrAffCampaignsRelations, $arrMerchant['defaultAffiliateID'], (string) $objXmlTrader->cTag, $campaign);
									$arrCtag = $this->extractCtag($strCtag);
									$arrData = [
										'merchant_id'  => $arrMerchant['id'],
										'ctag'         => $strCtag,
										'affiliate_id' => $arrCtag['affiliate_id'],
										'uid'          => $arrCtag['uid'],
										'banner_id'    => $arrCtag['banner_id'],
										'profile_id'   => $arrCtag['profile_id'],
										'country'      => $arrCtag['country'],
										'freeParam'    => $this->overrideDynamicParameter($arrCtag['freeParam']),
										'group_id'     => $this->getGroupId($arrCtag['affiliate_id'], $arrMerchant['defaultAffiliateID']),
										'trader_id'    => (int) $objXmlTrader->TraderID,
										'trader_alias' => (string) $objXmlTrader->TraderAlias,
										'email'        => (string) $objXmlTrader->login,
										'type'         => $this->getFxGlobeTraderStatus((string) $objXmlTrader->TraderStatus),
										'rdate'        => (string) $objXmlTrader->rdate,
										'saleStatus'   => (string) $objXmlTrader->SalesStatus,
									];
									
									if (property_exists($objXmlTrader, 'Country') && !empty($objXmlTrader->Country)) {
										$arrData['country'] = (string) $objXmlTrader->Country;
									}
									
									unset($arrCtag, $strCtag);
									
									$strPixel = $this->strSiteUrl . 'pixel.php?act=account&ctag=' . $arrData['ctag'] 
									          . '&merchant_id=' . $arrData['merchant_id'] 
									          . '&trader_id=' . $arrData['trader_id']
									          . '&trader_alias=' . $arrData['trader_alias'];
									
									if ($this->insertIntoDataReg($arrData)) {
										$strUidTmp             = isset($arrData['uid']) ? $arrData['uid'] : '';
										$arrInsertedTraders[]  = (int) $objXmlTrader->TraderID;
										$strDynamicTracker     = $this->getOverrideDynamicTracker($arrData['affiliate_id'], $strUidTmp);
										$strPixel 			  .= (empty($strDynamicTracker) ? '' : '&subid=' . $strDynamicTracker);
										unset($strUidTmp);
										
										echo 'Trader: ', $arrData['trader_id'], ' CTAG: ', $arrData['ctag'], ' - Inserted!<br />';
										
										/**
										 * Fire a pixel.
										 */
										$strPixelResponse = file_get_contents($strPixel);
										echo '<hr>Pixel URL:&nbsp;', $strPixel,
										     '<br />PixelResponse:&nbsp;', $strPixelResponse, '<hr>';
										
									} else {
										$arrNotInsertedTraders[] = (int) $objXmlTrader->TraderID;
										echo 'Trader: ', $arrData['trader_id'], ' CTAG: ', $arrData['ctag'], ' - NOT Inserted!<br />';
									}
									
									unset($arrData);
								}
								
								unset($objXmlTrader);
							}
							
							echo '<b>Following traders were not inserted due to SQL error: </b>', 
							     (empty($arrNotInsertedTraders) ? 'NONE' : implode(' , ', $arrNotInsertedTraders)), '<hr>',
							     '<b>Following traders were successfully inserted: </b>', 
								 (empty($arrInsertedTraders) ? 'NONE' : implode(' , ', $arrInsertedTraders)), '<hr>',
								 '<br /><b>Finished processing Customers\'s & Database (Campaign ID: ', $campaign, 
								 ')<span style="color:blue">&nbsp;&nbsp;Page: <u>', $page, '</u>...</span></b><hr>';
							
							unset($arrSql);
						}
						
						unset($objXmlReport);
						/**
						 * Process registration data.
						 * End.
						 */
						
						
						/**
						 * Process transactions data.
						 * Begin.
						 */
						echo '<hr><b>Connecting to Transaction\'s & Database (Campaign ID: ', $campaign, 
						     ')<span style="color:blue">&nbsp;&nbsp;Page: <u>', $page, '</u>...</span></b><br />';
						
						$objXmlReport = $this->getSalesDataFromTf($page);
						//////////////////////////////////////////////////////////////////////////////////////////////////////
						
						if (false === $objXmlReport) {
							continue;
						} else {
							
							foreach ($objXmlReport->Transaction as $objXmlTransaction) {
								
								/**
								 * Get original "ctag".
								 */
								$arrTraderInfo = $this->getTraderInfo($arrMerchant['id'], (int) $objXmlTransaction->TraderID);
								
								if (!is_array($arrTraderInfo) || !$this->ctagValid($arrTraderInfo['ctag'])) {
									unset($objXmlTransaction);
									echo '<b>TRANSACTIONS</b>&nbsp;<span style="color:red;">Record without an account: tranz_id = ', 
									     (string) $objXmlTransaction->TransactionID , 
									     '   |    trader_id = ', (int) $objXmlTransaction->TraderID, 
										 '   |    type = ', $this->getFxGlobeDataSalesType((string) $objXmlTransaction->ActionType), 
										 '   |    amount = ', (float) $objXmlTransaction->USDAmount, '</span><br />';
									
									continue;
								}
								
								if ($this->dataSaleExists(
									$arrMerchant['id'], 
									(int) $objXmlTransaction->TraderID, 
									$this->getFxGlobeDataSalesType((string) $objXmlTransaction->ActionType), 
									(string) $objXmlTransaction->TransactionID)
								) {
									echo '<br /><b>TRANSACTIONS Record exists!</b>&nbsp;tranz_id = ', (string) $objXmlTransaction->TransactionID, '<br />';
									continue;
								}
								
								$arrCtag = $this->extractCtag($arrTraderInfo['ctag']);
								$arrData = [
									'ctag'         => $arrTraderInfo['ctag'],
									'merchant_id'  => $arrMerchant['id'],
									'affiliate_id' => $arrCtag['affiliate_id'],
									'uid'          => $arrCtag['uid'],
									'banner_id'    => $arrCtag['banner_id'],
									'country'      => $arrCtag['country'],
									'profile_id'   => $arrCtag['profile_id'],
									'freeParam'    => $this->overrideDynamicParameter($arrCtag['freeParam']),
									'group_id'     => $this->getGroupId($arrCtag['affiliate_id'], $arrMerchant['defaultAffiliateID']),
									'trader_id'    => (int) $objXmlTransaction->TraderID,
									'tranz_id'     => (string) $objXmlTransaction->TransactionID,
									'rdate'        => (string) $objXmlTransaction->TransactionDate,
									'type'         => $this->getFxGlobeDataSalesType((string) $objXmlTransaction->ActionType),
									'amount'       => (float) $objXmlTransaction->USDAmount,
									'currency'     => (string) $objXmlTransaction->Coin,
								];
								
								if ($this->insertIntoDataSales($arrData)) {
									$this->getFtdForTrader($arrData['merchant_id'], $arrData['trader_id'], $arrData['amount'], $arrData['tranz_id'], $arrData['rdate']);
									echo '<li> [', $arrData['rdate'], '] ', $arrData['trader_id'], ' (ctag:&nbsp;', $arrData['ctag'], 
										 ') /', $arrData['type'], '&nbsp;Amount:&nbsp;$&nbsp;', $arrData['amount'], 
										 '/ - <b>Inserted</b>!</li><br />';
										
								} else {
									echo '<li> [', $arrData['rdate'], '] ', $arrData['trader_id'], ' (ctag:&nbsp;', $arrData['ctag'], 
										 ') /', $arrData['type'], '&nbsp;Amount:&nbsp;$&nbsp;', $arrData['amount'], 
										 '/ - <span style="color:red;"><b>NOT inserted due to an error</b></span>!</li><br />';
								}
								
								unset($objXmlTransaction, $arrData, $arrCtag);
							}
						}
						
						unset($objXmlReport);
						echo '<hr><b>Finished processing Transaction\'s & Database (Campaign ID: ', $campaign, 
						     ')<span style="color:blue">&nbsp;&nbsp;Page: <u>', $page, '</u>...</span></b><br />';
						/**
						 * Process transactions data.
						 * End.
						 */
						
						
						/**
						 * Process stats data.
						 * Begin.
						 */
						echo '<hr><b>Connecting to Stats\'s & Database (Campaign ID: ', $campaign, 
						     ')<span style="color:blue">&nbsp;&nbsp;Page: <u>', $page, '</u>...</span></b><br />';
						
						$objXmlReport = $this->getStatsDataFromTf($page);
						////////////////////////////////////////////////////////////////////////////////////////////////////
						
						if (false === $objXmlReport) {
							continue;
						} else {
							
							foreach ($objXmlReport->Stats as $objXmlTransaction) {
								
								/**
								 * Get original "ctag".
								 */
								$arrTraderInfo = $this->getTraderInfo($arrMerchant['id'], (int) $objXmlTransaction->TraderID);
								
								if (!is_array($arrTraderInfo) || !$this->ctagValid($arrTraderInfo['ctag'])) {
									unset($objXmlTransaction);
									echo '<b>STATS</b>&nbsp;<span style="color:red;">Record without an account: tranz_id = ', 
									     (string) $objXmlTransaction->StatsID , 
									     '   |    trader_id = ', (int) $objXmlTransaction->TraderID, 
										 '   |    type = ', $this->getFxGlobeDataStatsType((string) $objXmlTransaction->ActionType), 
										 '   |    amount = ', (float) $objXmlTransaction->USDAmount, '</span><br />';
									
									continue;
								}
								
								if ($this->dataStatExists(
									$arrMerchant['id'], 
									(int) $objXmlTransaction->TraderID, 
									$this->getFxGlobeDataStatsType((string) $objXmlTransaction->ActionType), 
									(string) $objXmlTransaction->StatsID)
								) {
									echo '<br /><b>STATS Record exists!</b>&nbsp;', (string) $objXmlTransaction->StatsID, '<br />';
									continue;
								}
								
								$arrCtag = $this->extractCtag($arrTraderInfo['ctag']);
								$arrData = [
									'ctag'         => $arrTraderInfo['ctag'],
									'merchant_id'  => $arrMerchant['id'],
									'affiliate_id' => $arrCtag['affiliate_id'],
									'uid'          => $arrCtag['uid'],
									'banner_id'    => $arrCtag['banner_id'],
									'country'      => $arrCtag['country'],
									'profile_id'   => $arrCtag['profile_id'],
									'freeParam'    => $this->overrideDynamicParameter($arrCtag['freeParam']),
									'group_id'     => $this->getGroupId($arrCtag['affiliate_id'], $arrMerchant['defaultAffiliateID']),
									'trader_id'    => (int) $objXmlTransaction->TraderID,
									'tranz_id'     => (string) $objXmlTransaction->StatsID,
									'rdate'        => (string) $objXmlTransaction->TransactionDate,
									'type'         => $this->getFxGlobeDataStatsType((string) $objXmlTransaction->ActionType),
									'turnover'     => (float) $objXmlTransaction->Turnover,
									'pnl'          => (float) $objXmlTransaction->PNL,
									'spread'       => (float) $objXmlTransaction->Spreads,
									'amount'       => (float) $objXmlTransaction->USDAmount,
								];
								
								if ($this->insertIntoDataStats($arrData)) {
									echo '<li> [', $arrData['rdate'], '] ', $arrData['trader_id'], ' (ctag:&nbsp;', $arrData['ctag'], 
										 ') /', $arrData['type'], '&nbsp;Amount:&nbsp;$&nbsp;', $arrData['amount'], 
										 '/ - <b>Inserted</b>!</li><br />';
										 
								} else {
									echo '<li> [', $arrData['rdate'], '] ', $arrData['trader_id'], ' (ctag:&nbsp;', $arrData['ctag'], 
										 ') /', $arrData['type'], '&nbsp;Amount:&nbsp;$&nbsp;', $arrData['amount'], 
										 '/ - <span style="color:red;"><b>NOT inserted due to an error</b></span>!</li><br />';
								}
								
								unset($objXmlTransaction, $arrData, $arrCtag);
							}
						}
						
						unset($objXmlReport);
						echo '<hr><b>Finished processing Stats\'s & Database (Campaign ID: ', $campaign, 
						     ')<span style="color:blue">&nbsp;&nbsp;Page: <u>', $page, '</u>...</span></b><br />';
						/**
						 * Process stats data.
						 * End.
						 */
						
						echo '<span style="color:blue;">Finished to process page: <b>', $page, '</b>&nbsp;at campaign:&nbsp;', $campaign, '</span><br />';
						break; // FxGlobe does not use "pages". 
					}
					/**
					 * Loop through pages.
					 * End.
					 */
					
					$intCampaignsCount++;
					echo '<span style="color:green">Finished to process campaign: ', $campaign, '</span><br />';
					unset($arrCampaign);
				}
				
				echo '<b style="color:blue;">Finished to process merchant:</b>', $arrMerchant['id'], '<br />';
				unset($arrMerchant, $intCampaignsCount);
			}
			
			
			$floatExecTime = round(microtime(true) - $floatSecStartExec);
			$execTime      = new \DateTime('@' . $floatExecTime);
			echo 'Cron job accomplished.<br />Total time:&nbsp;', $execTime->format('H:i:s'), '&nbsp;(hours:minutes:seconds)<br />';
		}
		
		//
	}
	
	