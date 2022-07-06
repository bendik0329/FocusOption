<?php 
$video  = isset($_GET['player'])? $_GET['player'] : "";
?>
<!DOCTYPE html>
<html>
<head>
<title>Affiliate Buddies - <?php echo $video; ?></title>

<link href="http://vjs.zencdn.net/4.0/video-js.css" rel="stylesheet">
 <script src="http://vjs.zencdn.net/4.0/video.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
 
<style>
div#video_player_box{ width:600px; background:#000; margin:0px auto;}
div#video_controls_bar{ background: #333; padding:10px; color:#CCC;}
input#seekslider{ width:180px; }
input#volumeslider{ width: 80px;}
</style>
<script>
var vid, playbtn, seekslider, curtimetext, durtimetext, mutebtn, volumeslider, fullscreenbtn;
function intializePlayer(){
	// Set object references
	vid = document.getElementById("my_video");
	playbtn = document.getElementById("playpausebtn");
	seekslider = document.getElementById("seekslider");
	curtimetext = document.getElementById("curtimetext");
	durtimetext = document.getElementById("durtimetext");
	mutebtn = document.getElementById("mutebtn");
	volumeslider = document.getElementById("volumeslider");
	fullscreenbtn = document.getElementById("fullscreenbtn");
	// Add event listeners
	playbtn.addEventListener("click",playPause,false);
	seekslider.addEventListener("change",vidSeek,false);
	vid.addEventListener("timeupdate",seektimeupdate,false);
	mutebtn.addEventListener("click",vidmute,false);
	volumeslider.addEventListener("change",setvolume,false);
	fullscreenbtn.addEventListener("click",toggleFullScreen,false);
}
window.onload = intializePlayer;
function playPause(){
	if(vid.paused){
		vid.play();
		playbtn.innerHTML = "Pause";
	} else {
		vid.pause();
		playbtn.innerHTML = "Play";
	}
}
function vidSeek(){
	var seekto = vid.duration * (seekslider.value / 100);
	vid.currentTime = seekto;
}
function seektimeupdate(){
	var nt = vid.currentTime * (100 / vid.duration);
	seekslider.value = nt;
	var curmins = Math.floor(vid.currentTime / 60);
	var cursecs = Math.floor(vid.currentTime - curmins * 60);
	var durmins = Math.floor(vid.duration / 60);
	var dursecs = Math.floor(vid.duration - durmins * 60);
	if(cursecs < 10){ cursecs = "0"+cursecs; }
	if(dursecs < 10){ dursecs = "0"+dursecs; }
	if(curmins < 10){ curmins = "0"+curmins; }
	if(durmins < 10){ durmins = "0"+durmins; }
	curtimetext.innerHTML = curmins+":"+cursecs;
	durtimetext.innerHTML = durmins+":"+dursecs;
}
function vidmute(){
	if(vid.muted){
		vid.muted = false;
		mutebtn.innerHTML = "Mute";
	} else {
		vid.muted = true;
		mutebtn.innerHTML = "Unmute";
	}
}
function setvolume(){
	vid.volume = volumeslider.value / 100;
}
function toggleFullScreen(){
	if(vid.requestFullScreen){
		vid.requestFullScreen();
	} else if(vid.webkitRequestFullScreen){
		vid.webkitRequestFullScreen();
	} else if(vid.mozRequestFullScreen){
		vid.mozRequestFullScreen();
	}
}



</script>
</head>
<body>
<div id="video_player_box">
  <video id="my_video" width="600" height="340" autoplay>
    <source src="http://www.affiliatebuddies.com/help/videos/<?php echo $video ; ?>.mp4">
  </video>
  <div id="video_controls_bar">
    <button id="playpausebtn">Pause</button>
    <input id="seekslider" type="range" min="0" max="100" value="0" step="1">
    <span id="curtimetext">00:00</span> / <span id="durtimetext">00:00</span>
    <button id="mutebtn">Mute</button>
    <input id="volumeslider" type="range" min="0" max="100" value="100" step="1">
    <button id="fullscreenbtn">Full Screen</button>
  </div>
</div>


</body>
</html>
