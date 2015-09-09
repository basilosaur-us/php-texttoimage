<?php

class Image_create {
	/***************
	* STILL TO IMPLEMENT: background images, max height
	*	Class takes an array structured like thus:
	*	array (
	*		'header' => array(
	*			'headerString' => '',
	*			'headerFont' => '',
	*			'headerFontSize' => '',
	*			'headerFontColor' => '',
	*			'headerImage' => '',
	*		),
	*		'footer' => array(
	*			'footerString' => '',
	*			'footerFont' => '',
	*			'footerFontSize' => '',
	*			'footerFontColor' => '',
	*			'footerImage' => '',
	*		),
	*		'body' => array(
	*			'bodyString' => '',
	*			'bodyFont' => '',
	*			'bodyFontSize' => '',
	*			'bodyFontColor' => '',
	*		),
	*		'lineHeight' => '',
	*		'maxWordLength' => '',
	*		'imageWidth' => '',
	*		'imageMaxHeight' => '',
	*		'verticalImageMargin' => '',
	*		'horizontalImageMargin => '',
	*		'backgroundColor' => ''
	*	);
	*/
	private $header = array(
		'headerString' => '',
		'headerFont' => '',
		'headerFontSize' => 18,
		'headerFontColor' => '#b0000b',
		'headerImage' => '',
		'scaleHeaderImage' => false,
		'headerPadding' => 0,
		'headerImageWidth' => 0,
		'headerImageHeight' => 0,
		'headerLineHeight' => '',
		'headerImageType' => 0,
		'headerTheImage' => '',
	);
	private $footer = array(
		'footerString' => '',
		'footerFont' => '',
		'footerFontSize' => 18,
		'footerFontColor' => '#b0000b',
		'footerImage' => '',
		'scaleFooterImage' => false,
		'footerPadding' => 0,
		'footerImageWidth' => 0,
		'footerImageHeight' => 0,
		'footerLineHeight' => '',
		'footerImageType' => 0,
		'footerTheImage' => '',
	);
	private $body = array(
		'bodyString' => '',
		'bodyFont' => '',
		'bodyFontSize' => 16,
		'bodyFontColor' => '#000',
		'bodyLineHeight' => '',
	);
	private $lineHeight = 1.5;
	private $maxWordLength = 27;
	private $imageWidth = 700;
	private $imageMaxHeight = '';
	private $verticalImageMargin = 10;
	private $horizontalImageMargin = 10;
	private $backgroundColor = '#FFF';

	private $imageHeight;
	private $theImage;

	public function __construct($arr) {
		//merges $arr parameters into the default values; WARNING: CLASS DOES NOT
		//RUN WITHOUT PROVIDING THE REMAINDER OF THE VALUES
		if ( $arr['lineHeight'] != '' ) $this->lineHeight = $arr['lineHeight'];
		if ( $arr['maxWordLength'] != '' ) $this->maxWordLength = $arr['maxWordLength'];
		if ( $arr['imageWidth'] != '' ) $this->imageWidth = $arr['imageWidth'];
		if ( $arr['imageMaxHeight'] != '' ) $this->imageMaxHeight = $arr['imageMaxHeight'];
		if ( $arr['verticalImageMargin'] != '' ) $this->verticalImageMargin = $arr['verticalImageMargin'];
		if ( $arr['horizontalImageMargin'] != '' ) $this->horizontalImageMargin = $arr['horizontalImageMargin'];
		if ( $arr['backgroundColor'] != '' ) $this->backgroundColor = $arr['backgroundColor'];

		$this->header = array_merge( $this->header, $arr['header'] );
		$this->footer = array_merge( $this->footer, $arr['footer'] );
		$this->body = array_merge( $this->body, $arr['body'] );

		//Sets lineheight in pixels for each section of the image
		$this->header['headerLineHeight'] = $this->header['headerFontSize'] * $this->lineHeight;
		$this->body['bodyLineHeight'] = $this->body['bodyFontSize'] * $this->lineHeight;
		$this->footer['footerLineHeight'] = $this->footer['footerFontSize'] * $this->lineHeight;

		//converts each string to an array with linebreaks
		$this->header['headerString'] = $this->wrap_text_to_width($this->header['headerString'], $this->header['headerFont'], $this->header['headerFontSize']);
		$this->body['bodyString'] = $this->wrap_text_to_width($this->body['bodyString'], $this->body['bodyFont'], $this->body['bodyFontSize']);
		$this->footer['footerString'] = $this->wrap_text_to_width($this->footer['footerString'], $this->footer['footerFont'], $this->footer['footerFontSize']);

		//converts colors into rgb arrays
		$this->header['headerFontColor'] = $this->hex2RGB($this->header['headerFontColor']);
		$this->body['bodyFontColor'] = $this->hex2RGB($this->body['bodyFontColor']);
		$this->footer['footerFontColor'] = $this->hex2RGB($this->footer['footerFontColor']);
		$this->backgroundColor = $this->hex2RGB($this->backgroundColor);

		//gets the dimensions of the header and footer images
		//gets image type and loads it.  Allows png, gif, or jpeg
		if ( !empty( $this->header['headerImage'] ) ) $this->set_bg_image_info('header');
		if ( !empty( $this->footer['footerImage'] ) ) $this->set_bg_image_info('footer');

		$this->imageHeight = $this->calc_image_height();

//		echo $this->footer['footerImage'] . ' ' . $this->footer['footerImageWidth']
//			. ' ' . $this->footer['footerImageHeight'] . ' ' . $this->footer['footerImageType'];

		$this->make_image();
	}

	//generates the image from the data given to the class
	public function make_image() {

		$this->theImage = imagecreatetruecolor( $this->imageWidth, $this->imageHeight );

		//defines colors for the fonts and background
		$headerColor = imagecolorallocate($this->theImage, $this->header['headerFontColor']['red'], $this->header['headerFontColor']['green'], $this->header['headerFontColor']['blue']);
		$bodyColor = imagecolorallocate($this->theImage, $this->body['bodyFontColor']['red'], $this->body['bodyFontColor']['green'], $this->body['bodyFontColor']['blue']);
		$footerColor = imagecolorallocate($this->theImage, $this->footer['footerFontColor']['red'], $this->footer['footerFontColor']['green'], $this->footer['footerFontColor']['blue']);
		$backgroundColor = imagecolorallocate($this->theImage, $this->backgroundColor['red'], $this->backgroundColor['green'], $this->backgroundColor['blue']);

		//fills the image with its background color
		imagefill($this->theImage, 0, 0, $backgroundColor);

		//inserts header and footer background images if they exist
		// only scales the header and footer image to fit the final image if it is
		// set to do so, and if your PHP version is over 5.5.0
		if ( !empty( $this->header['headerTheImage'] ) ) {
			if ( ( PHP_VERSION_ID >= 50500 ) && ( $this->header['scaleHeaderImage'] == true ) )
				imagescale($this->header['headerTheImage'], $this->imageWidth);
			imagecopy($this->theImage, $this->header['headerTheImage'], 0, 0, 0, 0, $this->header['headerImageWidth'], $this->header['headerImageHeight']);
		}
		if ( !empty( $this->footer['footerTheImage'] ) ) {
			if ( ( PHP_VERSION_ID >= 50500 ) && ( $this->footer['scaleFooterImage'] == true ) )
				imagescale($this->footer['footerTheImage'], $this->imageWidth);
			imagecopy($this->theImage, $this->footer['footerTheImage'], 0, ( $this->imageHeight - $this->footer['footerImageHeight'] ), 0, 0, $this->footer['footerImageWidth'], $this->footer['footerImageHeight']);
		}

		$lineHeight = $this->verticalImageMargin + $this->header['headerFontSize'] + ( $this->footer['footerFontSize'] / 2 );
		//echo $lineHeight;
		foreach ( $this->header['headerString'] as $s ) {
			imagettftext($this->theImage, $this->header['headerFontSize'], 0, $this->horizontalImageMargin, $lineHeight, $headerColor, $this->header['headerFont'], $s);
			$lineHeight += $this->header['headerLineHeight'];
		}

		$lineHeight += $this->verticalImageMargin + $this->header['headerPadding'];
		foreach ( $this->body['bodyString'] as $s ) {
			imagettftext($this->theImage, $this->body['bodyFontSize'], 0, $this->horizontalImageMargin, $lineHeight, $bodyColor, $this->body['bodyFont'], $s);
			$lineHeight += $this->body['bodyLineHeight'];
		}

		$lineHeight += $this->verticalImageMargin + $this->footer['footerPadding'];
		foreach ( $this->footer['footerString'] as $s ) {
			imagettftext($this->theImage, $this->footer['footerFontSize'], 0, $this->horizontalImageMargin, $lineHeight, $footerColor, $this->footer['footerFont'], $s);
			$lineHeight += $this->footer['footerLineHeight'];
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
		$maxWidth = $this->imageWidth - ( $this->horizontalImageMargin * 2 );

		//makes sure that hyphens wrap
		$string = str_replace( '-', '- ', $string );

		//adds spaces to words longer than the max characters defined by the class
		$stringArray = explode(' ', $string);
		foreach ( $stringArray as $s ) {
			if ( strlen( $s ) > $this->maxWordLength ) $s = wordwrap( $s , $this->maxWordLength , ' ' , true );
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
		//calculate the margins
		$totalMargin = $this->verticalImageMargin * 4;
		$padding = $this->header['headerPadding'] + $this->footer['footerPadding'];
		$stringHeight = 0;

		//get the collective height for the header, footer, and body string sections
		foreach ( $this->header['headerString'] as $s ) {
			$stringHeight += $this->header['headerLineHeight'];
		}
		foreach ( $this->footer['footerString'] as $s ) {
			$stringHeight += $this->footer['footerLineHeight'];
		}
		foreach ( $this->body['bodyString'] as $s ) {
			$stringHeight += $this->body['bodyLineHeight'];
		}

		return $stringHeight + $totalMargin + $padding;
	}

	//converts html hex strings to an array with r, g, & b
	//based on a function provided at php.com by hafees@msn.com
	//http://php.net/manual/en/function.hexdec.php#99478
	private function hex2RGB($hexStr, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
			$rgbArray['red'] = 000;
			$rgbArray['green'] = 000;
			$rgbArray['blue'] = 000;
    }
    return $rgbArray; // returns the rgb string or the associative array
	}

	//sets width and height for background images (headerImage, footerImage)
	private function set_bg_image_info( $which ) {
		if ( $which == 'header' ) {
			$img = $this->header['headerImage'];
		} elseif ( $which == 'footer' ) {
			$img = $this->footer['footerImage'];
		}
		//check if file exists
		if ( file_exists($img) ) {
			$info = getimagesize($img);
			if ( $which == 'header' ) {
				$this->header['headerImageWidth'] = $info[0];
				$this->header['headerImageHeight'] = $info[1];
				$this->header['headerImageType'] = $info[2];
				//makes image object
				if ( $this->header['headerImageType'] == IMAGETYPE_PNG ) {
					$this->header['headerTheImage'] = imagecreatefrompng($img);
				} elseif ( $this->header['headerImageType'] == IMAGETYPE_JPEG ) {
					$this->header['headerTheImage'] = imagecreatefromjpeg($img);
				} elseif ( $this->header['headerImageType'] == IMAGETYPE_GIF ) {
					$this->header['headerTheImage'] = imagecreatefromgif($img);
				}
			} elseif ( $which == 'footer' ) {
				$this->footer['footerImageWidth'] = $info[0];
				$this->footer['footerImageHeight'] = $info[1];
				$this->footer['footerImageType'] = $info[2];
				//makes image object
				if ( $this->footer['footerImageType'] == IMAGETYPE_PNG ) {
					$this->footer['footerTheImage'] = imagecreatefrompng($img);
				} elseif ( $this->footer['footerImageType'] == IMAGETYPE_JPEG ) {
					$this->footer['footerTheImage'] = imagecreatefromjpeg($img);
				} elseif ( $this->footer['footerImageType'] == IMAGETYPE_GIF ) {
					$this->footer['footerTheImage'] = imagecreatefromgif($img);
				}
			}
		}
		return false;
	}

}
