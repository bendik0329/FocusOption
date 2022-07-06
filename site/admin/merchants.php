<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix : "/admin/";
if (!isAdmin())
    _goto($lout);

$appTable = 'merchants';

// var_dump($_POST);
// die();

switch ($act) {
    case "valid":

        $db = dbGet($id, $appTable);

        if ($db['valid'] == 0 || $db['valid'] == 'off')
            $valid = '1';
        else
            $valid = '0';
        updateUnit($appTable, "valid='" . $valid . "'", "id='" . $db['id'] . "'");

        if ($valid !== $db['valid'] && $db['isSelfManaged'] == 1) {
            sendTemplate('MerchantAccountReview', $id, 0, '', 0, 0);
        }

        echo '<a onclick="ajax(\'' . $set->SSLprefix . $set->basepage . '?act=valid&id=' . $db['id'] . '\',\'merchant_' . $db['id'] . '\');" style="cursor: pointer;">' . xvPic($valid) . '</a>';
        die();
        break;

    case "relateaffiliates":


        //	$affiliatesQ = function_mysql_query('SELECT id,merchants FROM affiliates WHERE valid=1 AND id NOT IN (SELECT DISTINCT affiliate_id FROM affiliates_deals WHERE merchant_id='.$id.')',__FILE__);
        $affiliatesQ = function_mysql_query('SELECT id,merchants FROM affiliates WHERE valid=1', __FILE__);
        //$defaultCPA = mysql_result(function_mysql_query('SELECT cpa_amount FROM merchants WHERE id='.$id,__FILE__),0,0);
        while ($row = mysql_fetch_assoc($affiliatesQ)) {
            $mr = ($row['merchants'] != '' ? '|' : '') . $row['merchants'] . ($row['merchants'] != '' ? '|' : '');
            // |29| , |2|10|23|3|5|16|19|4|6|13|17|7|11|22|18|1|9|20|12|21|
            if (strpos($mr, '|' . $id . '|') === false) {
                // if($mr!=''){
                // $mr.='|';
                // }
                $mr .= $id;

                $mr = ltrim($mr, '|');
                function_mysql_query('UPDATE affiliates SET merchants="' . $mr . '" WHERE id=' . $row['id'], __FILE__);
                //echo 'UPDATE affiliates SET merchants="'.$mr.'" WHERE id='.$row['id'];
                //die();
            } else {
                
            }


            //function_mysql_query('INSERT INTO affiliates_deals (rdate,admin_id,merchant_id,affiliate_id,dealType,amount) VALUES (NOW(),1,'.$id.','.$row['id'].',2,"'.$defaultCPA.'")',__FILE__);
            //die('affiliateid: ' . $row['id']);
        }

        //echo '<a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$db['id'].'\',\'merchant_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
        die();
        break;

    case "unrelateaffiliates":

        $affiliatesQ = function_mysql_query('SELECT id,merchants FROM affiliates WHERE valid=1', __FILE__);

        while ($row = mysql_fetch_assoc($affiliatesQ)) {

            $mr = $row['merchants'];

            if (strpos('|' . $mr . '|', '|' . $id . '|') === false) {
                
            } else {

                $mr2 = '';
                $mr = explode('|', $mr);

                for ($i = 0; $i < count($mr); $i++) {
                    //die($mr[$i]);
                    if ($mr[$i] != $id) {
                        if ($mr2 != '') {
                            $mr2 .= '|';
                        }
                        $mr2 .= $mr[$i];
                    }
                }
                $mr = trim($mr2, '|');
            }


            function_mysql_query('UPDATE affiliates SET merchants="' . $mr . '" WHERE id=' . $row['id'], __FILE__);
            //echo 'UPDATE affiliates SET merchants="'.$mr.'" WHERE id='.$row['id'];
            //die();
            //function_mysql_query('INSERT INTO affiliates_deals (rdate,admin_id,merchant_id,affiliate_id,dealType,amount) VALUES (NOW(),1,'.$id.','.$row['id'].',2,"'.$defaultCPA.'")',__FILE__);
            //die();
        }
        //	die('affiliateid: ' . $row['id']);
        //echo 'delete from  `affiliates_deals` where merchant_id='.$id;
        //delete from `affiliates_deals` where merchant_id= $merchantID
        die();
        break;

    case "add":
        if (isset($db['id']) && !empty(isset($db['id']))) {
            $sql = "select valid from merchants where id = " . $db['id'];
            $merchant = mysql_fetch_assoc(function_mysql_query($sql, __FILE__));
            $merchant_valid = $merchant['valid'];
        }
        if (!$db['name'])
            $errors['name'] = 1;
        if (!$db['website'])
            $errors['website'] = 1;
        if (!$db['params'])
            $errors['params'] = 1;
        if (empty($errors)) {
            $db[rdate] = dbDate();

            if ($set->userInfo['userType'] != "default" && $set->userInfo['level'] == 'admin') {

                if ($valid == 1 || $db['valid'])
                    $db['valid'] = 1;
                else if ($valid == -1 || $db['valid'] == -1)
                    $db['valid'] = -1;
                else
                    $db['valid'] = 0;
            }
            else {
                unset($db['valid']);
            }

            $current_merchant_id = dbAdd($db, $appTable);

            if (!empty($current_merchant_id) && !empty($db['sub_com_level'])) {
                $db['id'] = $current_merchant_id;

                foreach ($db['sub_com_level'] as $level => $amount) {

                    $merchant_sub_com_level = mysql_fetch_assoc(function_mysql_query("select * from merchants_affiliate_level where affiliate_id = 0 AND level = " . (int) $level . " AND merchant_id = " . $db['id'] . " ORDER BY rdate DESC LIMIT 1", __FILE__));

                    if ($merchant_sub_com_level === false || $merchant_sub_com_level['amount'] != $amount ) {
                        $level_item = [
                            'admin_id' => $set->userInfo['id'],
                            'merchant_id' => $db['id'],
                            'affiliate_id' => 0,
                            'level' => $level,
                            'amount' => $amount,
                            'rdate' => date('Y-m-d H:i:s'),
                        ];
                        dbAdd($level_item, 'merchants_affiliate_level');
                    }
                }
            }


            if ($campaignispartofparams)
                $db['campaignispartofparams'] = 1;
            else
                $db['campaignispartofparams'] = 0;
            if ($toAutoRelateCampToAff)
                $db['toAutoRelateCampToAff'] = 1;
            else
                $db['toAutoRelateCampToAff'] = 0;
            if ($showLeadsNdemo)
                $db['showLeadsNdemo'] = 1;
            else
                $db['showLeadsNdemo'] = 0;
            if ($fetchMailsFromAPI)
                $db['fetchMailsFromAPI'] = 1;
            else
                $db['fetchMailsFromAPI'] = 0;
            if ($fetchPhonesFromAPI)
                $db['fetchPhonesFromAPI'] = 1;
            else
                $db['fetchPhonesFromAPI'] = 0;

            if (!isset($db['isSelfManaged']))
                $db['isSelfManaged'] = 0;


            dbAdd($db, $appTable);

            if (isset($db['id'])) {
                if ($db[valid] !== $merchant_valid && $db['isSelfManaged'] == 1) {
                    sendTemplate('MerchantAccountReview', $db['id'], 0, '', 0, 0);
                }
            }

            _goto($set->SSLprefix . $set->basepage);
        }

    case "new":

        if ($id) {

            $set->content = '<style>		
			aside {
					width: 20%;
					background-color: #f1f1f1;
					position:fixed;
					height:100vh;
			
			}
			ul.vertical {
			list-style-type: none;
			margin: 0;
			padding: 0;
			width: 20%;
			background-color: #f1f1f1;
			position:fixed;
			height:65vh;
			overflow:auto;
			min-width:80px;
			display:block;
		}

		ul.vertical li a {
			display: block;
			color: #000;
			padding: 8px 0 8px 16px;
			text-decoration: none;
		}

		ul.vertical li a:hover:not(.active) {
			background-color: #555;
			color:white;
		}

		ul.vertical a.active {
			background-color: #555;
			color:white;
		}	
		
		ul.vertical li.heading_active {
			padding: 8px 0 8px 5px;
			text-decoration: none;
			color:white;
			height: 32px;
		}
		ul.vertical  li input#config_filter
		{
			    width: 101px;
				/*margin-right: 5px;
				margin-left: 5px;
				margin-top: 10px;
				margin-bottom: 10px;*/
		}
		ul.vertical  li input#btnFilter
		{
			padding-left:0px !Important;
			padding-right:0px !important;
			width:100px !important;
		}
		.main {
			margin-left:20%;
			padding:1px 16px;
			height:100%;
		}
		
		.filter{
			padding-top:10px;
			padding-right:2px;
		}
		
		@media screen and (max-width: 290px) {
			.main{
			   margin-left:35%;
			}
		}
		</style>
		<script>
		$(document).ready(function(){
			$("ul.vertical li a").on("click",function(){
				var tabtoopen = $(this).data("tab");
				action = $("form#frmMain").attr("action");
				
				if(typeof action === "undefined"){
					action = window.location.href;
					
				}
				
				act = action.split(/[?#]/)[0];
				act = act + "?tab=" + tabtoopen;
				$("form#frmMain").attr("action", act);
				
				 $(this).toggleClass("active").parent().siblings().find("a").removeClass("active");
				show_hide_tabs(tabtoopen,"li");
			});
			
			
			
			
			function show_hide_tabs(open_tab,type){
				$(".config_tabs").hide();
				if(type=="search"){
					if(open_tab == "all"){
						$(".config_tabs").each(function(k,tab){
							$(this).show();
						});
					}
					else{
						$(".config_tabs").hide();
						$(".config_tabs").each(function(k,tab){
							var search_txt = $(tab).find("div.normalTableTitle").text();
							
							search_txt = search_txt.toLowerCase();
							open_tab = open_tab.toLowerCase();
							
							if(search_txt.search(open_tab)!==-1){
								$(this).show();
							}
							else{
								$(this).hide();
							}
						});
					}
				}
				else{
					if(open_tab == "all"){
						$(".config_tabs").each(function(k,tab){
							$(this).show();
						});
					}
					else{
					$(".config_tabs").each(function(k,tab){
						if($(tab).data("tab") == open_tab){
							$(this).show();
						}
						else{
							//console.log("out")
						}
					});
					}
					
				}
			}

			$("#config_filter").on("keyup",function(){
				var txtVal = $(this).val();
				if (txtVal != "") {
					$(".tblDetails").show();
					$.each($(".tblDetails"), function (i, o) {
						var match = $("tbody td:contains-ci(\'" + txtVal + "\'),thead td:contains-ci(\'" + txtVal + "\')", this);
						match.parent().siblings().hide();                         //  <<=== [LINE ADD]
						
						if (match.length > 0) {
							$(match).parent("tr").show();
							$(this).parent().prev().show();
							
							txt = $(this).parent().prev().data("tab2");
							$("ul.vertical li a").each(function(){
								console.log(txt + " ---- "+$(this).data("tab"));
									if(txt == $(this).data("tab")){
										$(this).css("color","grey");
									}
								});
						}
						else {
							$(this).hide();
							$(this).parent().prev().hide();
							
							txt = $(this).parent().prev().data("tab2");
							$("ul.vertical li a").each(function(){
								
									if(txt == $(this).data("tab")){
										$(this).css("color","black");
									}
							});
						}
					});
				} else {
					// When there is no input or clean again, show everything back
					$(".tblDetails").parent().prev().show();
					$(".tblDetails,.tblDetails tr").show();
					$(".config_tabs").show();
					$("ul.vertical li a").css("color","black");
				}
				if($(".tblDetails:visible").length == 0)
				{
					$(".config_tabs:first").find(".message").remove();
					$(".config_tabs:first").append("<p class=\"message\">' . lang('Sorry No results found!') . '</p>");
					$(".btnsave").hide();
				}
				else{
					$(".message").remove();
					$(".btnsave").show();
				}
			});


			
			// jQuery expression for case-insensitive filter
			$.extend($.expr[":"], {
				"contains-ci": function (elem, i, match, array) {
					return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
				}
			});
			
			$("#filter_form").submit(function(){
				var tab_text = $("#config_filter").val();
				show_hide_tabs(tab_text,"search");
				return false;
			});
			
		});
		</script>
		<aside>
		<ul class="vertical">
  <li class="heading_active"><span style="font-size:14px;float:left;width:95px;padding-top:8px;">' . lang('Sections') . '</span><form onsubmit="return false;" style="display:inline-flex;float:right;">
  <div class="filter">' . lang('Find') . ': </div><div style="margin-right:10px;"><input type="text" name="config_filter" id="config_filter"></form></div></li>
  <li><a href="javascript:void(0)" data-tab="all" class="active">' . lang('All') . '</a></li>
  <li><a href="javascript:void(0)" data-tab="merchant_details">' . lang('Merchant Details') . '</a></li>
  <li><a href="javascript:void(0)" data-tab="technical_configuration">' . lang('Technical Configuration') . '</a></li>
  <li><a href="javascript:void(0)" data-tab="default_commission">' . lang('Default Commission') . '</a></li>
  </ul></aside>
  <div class="main">';
        }


        if ($id) {
            $db = dbGet($id, $appTable);
            $pageTitle = lang('Editing') . ' ' . $db['name'];
        } else {
            //_goto($set->basepage.'?act=list');
            $pageTitle = lang('ADD NEW MERCHANT');
        }
        $set->breadcrumb_title = lang($pageTitle);
        $set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="' . $set->SSLprefix . 'admin/">' . lang('Dashboard') . '</a></li>
				<li><a href="' . $set->SSLprefix . 'admin/merchants.php">' . lang('Merchants') . '</a></li>
				<li><a href="' . $set->SSLprefix . $set->uri . '">' . lang($pageTitle) . '</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
        for ($i = 1; $i <= 5; $i++) {
            $listContacts .= '<tr>
							<td align="center">' . $i . '</td>
							<td><input type="text" name="db[name_' . $i . ']" value="' . $db['name_' . $i] . '" /></td>
							<td><input type="text" name="db[mail_' . $i . ']" value="' . $db['mail_' . $i] . '" /></td>
							<td><input type="text" name="db[phone_' . $i . ']" value="' . $db['phone_' . $i] . '" /></td>
						</tr><tr>
							<td colspan="4" height="10"></td>
						</tr>';
        }

        $qry = "SELECT COLUMN_NAME AS name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . $ss->db_name . "' AND TABLE_NAME = 'producttitles' and not column_name ='id' and not column_name ='source' order by column_name";
        $productListQ = function_mysql_query($qry, __FILE__);

        $productListStr = '';
        while ($row = mysql_fetch_assoc($productListQ)) {
            $productListStr .= '<option value="' . $row['name'] . '" ' . ($db['producttype'] == $row['name'] ? 'selected' : '') . '>' . $row['name'] . '</option>';
        }


        $apiTypesOptions = "";
        $apiTypes = array('internal' => 'Internal', 'spot' => 'SpotOption', 'progressplay' => 'ProgressPlay', 'tech' => 'TechFinancial', 'airsoft' => 'AirSoft', 'panda' => 'Panda', 'fxoro' => 'FXoro', 'generaltrade' => 'GeneralTrade', 'tradesmarter' => 'TradeSmarter', 'octagon' => 'Octagon', 'fxglobe' => 'FXglobe', 'finantick' => 'Finantick', 'zoho' => 'Zoho', 'fxtt' => 'FXTT', 'postback' => 'POSTBACK');
        asort($apiTypes);
        // var_dump($apiTypes);
        // die();
        foreach ($apiTypes as $key => $apiType) {

            // var_dump($apiTypes);
            // var_dump($db['apiType']);
            // die();

            $apiTypesOptions .= '<option value="' . $key . '" ' . (strtolower($db['apiType']) == strtolower($key) ? 'selected' : '') . '>' . $apiType . '</option>';
        }


        if ($db['valid'] == "1" || $valid == "1")
            $selectedvalid = "selected";
        else
            $selectedvalid = "";


        if ($db['valid'] == "0" || $valid == "0")
            $selectedinvalid = "selected";
        else
            $selectedinvalid = "";

        if ($db['valid'] == "-2" || $valid == "-2")
            $selectedRejectedvalid = "selected";
        else
            $selectedRejectedvalid = "";

        if ($db['valid'] == "-1" || $valid == "-1")
            $selectedHiddenvalid = "selected";
        else
            $selectedHiddenvalid = "";

        $set->content .= '<style>		
		
		 /* The switch - the box around the slider */
			.switch {
			  position: relative;
			  display: inline-block;
			  /*width: 60px;*/
			  width: 48px;
			  height: 25px;
			}

			/* Hide default HTML checkbox */
			.switch input {display:none;}

			/* The slider */
			.slider {
			  position: absolute;
			  cursor: pointer;
			  top: 0;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  background-color: #ccc;
			  -webkit-transition: .4s;
			  transition: .4s;
			  height: 20px;
			 width: 43px;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 12px;
			  width: 12px;
			  left: 3px;
			  bottom: 4px;
			  background-color: white;
			  -webkit-transition: .4s;
			  transition: .4s;
			}

			input:checked + .slider {
			  background-color: #2196F3;
			}

			input:focus + .slider {
			  box-shadow: 0 0 1px #2196F3;
			}

			input:checked + .slider:before {
			  -webkit-transform: translateX(26px);
			  -ms-transform: translateX(26px);
			  transform: translateX(26px);
			}

			/* Rounded sliders */
			.slider.round {
			  border-radius: 34px;
			}

			.slider.round:before {
			  border-radius: 50%;
			}
			</style>';

        $merchant_sub_aff_levels = function_mysql_query("SELECT lv1.*  FROM merchants_affiliate_level lv1 INNER JOIN ( SELECT max(rdate) MaxDate, level FROM merchants_affiliate_level WHERE affiliate_id = 0 AND merchant_id = " . $db['id'] . " GROUP BY level ) lv2 ON lv1.level = lv2.level AND lv1.rdate = lv2.MaxDate WHERE lv1.affiliate_id = 0 AND lv1.merchant_id = " . $db['id'] . " order by lv1.rdate desc", __FILE__);

        $merchant_sub_aff_levels_array = [];
        while ($row_level = mysql_fetch_assoc($merchant_sub_aff_levels)) {
            $merchant_sub_aff_levels_array[$row_level['level']] = $row_level['amount'];
        }

        $sub_com_level_lines = [];
        if ($set->sub_com_level > 0) {
            for ($sub_com_line = 1; $sub_com_line <= $set->sub_com_level; $sub_com_line++) {
                $sub_com_level_lines[] = '<tr><td width="100">Level ' . $sub_com_line . '</td><td align="left">% <input type="text" name="db[sub_com_level][' . $sub_com_line . ']" value="' . (!empty($merchant_sub_aff_levels_array[$sub_com_line]) ? $merchant_sub_aff_levels_array[$sub_com_line] : 0) . '" style="width: 100px;" /> </td></tr>';
            }
        }

        $set->content .= '<form action="' . $set->SSLprefix . $set->basepage . '?act=add" method="post">
						<input type="hidden" name="db[id]" value="' . $db['id'] . '" />
						<div id="merchant_details" data-tab="merchant_details" class="config_tabs">
						<div class="normalTableTitle" data-tab2="merchant_details">' . lang('Merchant Details') . '</div>
						<div align="center" style="width: 100%; background: #EFEFEF;">
						<table width="98%" border="0" cellpadding="0" cellspacing="0" class="tblDetails">
							<tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" ' . err('name') . '>' . lang('Merchant Name') . ': <span class="required">*</span></td>
								<td align="left"><input type="text" name="db[name]" value="' . $db['name'] . '" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" ' . err('website') . '>' . lang('Website') . ': <span class="required">*</span></td>
								<td align="left"><input type="text" name="db[website]" value="' . ($db['website'] ? $db['website'] : 'http://') . '" / style="width:250px;"></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" ' . err('producttype') . '>' . lang('Product Type') . ':</td>
								<td align="left">
								<!--<input type="text" name="db[pos]" value="' . $db['pos'] . '" style="width: 250px;" />-->
								<select name="db[producttype]">
									<option value=-1>' . lang('Choose Product Type') . '</option>
									' . $productListStr . '
								</select>
								</td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
							<td align="left" width="185" ' . err('pos') . '>' . lang('Position') . ':</td>
								<td align="left"><input type="text" name="db[pos]" value="' . $db['pos'] . '" style="width: 250px;" /></td>
							</tr><tr>
							<td align="left" width="185" ' . err('subbrandof') . '>' . lang('Sub Brand Of') . ':</td>
								<td align="left"><input type="text" name="db[subbrandof]" value="' . $db['subbrandof'] . '" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" ' . err('email') . '>' . lang('E-Mail') . ':</td>
								<td align="left"><input type="text" name="db[email]" value="' . $db['email'] . '" style="width: 250px;" /></td>
								<td colspan="2" height="10"></td>
							</tr>
							<tr>
								<td colspan="2" height="10"></td>
							</tr>'
                . ($set->userInfo['userType'] != "default" ? '<tr>
								<td align="left">' . lang('Merchant Status') . '</td></td>
								<td align="left"><select  name="valid">
											<option value="1" ' . $selectedvalid . '>' . lang('Active') . '</option>
											<option value="0" ' . $selectedinvalid . '>' . lang('Inactive') . '</option>
											<option value="-1" ' . $selectedHiddenvalid . '>' . lang('Hidden') . '</option>
											<option value="-2" ' . $selectedRejectedvalid . '>' . lang('Rejected') . '</option>
											</select></td>
							</tr>' : '') . '
							<tr>
								<td colspan="2" height="10"></td>
							</tr>
							<tr>
							
								<td align="left" width="185">' . lang('Is Self Managed?') . '</td>
								<td align="left"><div style="float:left;"><label class="switch"><input type="checkbox" name="db[isSelfManaged]" value=1 ' . ($db['isSelfManaged'] ? 'checked="checked"' : '') . ' /><div class="slider round"></div></label></div></td>
								<td colspan="2" height="10"></td>
							</tr>
							
							<tr>
								<td colspan="2" height="10"></td>
							</tr>
							</table>
							</div></div>
							<div id="technical_configuration" data-tab="technical_configuration" class="config_tabs">
							<div class="normalTableTitle" data-tab2 ="technical_configuration">' . lang('Technical Configuration') . '</div>
							<div align="center" style="width: 100%; background: #EFEFEF;">
							<table width="98%" border="0" cellpadding="0" cellspacing="0" class="tblDetails"><tbody>
							<tr>
								<td colspan="2" height="10"></td>
							</tr>
							<tr>
							
								<td align="left" width="185" ' . err('showLeadsNdemo') . '>' . lang('Show Leads and demo fields') . ':</td>
								<td align="left"><input type="checkbox" name="showLeadsNdemo" ' . ($db['showLeadsNdemo'] ? 'checked="checked"' : '') . ' />
							</tr><tr>
							<td align="left" width="185" ' . err('fetchMailsFromAPI') . '>' . lang('Fetch Mails From API') . ':</td>
								<td align="left"><input type="checkbox" name="fetchMailsFromAPI" ' . ($db['fetchMailsFromAPI'] ? 'checked="checked"' : '') . ' />
							</tr><tr>
							<td align="left" width="185" ' . err('fetchPhonesFromAPI') . '>' . lang('Fetch Phones From API') . ':</td>
								<td align="left"><input type="checkbox" name="fetchPhonesFromAPI" ' . ($db['fetchPhonesFromAPI'] ? 'checked="checked"' : '') . ' />
							</tr><tr>
								
								<td align="left" width="185" ' . err('wallet_id') . '>' . lang('Wallet ID') . ':</td>
								<td align="left"><input type="text" name="db[wallet_id]" value="' . $db['wallet_id'] . '" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" ' . err('params') . '>' . lang('Outgoing Tracking Parameter Name') . ':</td>
								<td align="left"><input type="text" name="db[params]" value="' . $db['params'] . '" style="width: 250px;" /></td></tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" ' . err('incomingParam') . '>' . lang('Incoming Tracker Parameter Name') . ':</td>
								<td align="left"><input type="text" name="db[incomingParam]" value="' . $db['incomingParam'] . '" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
							<td align="left" width="185" ' . err('incomingParamAlternative') . '>' . lang('Incoming Tracker Parameter Name Alternative') . ':</td>
								<td align="left"><input type="text" name="db[incomingParamAlternative]" value="' . $db['incomingParamAlternative'] . '" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" ' . err('campaignid') . '>' . lang('Default Campaign ID') . ':</td>
								<td align="left"><input type="text" name="db[campaignid]" value="' . $db['campaignid'] . '" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" ' . err('defaultAffiliateID') . '>' . lang('Default Affiliate ID') . ':</td>
								<td align="left"><input type="text" name="db[defaultAffiliateID]" value="' . $db['defaultAffiliateID'] . '" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" ' . err('campaignparamname') . '>' . lang('Campaign Parameter Name') . ':</td>
								<td align="left"><input type="text" name="db[campaignparamname]" value="' . $db['campaignparamname'] . '" style="width: 250px;" /></td>
							</tr><tr>
							<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" ' . err('campaignispartofparams') . '>' . lang('Is CampaignID parameter should be part of the Btag Parameters?') . ':</td>
								<td align="left"><input type="checkbox" name="campaignispartofparams" ' . ($db['campaignispartofparams'] ? 'checked="checked"' : '') . ' />
							
												
								
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr>
							<!--tr>
								<td align="left" width="185" ' . err('') . '>' . lang('Logo URL') . ':</td>
								<td align="left"><input type="text" name="db[LogoURL]" value="' . $db['LogoURL'] . '" style="width: 250px;" /></td>
								
							</tr-->
							<tr>
								<td colspan="2" height="10"></td>
							</tr>';
        if ($set->userInfo['id'] == "1" || ($set->userInfo['level'] == 'admin' && $set->userInfo['userType'] != 'default' )) {
            $set->content .= '
							<tr>
								<!--td align="left" width="185" ' . err('StylingURL') . '>' . lang('Styling URL') . ':</td>
								<td align="left"><input type="text" name="db[StylingURL]" value="' . $db['StylingURL'] . '" style="width: 250px;" /></td-->
								
								</tr><tr>
								<td colspan="2" height="20"></td>
						
						
							</tr>
						
								';

            $set->content .= '
								<tr>
								
								
							<td align="left" width="185" ' . err('apiURL') . '>' . lang('apiURL') . ':</td>
								<td align="left"><input type="text" name="db[APIurl]" value="' . $db['APIurl'] . '" style="width: 250px;" /></td>
								</tr><tr>
								<td align="left" width="185" ' . err('APIuser') . '>' . lang('APIuser') . ':</td>
								<td align="left"><input type="text" name="db[APIuser]" value="' . $db['APIuser'] . '" style="width: 250px;" /></td>
								</tr><tr>
								<td align="left" width="185" ' . err('APIpass') . '>' . lang('APIpass') . ':</td>
								<td align="left"><input type="password" name="db[APIpass]" value="' . $db['APIpass'] . '" style="width: 250px;" /></td>
								</tr><tr>
								<td align="left" width="185" ' . err('apiToken') . '>' . lang('APItoken') . ':</td>
								<td align="left"><input type="text" name="db[apiToken]" value="' . $db['apiToken'] . '" style="width: 250px;" /></td>
								</tr><tr>

								<td align="left" width="185" ' . err('api_token2') . '>' . lang('api_token2') . ':</td>
								<td align="left"><input type="text" name="db[api_token2]" value="' . $db['api_token2'] . '" style="width: 250px;" /></td>
								</tr><tr>
								
								<td align="left" width="185" ' . err('apiType') . '>' . lang('API Type') . ':</td>
								<td>
								<select name="db[apiType]" onchange="checkapitype(this)">
									<option value=-1>' . lang('Choose Integration Type') . '</option>
									' . $apiTypesOptions . '
								</select>
								</td>
								
								
								<!--/tr><tr>
								<td align="left" width="185" ' . err('apiType') . '>' . lang('apiType') . ':</td>
								<td align="left"><input type="text" name="db[apiType]" value="' . $db['apiType'] . '" style="width: 250px;" /></td-->
								</tr><tr>
								<td align="left" width="185" ' . err('cronjoburl') . '>' . lang('CronJob URL') . ':</td>
								<td align="left"><input type="text" name="db[cronjoburl]" value="' . $db['cronjoburl'] . '" style="width: 250px;" /></td>
								</tr><tr style="display:none" class="postback">
								<td align="left" width="185" ' . err('postbackIPlimit') . '>' . lang('Postback Integration IP Whitelist') . ':</td>
								<td align="left"><input type="text" name="db[postbackIPlimit]" value="' . $db['postbackIPlimit'] . '" style="width: 250px;" /></td>
								</tr><tr style="display:none" class="postback">
								<td align="left" width="185" ' . err('postbacktkn') . '>' . lang('PostBack Token') . ':</td>
								<td align="left"><input type="text" readonly value="' . (!empty($db['randomKey']) ? $db['id'] . '-' . $db['randomKey'] . '-' . md5($db['randomKey']) : '') . '" style="width: 250px;" /></td>
								</tr>
								<tr style="display:none" class="progressplay">
								<td align="left" width="185" ' . err('progressplaywli') . '>' . lang('ProgressPlay WhiteLabel ID') . ':</td>
								<td align="left"><input type="text"  name="db[API_whiteLabelId]" value="' . (!empty($db['API_whiteLabelId']) ? ($db['API_whiteLabelId']) : '') . '" style="width: 250px;" /></td>
								</tr>
								<tr>
							<tr>
								<td colspan="2" height="10"></td>
							</tr>
							
							';
        }

        $set->content .= '<tr>
								<td colspan="2" height="30"></td>
							</tr>
							<tr>
								<td colspan="2"><div>* ' . lang('An extra parameter for the landing url. Use campID or affID as value (optional):') . '</div></td>
							</tr>
							<tr>
								<td colspan="2" height="5"></td>
							</tr>
							<tr id="empn">
								<td align="left" width="185" ' . err('memberParameter') . '>' . lang('Member Parameter Name') . ':</td>
								<td align="left"><input id="empnt" type="text" name="db[extraMemberParamName]" value="' . $db['extraMemberParamName'] . '" style="width: 250px;" /></td>
								
							</tr>';

        $set->content .= '<tr>
									<td colspan="2" height="10"></td>
								</tr><tr id="empv" style="' . (($db['extraMemberParamName'] != '' AND $db['extraMemberParamName'] != NULL) ? '' : 'display:none') . '">
								<td align="left" width="185" ' . err('memberParameterV') . '>' . lang('Member Parameter Value') . ':</td>
								<td align="left"><input id="empvt" type="text" name="db[extraMemberParamValue]" value="' . $db['extraMemberParamValue'] . '" style="width: 250px;" /></td>
	</tr><tr>
	<td colspan="3" height="10"></td>							
							</tr><tr>
								<td align="left" width="185" ' . err('toAutoRelateCampToAff') . '>' . lang('Auto Relate Campaign To Affiliate On Registration') . ':</td>
								<td align="left"><input type="checkbox" name="toAutoRelateCampToAff" ' . ($db['toAutoRelateCampToAff'] ? 'checked="checked"' : '') . ' />
								
								
							</tr><tr>
								<td colspan="3" height="10"></td>
							</tr>
							
							
							<script type="text/javascript">
								$(document).ready(function(){
									$("#empn").keyup(function(e){
										
										//console.log($("#empnt").val().length);
										
										if($("#empnt").val().length>0){
											$("#empv").show();
										}else{
											$("#empv").hide();
										}
									});
								});
								function checkapitype(e){
									var val = $(e).val();
									if(val == "postback")
									{
										$(".postback").show();
										$(".progressplay").hide();
									}
									else if(val == "progressplay")
									{
										$(".progressplay").show();
										$(".postback").hide();
									}
									else{
										$(".postback").hide();
										$(".progressplay").hide();
									}
								}
							</script>
							
							';



        $set->content .= '<tr>
								<td colspan="2" height="10"></td>
							</tr></tbody>
							</div>
						</table>
						</div></div>
					<!--	<div id="actions" data-tab="actions" class="config_tabs">
						<div class="normalTableTitle" data-tab2="actions">' . lang('Actions') . '</div>
						<div style="width: 100%; background: #EFEFEF;">
						<table width="98%" border="0" cellpadding="0" cellspacing="0" bgcolor="#EFEFEF" class="tblDetails">
							<tbody><tr>
								<td colspan="3" height="10"></td>
							</tr><tr>
								<td width="30" align="left"><input type="checkbox" name="valid" ' . ($db['valid'] ? 'checked' : '') . ' />
								<td align="left" colspan="2">' . lang('Publish') . '</td></td>
							</tr><tr>
								<td colspan="3" height="10"></td>
							</tr></tbody>
						</table>
						</div></div>-->
						<div id="default_commission" data-tab="default_commission" class="config_tabs">
						<div class="normalTableTitle" data-tab2 ="default_commission">' . lang('Default Commissions') . '</div>
						<div style="width: 100%; background: #EFEFEF;" align="center">
                                                <table width="98%" valign="top">
                                                <tr>
                                                <td width="50%" valign="top">
                                                    <table width="98%" border="0" cellpadding="0" cellspacing="0" bgcolor="#EFEFEF" class="tblDetails">
                                                    <tbody>
                                                            <tr>
                                                                    <td colspan="2" height="10"></td>
                                                            </tr>
                                                            <tr>
                                                                    <td align="left">' . lang(ptitle('Minimum Deposit', ucwords($db['producttype']))) . '</td>
                                                                    <td align="left">' . $set->currency . '  <input type="text" name="db[min_cpa_amount]" value="' . $db['min_cpa_amount'] . '" style="text-align: center;" /></td>
                                                            </tr>
                                                            <tr>
                                                                    <td colspan="2" height="5"></td>
                                                            </tr>
                                                            <tr>
                                                                    <td align="left">' . lang('CPA') . '</td>
                                                                    <td align="left">' . $set->currency . '  <input type="text" name="db[cpa_amount]" value="' . $db['cpa_amount'] . '" style="text-align: center;" /></td>
                                                            </tr>
                                                            <tr>
                                                                    <td colspan="2" height="5"></td>
                                                            </tr>
                                                            <tr>
                                                                    <td align="left">' . lang('DCPA') . '</td>
                                                                    <td align="left">% <input type="text" name="db[dcpa_amount]" value="' . $db['dcpa_amount'] . '" style="text-align: center;" /></td>
                                                            </tr>
                                                            <tr>
                                                                    <td colspan="2" height="5"></td>
                                                            </tr>
                                                            <tr>
                                                                    <td align="left">' . lang('NetDeposit') . '</td>
                                                                    <td align="left">% <input type="text" name="db[revenue_amount]" value="' . $db['revenue_amount'] . '" style="text-align: center;" /></td>
                                                            </tr>
                                                            <tr>
                                                                    <td colspan="2" height="5"></td>
                                                            </tr>
                                                            <tr>
                                                                    <td align="left">' . lang('PNL') . '</td>
                                                                    <td align="left">% <input type="text" name="db[pnl_amount]" value="' . $db['pnl_amount'] . '" style="text-align: center;" /></td>
                                                            </tr>
                                                            <tr>
                                                                    <td colspan="2" height="5"></td>
                                                            </tr>
                                                            <tr>
                                                                    <td align="left">' . lang('Positions Rev. Share') . '</td>
                                                                    <td align="left">% <input type="text" name="db[positions_rev_share]" value="' . $db['positions_rev_share'] . '" style="text-align: center;" /></td>
                                                            </tr>
                                                            <tr>
                                                            <td colspan="2" height="5"></td>
                                                            </tr>' . ($set->deal_cpl ? '<tr>
                                                                    <td align="left">' . lang('CPL') . '</td>
                                                                    <td align="left">' . $set->currency . '  <input type="text" name="db[cpl_amount]" value="' . $db['cpl_amount'] . '" style="text-align: center;" /></td>
                                                            </tr><tr>
                                                                    <td colspan="2" height="5"></td>
                                                            </tr>' : '') . ($set->deal_cpi ? '<tr>
                                                                    <td align="left">' . lang('CPI') . '</td>
                                                                    <td align="left">' . $set->currency . '  <input type="text" name="db[cpi_amount]" value="' . $db['cpi_amount'] . '" style="text-align: center;" /></td>
                                                            </tr><tr>
                                                                    <td colspan="2" height="5"></td>
                                                            </tr>' : '') . ($set->deal_cpc ? '<tr>
                                                                    <td align="left">' . lang('CPC') . '</td>
                                                                    <td align="left">' . $set->currency . '  <input type="text" name="db[cpc_amount]" value="' . $db['cpc_amount'] . '" style="text-align: center;" /></td>
                                                            </tr><tr>
                                                                    <td colspan="2" height="5"></td>
                                                            </tr>' : '') . ($set->deal_cpm ? '<tr>
                                                                    <td align="left">' . lang('CPM') . '</td>
                                                                    <td align="left">' . $set->currency . '  <input type="text" name="db[cpm_amount]" value="' . $db['cpm_amount'] . '" style="text-align: center;" /></td>
                                                            </tr>' : '') . ($set->hidePendingProcessHighAmountDeposit ? '<tr>
                                                                    <td align="left">' . lang('Lowest amount for pending deposits') . '</td>
                                                                    <td align="left">' . $set->currency . '  <input type="text" name="db[lowestAmountPendingDeposit]" value="' . $db['lowestAmountPendingDeposit'] . '" style="text-align: center;" /></td>
                                                            </tr>' : '') . '<tr>
                                                                    <td colspan="3" height="10"></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td valign="top">'.CpaByCountry($db).'</td>
                                                </tr>
                                                </table>
                            <div class="normalTableTitle">' . lang('Sub Affiliate Comissions') . '(Max Level Depth: ' . $set->sub_com_level . ')</div>
                            <div style="width: 100%; background: #EFEFEF;" align="left">
                                <table border="0" cellpadding="3" cellspacing="3" bgcolor="#EFEFEF">
                                    <tbody>' . implode(' ', $sub_com_level_lines) . '</tbody>
                                </table>
                            </div>   
							
							<div class="normalTableTitle">' . lang('Revenue Calculation') . '</div>
							<div style="width: 100%; background: #EFEFEF;" align="center">
							<table width="98%" border="0" cellpadding="0" cellspacing="0" bgcolor="#EFEFEF"><tbody>
								<tr>
									<td><br></td>
									</tr>
								<tr>
										<td align="left"><input type="text" name="db[rev_formula]" value="' . $db['rev_formula'] . '" style="width: 350px;" /><br /><span style="font-size: 11px;"><b>' . lang('Variables') . ':</b> {deposits} / {bonus} / {withdrawals} / {chargebacks} / {static}</span></td>
									</tr><tr>
									<td><br></td>
									</tr>
						</tbody>
						</table>
						</div>
						<div class="normalTableTitle">' . lang('Qualify Commission') . '</div>
							<div style="width: 100%; background: #EFEFEF; padding-bottom: 20px;" align="center">
						<table width="98%" border="0" cellpadding="0" cellspacing="0" bgcolor="#EFEFEF"><tbody>
						<tr>
									<td><br></td>
									</tr>
									<tr>
										<td align="left">
						' . getQualifications($db['qualify_type'], 1) . '

											<input type="text" name="db[qualify_amount]" value="' . $db['qualify_amount'] . '" style="width: 100px; text-align: center;" maxlength="5" /></td>
									<td>&nbsp;</td>
										<td align="left">
                                                                                    ' . lang('Max Trader monthly PNL') . ': $<input type="text" name="db[max_pnl_monthly_amount]" value="' . $db['max_pnl_monthly_amount'] . '" style="width: 100px; text-align: center;" /><br><br>
                                                                                    ' . lang('Max Affiliate monthly PNL') . ': $<input type="text" name="db[max_pnl_monthly_amount_affiliate]" value="' . $db['max_pnl_monthly_amount_affiliate'] . '" style="width: 100px; text-align: center;" />
                                                                                
                                                                                </td>
									</tr>
									
								</tbody>
						</table>
                                                </div>
						</div></div>
						<div class="btnsave" align="center" style="width: 1000px;padding:10px 0"><input type="submit" value="' . lang('Save') . '" /></div>
						</form>';

        if ($id) {
            $set->content .= "</div>"; // main div closed
        }
        theme();
        break;

    default:
        $where = "";
        if (!isset($atype)) {
            $where .= " WHERE valid = 1";
        } else if ($atype != "all" && $atype != "") {
            $where .= " WHERE valid = " . $atype;
        }

        $sql = "SELECT * FROM " . $appTable . $where . " ORDER BY pos";

        $qq = function_mysql_query($sql, __FILE__);
        $walletCount = mysql_fetch_assoc(function_mysql_query("SELECT  count(distinct wallet_id) as count FROM " . $appTable, __FILE__));
        while ($ww = mysql_fetch_assoc($qq)) {
            $l++;
            $merchantList .= '<tr ' . ($l % 2 ? 'class="trLine"' : '') . '>
						<td align="center">' . $ww['id'] . '</td>
						<td align="center"><a href="' . $set->SSLprefix . $set->basepage . '?act=new&id=' . $ww['id'] . '">' . lang('Edit') . '</a></td>
						<td align="left">' . $ww['name'] . '</td>
						<td align="center">' . dbDate($ww['rdate']) . '</td>
						<td align="center">' . strtoupper($ww['producttype']) . '</td>
						<td align="left"><a href="' . $ww['website'] . '" target="_blank">' . $ww['website'] . '</a></td>
						<td align="left">% ' . $ww['positions_rev_share'] . '</td>
						<td align="center">' . $set->currency . ' ' . $ww['min_cpa_amount'] . '</td>
						<td align="center">' . $set->currency . ' ' . $ww['cpa_amount'] . '</td>
						<td align="center">% ' . $ww['dcpa_amount'] . '</td>
						<td align="center">% ' . $ww['revenue_amount'] . '</td>
						' . ($set->deal_cpl ? '<td align="center">' . $set->currency . ' ' . $ww['cpl_amount'] . '</td>' : '') . '
						' . ($set->deal_cpc ? '<td align="center">' . $set->currency . ' ' . $ww['cpc_amount'] . '</td>' : '') . '
						' . ($set->deal_cpm ? '<td align="center">' . $set->currency . ' ' . $ww['cpm_amount'] . '</td>' : '') . '
						<td align="center">' . $ww['pos'] . '</td>' .
                    ($walletCount > 1 ? '<td align="center">' . $ww['wallet_id'] . '</td>' : '') . ' 
						<td align="center" id="merchant_' . $ww['id'] . '"><a ' . ($set->userInfo['id'] == "1" || $set->userInfo['userType'] == 'sys' ? ' onclick="ajax(\'' . $set->SSLprefix . $set->basepage . '?act=valid&id=' . $ww['id'] . '\',\'merchant_' . $ww['id'] . '\');" ' : '' ) . ' style="cursor: pointer;">' . xvPic($ww['valid']) . '</a></td>
						<td align="center" id="merchant_' . $ww['id'] . '">
							<a onclick="ajax(\'' . $set->SSLprefix . $set->basepage . '?act=relateaffiliates&id=' . $ww['id'] . '\',\'merchant_' . $ww['id'] . '\'); setTimeout(function(){location.reload();},500);" style="cursor: pointer;">Relate All</a>
							<span>&nbsp;&nbsp;&nbsp;</span>
							<a onclick="var r=confirm(\'Are you sure you want to delete all affiliate relations to this merchant?\'); if(r==true){ajax(\'' . $set->SSLprefix . $set->basepage . '?act=unrelateaffiliates&id=' . $ww['id'] . '\',\'merchant_' . $ww['id'] . '\'); setTimeout(function(){location.reload();},500);}" style="cursor: pointer;">Unrelate All</a>
						</td>
					</tr>';
        }

        if ($set->userInfo['userType'] == "sys" && $set->userInfo['level'] == 'admin') {
            $set->content = '<div class="btn" style="float:left"><a href ="admin/merchants.php?act=new" >' . lang('Add New Merchant') . '</a></div>';
        }

        $set->content .= '<form method="post"><div style="text-align:right;margin-right:200px;margin-bottom:20px;">' . lang('Show') . ': <select name="atype" style="width:150px;" onchange="form.submit();">
					<option value="all" ' . ($atype == 'all' ? 'selected' : '') . '>' . lang('All') . '</option>
					<option value=1 ' . (!isset($atype) ? ' selected' : $atype == 1 ? ' selected' : '') . '>' . lang('Active') . '</option>
					<option value=0 ' . (isset($atype) && $atype == 0 && $atype != 'all' ? 'selected' : '') . '>' . lang('Inactive') . '</option>
					<option value=-1 ' . ($atype == -1 ? 'selected' : '') . '>' . lang('Hidden') . '</option>
					</select></div></form>';
        $set->content .= '<div class="normalTableTitle">' . lang('MERCHANTS LIST') . '</div>
					<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
						<thead>
						<tr>
							<td align="center">#</td>
							<td align="center">' . lang('Actions') . '</td>
							<td style="text-align: left;">' . lang('Merchant Name') . '</td>
							<td align="center">' . lang('Last Update') . '</td>
							<td align="center">' . lang('Type') . '</td>
							<td style="text-align: left;">' . lang('Merchant Website') . '</td>
							<td style="text-align: left;">' . lang(ptitle('Positions Rev. Share')) . '</td>
							<td align="center">' . lang(ptitle('Minimum Deposit')) . '</td>
							<td align="center">' . lang('CPA') . '</td>
							<td align="center">' . lang('DCPA') . '</td>
							<td align="center">' . lang('Revenue Share') . '</td>
							' . ($set->deal_cpl ? '<td align="center">' . lang('CPL') . '</td>' : '') . '
							' . ($set->deal_cpc ? '<td align="center">' . lang('CPC') . '</td>' : '') . '
							' . ($set->deal_cpm ? '<td align="center">' . lang('CPM') . '</td>' : '') . '
							<td align="center">' . lang('Position') . '</td>' .
                ($walletCount > 1 ? '<td align="center">' . lang('Wallet ID') . '</td>' : "") . '
							<td align="center">' . lang('Published') . '</td>
							<td align="center">' . lang('Affiliates Relation') . '</td>
						</tr>
						</thead>
						<tfoot>' . $merchantList . '</tfoot>
					</table>';
        theme();
        break;
}


function CpaByCountry($merchant){
    global $set;
    
    $my_country_list = '';
    $sql_find = 'SELECT * FROM cpa_countries_groups WHERE merchant_id = "' . (int) $merchant['id'] . '"';
    $resource = function_mysql_query($sql_find, __FILE__, __FUNCTION__);
    while($my_country = mysql_fetch_assoc($resource)){
        $my_country_list .= '<tr ref="'.$my_country['id'].'"><td><input type="text" name="my_cg_name" value="'.$my_country['name'].'"/></td><td>'.$set->currency.'<input type="text" name="my_cg_value" value="'.$my_country['value'].'"/></td><td>'.implode(', ',explode('|', $my_country['countries'])).'</td><td><button class="my_cg_update">Update</button> <button class="my_cg_delete">Delete</button></td></tr>';
    }
    
    $r = '<div style="text-align: left;margin: 20px;"><b>'.lang('CPA by Countries group').'</b>';
    
    
    $r .= '
        <script src="/admin/js/country_group.js"></script>
        <div id="addCountriesGroup" width="400">
            <input name="cpabc_mid" type="hidden" class="cpabc_mid" value="'.$merchant['id'].'"> 
            '.lang('Group Name').': <input name="cpabc_name" type="text" class="cpabc_name"> 
            &nbsp;&nbsp;&nbsp;&nbsp;
            '.lang('Default Commission').': '.$set->currency.' <input name="cpabc_name" style="width: 50px;" type="text" class="cpabc_value"><br>
            '.lang('Countries').' ('.lang('Press CTRL for multiple selection').'):<br> 
            <select multiple size="10" name="cpabc_countries" class="cpabc_countries" style="height: auto; vertical-align: top;">
                <option value="">'.lang('Select countries').'</option>
                '. getCountries('') .'
            </select><br><br>
            <button>Add Group</button><br><br>
            <span class="result"></span>
        </div>
        <hr>
        <div id="listCountriesGroup">
            <table class="table table-striped" style="font-size: 14px;">
                <tr style="font-weight: bold;"><td style="font-size: 14px;" width="170">'.lang('Name').'</td><td style="font-size: 14px;" width="170">'.lang('Default Commission').', '.$set->currency.': </td><td style="font-size: 14px;">'.lang('Countries').'</td><td style="font-size: 14px;" width="130">Action</td></tr>
                '.$my_country_list.' 
            </table>
        </div>
        ';
    
    
    
    
    
    $r .= '</div>';
    
    return $r;
}

?>
