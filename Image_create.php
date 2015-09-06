<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Image_create {
	/***************
	*	Class takes an array structured like thus:
	*	array (
	*		'headerString' => '',
	*		'footerString' => '',
	*		'bodyString' => '',
	*		'primaryFont' => '',
	*		'primaryFontSize' => '',
	*		'secondaryFont' => '',
	*		'secondaryFontSize' => '',
	*		'lineHeight' => '',
	*		'imageWidth' => '',
	*		'imageMargin' => ''
	*	);
	*/
	
	private $strings;
	private $fonts;
	private $imageWidth;
	private $imageHeight;
	private $imageMargin;
	private $imageWidthInChars = 27;
	
	private $theImage;
	
	public function __construct($arr) {
		$this->fonts = array(
			'primaryFont' => $arr['primaryFont'],
			'primaryFontSize' => $arr['primaryFontSize'],
			'primaryFontLineHeight' => $arr['primaryFontSize'] * $arr['lineHeight'],
			'secondaryFont' => $arr['secondaryFont'],
			'secondaryFontSize' => $arr['secondaryFontSize'],
			'secondaryFontLineHeight' => $arr['secondaryFontSize'] * $arr['lineHeight'],
		);
		$this->imageWidth = $arr['imageWidth'];
		$this->imageMargin = $arr['imageMargin'];
		
		//the strings come last because they rely on the other data to get linebreaks inserted
		$this->strings = array(
			'headerString' => $this->wrap_text_to_width( $arr['headerString'], $this->fonts['secondaryFont'], $this->fonts['secondaryFontSize'] ),
			'footerString' => $this->wrap_text_to_width( $arr['footerString'], $this->fonts['secondaryFont'], $this->fonts['secondaryFontSize'] ),
			'bodyString' => $this->wrap_text_to_width( $arr['bodyString'], $this->fonts['primaryFont'], $this->fonts['primaryFontSize'] ),
		);
		$this->imageHeight = $this->calc_image_height();
		
		$this->make_image();
	}
	
	//generates the image from the data given to the class
	public function make_image() {
		
		$this->theImage = imagecreatetruecolor( $this->imageWidth, $this->imageHeight );
		$black = imagecolorallocate($this->theImage, 0, 0, 0);
		$white = imagecolorallocate($this->theImage, 255, 255, 255);
		imagefill($this->theImage, 0, 0, $white);
		
		$lineHeight = $this->imageMargin * 2;
		foreach ( $this->strings['headerString'] as $s ) {
			imagettftext($this->theImage, $this->fonts['secondaryFontSize'], 0, $this->imageMargin, $lineHeight, $black, $this->fonts['secondaryFont'], $s);
			$lineHeight += $this->fonts['secondaryFontLineHeight'];
		}
		
		$lineHeight += $this->imageMargin * 2;
		foreach ( $this->strings['bodyString'] as $s ) {
			imagettftext($this->theImage, $this->fonts['primaryFontSize'], 0, $this->imageMargin, $lineHeight, $black, $this->fonts['primaryFont'], $s);
			$lineHeight += $this->fonts['primaryFontLineHeight'];
		}

		$lineHeight += $this->imageMargin;
		foreach ( $this->strings['footerString'] as $s ) {
			imagettftext($this->theImage, $this->fonts['secondaryFontSize'], 0, $this->imageMargin, $lineHeight, $black, $this->fonts['secondaryFont'], $s);
			$lineHeight += $this->fonts['secondaryFontLineHeight'];
		}

	}
	
	//Sends image direct to browser
	public function image_to_browser() {
		header ('Content-Type: image/png');
		imagepng($this->theImage);
		imagedestroy($this->theImage);
	}

	//Sends image to file
	public function image_to_file( $pathToImage ) {
		imagepng($this->theImage, $pathToImage);
		imagedestroy($this->theImage);
	}
	
	//word wraps the string to fit into the desired width of the image
	private function wrap_text_to_width($string, $font, $fontSize) {
		$maxWidth = $this->imageWidth - $this->imageMargin * 2;
		
		//makes sure that hyphens wrap
		$string = str_replace( '-', '- ', $string );
		
		//adds spaces to words longer than the max characters defined by the class
		$stringArray = explode(' ', $string);
		foreach ( $stringArray as $s ) {
			if ( strlen( $s ) > $this->imageWidthInChars ) $s = wordwrap( $s , $this->imageWidthInChars , ' ' , true );
			$sa[] = $s;
		}
		$string = implode(' ', $sa);
		
		//takes each word in the string, now processed, and puts it in the final word array
		$stringArray = explode(' ', $string);
		$finalString[] = '';
		$i = 0;
		//takes each word and makes sure it fits within the bbox, and if it doesn't, creates a new line
		foreach( $stringArray as $s ) {
			$bb = imagettfbbox( $fontSize, 0, $font, $finalString[$i] );
			$bbs = imagettfbbox( $fontSize, 0, $font, $s . ' ' );
			if ( $bb[2] + $bbs[2] < $maxWidth ) {
				$finalString[$i] .= $s . ' ';
			} else {
				$finalString[$i] = trim( str_replace( '- ', '-', $finalString[$i] ) );
				$i++;
				$finalString[] .= $s . ' ';
			}
		}
		//tidies the last line of the text
		$finalString[ count($finalString) - 1 ] = trim( str_replace( '- ', '-', $finalString[ count($finalString) - 1 ] ) );

		return $finalString; // returns array with the string broken into appropriate lengths
	}
	
	//calculates the height that the image needs to be
	private function calc_image_height() {
		$totalMargin = $this->imageMargin * 5;
		$stringHeight = 0;
		
		//get the collective height for the header, footer, and body string sections
		foreach ( $this->strings['headerString'] as $s ) {
			$stringHeight += $this->fonts['secondaryFontLineHeight'];
		}
		foreach ( $this->strings['footerString'] as $s ) {
			$stringHeight += $this->fonts['secondaryFontLineHeight'];
		}
		foreach ( $this->strings['bodyString'] as $s ) {
			$stringHeight += $this->fonts['primaryFontLineHeight'];
		}
		
		return $stringHeight + $totalMargin;
	}

}