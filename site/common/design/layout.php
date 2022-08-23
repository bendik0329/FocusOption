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
   <meta name="description" content=" Afiliate admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template.">
   <meta name="keywords" content="admin template">
   <meta name="author" content=" Afiliate">
   <title><?= $pageTitle; ?></title>
   <base href="<?= $set->webAddress; ?>" />

   <!-- Styles -->
   <link href="<?= $SSLprefix; ?>css/style.css" rel="stylesheet" type="text/css" />
   <link href="<?= $SSLprefix . '' . $set->getFolder[1]; ?>/css/style.css?v=<?= date('ymdHi') ?>" rel="stylesheet" type="text/css" />
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

<body class="vertical-layout vertical-menu 2-columns   menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-col="2-columns">

   <!-- fixed-top-->
   <nav class="header-navbar navbar-expand-md navbar navbar-with-menu fixed-top navbar-semi-dark navbar-shadow">
      <div class="navbar-wrapper">

         <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
               <li class="nav-item mobile-menu d-md-none mr-auto toggle-bar-icon"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="/<?= ($set->getFolder[1] != "index.php" ? $set->getFolder[1] : '') ?>"><i class="ft-menu font-large-1"></i></a></li>
               <li class="nav-item">
                  <a class="navbar-brand" href="/<?= ($set->getFolder[1] != "index.php" ? $set->getFolder[1] : '') ?>">
                     <h3 class="brand-text"><?= $theLogoText ?></h3>
                  </a>

               </li>
               <li class="nav-item d-md-none"><a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="fa fa-ellipsis-v"></i></a></li>
            </ul>
         </div>


         <div class="navbar-container content">
            <div class="collapse navbar-collapse" id="navbar-mobile">
               <ul class="nav navbar-nav mr-auto float-left ">
                  <li class="nav-item d-none d-md-block toggle-bar-icon">
                     <a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu"></i></a>
                  </li>

               </ul>
               <ul class="nav navbar-nav float-right">
                  <li class="price nav-item"><span class="commision_dashboard"></span></li>
                  <li class=" notification nav-item">
                     <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="24" viewBox="0 0 20 24">
                           <defs>
                              <style>
                                 .a {
                                    fill: #282560;
                                 }
                              </style>
                           </defs>
                           <path class="a" d="M21.379,16.913A6.7,6.7,0,0,1,19,11.788V9a7.006,7.006,0,0,0-6-6.92V1a1,1,0,0,0-2,0V2.08A7,7,0,0,0,5,9v2.788a6.705,6.705,0,0,1-2.388,5.133A1.75,1.75,0,0,0,3.75,20h16.5a1.75,1.75,0,0,0,1.129-3.087Z" transform="translate(-2)"></path>
                           <path class="a" d="M12,24a3.756,3.756,0,0,0,3.674-3H8.326A3.756,3.756,0,0,0,12,24Z" transform="translate(-2)"></path>
                        </svg>
                     </a>
                  </li>
                  <li class="dropdown dropdown-user nav-item">
                     <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                        <!-- <span class="avatar avatar-online">
                                        <img src="/images/profile_img.png" alt="avatar">
                                        <i></i>
                                    </span> -->
                        <span class="user-name text-capitalize"><?= '  <b>' . $set->userInfo['first_name'] . ' ' . $set->userInfo['id'] . '</b>'; ?></span>
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
   <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow">
      <!-- main menu header-->
      <!-- / main menu header-->
      <!-- main menu content-->
      <div class="main-menu-content">


         <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <?php
            $layout_sidebar_menu_title_icons = [
               '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <defs>
                           <style>
                              .a {
                                 fill: #8f8f8f;
                              }
                           </style>
                        </defs>
                        <path class="a"
                           d="M12.231,3.769A.772.772,0,0,0,11.462,3H3.769A.772.772,0,0,0,3,3.769v7.693a.772.772,0,0,0,.769.769h7.693a.772.772,0,0,0,.769-.769Z"
                           transform="translate(-3 -3)"></path>
                        <path class="a"
                           d="M26.231,3.769A.772.772,0,0,0,25.462,3H17.769A.772.772,0,0,0,17,3.769v7.693a.772.772,0,0,0,.769.769h7.693a.772.772,0,0,0,.769-.769Z"
                           transform="translate(-6.231 -3)"></path>
                        <path class="a"
                           d="M17.769,26.231h7.693a.772.772,0,0,0,.769-.769V17.769A.772.772,0,0,0,25.462,17H17.769a.772.772,0,0,0-.769.769v7.693A.772.772,0,0,0,17.769,26.231Z"
                           transform="translate(-6.231 -6.231)"></path>
                        <path class="a"
                           d="M11.462,17H3.769A.772.772,0,0,0,3,17.769v7.693a.772.772,0,0,0,.769.769h7.693a.772.772,0,0,0,.769-.769V17.769A.772.772,0,0,0,11.462,17Z"
                           transform="translate(-3 -6.231)"></path>
                     </svg>',
               '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <defs>
                           <style>
                              .a {
                                 fill: #8f8f8f;
                              }
                           </style>
                        </defs>
                        <g transform="translate(-0.373 -0.001)">
                           <path class="a"
                              d="M138.431,9.589a1.668,1.668,0,0,1,1.927.3l.675-.673A4.75,4.75,0,0,0,147.3,3.98a.434.434,0,0,0-.734-.237l-1,1a2.087,2.087,0,1,1-2.956-2.948l1-1a.432.432,0,0,0-.237-.732,4.746,4.746,0,0,0-5.223,6.326l-1.464,1.46Z"
                              transform="translate(-126.99 0)"></path>
                           <path class="a"
                              d="M8.773,155.643l-1.736-1.732L.965,159.967a2.019,2.019,0,0,0,2.859,2.852L9.058,157.6A1.657,1.657,0,0,1,8.773,155.643Z"
                              transform="translate(0 -143.408)"></path>
                           <path class="a"
                              d="M12.888,27.256a.637.637,0,0,0-.9,0l-.362.362L5.17,21.177l.3-.3a.634.634,0,0,0-.073-.959L2.765,17.982a.637.637,0,0,0-.827.062L.819,19.159a.633.633,0,0,0-.062.825L2.7,22.612a.637.637,0,0,0,.962.072l.3-.3,6.457,6.44-.388.387a.633.633,0,0,0,0,.9l5.956,5.941A2.019,2.019,0,0,0,18.845,33.2Z"
                              transform="translate(-0.243 -16.638)"></path>
                        </g>
                     </svg>',
               '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <defs>
                           <style>
                              .a {
                                 fill: #8f8f8f;
                              }
                           </style>
                        </defs>
                        <g transform="translate(0 0)">
                           <path class="a"
                              d="M32.758,9.375a1.749,1.749,0,0,0,1.217-.494l1.734.867a1.722,1.722,0,0,0-.022.213,1.758,1.758,0,0,0,3.516,0,1.736,1.736,0,0,0-.176-.753L41.38,6.855a1.736,1.736,0,0,0,.753.176,1.76,1.76,0,0,0,1.758-1.758,1.731,1.731,0,0,0-.088-.521L45.847,3.22a1.757,1.757,0,1,0-.785-1.462,1.731,1.731,0,0,0,.088.521L43.106,3.811a1.75,1.75,0,0,0-2.555,2.215L38.2,8.379a1.727,1.727,0,0,0-1.97.318L34.494,7.83a1.722,1.722,0,0,0,.022-.213,1.758,1.758,0,1,0-1.758,1.758Zm0,0"
                              transform="translate(-29.789)"></path>
                           <path class="a"
                              d="M19.414,162.969h-.625V150.586A.586.586,0,0,0,18.2,150H15.859a.586.586,0,0,0-.586.586v12.383H14.1V154.1a.586.586,0,0,0-.586-.586H11.172a.586.586,0,0,0-.586.586v8.867H9.414v-4.18a.586.586,0,0,0-.586-.586H6.484a.586.586,0,0,0-.586.586v4.18H4.727v-6.523a.586.586,0,0,0-.586-.586H1.8a.586.586,0,0,0-.586.586v6.523H.586a.586.586,0,0,0,0,1.172H19.414a.586.586,0,0,0,0-1.172Zm0,0"
                              transform="translate(0 -144.141)"></path>
                        </g>
                     </svg>',
               '<svg xmlns="http://www.w3.org/2000/svg" width="16.686" height="20" viewBox="0 0 16.686 20">
                        <defs>
                           <style>
                              .a {
                                 fill: #8f8f8f;
                              }
                           </style>
                        </defs>
                        <g transform="translate(0 0)">
                           <path class="a"
                              d="M91.849,9.634a4.661,4.661,0,0,0,3.406-1.411,4.661,4.661,0,0,0,1.411-3.406,4.662,4.662,0,0,0-1.411-3.406,4.816,4.816,0,0,0-6.812,0,4.661,4.661,0,0,0-1.411,3.406,4.661,4.661,0,0,0,1.411,3.406A4.662,4.662,0,0,0,91.849,9.634Zm0,0"
                              transform="translate(-83.632)"></path>
                           <path class="a"
                              d="M16.645,253.034a11.9,11.9,0,0,0-.162-1.264,9.957,9.957,0,0,0-.311-1.271,6.276,6.276,0,0,0-.522-1.185,4.472,4.472,0,0,0-.788-1.026,3.473,3.473,0,0,0-1.131-.711,3.911,3.911,0,0,0-1.444-.261,1.466,1.466,0,0,0-.783.332c-.235.153-.509.33-.816.526a4.673,4.673,0,0,1-1.055.465,4.1,4.1,0,0,1-2.581,0A4.662,4.662,0,0,1,6,248.174c-.3-.194-.578-.371-.816-.526a1.464,1.464,0,0,0-.783-.332,3.905,3.905,0,0,0-1.444.262,3.47,3.47,0,0,0-1.132.711,4.473,4.473,0,0,0-.787,1.026A6.288,6.288,0,0,0,.513,250.5a9.98,9.98,0,0,0-.311,1.27,11.856,11.856,0,0,0-.162,1.264c-.027.383-.04.78-.04,1.181a3.321,3.321,0,0,0,.986,2.513,3.551,3.551,0,0,0,2.542.927h9.63a3.55,3.55,0,0,0,2.541-.927,3.319,3.319,0,0,0,.986-2.513c0-.4-.014-.8-.04-1.181Zm0,0"
                              transform="translate(0 -237.655)"></path>
                        </g>
                     </svg>',

               '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <defs>
                           <style>
                              .a {
                                 fill: #8f8f8f;
                              }
                           </style>
                        </defs>
                        <path class="a"
                           d="M4.167,0H.833A.834.834,0,0,0,0,.833V4.167A.834.834,0,0,0,.833,5H4.167A.834.834,0,0,0,5,4.167V.833A.834.834,0,0,0,4.167,0Z">
                        </path>
                        <path class="a"
                           d="M20.5,0H8.833A.834.834,0,0,0,8,.833V4.167A.834.834,0,0,0,8.833,5H20.5a.834.834,0,0,0,.833-.833V.833A.834.834,0,0,0,20.5,0Z"
                           transform="translate(-1.333)"></path>
                        <path class="a"
                           d="M4.167,9H.833A.834.834,0,0,0,0,9.833v3.333A.834.834,0,0,0,.833,14H4.167A.834.834,0,0,0,5,13.167V9.833A.834.834,0,0,0,4.167,9Z"
                           transform="translate(0 -1.5)"></path>
                        <path class="a"
                           d="M20.5,9H8.833A.834.834,0,0,0,8,9.833v3.333A.834.834,0,0,0,8.833,14H20.5a.834.834,0,0,0,.833-.833V9.833A.834.834,0,0,0,20.5,9Z"
                           transform="translate(-1.333 -1.5)"></path>
                        <path class="a"
                           d="M4.167,18H.833A.834.834,0,0,0,0,18.833v3.333A.834.834,0,0,0,.833,23H4.167A.834.834,0,0,0,5,22.167V18.833A.834.834,0,0,0,4.167,18Z"
                           transform="translate(0 -3)"></path>
                        <path class="a"
                           d="M20.5,18H8.833A.834.834,0,0,0,8,18.833v3.333A.834.834,0,0,0,8.833,23H20.5a.834.834,0,0,0,.833-.833V18.833A.834.834,0,0,0,20.5,18Z"
                           transform="translate(-1.333 -3)"></path>
                     </svg>',
               '<svg xmlns="http://www.w3.org/2000/svg" width="15.223" height="20" viewBox="0 0 15.223 20">
                        <defs>
                           <style>
                              .a {
                                 fill: #8f8f8f;
                              }
                           </style>
                        </defs>
                        <g transform="translate(-50.526)">
                           <g transform="translate(50.526)">
                              <path class="a"
                                 d="M65.252.514a.837.837,0,0,0-.911.139L63.029,1.814a.094.094,0,0,1-.125,0L61.119.217a.85.85,0,0,0-1.135,0L58.2,1.812a.1.1,0,0,1-.126,0L56.291.217a.85.85,0,0,0-1.135,0l-1.786,1.6a.1.1,0,0,1-.126,0L51.932.652a.844.844,0,0,0-1.405.637V18.71a.846.846,0,0,0,1.408.637l1.312-1.162a.094.094,0,0,1,.125,0l1.785,1.6a.85.85,0,0,0,1.135,0l1.784-1.6a.1.1,0,0,1,.126,0l1.784,1.6a.85.85,0,0,0,1.135,0l1.786-1.6a.1.1,0,0,1,.126,0l1.313,1.162a.844.844,0,0,0,1.4-.637V1.29A.837.837,0,0,0,65.252.514Zm-11.6,6.271h4.708a.378.378,0,1,1,0,.756H53.656a.378.378,0,0,1,0-.756Zm8.963,6.429H53.656a.378.378,0,0,1,0-.756h8.963a.378.378,0,0,1,0,.756Zm0-2.837H53.656a.378.378,0,1,1,0-.756h8.963a.378.378,0,1,1,0,.756Z"
                                 transform="translate(-50.526 0)"></path>
                           </g>
                        </g>
                     </svg>',
               '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" style="
                     ">
                    <defs>
                       <style>
                          .a {
                             fill: #8f8f8f;
                          }
                       </style>
                    </defs>
                    <g transform="translate(7.057 14.114)" style="
                        fill: #f00;
                        ">
                       <rect class="a" width="5.886" height="5.886" style="
                           "></rect>
                    </g>
                    <g transform="translate(7.057 7.057)">
                       <rect class="a" width="5.886" height="5.886"></rect>
                    </g>
                    <g transform="translate(14.114)">
                       <rect class="a" width="5.886" height="5.885"></rect>
                    </g>
                    <g transform="translate(0 7.057)">
                       <rect class="a" width="5.886" height="5.886"></rect>
                    </g>
                    <g transform="translate(14.114 7.057)">
                       <rect class="a" width="5.886" height="5.886"></rect>
                    </g>
                    <g transform="translate(0 14.114)">
                       <rect class="a" width="5.886" height="5.886"></rect>
                    </g>
                    <g transform="translate(14.114 14.114)">
                       <rect class="a" width="5.886" height="5.886"></rect>
                    </g>
                    <rect class="a" width="5.886" height="5.885"></rect>
                    <g transform="translate(7.057)">
                       <rect class="a" width="5.886" height="5.885"></rect>
                    </g>
                 </svg>',
               '<svg xmlns="http://www.w3.org/2000/svg" width="15.755" height="20" viewBox="0 0 15.755 20"><defs><style>.a{fill:#8f8f8f;}</style></defs><g transform="translate(-54.334)"><path class="a" d="M355.333,413.569l3.9-3.9h-3.9Z" transform="translate(-289.241 -393.664)"/><path class="a" d="M70.089,14.831V0H54.334V20H64.92V14.831ZM59.712,3.581h5V4.753h-5ZM57.42,6.5H67V7.669H57.42Zm0,2.5H67v1.172H57.42ZM58.65,16H57.42V14.831h1.23Zm3.561,0H59.822V14.831h2.389ZM57.42,12.669V11.5H67v1.172Z"/></g></svg>',
               '<svg xmlns="http://www.w3.org/2000/svg" width="17.801" height="20" viewBox="0 0 17.801 20">
                        <defs>
                           <style>
                              .a {
                                 fill: #8f8f8f;
                              }
                           </style>
                        </defs>
                        <g transform="translate(-25.274)">
                           <g transform="translate(25.274)">
                              <g transform="translate(0)">
                                 <path class="a"
                                    d="M39.819,281.051a4.349,4.349,0,0,1-3.521,1.8H34.27a1.621,1.621,0,0,1-1.523-1.067,5.143,5.143,0,0,1-.819-.324,5.517,5.517,0,0,1-1.53-1.133,6.533,6.533,0,0,0-5.124,6.379v.408a1.016,1.016,0,0,0,1.016,1.016H42.058a1.016,1.016,0,0,0,1.016-1.016V286.7A6.528,6.528,0,0,0,39.819,281.051Z"
                                    transform="translate(-25.274 -268.126)"></path>
                                 <path class="a"
                                    d="M92.765,10.1a1.157,1.157,0,0,0,1.014-.6l.016.042,0,.013a4.517,4.517,0,0,0,2.547,2.681,1.619,1.619,0,0,1,1.372-.759h2.028a1.136,1.136,0,0,0,.556-.149,2.292,2.292,0,0,0,.655-.757,5.848,5.848,0,0,0,.507-1.072,1.163,1.163,0,0,0,.32.369v.5a2.041,2.041,0,0,1-2.038,2.038H97.718a.694.694,0,1,0,0,1.389h2.028a3.431,3.431,0,0,0,3.427-3.427v-.5a1.155,1.155,0,0,0,.463-.926v-2.9a1.155,1.155,0,0,0-.48-.938,5.551,5.551,0,0,0-11.067,0,1.155,1.155,0,0,0-.48.938v2.9A1.158,1.158,0,0,0,92.765,10.1Zm4.857-8.713a4.168,4.168,0,0,1,4.142,3.75,1.161,1.161,0,0,0-.325.4,3.962,3.962,0,0,0-7.631-.007l0,.007a1.161,1.161,0,0,0-.325-.4A4.168,4.168,0,0,1,97.622,1.389Z"
                                    transform="translate(-88.722)"></path>
                              </g>
                           </g>
                        </g>
                     </svg>',
               '<svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="512.000000pt" height="512.000000pt"
                     viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                     <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#8F8F8F" stroke="none">
                        <path class="a"
                           d="M2315 5109 c-800 -83 -1501 -518 -1927 -1196 -604 -961 -490 -2237 274 -3068 425 -462 951 -737 1583 -827 119 -17 512 -16 635 1 622 86 1148 360 1572 820 349 378 572 862 650 1406 17 118 17 512 0 630 -59 416 -191 769 -410 1099 -92 140 -185 254 -315 385 -399 404 -893 653 -1462 737 -123 18 -478 26 -600 13z m1175 -1189 l0 -110 -825 0 -825 0 0 -1250 0 -1250 825 0 825 0 0 -110 0 -110 -930 0 -930 0 0 1470 0 1470 930 0 930 0 0 -110z m-25 -1770 l-410 -410 -75 75 -75 75 280 280 280 280 -645 2 -645 3 0 105 0 105 645 3 645 2 -280 280 -280 280 75 75 75 75 410 -410 410 -410 -410 -410z">
                        </path>
                     </g>
                  </svg>'
            ];
            $layout_sidebar_menu_data_array = adminMenu(true);
            for ($i = 0; $i <= count($layout_sidebar_menu_data_array['group']) - 1; $i++) {
            ?>
               <?php
               preg_match('/<a (?:.*?)href="(.+)">(.+)<\/a>/', $layout_sidebar_menu_data_array['group'][$i], $layout_sidebar_menu_title_matches);
               $active = '';
               if ((trim($set->SSLprefix . ltrim($_SERVER['REQUEST_URI'], '/'))) ===  ($layout_sidebar_menu_title_matches[1])) {
                  $active = 'active';
               } else {
                  $active = 'inactive';
               }
               ?>
               <li class="nav-item <?= $active ?>">
                  <a href="<?= $layout_sidebar_menu_title_matches[1] ?>">
                     <?php echo $layout_sidebar_menu_title_icons[$i]; ?>
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
                           <li class="<?php if ($set->SSLprefix . ltrim($_SERVER['REQUEST_URI'], '/') ==  $layout_sidebar_menu_matches[1]) echo 'active'; ?>">
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


            $count_signup = 'SELECT COUNT(*) FROM affiliates WHERE id=' . $set->userInfo['id'] . ' AND valid=1';


            ?>
         </ul>
      </div>
      <!-- /main menu content-->
   </div>
   <!-- / main menu-->

   <div class="app-content content">
      <div class="content-wrapper">
         <?php
         $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
         $parts = explode('/', $url);
         $new_url = $parts[4];
         if ($new_url == '') {
         ?>
            <div class="dashboard-title">
               <h2>Affiliate Program Dashboard</h2>
            </div>
            <div class="dashboard-aff-box">
               <div class="row">
                  <div class="col-lg-3 col-md-3 col-sm-6">
                     <div class="dashboard-aff-box-item">
                        <span>
                           <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
                              <defs>
                                 <style>
                                    .a {
                                       fill: #282560;
                                    }
                                 </style>
                              </defs>
                              <g transform="translate(14.247 14.246)">
                                 <path class="a" d="M201.143,207.755l-6.837-6.836-3.314,3.314a1.171,1.171,0,0,1-1.94-.458l-6.63-19.888a1.171,1.171,0,0,1,1.482-1.482l19.888,6.63a1.171,1.171,0,0,1,.458,1.94l-3.314,3.314,6.836,6.837a1.172,1.172,0,0,1,0,1.657l-4.971,4.971A1.171,1.171,0,0,1,201.143,207.755Z" transform="translate(-182.362 -182.344)" />
                              </g>
                              <g transform="translate(14.063)">
                                 <path class="a" d="M181.172,9.375A1.171,1.171,0,0,1,180,8.2V1.172a1.172,1.172,0,1,1,2.344,0V8.2A1.171,1.171,0,0,1,181.172,9.375Z" transform="translate(-180)" />
                              </g>
                              <g transform="translate(4.119 4.119)">
                                 <path class="a" d="M58.036,59.693,53.063,54.72a1.172,1.172,0,1,1,1.657-1.657l4.972,4.972a1.172,1.172,0,0,1-1.657,1.657Z" transform="translate(-52.72 -52.72)" />
                              </g>
                              <g transform="translate(4.119 19.034)">
                                 <path class="a" d="M53.063,250.607a1.172,1.172,0,0,1,0-1.657l4.972-4.972a1.172,1.172,0,0,1,1.657,1.657l-4.972,4.972A1.172,1.172,0,0,1,53.063,250.607Z" transform="translate(-52.72 -243.634)" />
                              </g>
                              <g transform="translate(19.034 4.119)">
                                 <path class="a" d="M243.976,59.694a1.172,1.172,0,0,1,0-1.657l4.972-4.972a1.172,1.172,0,1,1,1.657,1.657l-4.972,4.972A1.172,1.172,0,0,1,243.976,59.694Z" transform="translate(-243.633 -52.721)" />
                              </g>
                              <g transform="translate(0 14.063)">
                                 <path class="a" d="M8.2,182.344H1.172a1.172,1.172,0,1,1,0-2.344H8.2a1.172,1.172,0,0,1,0,2.344Z" transform="translate(0 -180)" />
                              </g>
                           </svg>
                        </span>
                        <div class="desc">
                           <p>Clicks</p>
                           <h3 id="clicks_dashboard"></h3>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-6">
                     <div class="dashboard-aff-box-item">
                        <span>
                           <svg xmlns="http://www.w3.org/2000/svg" width="39.755" height="40" viewBox="0 0 39.755 40">
                              <defs>
                                 <style>
                                    .a {
                                       fill: #282560;
                                    }
                                 </style>
                              </defs>
                              <g transform="translate(-32 -32)">
                                 <path class="a" d="M32.893,72H61.286V32.893A.893.893,0,0,0,60.393,32h-27.5a.893.893,0,0,0-.893.893V71.107A.893.893,0,0,0,32.893,72Zm6.792-14.535a.714.714,0,0,1-.515.249h-.027a.714.714,0,0,1-.505-.209l-2.143-2.143,1.01-1.01,1.6,1.6,3.784-4.414,1.085.93Zm4.286,5.714-4.286,5a.714.714,0,0,1-.515.249h-.027a.714.714,0,0,1-.505-.209l-2.143-2.143,1.01-1.01,1.6,1.6,3.784-4.414ZM49.143,67H45.571V65.571h3.571Zm7.143,0H50.571V65.571h5.714Zm0-3.571H45.571V62H56.286ZM45.571,56.286V54.857h3.571v1.429Zm10.714,0H50.571V54.857h5.714Zm0-3.571H45.571V51.286H56.286Zm2.143-19.286h1.429v1.429H58.429Zm-2.857,0H57v1.429H55.571ZM40.281,36.353,46.7,33.49a.714.714,0,0,1,.582,0l6.42,2.863a.714.714,0,0,1,.415.764l-.541,3.425,0,.017A11.688,11.688,0,0,1,48.9,47.934l-1.484,1.078a.714.714,0,0,1-.844,0l-1.557-1.147A11.692,11.692,0,0,1,40.4,40.531l0-.017-.535-3.4A.714.714,0,0,1,40.281,36.353Zm-6.138-2.924h4.286v1.429H34.143Zm0,2.857H37v1.429H34.143Zm0,17.5a2.5,2.5,0,0,1,2.5-2.5h3.214v1.429H36.643a1.073,1.073,0,0,0-1.071,1.071v3.571a1.073,1.073,0,0,0,1.071,1.071h3.571a1.073,1.073,0,0,0,1.071-1.071V57h1.429v.357a2.5,2.5,0,0,1-2.5,2.5H36.643a2.5,2.5,0,0,1-2.5-2.5Zm0,10.714a2.5,2.5,0,0,1,2.5-2.5h3.214v1.429H36.643A1.073,1.073,0,0,0,35.571,64.5v3.571a1.073,1.073,0,0,0,1.071,1.071h3.571a1.073,1.073,0,0,0,1.071-1.071v-.357h1.429v.357a2.5,2.5,0,0,1-2.5,2.5H36.643a2.5,2.5,0,0,1-2.5-2.5Z" />
                                 <path class="a" d="M376,52.375V90.556h2.194a.893.893,0,0,0,.88-.743l5.954-34.945a.893.893,0,0,0-.725-1.029Z" transform="translate(-313.286 -18.556)" />
                                 <path class="a" d="M141.357,76.546l1.137.837,1.061-.771a10.266,10.266,0,0,0,4.105-6.467l.454-2.876-5.629-2.51-5.629,2.51.449,2.848A10.269,10.269,0,0,0,141.357,76.546Zm-1.232-6.64,1.638,1.638,3.78-3.781,1.01,1.01-4.286,4.286a.714.714,0,0,1-1.01,0l-2.143-2.143Z" transform="translate(-95.494 -29.834)" />
                              </g>
                           </svg>
                        </span>
                        <div class="desc">
                           <p>Signups</p>
                           <h3 id="signups_dashboard"></h3>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-6">
                     <div class="dashboard-aff-box-item">
                        <span>
                           <svg xmlns="http://www.w3.org/2000/svg" width="36" height="40" viewBox="0 0 36 40">
                              <defs>
                                 <style>
                                    .a {
                                       fill: #282560;
                                    }
                                 </style>
                              </defs>
                              <g transform="translate(-24.991)">
                                 <g transform="translate(24.991 0)">
                                    <path class="a" d="M56.6,27.618a4.676,4.676,0,0,1-8.46-2.709V23.244H46.462a4.637,4.637,0,1,1,0-9.274h1.677V12.306a4.654,4.654,0,0,1,3.875-4.569A10.152,10.152,0,1,0,36.771,18.658C29.355,21.206,26.216,29.13,25.018,37.163A2.492,2.492,0,0,0,27.508,40H56.77a2.5,2.5,0,0,0,2.489-2.837A36.422,36.422,0,0,0,56.6,27.618ZM38.824,10.089a1.514,1.514,0,0,1-3.028,0,6.333,6.333,0,0,1,6.347-6.3,1.5,1.5,0,1,1,0,3.006A3.31,3.31,0,0,0,38.824,10.089Z" transform="translate(-24.991 0)" />
                                    <g transform="translate(19.72 10.471)">
                                       <path class="a" d="M268.753,129.6h-4.467v-4.467a1.832,1.832,0,0,0-1.834-1.834h0a1.839,1.839,0,0,0-1.843,1.834V129.6h-4.467a1.834,1.834,0,1,0,0,3.669h4.467v4.467a1.834,1.834,0,0,0,1.843,1.834h0a1.832,1.832,0,0,0,1.834-1.834V133.27h4.467a1.834,1.834,0,1,0,0-3.669Z" transform="translate(-254.307 -123.3)" />
                                    </g>
                                 </g>
                              </g>
                           </svg>
                        </span>
                        <div class="desc">
                           <p>Acquisition</p>
                           <h3>86</h3>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-6">
                     <div class="dashboard-aff-box-item">
                        <span>
                           <svg xmlns="http://www.w3.org/2000/svg" width="20" height="40" viewBox="0 0 20 40">
                              <defs>
                                 <style>
                                    .a {
                                       fill: #282560;
                                    }
                                 </style>
                              </defs>
                              <g transform="translate(-116.652 0)">
                                 <path class="a" d="M132.02,18.17a45.7,45.7,0,0,0-4.719-2.214,10.5,10.5,0,0,1-2.591-1.5,2.37,2.37,0,0,1,.558-4.155,4.411,4.411,0,0,1,1.586-.339,11.681,11.681,0,0,1,5.963,1.223c.941.471,1.252.322,1.57-.694.335-1.074.614-2.164.925-3.247a1.144,1.144,0,0,0-.71-1.512,14.765,14.765,0,0,0-3.763-1.165c-1.706-.273-1.706-.281-1.714-2.057-.008-2.5-.008-2.5-2.431-2.5-.351,0-.7-.008-1.052,0-1.132.033-1.323.24-1.355,1.421-.016.529,0,1.057-.008,1.594-.008,1.57-.016,1.545-1.467,2.09-3.508,1.322-5.676,3.8-5.907,7.766-.207,3.511,1.563,5.882,4.345,7.609a47.215,47.215,0,0,0,5.429,2.528,8.007,8.007,0,0,1,1.977,1.206,2.854,2.854,0,0,1-.646,4.924,6.294,6.294,0,0,1-3.484.471,15.148,15.148,0,0,1-5.309-1.644c-.981-.529-1.268-.388-1.6.71-.287.95-.542,1.908-.8,2.867-.343,1.289-.215,1.594.973,2.2a16.5,16.5,0,0,0,4.783,1.421c1.291.215,1.331.273,1.347,1.661.008.628.008,1.264.016,1.892.008.793.375,1.256,1.164,1.272.893.017,1.794.017,2.687-.008a1.043,1.043,0,0,0,1.108-1.2c0-.859.04-1.727.008-2.586a1.358,1.358,0,0,1,1.14-1.553A8.955,8.955,0,0,0,132.02,18.17Z" transform="translate(0 0)" />
                              </g>
                           </svg>
                        </span>
                        <div class="desc">
                           <p>Comission</p>
                           <h3 class="commision_dashboard"></h3>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

         <?php
         } else {
         ?>
            <div class="content-header row">

               <div class="content-header-left col-12 mb-2 breadcrumb-new">
                  <!-- <h3 class="content-header-title mb-0 d-inline-block"><?= $pageTitle ?></h3> -->
                  <div class="row breadcrumbs-top ">
                     <div class="breadcrumb-wrapper col-12">
                        <?= $set->pageTitle ?>
                     </div>
                  </div>
               </div>
            </div>
         <?php
         }
         ?>
         <!-- <div class="col-6 mb-2 breadcrumb-new content-right"> -->
         <div class="engine">
            <div class="row align-items-center">
               <div class="col-md-5">
                  <div class="chart-nav-tab"></div>
               </div>
               <div class="col-md-7">
                  <?= $set->rightBar; ?>
               </div>
            </div>

         </div>
         <!-- </div> -->




         <div class="content-body">

            <?= $set->content; ?>

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
   <script>
      toastr.options = {
         "closeButton": true,
         "debug": false,
         "newestOnTop": true,
         "progressBar": true,
         "positionClass": "toast-top-right",
         "preventDuplicates": false,
         "onclick": null,
         "showDuration": "300",
         "hideDuration": "1000",
         "timeOut": "5000",
         "extendedTimeOut": "1000",
         "showEasing": "swing",
         "hideEasing": "linear",
         "showMethod": "fadeIn",
         "hideMethod": "fadeOut"
      };
   </script>

   <script type="text/javascript">
      $(document).ready(function() {

         $('.chart-nav-tab').append($('.unslider-nav'));
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

         /*  Get value from hidden field commision and set data on dashboard. */
         var commision = $('#commision_hidden').val();
         $('.commision_dashboard').html('$ ' + Number(commision).toFixed(2));

         /*  Get value from hidden field clicks and set data on dashboard. */
         var clicks = $('#clicks_hidden').val();
         $('#clicks_dashboard').html(Number(clicks));

         /*  Get value from hidden field clicks and set data on dashboard. */
         var signups = $('#signups_hidden').val();
         $('#signups_dashboard').html(Number(signups));

      });
      // function setNavigation() {
      //    var path = window.location.pathname;
      //    path = path.replace(/\/$/, "");
      //    path = decodeURIComponent(path);

      //    $(".nav-item a").each(function () {
      //       var href = $(this).attr("href");
      //       if (path.substring(0, href.length) === href) {
      //          alert(path.substring(0, href.length)+'---'+href);
      //          $(this).parent("li").addClass("active");
      //       }
      //    });
      // }
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