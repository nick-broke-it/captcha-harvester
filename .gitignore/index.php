<?php
	function remove_line($fname,$index){
		$line_no = 0; 
		$lines = file($fname); 

		foreach($lines as $line) { 
		   $line_no++; 
		   if($line_no !== $index) $out .= $line; 
		} 

		$f = fopen($fname, "w"); 
		fwrite($f, $out); 
		fclose($f); 
	}

	function remove_old(){
		$removed = 0;
		$file="files/timestamps.txt";
		$handle = fopen($file, "r");
		$linecount = 0;
		while(!feof($handle)){
		  $linecount++;
		  $line = fgets($handle);
		  $now = strtotime("-100 seconds");

		  if($now > strtotime("@".$line) && !empty($line)){
		  	$removed++;
		  	remove_line("files/captcha.txt",$linecount);
		  	remove_line("files/timestamps.txt",$linecount);
		  }	  	
		}

		fclose($handle);
		return $removed;
	}

	error_reporting(E_ERROR);
	$removed_entries = 0;
    if(!empty($_POST['g-recaptcha-response']))
    {
    	$removed_entries = remove_old();
        file_put_contents("files/captcha.txt", trim($_POST['g-recaptcha-response'])."\r\n", FILE_APPEND | LOCK_EX);
        file_put_contents("files/timestamps.txt", time()."\r\n", FILE_APPEND | LOCK_EX);
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Harvester</title>
		<script src="http://code.jquery.com/jquery-latest.min.js"
        type="text/javascript"></script>

        <style>
	        body,html{
	        	background:#001240;
	        	color:#2495BD;
	        	font-family:Arial;
	        }

	        textarea {
	        	border:2px solid #2495BD;
	        	background:#001240;
	        	color:#99D9EA;
	        }

	        h3{
	        	color:#99D9EA;
	        }

	        form {
	        	display:table;
	        	margin:0 auto;
	        	text-align:center;
	        }

	        .g-recaptcha {
	        	display:table;
	        	margin:0 auto;
	        }

	        #submit{
	        	border:2px solid #2495BD;
	        	background:#001240;
	        	color:#2495BD;background:;
	        	padding:20px;
	        	width:400px;
	        }

	        #container {
	        	width:500px;
	        	border:2px solid #2495BD;
	        	word-wrap: break-word;
	        	text-align:left;
	        	padding:10px;
	        }
        </style>
	</head>
	<body>
		<form method="post" action="">
			<h1>Adidas Captcha Harvester</h1>
			<h3 id="tokens_available">Total Captchas Harvested:
			<?php
					$file="files/captcha.txt";
					$linecount = 0;
					$handle = fopen($file, "r");
					while(!feof($handle)){
					  $line = fgets($handle);
					  $linecount++;
					  $content .= $line."<br><br>";
					}

					$content = substr($content,0,strlen($string)-10);

					fclose($handle);
					echo $linecount-1;
			?></h3>
			<h3 id="tokens_removed">Removed expired Captchas: <?= $removed_entries; ?></h3>

		    <div class="g-recaptcha" data-sitekey="6Lc2fhwTAAAAAGatXTzFYfvlQMI2T7B6ji8UVV_f"></div><br>

		    <input type="submit" value="Submit" id="submit" /><br><br>

		    <h3>Usable tokens</h3>
			<div id="container">
				<?= $content; ?>
			</div>
	    </form>

	    <script>
	    	var removed = 0;
	    	$( document ).ready(function() {
			    setInterval(function(){
			    	$.get( "./api.php", function( data ) {
					  	$("#tokens_available").html("Total Captchas Harvested: "+data.tokens_available);
					  	removed += data.tokens_removed;
					  	$("#tokens_removed").html("Removed expired Captchas: "+removed);
					  	$("#container").html(data.tokens);
					},"json");
			    }, 1000);
			});
	    </script>

	   <script src='https://www.google.com/recaptcha/api.js'></script>
	</body>
</html>