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

$theLogoText = (!empty($set->logoPath) && strpos($set->logoPath, "/tmp") === false ? '<img class="headerLogo mb-4" style="max-width: 330px;" border="0" src="' . $logoPath . '" alt="' . $set->dashBoardMainTitle . '" />' : $altTextLogo);
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
        ?>

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
                                    
                                    <form class="form-horizontal form-simple needs-validation" novalidate  id="SignInFormManager">
                                        <div class="header-sing  text-center mb-2">
                                            <h1>
                                                <?= lang('Sign In to your'); ?><br>
                                                <b><?= lang('Manager Account'); ?></b>
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
                                            
                                            <?= ($set->multi ? '<div class="form-group"><select onchange="langRedirect(this.value)" id="selectLanguage" name="lang" class="form-control form-control-sm margin-botom-10"><option value="">' . lang('Choose your language') . '</option>' . listMulti($lang) . '</select></div>' : ''); ?>

                                            <button id="SignInBtnManager" type="submit" class="btn btn-primary btn-block"><?= lang('Sign In'); ?></button>

                                        </div>
                                    </form>
                                        </div>
                            </div>
                        </div>
                    </section>
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
                                        
</body>
</html>