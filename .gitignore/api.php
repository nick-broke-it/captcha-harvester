<?php
	error_reporting(E_ERROR);
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

	echo json_encode(array(
		"tokens_available" => $linecount-1,
		"tokens_removed" => remove_old(),
		"tokens" => $content
	));
?>