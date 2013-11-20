<?php 

$images = array();
$newImages = array();
$images1 = array();
$notification = "";
$newNotif = "";
$deletedNotif = "";
// momentalen folder
$f = getcwd();
$curUrl =  $f.$_SERVER['REQUEST_URI'];
if ($curUrl != $f.'/'){
	if(is_dir($curUrl)){
		// proverka dali ima sliki, i vnesuvanje na slikite vo niza ako ima
		$empty = true;
		$folder = opendir($curUrl);
		$pic_types = array("jpg", "jpeg", "gif", "png");
		while ($file = readdir ($folder)) {
			$parts = explode(".", $file);
			$ext = strtolower($parts[count($parts) - 1]);
			if(in_array($ext, $pic_types))
			{
				array_push($images, $file);
				$empty = false;
			}
		}
		closedir($folder);

		// proveruva dali postoi .conf file,
		$filename = '.conf';
		$filename = $curUrl.$filename;
		//ako ne postoi SAMO go kreira, 
		if(!file_exists($filename)){ 
			if ($empty == false){
				$fp = fopen($filename,"w");
				foreach($images as $key => $value){
					fwrite($fp,$value."\r\n");
				}
				fclose($fp);
				$notification = "Images added.";
			}
			else{ 
				$fp = fopen($filename,"w");
				fclose($fp);
				$notification = "Configuration file created.";
			}
		}
		else{
			if ($empty == false){
				//ako postoi I NE E PRAZEN,
				$isEmpty = filesize($filename);
				if($isEmpty > 0){
					//proveruva dali ima novi sliki i gi dodava POSLE veke POSTOECKITE,
					$oldImages = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
					$imagesSize = sizeof($images);
					$oldImagesSize = sizeof($oldImages);
					$i = 0;
					for($i=0;$i<$imagesSize;$i++){
						if (!(in_array($images[$i],$oldImages))){
							array_push($newImages, $images[$i]);
						}
					}
					$fp = fopen($filename,"a");
					foreach($newImages as $key => $value){
						fwrite($fp,$value."\r\n");
						$newNotif = "New images added.";
					}
					fclose($fp);
					//ako ima izbriseni sliki, gi dodava SITE sliki ODNOVO zaedno so NOVITE
					$oldImages = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
					for($i=0;$i<$oldImagesSize;$i++){
						if (!(in_array($oldImages[$i],$images))){
							unset($oldImages[$i]);
						}				
					}
					$oldImages = array_values($oldImages);
					$fp = fopen($filename,"w");
					foreach($oldImages as $key => $value){
						fwrite($fp,$value."\r\n");
						$deletedNotif = "Images deleted.";
					}
					fclose($fp);
					$notification = "Images refreshed.";
				}
				//ako postoi I E PRAZEN, gi zapisuva iminjata na slikite,
				else{
					$fp = fopen($filename,"w");
					foreach($images as $key => $value){
						fwrite($fp,$value."\r\n");
					}
					fclose($fp);
					$notification = "Images added.";
				}
			}
			else{
				if(file_exists($filename)){ 
				   $fp = fopen($filename,"w");  
				   fclose($fp); 
				   $notification = "Folder empty.";
				}
			}
		}
		$images1 = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}
	else{
		$notification = "Folder does not exists.";
	}
}

$fixPath = $_SERVER['REQUEST_URI'];
?>
<html>
<head>
	<title>Images</title>
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script src="../js/jquery.flexslider-min.js"></script>
	<script src="../js/modernizr.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
	<link rel="stylesheet" href="../css/flexslider.css" type="text/css" media="screen" />
	
	<script type="text/javascript">
		$(function(){
		  SyntaxHighlighter.all();
		});
		$(window).load(function(){
		  $('#carousel').flexslider({
			animation: "slide",
			controlNav: false,
			animationLoop: false,
			slideshow: false,
			itemWidth: 210,
			itemMargin: 5,
			asNavFor: '#slider'
		  });

		  $('#slider').flexslider({
			animation: "slide",
			controlNav: false,
			animationLoop: false,
			slideshow: false,
			sync: "#carousel",
			start: function(slider){
			  $('body').removeClass('loading');
			}
		  });
		});
	</script>
	<!-- Syntax Highlighter -->
	<script type="text/javascript" src="../js/shCore.js"></script>
	<script type="text/javascript" src="../js/shBrushXml.js"></script>
	<script type="text/javascript" src="../js/shBrushJScript.js"></script>

	<!-- Optional FlexSlider Additions -->
	<script src="../js/jquery.easing.js"></script>
	<script src="../js/jquery.mousewheel.js"></script>
</head>
<body>
<div class="wrapper">
	<div class="content">
	<?php 
		if(sizeof($images1) == 0){
			echo "<div class='notification'>".$notification."</div>";
		}
		else{
			if (sizeof($newNotif) > 0 && sizeof($deletedNotif) > 0){
			}
			if (sizeof($newNotif) > 0){}
			if (sizeof($deletedNotif) > 0){
				
			}
			echo "<div class='notification'>".$notification."</div>";
	?>
		<div id="slider" class="flexslider">
			<ul class="slides">
			<?php 
				$i = 0;
				for($i=0;$i<sizeof($images1);$i++){
			?>
			<li>
				<img src="<?php echo $images1[$i]; ?>" height="auto" width="500"/>
			</li>
			<?php 
				}
			?>	
			</ul>
		</div>
		<div id="carousel" class="flexslider">
			<ul class="slides">
			<?php 
				$i = 0;
				for($i=0;$i<sizeof($images1);$i++){
			?>
			<li>
				<img src="<?php echo $images1[$i]; ?>" height="auto" width="500"/>
			</li>
			<?php 
				}
			?>	
			</ul>
		</div>
	<?php } ?>
	</div>
</div>
</body>
</html>