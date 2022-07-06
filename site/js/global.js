var pattern = new RegExp(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
function gid(gelement) {
	return document.getElementById(gelement);
	}

function confirmation(txt,link) {
	UserConfirm=confirm(txt);
	if (UserConfirm==true) location.href = link;
	return (UserConfirm);
	}

function NewWin(location, name,w, h, scr) {
	window.open(location, name, 'width='+w+', height='+h+', scrollbars='+scr+', top='+((screen.height / 4)-(h / 2))+', left='+((screen.width / 4) - (w / 2)));
	}

function ShowDiv(stylefeild) {
	if (document.getElementById(stylefeild).style.display=='none')
		document.getElementById(stylefeild).style.display='block';
	else
		document.getElementById(stylefeild).style.display='none';
	}

function selectAll() {
	var arr = document.getElementsByTagName("input");
	for (i=0;i<arr.length;i++) arr[i].checked=true;
	}

function unselectAll() {
	var arr = document.getElementsByTagName("input");
	for (i=0;i<arr.length;i++) arr[i].checked=false;
	}
	
function ajax(url,target) {
	var xmlHttp;
	try {  // Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();  }
		catch (e) {  // Internet Explorer 
			try {
				xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {
				alert("Your browser does not support AJAX!");
				return false;
				}
			}
		}

	xmlHttp.onreadystatechange=function() {
		if (xmlHttp.readyState == 4) {
			if (gid(target)) gid(target).innerHTML = xmlHttp.responseText;
			}
		}
	if (gid(target)) {
		xmlHttp.open("POST",url,true);
		} else {
		xmlHttp.open("POST",url,false);
		}
	xmlHttp.send(null); 
	if (!gid(target)) return xmlHttp.responseText;
	}

function numbersOnly(e,o) {
	var ev = o.charCode ? o.charCode : o.keyCode;
	if (ev < 48 || ev > 57) return false;
	// onkeypress="return numbersOnly(this,event);" onpaste="return false"
	}

function chkMail(str) {
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if (reg.test(str) == false) return false; else return true;
	}
function moveMultiple(fromSelect,toSelect,strongField,updateInput) {
	var totalItems = gid(fromSelect).options.length;
	for (i=totalItems -1; i >= 0;i--) {
		if (gid(fromSelect).options[i].selected) {
			var newOption = document.createElement('option');
			newOption.text = gid(fromSelect).options[i].text;
			newOption.value = gid(fromSelect).options[i].value;
			gid(toSelect).add(newOption);
			gid(fromSelect).remove(i);
		}
	}

	var totalNewItems = gid(strongField).options.length;
	var listArr = new Array();
	for (i=totalNewItems-1; i >= 0;i--)
		listArr[i] = gid(strongField).options[i].value;

	gid(updateInput).value = listArr.join(",");
	}
	
/* function checkReg() {
	
	var check_ids = new Array("username","password","repassword","website","street","postalCode","city","country","last_name","first_name","mail","phone","code");
	var names = new Array("Username","Password","Repeat Password","Website","Street","Postal / Zip Code","City","Country","Last Name","First Name","E-mail","Phone","Code");
	var check_ids_length = check_ids.length;
	for (i = 0; i < check_ids_length; i++) {
		key = $("#"+check_ids[i]);
		if (key.val() == "") {
			//alert("Please fill out "+names[i]);
			  $.fancybox({ 
			 closeBtn:false, 
			  minWidth:'250', 
			  minHeight:'180', 
			  autoCenter: true, 
			  afterClose:function(){
				  key.focus();
			  },			  
			  content: "<h1><div style='float:left;'><img src='../images/warning-3.png' width='30px' height='30px' ></div>&nbsp;&nbsp;<span>Error</span></h1><div align='center'><div id='alert1'  style='margin-top:40px'><h2>Please fill in " + names[i] +"</h2></div><div style='margin-top:30px;'><input type='button' class='btnContinue' class='btnContinue' value='Continue' onClick='$.fancybox.close()'></div></div>" 
			  });
			  key.focus();
			return false;
		}
	}

	var email = $("#mail");
	if (!(pattern.test(email.val()))) {
		//alert("Your e-mail is not valid");
		 $.fancybox({ 
		 closeBtn:false, 
		  minWidth:'250', 
		  minHeight:'180', 
		  autoCenter: true, 
		  afterClose:function(){
			  email.focus();
		  },			  
		  content: "<h1><div style='float:left;'><img src='../images/warning-3.png' width='30px' height='30px' ></div>&nbsp;&nbsp;<span style='padding-top:-5px'>Error</span></h1><div align='center'><div id='alert1'  style='margin-top:40px'><h2>Your e-mail is not valid.</h2></div><div style='margin-top:30px;'><input type='button' class='btnContinue' value='Continue' onClick='$.fancybox.close()'></div></div>" 
		  });
		
		return false;
		}
	if ($("#password").val() != $("#repassword").val()) {
//		alert("Password does not match");
		$.fancybox({ 
		 closeBtn:false, 
		  minWidth:'250', 
		  minHeight:'180', 
		  autoCenter: true, 
		  afterClose:function(){
			 $("#password").focus();
		  },			  
		  content: "<h1><div style='float:left;'><img src='../images/warning-3.png' width='30px' height='30px' ></div>&nbsp;&nbsp;<span style='padding-top:-5px'>Error</span></h1><div align='center'><div id='alert1' style='margin-top:40px' ><h2>Password does not match.</h2></div><div style='margin-top:30px;'><input type='button' class='btnContinue' value='Continue' onClick='$.fancybox.close()'></div></div>" 
		  });
		$("#password").focus();
		return false;
		}
	
	if (!($("#approve").is(":checked"))) {
		//alert("Your have to approve the terms & conditions");
		$.fancybox({ 
		 closeBtn:false, 
		  minWidth:'250', 
		  minHeight:'180', 
		  autoCenter: true, 
		  afterClose:function(){
			 $("#approve").focus();
		  },
		 
		  content: "<h1><div style='float:left;'><img src='../images/warning-3.png' width='30px' height='30px' ></div>&nbsp;&nbsp;<span style='padding-top:-5px'>Error</span></h1><div align='center'><div id='alert1' style='margin-top:40px'><h2>Your have to approve the terms & conditions.</h2></div><div style='margin-top:30px;'><input type='button' class='btnContinue' value='Continue' onClick='$.fancybox.close()'></div></div>" 
		  });
		$("#approve").focus();
		return false;
		}
	} */
	
	
function checkUpdate() {
	var check_ids = new Array("website","street","postalCode","city","country","last_name","first_name","mail","phone");
	var names = new Array("Website","Street","Postal / Zip Code","City","Country","Last Name","First Name","E-mail","Phone");
	var check_ids_length = check_ids.length
	for (i = 0; i < check_ids_length; i++) {
		key = $("#"+check_ids[i]);
		if (key.val() == "") {
			alert("Please fill out "+names[i]);
			key.focus();
			return false;
		}
	}
	
	
	var email = $("#mail");
	if (!(pattern.test(email.val()))) {
		alert("Your e-mail is not valid");
		email.focus();
		return false;
		}
	if ($("#password").val() && $("#password").val() != $("#repassword").val()) {
		alert("Password does not match");
		$("#password").focus();
		return false;
		}
	}
	
	function extractAffiliateID(affiliate_id){
		
		if(affiliate_id != ""){
			
			pos = affiliate_id.indexOf("[") + 1;
			affiliate_id = affiliate_id.slice(pos, affiliate_id.lastIndexOf("]"));
			
			return affiliate_id;
			
		}
		
	}
	function submitReportsForm(e){
			formdata = $(e).serialize();
			
			affid = $("input[name=\'affiliate_id\']").val();
			
			affid = extractAffiliateID(affid);
			$("input[name=\'affiliate_id\']").val(affid);
			return true;			
	}
	
	function saveReportToMyFav(name,report,user,user_level,rec_type){
		url = window.location.href;
		url += "&isFav=1";
		$.post("../ajax/saveMyFavorites.php",{ user_id : user,type : rec_type,level: user_level, report_name: name, report_url: url,report:report}, function(res) {
				try {
					
				} catch (error) {
					console.log(error);
				}
			});		
	}
	
	
	function simpleConfirmation(txt,link) {
	UserConfirm=confirm(txt);
	if (UserConfirm==true) location.href = link;
	return (UserConfirm);
	}
	
	function confirmation() {
							
							Id = $("#promotion_name").val();
							
							$.get("ajax/managePromotion.php?id="+Id,function(res){
								if(res!==0){
								$.prompt(PromotionConfirmMessage, {
												top:200,
												title:PromotionHeading,
												buttons: { "Yes" : true,"Switch all banners to General Promotion": "true1","Cancel" : false },
												submit: function(e,v,m,f){
													if(v === true){
														$("#frmDelPromotion").submit();
													}
													else if (v=="true1"){
														$.get("'.$set->SSLprefix.'/ajax/managePromotion.php?act=switchToGeneral&id="+Id,function(res){
															if(res==1){
															$.prompt(PromotionConfirmMessage_2, {
																		top:200,
																		title: PromotionHeading,
																		buttons: { "Yes": true, "Cancel": false },
																		submit: function(e,v,m,f){
																			if(v){
																				$("#frmDelPromotion").submit();
																			}
																			else{
																				//return false;
																			}
																		}
															});
															}
															else{
																console.log("ERROR!");
															}
														});
													}
													else{
														//return false;
													}
												}
									});
								}
							});
							return false;
							/* var msg = "Are you sure you want to delete promotions?";
							return confirm(msg); */
						}