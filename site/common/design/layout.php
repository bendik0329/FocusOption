<?php
if (!defined('SITE_INCLUDE_START')) {
    die('Access denied');
}
?>
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description"
              content=" Afiliate admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template.">
        <meta name="keywords" content="admin template">
        <meta name="author" content=" Afiliate">
        <title><?= $pageTitle; ?></title>
        <base href="<?= $set->webAddress; ?>" />

        <!-- Styles -->
        <link href="<?= $SSLprefix; ?>css/style.css" rel="stylesheet" type="text/css" />
        <link href="<?= $SSLprefix . '' . $set->getFolder[1]; ?>/css/style.css?v=<?=date('ymdHi')?>" rel="stylesheet" type="text/css" />
        <link href="<?= $SSLprefix; ?>css/dropdown.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="<?= $SSLprefix; ?>css/colorbox.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="<?= $SSLprefix; ?>css/default.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="<?= $SSLprefix; ?>css/tooltips.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="//fonts.googleapis.com/css?family=Lato:400,700" rel="stylesheet" type="text/css">



        <?= $zopimChat; ?>
        <?= $set->analyticsCode ?>




        <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700&display=swap" rel="stylesheet">

        <?php
        echo ($set->faviconPath && strpos($set->faviconPath, "/tmp") === false ? '<link rel="shortcut icon" href="' . ($set->faviconPath) . '"  />' : '');
        ?>

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CMuli:300,400,500,700" rel="stylesheet">
        <!-- BEGIN VENDOR CSS-->
        <link rel="stylesheet" type="text/css" href="/app-assets/css/vendors.css">
        <!-- END VENDOR CSS-->
        <!-- BEGIN ROBUST CSS-->
        <link rel="stylesheet" type="text/css" href="/app-assets/css/app.css">
        <!-- END ROBUST CSS-->
        <!-- BEGIN Page Level CSS-->
        <link rel="stylesheet" type="text/css" href="/app-assets/css/core/menu/menu-types/vertical-menu.css">
        <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/forms/selects/select2.min.css">
        <!-- END Page Level CSS-->
        <!-- BEGIN Custom CSS-->
        <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
        <!-- END Custom CSS-->



        <style>
            @import url('https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700&display=swap');
            @import url('https://fonts.googleapis.com/css?family=Roboto:300,400,500,700');
            .headerLogo {
                background: none;
            }
        </style>

        <?php
        if (!empty($set->metaTrackingHeader && $location == 'mainHomePage')) {
            echo $set->metaTrackingHeader;
        }
        ?>

        
        
        <!-- BEGIN VENDOR JS-->
        <script src="/app-assets/vendors/js/vendors.min.js"></script>
        <!-- BEGIN VENDOR JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/jquery.colorbox-min.js" integrity="sha512-DAVSi/Ovew9ZRpBgHs6hJ+EMdj1fVKE+csL7mdf9v7tMbzM1i4c/jAvHE8AhcKYazlFl7M8guWuO3lDNzIA48A==" crossorigin="anonymous"></script>
        <!-- BEGIN PAGE VENDOR JS-->
        <script src="/app-assets/vendors/js/menu/jquery.mmenu.all.min.js"></script>
        <!-- END PAGE VENDOR JS-->
        
    </head>

    <body class="vertical-layout vertical-menu 2-columns   menu-expanded fixed-navbar" data-open="click"
          data-menu="vertical-menu" data-col="2-columns">

        <!-- fixed-top-->
        <nav class="header-navbar navbar-expand-md navbar navbar-with-menu fixed-top navbar-semi-dark navbar-shadow">
            <div class="navbar-wrapper">

                <div class="navbar-header">
                    <ul class="nav navbar-nav flex-row">
                        <li class="nav-item mobile-menu d-md-none mr-auto"><a
                                class="nav-link nav-menu-main menu-toggle hidden-xs" href="/<?= ($set->getFolder[1] != "index.php" ? $set->getFolder[1] : '') ?>"><i
                                    class="ft-menu font-large-1"></i></a></li>
                        <li class="nav-item">
                            <a class="navbar-brand" href="/<?= ($set->getFolder[1] != "index.php" ? $set->getFolder[1] : '') ?>">
                                <h3 class="brand-text"><?= $theLogoText ?></h3>
                            </a>

                        </li>
                        <li class="nav-item d-md-none"><a class="nav-link open-navbar-container" data-toggle="collapse"
                                                          data-target="#navbar-mobile"><i class="fa fa-ellipsis-v"></i></a></li>
                    </ul>
                </div>


                <div class="navbar-container content">
                    <div class="collapse navbar-collapse" id="navbar-mobile">
                        <ul class="nav navbar-nav mr-auto float-left">
                            <li class="nav-item d-none d-md-block">
                                <a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu"></i></a>
                            </li>

                        </ul>
                        <ul class="nav navbar-nav float-right">
                            <li class="dropdown dropdown-user nav-item">
                                <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                    <span class="avatar avatar-online">
                                        <img src="/images/profile_img.png" alt="avatar">
                                        <i></i>
                                    </span>
                                    <span class="user-name"><?= '[#' . $set->userInfo['id'] . '] ' . lang('Welcome Back') . ' <b>' . $set->userInfo['first_name'] . '</b>'; ?></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="/affiliate/account.php"><i class="ft-user"></i> Edit Profile</a>
                                    <div class="dropdown-divider"></div><a class="dropdown-item" href="/affiliate/?act=logout"><i class="ft-power"></i> Logout</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- ////////////////////////////////////////////////////////////////////////////2-->


        <!-- main menu-->
        <div class="main-menu menu-fixed menu-dark menu-accordion    menu-shadow " data-scroll-to-active="true">
            <!-- main menu header-->
            <!-- / main menu header-->
            <!-- main menu content-->
            <div class="main-menu-content">


                <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                    <?php
                    $layout_sidebar_menu_title_icons = [
                        'icon-home',
                        'fa fa-filter',
                        'fa fa-list-ul',
                        'fa fa-users',
                        'fa fa-envelope-o',
                        'fa fa-briefcase',
                        'fa fa-question-circle-o',
                    ];
                    $layout_sidebar_menu_data_array = adminMenu(true);
                    for ($i = 0; $i <= count($layout_sidebar_menu_data_array['group']) - 1; $i++) {
                        ?>

                        <li class="nav-item">
                            <?php
                            preg_match('/<a (?:.*?)href="(.+)">(.+)<\/a>/', $layout_sidebar_menu_data_array['group'][$i], $layout_sidebar_menu_title_matches);
                            ?>
                            <a href="<?= $layout_sidebar_menu_title_matches[1] ?>">
                                <i class="<?= $layout_sidebar_menu_title_icons[$i] ?>"></i>
                                <span class="menu-title"><?= $layout_sidebar_menu_title_matches[2] ?></span>
                            </a>
                            <?php
                            if (count($layout_sidebar_menu_data_array['list'][$i]) > 0) {
                                ?>
                                <ul class="menu-content">
                                    <?php
                                    for ($b = 0; $b <= count($layout_sidebar_menu_data_array['list'][$i]) - 1; $b++) {
                                        preg_match('/<a (?:.*?)href="(.+)">(.+)<\/a>/', $layout_sidebar_menu_data_array['list'][$i][$b], $layout_sidebar_menu_matches);
                                        ?>
                                        <li>
                                            <a class="menu-item" href="<?= $layout_sidebar_menu_matches[1]; ?>"><?= $layout_sidebar_menu_matches[2]; ?></a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                                <?php
                            }
                            ?>
                        </li>
                        <?php
                    }
                    unset($layout_sidebar_menu_title_matches);
                    unset($layout_sidebar_menu_matches);
                    unset($layout_sidebar_menu_title_icons);
                    unset($layout_sidebar_menu_data_array);
                    ?>
                </ul>
            </div>
            <!-- /main menu content-->
        </div>
        <!-- / main menu-->

        <div class="app-content content">
            <div class="content-wrapper">



                <div class="content-header row">
                    <div class="content-header-left col-6 mb-2 breadcrumb-new">
                        <h3 class="content-header-title mb-0 d-inline-block"><?=$pageTitle?></h3>
                            
                        <div class="row breadcrumbs-top d-inline-block">
                            <div class="breadcrumb-wrapper col-12">
                              <?=$set->pageTitle?>
                            </div>
                        </div>
                            
                            
                       
                    </div>
                    <div class="col-6 mb-2 breadcrumb-new content-right">
                        <div class="engine"><?=$set->rightBar;?></div>    
                    </div>
                </div>
                <div class="content-body">
                    
                    <?=$set->content;?>

                </div>

            </div>
        </div>


        <!-- ////////////////////////////////////////////////////////////////////////////-->



        






<!-- BEGIN ROBUST JS-->
        <script src="/app-assets/js/core/app-menu.js"></script>
        <script src="/app-assets/js/core/app.js"></script>
        <!-- END ROBUST JS-->
        <!-- BEGIN PAGE LEVEL JS-->
        <script src="/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
        <script src="/app-assets/js/scripts/forms/select/form-select2.js"></script>

        <!-- END PAGE LEVEL JS-->

        <script src="https://code.jquery.com/jquery-migrate-1.2.1.js"></script>

        <!-- Toastr -->
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script>toastr.options = {"closeButton": true, "debug": false, "newestOnTop": true, "progressBar": true, "positionClass": "toast-top-right", "preventDuplicates": false, "onclick": null, "showDuration": "300", "hideDuration": "1000", "timeOut": "5000", "extendedTimeOut": "1000", "showEasing": "swing", "hideEasing": "linear", "showMethod": "fadeIn", "hideMethod": "fadeOut"};</script>


        <script type="text/javascript">
            $(document).ready(function () {
                if ($("body").height() < $(window).height()) {
                    $("#bottom_table").css("position", "fixed");
                    $("#bottom_table").css("bottom", "0");
                }

                $("a[rel=fancybox]").fancybox({
                    openEffect: "elastic",
                    closeEffect: "elastic",
                    openSpeed: 400,
                    closeSpeed: 400,
                    showNavArrows: false
                });
            });
        </script>

        <!-- Fancybox -->
        <link href="<?= $SSLprefix; ?>fancybox/fancybox.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?= $SSLprefix; ?>fancybox/fancybox.js"></script>
        <link href="<?= $SSLprefix; ?>fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?= $SSLprefix; ?>fancybox/jquery.fancybox.js"></script>    

        <!-- Sorter table -->
        <?= ($set->sortTable ? (empty($set->sortTableCssDisable) ? '<link rel="stylesheet" href="' . $SSLprefix . 'pages/css/sort_table.css" type="text/css" media="print, projection, screen" />' : '') . '
    <script type="text/javascript" src="' . $SSLprefix . 'pages/js/__jquery.tablesorter.js"></script>
    <script type="text/javascript" src="' . $SSLprefix . 'pages/js/jquery.tablesorter.pager.js"></script>
    ' . ($set->sortTableScript ? '<script type="text/javascript">
    $(function() {
            $("table.tablesorter")
                    .tablesorter({
                            widthFixed: true,
                            widgets: [\'zebra\'],
                            //dateFormat : "mmddyyyy",
                            dateFormat : "uk",

                            headers: {
                              1: { sorter: "shortDate", dateFormat: "ddmmyyyy" },
                            }
                    })
            .tablesorterPager({container: $("#pager"),size:' . $set->rowsNumberAfterSearch . '});
    });
    </script>' : '') : ''); ?>
        
        <script src="/app-assets/js/scripts/modal/components-modal.min.js"></script>
        

    </body>

</html>
