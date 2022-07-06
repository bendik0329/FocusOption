<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'affiliates_msgs';



switch ($act) {
	/* ------------------------------------ [ Manage Messages ] ------------------------------------ */
	
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	
	case "delete":
		$sql = "delete  from ". $appTable . " where id=" . $id;
		mysql_query($sql);
		_goto($set->SSLprefix.$set->basepage);
		break;
		
	case "add":
		if (!$db['title']) $errors['title'] = 1;
		if (!$db['text']) $errors['text'] = 1;
		if (empty($errors)) {
			$db[rdate] = dbDate();
			$db[valid] = 0;
			$db['admin_id'] = $set->userInfo['id'];
			
			// Send to Affiliates
			if ($sendMail) {
				if ($db['group_id']) { // Send to Group
											$qq=function_mysql_query("SELECT * FROM affiliates af inner join WHERE valid='1' AND group_id='".$db['group_id']."' ORDER BY id ASC",__FILE__);
				} else if ($db['affiliate_id']) {
											$qq=function_mysql_query("SELECT * FROM affiliates WHERE valid='1' AND id='".$db['affiliate_id']."'",__FILE__);
					} else if ($db['status_id']) {
											$qq=function_mysql_query("SELECT * FROM affiliates WHERE valid='1' AND id='".$db['status_id']."'",__FILE__);
					} else if (!$db['group_id'] AND !$db['affiliate_id'] AND !$db['status_id']) {
											$qq=function_mysql_query("SELECT * FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);
					}
					while ($ww=mysql_fetch_assoc($qq)) {
						$set->sendFrom = $set->webMail;
						$set->sendTo = $ww['mail'];

							

						// $find = Array("{custom_field}","{affiliate_username}","{affiliate_name}","{affiliate_email}","{accountManager_name}","{accountManager_email}","{accountManager_IM}","{brand_name}","{affiliate_password}", "{web_address}", "{web_mail}", "{aff_uname}", "{aff_fname}", "{merchant_name}", "{ticket_id}", "{base_url}", "{first_name}", "{last_name}");
						// $replace = Array($set->custom_field,$ww['username'],$ww['first_name'].' '.$ww['last_name'],$ww['mail'],$set->userInfo['first_name'].' '.$set->userInfo['last_name'],$set->userInfo['email'],$set->userInfo['IMUser'],$set->webTitle, $ww, $set->webAddress, $set->webMail, $set->aff_uname, $set->aff_fname, $merchantName, $ticketID, $set->webAddress, $FirstName, $LastName);
		
						//$set->subject = $db['title'];
						// $set->subject = str_replace($find,$replace,$db['title']);
						//$db['title'] = mysql_real_escape_string($db['title']);
						$db['title'] = $db['title'];
						$set->subject = messagesSearchNReplace($ww,$db['title']);
						$set->body = messagesSearchNReplace($ww,$db['text']);
						//$set->body = ($db['text']);
						// $set->body = str_replace($find,$replace,$db['text']);

						
						sendMail();
					}
			}
					/* while ($ww=mysql_fetch_assoc($qq)) {
						$set->sendFrom = $set->webMail;
						$set->sendTo = $ww['mail'];
						$set->subject = $db['title'];
						$set->body = $db['text'];
						sendMail();
						}
					while ($ww=mysql_fetch_assoc($qq)) {
						$set->sendFrom = $set->webMail;
						$set->sendTo = $ww['mail'];
						$set->subject = $db['title'];
						$set->body = $db['text'];
						sendMail();
						}
					while ($ww=mysql_fetch_assoc($qq)) {
						$set->sendFrom = $set->webMail;
						$set->sendTo = $ww['mail'];
						$set->subject = $db['title'];
						$set->body = $db['text'];
						sendMail();
						}
					} */

				
			// Send to Affiliates
			$db['display_approval_popup'] = $db['display_approval_popup'] == "on"?1:0;
			
			
			$db['valid'] = $db['automatic_approve_msg'] == "on"?1:0;
			unset ($db['automatic_approve_msg']);
			$a = dbAdd($db,$appTable);
			// var_dump($a);
			// die();
			_goto($set->SSLprefix.$set->basepage);
			}
	
	default:
		$pageTitle = lang('Messages For Affiliates');
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
			
	
					
					
					
		if ($id){
			$db=dbGet($id,$appTable);
			//$db['title'] = mysql_real_escape_string($db['title']);
		}
		$qq=function_mysql_query("SELECT * FROM ".$appTable." ORDER BY id Desc",__FILE__);
		$qq=function_mysql_query("SELECT * FROM ".$appTable." where affiliate_id>0 or (affiliate_id=0 and advertiser_id=-1 ) ORDER BY id Desc",__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$adminww = dbGet($ww['admin_id'],"admins");
			
			// $msgTitle =  messagesSearchNReplace($adminww,$ww['title']);
			$msgTitle =  $ww['title'];
			$msgText = $ww['text'];
			
			$group = ($ww['group_id'] ? listGroups($ww['group_id'],1):"");
			$status = ($ww['status_id'] ? listStatus($ww['status_id'],1) : '');
			$affiliate = ($ww['affiliate_id'] ? '<a href="/admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'">'.listAffiliates($ww['affiliate_id'],1): "");
			$all = [];
			if($group != "")
			$all[] = $group;
			if($status != "")
			$all[] = $status; 
			if($affiliate != "")
			$all[] = $affiliate; 
			
			if($group == "" && $status == "" && $affiliate =="")
				$val = lang('All');
			else{
				$val = implode(" / ",$all);
			}
			
			$messagesList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td>'.$ww['id'].'</td>
						<td><a href="'.$set->SSLprefix.$set->basepage.'?id='.$ww['id'].'">'.lang('Edit').'</a> | <a href="'.$set->SSLprefix.$set->basepage.'?act=delete&id='.$ww['id'].'">'.lang('Delete').'</a></td>
						<td align="left">'.nl2br($msgTitle).'</td>
						<td align="center">'.$adminww['first_name'].' '.$adminww['last_name'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						
						<td align="center">'. $val .'</a></td>
						
						
						
						<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
						<td align="center"><a class="inline" href="#message_logs" data-msg_id="' . $ww['id'] . '" data-affiliate_id="' . $ww['affiliate_id'] . '">'.lang('Logs').'</a></td>
					</tr>';
			}

		
		$set->content .= '
		<form method="post">
						<input type="hidden" name="act" value="add" />
						<input type="hidden" name="db[id]" value="'.$db['id'].'" />
						<input type="hidden" name="db[advertiser_id]" value="-1" />		
						<div class="normalTableTitle">'.lang('Add New Message').'</div>
						<div align="left" style="background: #EFEFEF;">
		
						<table border="0" cellpadding="0" cellspacing="5" id="tab_1">
							<tr>
								<td align="left">'.lang('Choose Group').':</td>
								<td><select id="groups" name="db[group_id]" style="width: 140px;"><option value="">'.lang('All Groups').'</option>'.listGroups($db['group_id']).'</select></td>
								
								<td align="right">'.lang('Choose Category').': <select id="category" name="db[status_id]" style="width: 140px;"><option value="">'.lang('All Categories').'</option>'.listStatus($db['status_id']).'</select></td>
								
								<td align="right">'.lang('Specific Affiliate').': <select id="affiliates" name="db[affiliate_id]" style="width: 140px;"><option value="">'.lang('All Affiliate').'</option>'.listAffiliates($db['affiliate_id']).'</select></td>
							</tr><tr>
								<td align="left" valign="top">'.lang('Subject').':</td>
								<td colspan="3" align="left"><!--input type="text" name="db[title]" value="'.$db['title'].'" style="width: 98.5%;" id="fieldClear" /-->
								<textarea name="db[title]" cols="119" rows="1">'.$db['title'].'</textarea>
								</td>
							</tr><tr>
								<td align="left" valign="top">'.lang('Content').':</td>
								<td colspan="3" align="left">
									<textarea name="db[text]" id="contentMail" cols="119" rows="12" style="font-family: Arial; background: #FFF;">'.$db['text'].'</textarea>
								</td>
							</tr>
							<tr>
								<td></td>
								<td align="left"><input type="checkbox" name="db[automatic_approve_msg]" '. ($db['automatic_approve_msg']==1?'checked':'') .'/> '.lang('Auto activate message on creation').'</td>
								<td align="right"><input type="checkbox" name="db[display_approval_popup]" '. ($db['display_approval_popup']==1?'checked':'') .'/> '.lang('Popup untill affiliate\'s approval').'</td>
								<td align="right"><input type="checkbox" name="sendMail" /> '.lang('Send by e-mail to the affiliates').'</td>
							</tr>
							<tr>
								<td></td>
								</tr>
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><input type="submit" value="'.lang('Save').'" /></td>
							</tr>
						</table>
						<link rel="stylesheet" type="text/css" href="'.$set->SSLprefix.'css/jquery.cleditor.css" />
						<script type="text/javascript" src="'.$set->SSLprefix.'js/jquery.cleditor.js"></script>
						<script type="text/javascript">
							$(document).ready(function () { 
							
							$("#contentMail").cleditor(); 
							
							$("#groups").on("change", function(){
								var group = $(this).val();
								var cat = $("#category").val();
								$.get( "' . $_SERVER['SERVER_HOST'] . '/ajax/getCategoryAffiliates.php?category="+cat+"&group="+group, function(res) {
									try {
										$("#affiliates").html(res);
									} catch (error) {
										console.log(error);
									}
								});
							});
							
							$("#category").on("change", function(){
								var cat = $(this).val();
								var group = $("#groups").val();
								$.get( "' . $_SERVER['SERVER_HOST'] . '/ajax/getCategoryAffiliates.php?category="+cat+"&group="+group, function(res) {
									try {
										$("#affiliates").html(res);
									} catch (error) {
										console.log(error);
									}
								});
								
							});
							
							});
						</script>
						</div>
						</form>
						';
						$set->content .= '<br>					<b><u>'.lang('Variables List').'</u></b><br />
					<ul type="*" style="line-height: 18px;">
						<li><b>{Brand Name}</b> - '.lang('Brand Name').'</li>
						<li><b>{affiliate_username}</b> - '.lang('Affiliate Username').'</li>
						<li><b>{affiliate_name}</b> - '.lang('Affiliate Full Name').'</li>
						<li><b>{affiliate_email}</b> - '.lang('Affiliate E-mail').'</li>
						<!--li><b>{affiliate_password}</b> - '.lang('Affiliate Password').'</li-->
						<li><b>{accountManager_name}</b> - '.lang('Account Manager Name').'</li>
						<li><b>{accountManager_email}</b> - '.lang('Account Manager E-mail').'</li>
						<li><b>{accountManager_IM}</b> - '.lang('Account Manager IM').'</li>
						<!--li><b>{ticket_id}</b> - '.lang('Ticket ID').'</li-->
						<li><b>{custom_field}</b> - '.lang('Custom Variable').'</li>
					</ul><hr />';
					
							$set->content .= '<br><br>		
						<div class="normalTableTitle">'.lang('Messsage List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Actions').'</td>
								<td style="text-align: left;">'.lang('Subject').'</td>
								<td align="center">'.lang('Added By').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<!--td align="center">'.lang('Group').'</td>
								<td align="center">'.lang('Affiliate').'</td>
								<td align="center">'.lang('Available').'</td-->
								<td align="center">'.lang('Related To').'</td>
								<td align="center">'.lang('Status').'</td>
								<td align="center">'.lang('Log').'</td>
							</tr></thead><tfoot><tr>
							</tr>'.$messagesList.'</tfoot>
						</table>';
						
						
		$set->content .= '<div id="message_logs" style="display: none; border: "1px black solid" ,height: "220px", width:"250px;">
            <center>
            <h2><u>' . lang('Message Log History') . '</u></h2>
            <table border="1" style="width: 99%;">
                    <thead>
                        <tr>
                            <td><label style="color: White">' . lang('ID') . '</label></td>
                            <td><label style="color: White">' . lang('Affiliate ID') . '</label></td>
                            <td><label style="color: White">' . lang('Affiliae Username') . '</label></td>
                            <td><label style="color: White">' . lang('Date of Approval') . '</label></td>
                        </tr>
                    </thead>

                    <tbody>
                            <!-- Pixel Logs. -->
                            <!-- Data will be loaded via an ajax call. --> 
                    </tbody>
            </table>
            </center>
    </div>
</div>
    <script>
	
        $(".inline").colorbox({inline:true,border: "1px black solid" ,height: "400px", width:"50%"});

        $(".inline").click(function() {
            var elem = $(this);

            $.get("'.$set->SSLprefix.'ajax/loadMessageApprovalLogs.php",
                { "msg_id": elem.data("msg_id"),"affiliate_id":elem.data("affiliate_id") },
                function(res) {
                    try {
                        res = JSON.parse(res);
                        var strTr = "";

                        for (var i = 0; i < res["success"].length; i++) {
							var respCode = res["success"][i]["msgResponse"];
							respCode = respCode ? respCode.split(" ") : [\'0\', \'\'];
							var color = "white";
							if (respCode[0]==200) {
								color = "lightgreen";
							}
							else if (respCode[0]==301 || respCode[0]==307) {
								color = "yellow";
							}
							else if (respCode[0]==403 || respCode[0]==500 || respCode[0]==400 || respCode[0]==401|| respCode[0]==404 || respCode[0]==501 || respCode[0]==503|| respCode[0]==550) {
								color = "red";
							}
							else  {
								color = "white";
							}
							
							
                            strTr += "<tr><td>" + (i + 1) + "</td><td >"+res["success"][i]["affiliate_id"]+"</td><td >"+res["success"][i]["username"]+"</td><td>" + res["success"][i]["approval_date"]+"</td></tr>";
                        }

                        $("#message_logs table tbody").html(strTr);

                    } catch (error) {
                        console.log(JSON.stringify(error));
                    }
                });

            $("#message_logs").show();
        });

        $(document).bind("cbox_closed", function() {
            $("#message_logs").hide();
        });
    </script>
';
			
		theme();
		break;
	}

?>