<?php
if (empty($set)) {
    header('/');
    die();
}

$SSLprefix = $set->SSLprefix;
$SSLswitch = $set->SSLswitch;

$pageTitle = ($set->breadcrumb_title ? $set->breadcrumb_title : ($set->pageTitle ? $set->pageTitle . ' - ' : '') . $set->webTitle);


$logoPath = @strpos($set->logoPath, $SSLprefix) !== false ? $set->logoPath : $SSLprefix . $set->logoPath;
$altTextLogo = $set->dashBoardMainTitle;

$theLogoText = (!empty($set->logoPath) && strpos($set->logoPath, "/tmp") === false ? '<img class="headerLogo" style="width: 40%; " border="0" src="' . $logoPath . '" alt="' . $set->dashBoardMainTitle . '" />' : $altTextLogo);
?>
<!doctype html>
<html lang="en">
    <head>


        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

        <?php echo ($set->faviconPath && strpos($set->faviconPath, "/tmp") === false ? '<link rel="shortcut icon" href="' . ($set->faviconPath) . '"  />' : '') ?>

        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&display=swap" rel="stylesheet">

        <!-- BEGIN VENDOR CSS-->
        <link rel="stylesheet" type="text/css" href="/app-assets/css/vendors.css">
        <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/icheck/icheck.css">
        <!-- END VENDOR CSS-->
        <!-- BEGIN ROBUST CSS-->
        <link rel="stylesheet" type="text/css" href="/app-assets/css/app.css">
        <!-- END ROBUST CSS-->
        <!-- BEGIN Page Level CSS-->
        <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/forms/selects/select2.min.css">
        <!-- END Page Level CSS-->
        <!-- BEGIN Custom CSS-->
        <link rel="stylesheet" href="/assets/js/scripts/phonecode-master/phonecode.css">
        <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
        <!-- END Custom CSS-->


        <title><?= $pageTitle; ?></title>

        <!-- Bootstrap core CSS -->
        <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

        <!-- Custom styles for this template -->
        <script src="/js/login/login.js?v=<?= date('Ym'); ?>" type="text/javascript"></script>

        <?php
        if (!empty($set->metaTrackingHeader)) {
            echo $set->metaTrackingHeader;
        }
        
        if(!empty($_GET['p'])){
            if($_GET['p'] == 'register'){
                ?>
                <script>
                $(document).ready(function () {
                    $("#SignInForm").hide();
                    $("#ForgotForm").hide();
                    $("#RegisterForm").show();
                });
                </script>
                <?
            }
        }
        
        ?>
<style>

    #SignInForm a,
    #ForgotForm a,
    #RegisterForm  a,
    #RegisterFormComplete a,
    #ForgotFormComplete a
    {
        color: #7a26c4;    
    }

    .b-not-account {
        color: #7a26c4;
    }

    #SignInForm .btn, #SignInForm .btn:hover,
    #ForgotForm .btn,#ForgotForm .btn:hover,
    #RegisterForm  .btn,#RegisterForm  .btn:hover,
    #RegisterFormComplete .btn,#RegisterFormComplete .btn:hover,
    #ForgotFormComplete .btn, #ForgotFormComplete .btn:hover
    {
        border-radius: 20px;
        color: #28275f;
        background-color: transparent;
    }
    
    #SignInForm button.btn,
    #ForgotForm button.btn,
    #RegisterForm  button.btn,
    #RegisterFormComplete button.btn,
    #ForgotFormComplete button.btn
    {
        background-color: #fbba00 !important;
        border:  #fbba00 !important;
        color: #fff;
    }
    
        #SignInForm button.btn:hover,
    #ForgotForm button.btn:hover,
    #RegisterForm  button.btn:hover,
    #RegisterFormComplete button.btn:hover,
    #ForgotFormComplete button.btn:hover
    {
        background-color: #e9ad00 !important;
        border:  #e9ad00 !important;
    }
    
    
    .newsletterCheckboxCaption{
        margin-left: 32px;
        display: block;
    }
</style>
    </head>

    <body class="vertical-layout vertical-menu 1-column blank-page blank-page pace-done menu-expanded" data-open="click" data-menu="vertical-menu" data-col="1-column">

        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    <section class="">
                        <div class="col-12 d-flex align-items-center justify-content-center">
                            <div class="b-sing b-register">
                                <div class="card-title text-center mt-2">
            <?= $theLogoText; ?>
                                </div>
                                <div class="card border-grey border-lighten-3 px-2 py-2 m-0 box-shadow-2">



                                    <form class="form-horizontal form-simple needs-validation" novalidate  id="SignInForm">
                                        <div class="header-sing  text-center mb-2">
                                            <h1>
                                                <?= lang('Sign In to your'); ?><br>
                                                <b><?= lang('Affiliate Account'); ?></b>
                                            </h1>
                                        </div>
                                        <div class="card-content">
                <div class="alert alert-danger" id="errorMessage" role="alert" style="display:none"></div>

                                            <fieldset class="form-group  mb-1">
                                                <input type="text" class="form-control form-control-lg input-lg"
                                                       id="username" placeholder="<?= lang('Username'); ?>" required autofocus>
                                                <div class="invalid-feedback">
                                                    Please choose a username.
                </div>

                                            </fieldset>

                                            <fieldset class="form-group  ">
                                                <input type="password" class="form-control" id="password" placeholder="Password" required>
                                                <div class="invalid-feedback">Please choose a Password.</div>

                                            </fieldset>
                                            <p><input type="checkbox" value="remember-me"> <?= lang('Stay signed in'); ?></p>

                <?= ($set->multi ? '<div class="form-group"><select onchange="langRedirect(this.value)" id="selectLanguage" name="lang" class="form-control form-control-sm margin-botom-10"><option value="">' . lang('Choose your language') . '</option>' . listMulti($lang) . '</select></div>' : ''); ?>

                                            <button id="SignInBtn" type="submit" class="btn btn-primary btn-block"><?= lang('Sign In'); ?></button>


                                            <p class="mt-2">
                                                <?= (lang('Forgot your') . ' <a href="#" id="toForgotPage">' . lang('Username') . '</a> ' . lang('or') . ' <a href="#" id="toForgotPage2">' . lang('Password') . '</a>?'); ?>
                                            </p>
                                            <div class=" line-on-side  pt-1"></div>
                                            <div class="text-center b-not-account">
                                                <p><?= lang("Don't have an account yet?"); ?></p>
                                                <a href="#" id="toRegisterPage" class="btn btn-outline-dark btn-min-width  mb-1"><?= lang('Register'); ?></a>
                                            </div>

            </div>
        </form>

                                    <form class="form-horizontal form-simple needs-validation"  id="ForgotForm"  style="display: none;" novalidate>
                                        <div class="header-sing  text-center mb-2">
                                            <h1>
                                                <?= lang('Forgot your password?'); ?><br>
                                            </h1>
                                            <?= lang('Please fill out username or e-mail'); ?>
                                        </div>
                                        <div class="card-content">
                <div class="alert alert-danger" id="errorMessage" role="alert" style="display:none"></div>
                                            <fieldset class="form-group  mb-1">
                                                <input type="text" class="form-control form-control-lg input-lg" id="forgotname" placeholder="Username" required>
                                                <div class="invalid-feedback">
                                                    Please choose a username.
                                                </div>
                                            </fieldset>
                                            <h6 class=" line-on-side  text-center  pt-1"><span>or</span></h6>
                                            <fieldset class="form-group  ">
                                                <input type="email" class="form-control" id="forgotemail" placeholder="Email" required>
                                                <div class="invalid-feedback">
                                                    Please choose a Email.
                                                </div>

                                            </fieldset>
                                            <button type="submit" id="ForgotBtnForm" class="btn btn-primary btn-block">Reset Password</button>

                                            <div class=" line-on-side  pt-1"></div>
                                            <div class="text-center b-not-account">
                                                <p>Don’t have an account yet?</p>
                                                <a href="#" id="toRegisterPage2" class="btn btn-outline-dark btn-min-width  mb-1">Register</a>
                                            </div>

                                        </div>
                                    </form>

                                    <form class="form-horizontal form-simple needs-validation"  id="RegisterForm" style="display: none;width: 353px;" novalidate>
                                        <div class="header-sing  text-center mb-2">
                                            <h1>
                                                <?= lang('Sign Up to your'); ?><br>
                                                <b><?= lang('Affiliate Account'); ?></b>
                                            </h1>
                                        </div>
                                        <div class="card-content">

                <div class="row">
                                                <div class="<?= (!$set->hideInvoiceSectionOnAffiliateRegPage || !$set->hideMarketingSectionOnAffiliateRegPage) ? 'col-md-6' : 'col-md-12' ?>">

                        <div class="form-group">
                                                        <input type="text" class="form-control form-control-sm" name="db[username]" id="uname" placeholder="User Name" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">                                
                                                                <input type="text" name="db[first_name]" id="first_name" value ="" placeholder="First Name" class="form-control form-control-sm" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">                                
                                                                <input type="text" name="db[last_name]" id="last_name" value ="" placeholder="Last Name" class="form-control form-control-sm" required />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">                                
                                                        <input type="text" name="db[mail]"  id="mail" value ="" placeholder="E-Mail" class="form-control form-control-sm" required />
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                                                <input type="password" class="form-control form-control-sm" name="password" id="pwd" placeholder="Password" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                                                <input type="password" class="form-control form-control-sm" name="repassword" id="repassword" placeholder="Repeat Password" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                                                        <div class="col-md-12">
                                <div class="form-group">                                
                                                                <input type="text" name="db[phone]" value="" id="phone" placeholder="Phone" required />
                                </div>
                            </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                <div class="form-group">                                
                                    <select name="db[IMUserType]" class="form-control form-control-sm">
                                        <option value=""><?php echo lang('Choose I.M. Type') ?></option>
                                        <option value="Skype"><?php echo lang('Skype') ?></option>
                                        <option value="MSN"><?php echo lang('MSN') ?></option>
                                        <option value="Google Talk"><?php echo lang('Google Talk') ?></option>
                                        <option value="QQ"><?php echo lang('QQ') ?></option>
                                        <option value="ICQ"><?php echo lang('ICQ') ?></option>
                                        <option value="Yahoo"><?php echo lang('Yahoo') ?></option>
                                        <option value="AIM"><?php echo lang('AIM') ?></option>
                                    </select>
                                </div>
                            </div>
                                                        <div class="col-md-6">
                                <div class="form-group">                                
                                    <input type="text" name="db[IMUser]" value="" class="form-control form-control-sm" placeholder='I.M. Account' />
                                </div>
                            </div>
                        </div>


                        <?php
                        if ($set->showGroupValuesOnAffReg) {
                            if ($set->showGroupsLanguages == "group_name") {
                                $qry = "select * from groups where valid=1 order by lower(title)";
                                if ($_GET['group_id'] && $_GET['group_id'] > 0) {
                                    $qry = $qry . " and id =" . $_GET['group_id'];
                                }
                                $groups = function_mysql_query($qry, __FILE__);
                                $numRows = mysql_num_rows($groups);
                                if ($numRows > 0) {
                                    ?>
                                    <div class="form-group">
                                        <select name="group_id" class="form-control form-control-sm" id="groups"><option  value = ""><?php echo lang('Group'); ?></option>
                                            <?php
                                            while ($group = mysql_fetch_assoc($groups)) {
                                                echo '<option ' . ($numRows == 1 ? ' selected ' : '') . ' value=' . $group['id'] . '>' . $group['title'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php
                                }
                            } elseif ($set->showGroupsLanguages == "language_name") {
                                if ($_GET['group_id'] && $_GET['group_id'] > 0) {
                                    $qry = "SELECT groups.id, languages.title
                                    FROM groups
                                    LEFT JOIN languages ON languages.id = groups.language_id
                                    WHERE groups.valid =1  and groups.id =" . $_GET['group_id'] . "
                                    GROUP BY title
                                    ORDER BY LOWER( languages.title ) ";
                                } else {
                                    $qry = "SELECT groups.id, languages.title
                                    FROM groups
                                    LEFT JOIN languages ON languages.id = groups.language_id
                                    WHERE groups.valid =1
                                    GROUP BY title
                                    ORDER BY LOWER( languages.title ) ";
                                }

                                $langs = mysql_query($qry);
                                $numRows = mysql_num_rows($langs);
                                if ($numRows > 0) {
                                    ?>
                                    <div class="form-group">
                                        <select name="group_id" class="form-control form-control-sm" id="language">
                                            <option  value = ""><?php echo lang('Language') ?></option>
                                            <?php
                                            while ($lang = mysql_fetch_assoc($langs)) {
                                                echo '<option ' . ($numRows == 1 ? ' selected ' : '') . ' value=' . $lang['id'] . '>' . $lang['title'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php
                                }
                            } elseif ($set->showGroupsLanguages == "display_lname") {
                                if ($_GET['group_id'] && $_GET['group_id'] > 0) {
                                    $qry = "SELECT groups.id, languages.title,languages.displayText
                                    FROM groups
                                    LEFT JOIN languages ON languages.id = groups.language_id
                                    WHERE groups.valid =1  and groups.id =" . $_GET['group_id'] . "
                                    GROUP BY languages.title
                                    ORDER BY LOWER(languages.displayText ) ";
                                } else {
                                    $qry = "SELECT groups.id,languages.title, languages.displayText
                                    FROM groups
                                    LEFT JOIN languages ON languages.id = groups.language_id
                                    WHERE groups.valid =1
                                    GROUP BY languages.title
                                    ORDER BY LOWER( languages.displayText ) ";
                                }

                                $langs = function_mysql_query($qry, __FILE__);
                                $numRows = mysql_num_rows($langs);
                                if ($numRows > 0) {
                                    ?>
                                    <div class="form-group">
                                        <select name="group_id" class="form-control form-control-sm" id="language"><option  value = ""><?php echo lang('Language') ?></option>
                                            <?php
                                            while ($lang = mysql_fetch_assoc($langs)) {
                                                echo '<option ' . ($numRows == 1 ? ' selected ' : '') . ' value=' . $lang['id'] . '>' . $lang['displayText'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>';
                                    <?php
                                }
                            }
                        }
                        ?>

                        <?php if ($set->ShowAffiliateTypes): ?>
                            <div class="form-group">
                                <select name="utype"  id="utype" class="form-control form-control-sm"><option selected=1 value = "Affiliate"><?php echo lang('Affiliate') ?></option><option value = "IB"><?php echo lang('IB'); ?></option><option value = "WhileLabel"><?php echo lang('WhiteLabel') ?></option><option value = "PortfolioManager"><?php echo lang('Porfolio Manager') ?></option></select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">                                
                            <input type="text" name="db[company]" value="" placeholder="Company Name" class="form-control form-control-sm" />
                        </div>

                        <div class="form-group">                                
                                                        <input type="url" name="db[website]" id="website" value="" class="form-control form-control-sm"  placeholder="Website (Example: http://domain.tld/)" required/>              
                        </div>
                        
                        <div class="form-group" style="font-size:12px;">
                            <?php
                            if ($set->allowCapthaOnReg){
                                echo secureCode();
                            }
                            ?>
                        </div>      



                    </div>

                                                <?php if (!$set->hideInvoiceSectionOnAffiliateRegPage || !$set->hideMarketingSectionOnAffiliateRegPage): ?>

                        <div class="col-md-6">
                            <?php if (!$set->hideInvoiceSectionOnAffiliateRegPage): ?>
                                <div class="form-group">                                
                                    <input type="text" name="db[street]" value="" id="street" class="form-control form-control-sm" placeholder="Street"/>    
                                </div>

                                <div class="form-group">                                
                                    <input type="text" name="db[postalCode]" value="" id="postalCode" class="form-control form-control-sm" placeholder="Postal / Zip Code"/>    
                                </div>

                                <div class="form-group">                                
                                    <input type="text" name="db[city]" value="" id="city" class="form-control form-control-sm" placeholder="City"/>
                                </div>

                                <div class="form-group">                                
                                    <select name="db[country]" class="form-control form-control-sm" id="country">
                                        <option value=""><?php echo lang('Choose Your Country') ?></option>
                                        <?php echo getCountry(); ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <?php if (!$set->hideMarketingSectionOnAffiliateRegPage): ?>

                                <h4><?php echo lang('Marketing Information'); ?></h4>
                                <div class="form-group">                                
                                    <p style="font-size:12px;"><?php echo lang('What are your traffic sources?') ?></p>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <select id="q1" size="5" multiple="true" class="form-control form-control-sm">
                                                <option value="1"><?php echo lang('Africa') ?></option>
                                                <option value="2"><?php echo lang('Afro Eurasia') ?></option>
                                                <option value="3"><?php echo lang('Americas') ?></option>
                                                <option value="4"><?php echo lang('Asia') ?></option>
                                                <option value="5"><?php echo lang('Australia') ?></option>
                                                <option value="6"><?php echo lang('Eurasia') ?></option>
                                                <option value="7"><?php echo lang('Europe') ?></option>
                                                <option value="8"><?php echo lang('North America') ?></option>
                                                <option value="9"><?php echo lang('South America') ?></option>
                                                <option value="10"><?php echo lang('United Kingdom') ?></option>
                                                <option value="11"><?php echo lang('World Wide') ?></option>
                                            </select>
                                        </div>

                                        <div class="col-md-1 center-block">
                                            <img border="0" src="/images/reg/right.jpg" alt="" onclick="moveMultiple('q1', 'q2', 'q2', 'update'); return false;" style="cursor: pointer;" />
                                            <br/>
                                            <img border="0" src="/images/reg/left.jpg" alt="" onclick="moveMultiple('q2', 'q1', 'q2', 'update'); return false;" style="cursor: pointer;" />
                                        </div>

                                        <div class="col-md-5">
                                            <select id="q2" multiple="true" size="5" class="form-control form-control-sm"></select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="db[marketInfo]" id="update" value="" />
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>

                                            <div style="">
                                                <div class="text-left">

                                                    <div class=" skin skin-square pb-2">

                        <div class="form-group">        
                                                            <label for="approve">
                                <input type="checkbox" class="form-check-input" name="approve" id="approve" /> 
                                    <?php echo lang('I have read and accepted the'); ?><a href="#" data-toggle="modal" data-target="#tersandconditions"><?php echo lang('Terms & Conditions') ?></a> <?php echo ((!empty($set->extraAgreement2Name) && !empty($set->extraAgreement2Link)) ? lang('&') . ' ' . '<a href="' . $set->extraAgreement2Link . '" target="_blank">' . lang($set->extraAgreement2Name) . '</a> ' : '') ?> <?php echo ((!empty($set->extraAgreement3Name) && !empty($set->extraAgreement3Link)) ? lang('&') . ' ' . '<a href="' . $set->extraAgreement3Link . '" target="_blank">' . lang($set->extraAgreement3Name) . '</a> ' : '') ?>
                                </label>
                            </div>
                        <div class="form-group">                  
                                                            <label for="newsletter">
                            <input type="checkbox" name="newsletter" <?php echo (($newsletter || $set->affiliateNewsletterCheckboxValue == 1) ? 'checked="checked"' : '') ?>/> 
                                                                <span class="newsletterCheckboxCaption"><?= lang($set->newsletterCheckboxCaption); ?></span>
                            </label>
                        </div>


                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-block" id="btnRegister">Create Account</button>
                                                    <div class="b-not-account text-center mt-2">
                                                        <p>Already have an account? <a href="#" id="toSignInPage"><?= lang('Sign In'); ?></a></p>
                        </div>
                    </div>
                </div>

            </div>
        </form>

                                    <form class="form-signin form-registration" id="RegisterFormComplete" style="display: none;width: 353px;">
            <div class="bg-place">
                <h4><?php echo lang('Thank you for registering with') . ' ' . $set->webTitle; ?></h4>
                <h4><?php echo lang('Your account be will activated as soon as it is approved'); ?></h4>
            </div>
        </form>

                                    <form class="form-signin form-registration" id="ForgotFormComplete" style="display: none;">
                                        <div class="bg-place text-center">
                                            <h4><?php echo lang('Password has sent to your inbox!') . ' ' . $set->webTitle; ?></h4>
                                            <h4><a href="/"><?php echo lang('Click here to login'); ?></a></h4>
                                        </div>
                                    </form>

                                </div>
                                
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <!-- Terms And Conditions -->
        <div class="modal fade bd-example-modal-lg" id="tersandconditions" tabindex="-1" role="dialog" aria-labelledby="tersandconditionsTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tersandconditionsTitle"><?= lang('Terms and conditions'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <iframe style="width:95%; height:272px!important; border: none; display: block;" src="<?= $set->terms_link; ?>"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function () {
                'use strict';
                window.addEventListener('load', function () {
                    // Fetch all the forms we want to apply custom Bootstrap validation styles to
                    var forms = document.getElementsByClassName('needs-validation');
                    // Loop over them and prevent submission
                    var validation = Array.prototype.filter.call(forms, function (form) {
                        form.addEventListener('submit', function (event) {
                            if (form.checkValidity() === false) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                            form.classList.add('was-validated');
                        }, false);
                    });
                }, false);
            })();
        </script>

        <!-- BEGIN VENDOR JS-->
        <script src="/app-assets/vendors/js/vendors.min.js"></script>
        <!-- BEGIN VENDOR JS-->
        <!-- BEGIN PAGE VENDOR JS-->
        <!-- <script src="app-assets/vendors/js/ui/jquery.sticky.js"></script> -->
        <script src="/app-assets/vendors/js/forms/icheck/icheck.min.js"></script>
        <!-- END PAGE VENDOR JS-->
        <!-- BEGIN ROBUST JS-->
        <script src="/app-assets/js/core/app-menu.js"></script>
        <script src="/app-assets/js/core/app.js"></script>
        <!-- END ROBUST JS-->
        <!-- BEGIN PAGE LEVEL JS-->
        <script src="/app-assets/js/scripts/forms/checkbox-radio.js"></script>
        <script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
        <script src="/app-assets/js/scripts/forms/select/form-select2.js"></script>
        <!-- END PAGE LEVEL JS-->

        <script src="/assets/js/scripts/phonecode-master/jquery-ui-1.10.4.custom.min.js"></script>
        <script src="/assets/js/scripts/phonecode-master/counties.js"></script>
        <script src="/assets/js/scripts/phonecode-master/phonecode.js"></script>
        <script>
            $(function(){
                $('#phone').phonecode({
                    preferCo: 'en'
                });
            });
        </script>

    </body>
</html>
