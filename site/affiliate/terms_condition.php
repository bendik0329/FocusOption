<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);

switch ($act) {
	default:
		$pageTitle = lang('Terms and condition');
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
			</ul>

            <div class="teams-condition">
                <div class="teams">
                    <div class="condition">
                        <div class="teams-details">
                            <h2>
                                TERMS AND CONDITIONS
                            </h2>
                            <p class="mb-0"> 
                            Before you can proceed with registration, please accept the terms and conditions below.</p>
                            <p>Please Read and Print for Future Reference</p>
                            <p>If you wish to participate in our Affiliate program, indicate your agreement to do so by clicking the â€œI Agreeâ€ button below. Additionally, by accessing and utilizing any of Gaming affiliates Marketing Tools or accepting of any reward, bonus or commission whether contained in this agreement or elsewhere on our website, you are deemed to have agreed to be bound by all the terms and conditions set out in this agreement. For purposes of clarity, the terms â€œweâ€ and â€œusâ€ refer to Gaming affiliates and â€œyouâ€ and â€œMemberâ€ refers to the other party to the contract formed by the acceptance of these Terms and Conditions. The term â€œMerchantâ€ is defined as any company that has contracted Gaming affiliates to promote their Site(s) and/or products. The contract provides you with the non-exclusive right to direct users (â€œVisitorsâ€) from your site or sites to the Merchantâ€™s websites, in return for the payment of commissions and referral bonuses as specified below.
                            </p>
                        </div>
                        <div class="teams-details">
                            <h3>
                                ENROLLMENT
                            </h3>
                            <p>
                                To enroll please read this Agreement and then submit a complete Gaming Affiliates Member Account application to us via our Web site. We will evaluate your application and notify you whether your application was accepted. Your application will be rejected if we determine, in our sole discretion, that your site is unsuitable for any reason, including but not limited to, sites that are under construction, aimed at children, promote sexually explicit materials, promote violence, promote discrimination based on race, sex, religion, nationality, disability, sexual orientation, or age, promote illegal activities, or violate intellectual property rights.
                            </p>
                        </div>
                        <div class="teams-details">
                            <h3>
                                ENROLLMENT
                            </h3>
                            <p>
                                To enroll please read this Agreement and then submit a complete Gaming Affiliates Member Account application to us via our Web site. We will evaluate your application and notify you whether your application was accepted. Your application will be rejected if we determine, in our sole discretion, that your site is unsuitable for any reason, including but not limited to, sites that are under construction, aimed at children, promote sexually explicit materials, promote violence, promote discrimination based on race, sex, religion, nationality, disability, sexual orientation, or age, promote illegal activities, or violate intellectual property rights.
                            </p>
                        </div>
                        <div class="teams-details">
                            <h3>
                                MEMBER AGREEMENT
                            </h3>
                            <p>
                                During the term of this arrangement (which shall commence when you indicate your acceptance in the manner specified above, and shall end when either you or we notify the other, by email, of the termination of this Agreement), you shall display a banner or banners provided by Gaming Affiliates on your site (the â€œMember siteâ€) as a hyperlink to direct Visitors from the Member Site to the Merchantâ€™s Sites, using distinct URLs supplied by Gaming Affiliates exclusively for linking (the â€œSupplied Bannersâ€).
                            </p>
                        </div>
                    </div>
                </div>
            </div>


			';
		theme();
		break;
		
    }

?>