<?php 
chdir('../');
require_once('common/global.php');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <title>Affiliate Buddies - User Manual</title>
    <script type="text/javascript">
      function escapeHtml(unsafe) {
        return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;").replace(/:/g, "");
      }
      var sTopic = "";
    // alert (top.location.href);
      if (top.location.href.lastIndexOf("?") > 0)
        sTopic = top.location.href.substring(top.location.href.lastIndexOf("?") + 1, top.location.href.length);
      if (sTopic == "") sTopic = "//www.affiliatets.com/help/Overview.php";
    //sTopic = escapeHtml(sTopic);
      document.write('<frameset cols="300,*">');
      document.write('<frame src="//www.affiliatets.com/help/toc.php" name="FrameTOC">');
      document.write('<frame src="' + sTopic + '" name="FrameMain">');
      document.write('</frameset>');
    
    </script>
<link type="text/css" rel="stylesheet" media="all" href="css/int.css">

</head>
<noscript>
  <frameset cols="300,*">
    <frame src="//www.affiliatets.com/help/toc.php" name="FrameTOC">
    <frame src="//www.affiliatets.com/help/Overview.php" name="FrameMain">
    
  </frameset>
</noscript>

</html>

