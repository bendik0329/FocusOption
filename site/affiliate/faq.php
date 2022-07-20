<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);


$pageTitle = lang("FAQ");
$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'.$set->SSLprefix.'affiliate/tickets.php">'.lang('Support').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
$set->content = '
<br />
<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>Where can I see Broker activities?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('On your Home screen or').' <a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a><br /><br />
		<img border="0" src="'.$set->SSLprefix.'affiliate/images/faq/broker_active.jpg" alt="" />
	</div>
</div>

<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>What\'s the difference between FTD and Deposits?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('\'FTD\' = First Time Deposit. \'Deposits\' = Total deposits').'<br /><br />
		<img border="0" src="'.$set->SSLprefix.'affiliate/images/faq/dif_ftd.jpg" alt="" />
	</div>
</div>

<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>What are the "Top 5 Banners" at the bottom right?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('Best Performing banners according the CTR IMP / Clicks - available in').' <a href="'.$set->SSLprefix.'affiliate/creative.php">'.lang('Marketing Tools').'</a><br /><br />
		<img border="0" src="'.$set->SSLprefix.'affiliate/images/faq/top_5.jpg" alt="" />
	</div>
</div>

<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>How do I place banners and links on my website?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('Go to').': <a href="'.$set->SSLprefix.'affiliate/creative.php">'.lang('Marketing Tools').'</a> - '.lang('Choose Merchant, Language, Type, Size and click on Search > Press \'Get Tracking Code\' and Copy / Paste the code into your website').'<br /><br />
		<img border="0" src="'.$set->SSLprefix.'affiliate/images/faq/banner_code.jpg" alt="" />
	</div>
</div>

	<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>What are Widgets and how do they work?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('Dynamic banners providing live feeds to market stats and other financial information about the market, Most of the time the feeds working according the GMT').'
	</div>
</div>

	<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>How do I see which banners perform best for me?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('Reports').' > <a href="'.$set->SSLprefix.'affiliate/reports.php?act=banner">'.lang('Banner Reports').'</a>
	</div>
</div>

	<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>How do I know which Brokers are converting the best?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('Reports').' > <a href="'.$set->SSLprefix.'affiliate/reports.php?act=traffic">'.lang('Traffic Report').'</a> '.lang('and compare CTR\'s (Click-Through Ratio)').'
	</div>
</div>

	<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>How do I add additional websites to my account?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		<a href="'.$set->SSLprefix.'affiliate/profiles.php">'.lang('Profiles').'</a> > '.lang('Enter your new website\'s details and Save').'
	</div>
</div>

	<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>How do I Edit my Website Profiles and status?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		<a href="'.$set->SSLprefix.'affiliate/tickets.php?act=new">'.lang('Contact').'</a> '.lang('your Account Manager').'
	</div>
</div>

<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>What is the Sub-Affiliate Link in my account?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('Your Affiliate Link is used to refer other affiliates').'.<br />
		'.lang('They will be allocated under your account and you will earn a percentage of their commission').'
	</div>
</div>

	<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>When do I get paid?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('Until the').' '.$set->dateOfMonthlyPayment.' '.lang('of the month - for earnings from the previous month').'
	</div>
</div>

	<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>Where can I see how much I will be paid?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('On your').' <a href="'.$set->SSLprefix.'affiliate/">'.lang('Homescreen / Dashboard').'</a> - '.lang('Select \'This Month\' for the Time Frame').'
	</div>
</div>

	<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>How do I choose or edit my Payment Method(s)?</p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('Login and go to').': '.lang('My Account').' > <a href="'.$set->SSLprefix.'affiliate/account.php?act=payment">'.lang('Payment Details').'</a> - '.lang('Select your \'Primary Payment Method\' and click Save Details').'
	</div>
</div>

	<div class="faq-question">
	<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
		<span class="plus">+</span> '.lang('<p>How do I know how much commission each Broker is paying me?<p>').'
	</div>
	<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
		'.lang('Login and go to').': '.lang('My Account').' > <a href="'.$set->SSLprefix.'affiliate/account.php?act=commission">'.lang('Commission Structure').'</a>
	</div>
</div>

<div class="faq-question">
<div class="question" style="border-radius: 5px; border: 1px #2c7cd0 solid; background: #f8f8f8; color: #1d1d1d; padding: 3px; font-size: 16px; font-family: Calibri; cursor: pointer;">
	<span class="plus">+</span> '.lang('<p>How do I contact my Account Manager?</p>').'
</div>
<div class="answer" style="font-family: Calibri; font-size: 18px; padding: 5px; display: none;">
	'.lang('You can receive Support by: Email, Telephone, Live Chat (Instant Messaging) and the internal ticketing system').' -<br />
	'.lang('Support').' > <a href="'.$set->SSLprefix.'affiliate/tickets.php?act=new">'.lang('New Ticket').'</a> / '.lang('Search Ticket').'
</div>
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
