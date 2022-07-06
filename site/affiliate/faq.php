<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);


$pageTitle = lang("FAQ's");
$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
$set->content = '<div class="normalTableTitle" style="width: 100%;">'.lang("FAQ's").'</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('Where can I see Broker activities?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('On your Home screen or').' <a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a><br /><br />
	<img border="0" src="'.$set->SSLprefix.'affiliate/images/faq/broker_active.jpg" alt="" />
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('What\'s the difference between FTD and Deposits?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('\'FTD\' = First Time Deposit. \'Deposits\' = Total deposits').'<br /><br />
	<img border="0" src="'.$set->SSLprefix.'affiliate/images/faq/dif_ftd.jpg" alt="" />
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('What are the "Top 5 Banners" at the bottom right?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('Best Performing banners according the CTR IMP / Clicks - available in').' <a href="'.$set->SSLprefix.'affiliate/creative.php">'.lang('Marketing Tools').'</a><br /><br />
	<img border="0" src="'.$set->SSLprefix.'affiliate/images/faq/top_5.jpg" alt="" />
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('How do I place banners and links on my website?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('Go to').': <a href="'.$set->SSLprefix.'affiliate/creative.php">'.lang('Marketing Tools').'</a> - '.lang('Choose Merchant, Language, Type, Size and click on Search > Press \'Get Tracking Code\' and Copy / Paste the code into your website').'<br /><br />
	<img border="0" src="'.$set->SSLprefix.'affiliate/images/faq/banner_code.jpg" alt="" />
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('What are Widgets and how do they work?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('Dynamic banners providing live feeds to market stats and other financial information about the market, Most of the time the feeds working according the GMT').'
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('How do I see which banners perform best for me?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('Reports').' > <a href="'.$set->SSLprefix.'affiliate/reports.php?act=banner">'.lang('Banner Reports').'</a>
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('How do I know which Brokers are converting the best?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('Reports').' > <a href="'.$set->SSLprefix.'affiliate/reports.php?act=traffic">'.lang('Traffic Report').'</a> '.lang('and compare CTR\'s (Click-Through Ratio)').'
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('How do I add additional websites to my account?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	<a href="'.$set->SSLprefix.'affiliate/profiles.php">'.lang('Profiles').'</a> > '.lang('Enter your new website\'s details and Save').'
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('How do I Edit my Website Profiles and status?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	<a href="'.$set->SSLprefix.'affiliate/tickets.php?act=new">'.lang('Contact').'</a> '.lang('your Account Manager').'
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('What is the Sub-Affiliate Link in my account?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('Your Affiliate Link is used to refer other affiliates').'.<br />
	'.lang('They will be allocated under your account and you will earn a percentage of their commission').'
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('When do I get paid?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('Until the').' '.$set->dateOfMonthlyPayment.' '.lang('of the month - for earnings from the previous month').'
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('Where can I see how much I will be paid?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('On your').' <a href="'.$set->SSLprefix.'affiliate/">'.lang('Homescreen / Dashboard').'</a> - '.lang('Select \'This Month\' for the Time Frame').'
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('How do I choose or edit my Payment Method(s)?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('Login and go to').': '.lang('My Account').' > <a href="'.$set->SSLprefix.'affiliate/account.php?act=payment">'.lang('Payment Details').'</a> - '.lang('Select your \'Primary Payment Method\' and click Save Details').'
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('How do I know how much commission each Broker is paying me?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('Login and go to').': '.lang('My Account').' > <a href="'.$set->SSLprefix.'affiliate/account.php?act=commission">'.lang('Commission Structure').'</a>
</div>
<br />
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('How do I contact my Account Manager?').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('You can receive Support by: Email, Telephone, Live Chat (Instant Messaging) and the internal ticketing system').' -<br />
	'.lang('Support').' > <a href="'.$set->SSLprefix.'affiliate/tickets.php?act=new">'.lang('New Ticket').'</a> / '.lang('Search Ticket').'
</div>

<script type="text/javascript">

$(".question").click(function() {
	$(this).next().slideToggle("fast");
	if ($(this).find(".plus").html() == "+") $(this).find(".plus").html("-");
		else $(this).find(".plus").html("+");
	});

</script>

';

theme(2);

?>
