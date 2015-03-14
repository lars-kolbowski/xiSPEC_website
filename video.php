<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $pageName = "Video";
        include('./head.php'); ?>
    </head>
    <body>
<div class="container" >
	<?php include('navigation.php'); ?>
	   <video  id="movie" controls autoplay preload="none">
           <source src="./vid/vidTut.webm" type="video/webm" />
           <!-- <source src="./files/vidTut.ogv" type="video/ogg" /> -->
           <source src="./vid/vidTut.mp4" />
           <object type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">
               <param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />
               <param name="allowfullscreen" value="true" />
               <param name="flashvars" value='config={"clip":{"url":"http://129.215.14.125/trunk/xinet/vid/vidTut.mp4","autoPlay":true,"autoBuffering":true}}' />
               <p>Download video tutorial as <a href=./vid/vidTut.mp4>MP4</a> or <a href=./vid/vidTut.webm>WebM</a>.</p>
           </object>
       </video>
       <script>
           var v = document.getElementById("movie");
           v.onclick = function() {
               if (v.paused) {
                   v.play();
               } else {
                   v.pause();
               }
           };
       </script>
		<!-- OLD BROWSER NOTIFICATION -->
		
		<script type="text/javascript"> 
		var $buoop = {vs:{i:8,f:15,s:4,n:9}} 
		$buoop.ol = window.onload; 
		window.onload=function(){ 
		 try {if ($buoop.ol) $buoop.ol();}catch (e) {} 
		 var e = document.createElement("script"); 
		 e.setAttribute("type", "text/javascript"); 
		 e.setAttribute("src", "http://browser-update.org/update.js"); 
		 document.body.appendChild(e); 
		} 
		</script>
	</body>
</html>
