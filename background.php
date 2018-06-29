<?php


/**
 * Create file for background image rotation. 
 * You can set the directory below to search for image files
 * to be used. Make sure that only image files are in this directory. 
 */	
class createBackgrounds {
		
	// Folder where photos to be used are
	private $dir = '/home/kyle/Pictures/Backgrounds/';	
	private $images = array();
	
	// xml filename
	private $xmlFileName = 'xenial.xml';
	// fopen File Mode
	private $xmlFileMode = 'w';
	
	// Make Sure this is set to time in past to start immediately
	private $year = '2009';
	private $month = '08';
	private $day = '04';
	private $hour = '00';
	private $minute = '00';
	private $second = '10';
	
	// How Long Photo is Displayed For, in seconds
	private $displayDuration = 1795.0; // 60 = 1 minute
	// Time transitioning between photos, in seconds
	private $transitionDuration = 5.0;
		
	function __construct() {
		//$this->makeFile();
		$this->getFiles();
		$this->makeFile();
	}
	
	/**
	 * This gets the images that are to be used for
	 * the .xml file for the backgrounds
	 */
	private function getFiles(){
		
		$is_dir = is_dir($this->dir);
		
		if($is_dir){
			if($dh = opendir($this->dir)){
					
				$file_names = array();
				
				while (($file = readdir($dh)) !== false) {
					if(!is_dir($file)){
						$file_names[] = $file;
					}
				}
				
				$this->images = $file_names;
				
				//fclose($dh);
					
			}
		}
		
	}
	
	private function prepFile(){
			
		$response = '';
		
		if(is_dir($this->dir)){
						
			$writeableData = array();
			$endKey = key( array_slice( $this->images, -1, 1, TRUE ) );
			foreach ($this->images as $key => $value) {
				
				$next = $key+1;
				
				$images = "";
				$images .= "<static>\n";
				$images .= "  <duration>".$this->displayDuration."</duration>\n";
				$images .= "  <file>".$this->dir.$value."</file>\n";
				$images .= "</static>\n";
				$images .= "<transition>\n";
				$images .= "  <duration>".$this->transitionDuration."</duration>\n";
				$images .= "  <from>".$this->dir.$value."</from>\n";
				if($next <= $endKey){
					//echo $next . "<to>".$this->dir.$this->images[$next]."</to>\n";
					$images .= "  <to>".$this->dir.$this->images[$next]."</to>\n";
				}else{
					//echo $key . "<to>".$this->dir.$this->images[0]."</to>\n";
					$images .= "  <to>".$this->dir.$this->images[0]."</to>\n";
				}
				$images .= "</transition>\n";
				
				$writeableData[] = $images;

			}
			
			$response = $writeableData;
		}			
		
		return $response;
		
	}
	/**
	 * Create .xml file to store data. We will create this file in the
	 * same directory as the images if we can.
	 */	
	private function makeFile(){
		
		$response = '';
		
		if($fh = @fopen($this->dir.$this->xmlFileName, $this->xmlFileMode)){
				
			$boilerplate = "";
			$boilerplate .= "<background>\n";
			$boilerplate .= "  <starttime>\n";
			$boilerplate .= "    <year>".$this->year."</year>\n";
			$boilerplate .= "    <month>".$this->month."</month>\n";
			$boilerplate .= "    <day>".$this->day."</day>\n";
			$boilerplate .= "    <hour>".$this->hour."</hour>\n";
			$boilerplate .= "    <minute>".$this->minute."</minute>\n";
			$boilerplate .= "    <second>".$this->second."</second>\n";
			$boilerplate .= "  </starttime>\n";
			$boilerplate .= "<!-- This animation will start at midnight. -->\n";
			// Write Boilerplate Data
			if(false !== fwrite($fh, $boilerplate)){
				$response .= "Wrote Boiler Plate...\n";	
			}else{
				$response .= "Failed Adding Boilerplate...\n";
			}
			
			// Add images to file
			$imageData = $this->prepFile();
			$imageMsg = '';
			foreach ($imageData as $key => $value) {
				if(false !== fwrite($fh, $value)){
					$imageMsg = "Added Photos!\n";	
				}else{
					$imageMsg = "Failed Adding Photos!\n";
				}
			}
			
			$response .= $imageMsg;
			
			// End of File
			if(false !== fwrite($fh, "</background>")){
				$response .= "Wrote End Of File...\n";
			}
			
			if(false !== fclose($fh)){
				$response .= "Closed File.\n";
				$response .= "Check Directory ".$this->dir.$this->xmlFileName." For Your File. \n";
				$response .= "Copy New File To /usr/share/backgrounds/contest \n";
			}
			
		}else{
			$response .= "Failed To Create File. \n";
			$response .= "Check Premission \n";
		}		
		
		echo $response;
	}
	
}

$background = new createBackgrounds;


?>
