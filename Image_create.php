<?php

class Image_create {
	/***************
	* STILL TO IMPLEMENT: background images, max height
	*	Class takes an array structured like thus:
	*	array (
	*		'header' => array(
	*			'string' => '',
	*			'font' => '',
	*			'fontSize' => '',
	*			'fontColor' => '',
	*			'bgImage' => '',
	*		),
	*		'footer' => array(
	*			'string' => '',
	*			'font' => '',
	*			'fontSize' => '',
	*			'fontColor' => '',
	*			'bgImage' => '',
	*		),
	*		'body' => array(
	*			'string' => '',
	*			'font' => '',
	*			'fontSize' => '',
	*			'fontColor' => '',
	*		),
	*		'lineHeight' => '',
	*		'imageWidth' => '',
	*		'imageMaxHeight' => '',
	*		'verticalImageMargin' => '',
	*		'horizontalImageMargin => '',
	*		'backgroundColor' => ''
	*	);
	*/
	private $header = array(
		'string' => '',
		'font' => '',
		'fontSize' => 18,
		'fontColor' => '#b0000b',
		'bgImage' => '',
		'scalebgImage' => false,
		'padding' => 0,
		'bgImageWidth' => 0,
		'bgImageHeight' => 0,
		'lineHeight' => '',
		'bgImageType' => 0,
		'theImage' => '',
	);
	private $footer = array(
		'string' => '',
		'font' => '',
		'fontSize' => 18,
		'fontColor' => '#b0000b',
		'bgImage' => '',
		'scalebgImage' => false,
		'padding' => 0,
		'bgImageWidth' => 0,
		'bgImageHeight' => 0,
		'lineHeight' => '',
		'bgImageType' => 0,
		'theImage' => '',
	);
	private $body = array(
		'string' => '',
		'font' => '',
		'fontSize' => 16,
		'fontColor' => '#000',
		'lineHeight' => '',
	);
	private $lineHeight = 1.5;
	private $imageWidth = 700;
	private $imageMaxHeight = '';
	private $verticalImageMargin = 10;
	private $horizontalImageMargin = 10;
	private $backgroundColor = '#FFF';

	private $imageHeight;
	private $theImage;
	private $maxWordLength;

	public function __construct($arr) {
		//merges $arr parameters into the default values; WARNING: CLASS DOES NOT
		//RUN WITHOUT PROVIDING THE REMAINDER OF THE VALUES
		if ( $arr['lineHeight'] != '' ) $this->lineHeight = $arr['lineHeight'];
		//if ( $arr['maxWordLength'] != '' ) $this->maxWordLength = $arr['maxWordLength'];
		if ( $arr['imageWidth'] != '' ) $this->imageWidth = $arr['imageWidth'];
		if ( $arr['imageMaxHeight'] != '' ) $this->imageMaxHeight = $arr['imageMaxHeight'];
		if ( $arr['verticalImageMargin'] != '' ) $this->verticalImageMargin = $arr['verticalImageMargin'];
		if ( $arr['horizontalImageMargin'] != '' ) $this->horizontalImageMargin = $arr['horizontalImageMargin'];
		if ( $arr['backgroundColor'] != '' ) $this->backgroundColor = $arr['backgroundColor'];

		$this->header = array_merge( $this->header, $arr['header'] );
		$this->footer = array_merge( $this->footer, $arr['footer'] );
		$this->body = array_merge( $this->body, $arr['body'] );

		//Sets lineheight in pixels for each section of the image
		$this->header['lineHeight'] = $this->header['fontSize'] * $this->lineHeight;
		$this->body['lineHeight'] = $this->body['fontSize'] * $this->lineHeight;
		$this->footer['lineHeight'] = $this->footer['fontSize'] * $this->lineHeight;

		//sets max word length
		$this->maxWordLength = $this->calc_max_word_length($this->body['font'], $this->body['fontSize']);

		//converts each string to an array with linebreaks
		$this->header['string'] = $this->wrap_text_to_width($this->header['string'], $this->header['font'], $this->header['fontSize']);
		$this->body['string'] = $this->wrap_text_to_width($this->body['string'], $this->body['font'], $this->body['fontSize']);
		$this->footer['string'] = $this->wrap_text_to_width($this->footer['string'], $this->footer['font'], $this->footer['fontSize']);

		//converts colors into rgb arrays
		$this->header['fontColor'] = $this->hex2RGB($this->header['fontColor']);
		$this->body['fontColor'] = $this->hex2RGB($this->body['fontColor']);
		$this->footer['fontColor'] = $this->hex2RGB($this->footer['fontColor']);
		$this->backgroundColor = $this->hex2RGB($this->backgroundColor);

		//gets the dimensions of the header and footer images
		//gets image type and loads it.  Allows png, gif, or jpeg
		if ( !empty( $this->header['bgImage'] ) ) $this->set_bg_image_info('header');
		if ( !empty( $this->footer['bgImage'] ) ) $this->set_bg_image_info('footer');

		$this->imageHeight = $this->calc_image_height();

//		echo $this->footer['bgImage'] . ' ' . $this->footer['bgImageWidth']
//			. ' ' . $this->footer['bgImageHeight'] . ' ' . $this->footer['bgImageType'];

		$this->make_image();
	}

	//generates the image from the data given to the class
	public function make_image() {

		$this->theImage = imagecreatetruecolor( $this->imageWidth, $this->imageHeight );

		//defines colors for the fonts and background
		$headerColor = imagecolorallocate($this->theImage, $this->header['fontColor']['red'], $this->header['fontColor']['green'], $this->header['fontColor']['blue']);
		$bodyColor = imagecolorallocate($this->theImage, $this->body['fontColor']['red'], $this->body['fontColor']['green'], $this->body['fontColor']['blue']);
		$footerColor = imagecolorallocate($this->theImage, $this->footer['fontColor']['red'], $this->footer['fontColor']['green'], $this->footer['fontColor']['blue']);
		$backgroundColor = imagecolorallocate($this->theImage, $this->backgroundColor['red'], $this->backgroundColor['green'], $this->backgroundColor['blue']);

		//fills the image with its background color
		imagefill($this->theImage, 0, 0, $backgroundColor);

		//inserts header and footer background images if they exist
		// only scales the header and footer image to fit the final image if it is
		// set to do so, and if your PHP version is over 5.5.0
		if ( !empty( $this->header['theImage'] ) ) {
			if ( ( PHP_VERSION_ID >= 50500 ) && ( $this->header['scalebgImage'] == true ) )
				imagescale($this->header['theImage'], $this->imageWidth);
			imagecopy($this->theImage, $this->header['theImage'], 0, 0, 0, 0, $this->header['bgImageWidth'], $this->header['bgImageHeight']);
		}
		if ( !empty( $this->footer['theImage'] ) ) {
			if ( ( PHP_VERSION_ID >= 50500 ) && ( $this->footer['scalebgImage'] == true ) )
				imagescale($this->footer['theImage'], $this->imageWidth);
			imagecopy($this->theImage, $this->footer['theImage'], 0, ( $this->imageHeight - $this->footer['bgImageHeight'] ), 0, 0, $this->footer['bgImageWidth'], $this->footer['bgImageHeight']);
		}

		$lineHeight = $this->verticalImageMargin + $this->header['fontSize'] + ( $this->footer['fontSize'] / 3 );
		if ( !empty($this->header['string'][0] ) ) {
			foreach ( $this->header['string'] as $s ) {
				imagettftext($this->theImage, $this->header['fontSize'], 0, $this->horizontalImageMargin, $lineHeight, $headerColor, $this->header['font'], $s);
				$lineHeight += $this->header['lineHeight'];
			}
		}

		$lineHeight += $this->verticalImageMargin + $this->header['padding'];
		foreach ( $this->body['string'] as $s ) {
			imagettftext($this->theImage, $this->body['fontSize'], 0, $this->horizontalImageMargin, $lineHeight, $bodyColor, $this->body['font'], $s);
			$lineHeight += $this->body['lineHeight'];
		}

		$lineHeight += $this->verticalImageMargin + $this->footer['padding'];
		if ( !empty($this->footer['string'][0] ) ) {
			foreach ( $this->footer['string'] as $s ) {
				imagettftext($this->theImage, $this->footer['fontSize'], 0, $this->horizontalImageMargin, $lineHeight, $footerColor, $this->footer['font'], $s);
				$lineHeight += $this->footer['lineHeight'];
			}
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

	//given font size and font, calculates max length of a word in the image
	private function calc_max_word_length($font, $fontSize, $sizerChar = 'T') {
		$maxWidth = $this->imageWidth - ( $this->horizontalImageMargin * 2 );
		$i = false; //sets to true when we reach max line length
		$s = ''; //adds a character per loop until reaches max line length
		while ( $i == false ) {
			$bb = imagettfbbox( $fontSize, 0, $font, $s );
			if ( $bb[2] - $bb[0] < $maxWidth ) {
				$s .= $sizerChar;
			} else {
				$i = true;
			}
		}
		return strlen($s) - 1;
	}

	//calculates the height that the image needs to be
	private function calc_image_height() {
		//calculate the margins
		$totalMargin = $this->verticalImageMargin * 4;
		$padding = $this->header['padding'] + $this->footer['padding'];
		$stringHeight = 0;

		//get the collective height for the header, footer, and body string sections
		if ( !empty($this->header['string'][0] ) ) {
			foreach ( $this->header['string'] as $s ) {
				$stringHeight += $this->header['lineHeight'];
			}
		}
		if ( !empty($this->footer['string'][0] ) ) {
			foreach ( $this->footer['string'] as $s ) {
				$stringHeight += $this->footer['lineHeight'];
			}
		}
		foreach ( $this->body['string'] as $s ) {
			$stringHeight += $this->body['lineHeight'];
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

	//sets width and height for background images (bgImage, bgImage)
	private function set_bg_image_info( $which ) {
		if ( $which == 'header' ) {
			$img = $this->header['bgImage'];
		} elseif ( $which == 'footer' ) {
			$img = $this->footer['bgImage'];
		}
		//check if file exists
		if ( file_exists($img) ) {
			$info = getimagesize($img);
			if ( $which == 'header' ) {
				$this->header['bgImageWidth'] = $info[0];
				$this->header['bgImageHeight'] = $info[1];
				$this->header['bgImageType'] = $info[2];
				//makes image object
				if ( $this->header['bgImageType'] == IMAGETYPE_PNG ) {
					$this->header['theImage'] = imagecreatefrompng($img);
				} elseif ( $this->header['bgImageType'] == IMAGETYPE_JPEG ) {
					$this->header['theImage'] = imagecreatefromjpeg($img);
				} elseif ( $this->header['bgImageType'] == IMAGETYPE_GIF ) {
					$this->header['theImage'] = imagecreatefromgif($img);
				}
			} elseif ( $which == 'footer' ) {
				$this->footer['bgImageWidth'] = $info[0];
				$this->footer['bgImageHeight'] = $info[1];
				$this->footer['bgImageType'] = $info[2];
				//makes image object
				if ( $this->footer['bgImageType'] == IMAGETYPE_PNG ) {
					$this->footer['theImage'] = imagecreatefrompng($img);
				} elseif ( $this->footer['bgImageType'] == IMAGETYPE_JPEG ) {
					$this->footer['theImage'] = imagecreatefromjpeg($img);
				} elseif ( $this->footer['bgImageType'] == IMAGETYPE_GIF ) {
					$this->footer['theImage'] = imagecreatefromgif($img);
				}
			}
		}
		return false;
	}

}
