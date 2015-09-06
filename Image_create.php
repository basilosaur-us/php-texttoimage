<?php

$arr = array (
	'header' => array(
		'headerString' => 'Dispatch sent ' . date('Y M d a h i T'),
		'headerFont' => 'fonts/gtw.ttf',
		'headerFontSize' => 18,
		'headerFontColor' => '',
		'headerImage' => '',
		'headerImageWidth' => '',
	),
	'footer' => array(
		'footerString' => 'This has been a test.',
		'footerFont' => 'fonts/gtw.ttf',
		'footerFontSize' => 18,
		'footerFontColor' => '',
		'footerImage' => '',
		'footerImageWidth' => '',
	),
	'body' => array(
		'bodyString' => 'Lorem ipsum dolor sit amet. When in the course of human events, or when it rains it pours. Pores? Mine have blackheads. Lorem ipsum dolor sit amet. When in the course of human events, or when it rains it pours. Pores? Mine have blackheads. Lorem ipsum dolor sit amet. When in the course of human events, or when it rains it pours. Pores? Mine have blackheads. Lorem ipsum dolor sit amet. When in the course of human events, or when it rains it pours. Pores? Mine have blackheads. Lorem ipsum dolor sit amet. When in the course of human events, or when it rains it pours. Pores? Mine have blackheads. Lorem ipsum dolor sit amet. When in the course of human events, or when it rains it pours. Pores? Mine have blackheads. Lorem ipsum dolor sit amet. When in the course of human events, or when it rains it pours. Pores? Mine have blackheads. Lorem ipsum dolor sit amet. When in the course of human events, or when it rains it pours. Pores? Mine have blackheads.',
		'bodyFont' => 'fonts/gtw.ttf',
		'bodyFontSize' => 16,
		'bodyFontColor' => '',
	),
	'lineHeight' => 2,
	'maxWordLength' => 27,
	'imageWidth' => 700,
	'imageMaxHeight' => '',
	'verticalImageMargin' => 20,
	'horizontalImageMargin' => 28,
	'backgroundColor' => '#b0000b'
);

$test = new Image_create($arr);
$test->image_to_browser();

class Image_create {
	/***************
	* STILL TO IMPLEMENT: background images, font colors, max height, background color
	*	Class takes an array structured like thus:
	*	array (
	*		'header' => array(
	*			'headerString' => '',
	*			'headerFont' => '',
	*			'headerFontSize' => '',
	*			'headerFontColor' => '',
	*			'headerImage' => '',
	*			'headerImageWidth' => '',
	*		),
	*		'footer' => array(
	*			'footerString' => '',
	*			'footerFont' => '',
	*			'footerFontSize' => '',
	*			'footerFontColor' => '',
	*			'footerImage' => '',
	*			'footerImageWidth' => '',
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
		'headerFontColor' => '',
		'headerImage' => '',
		'headerImageWidth' => '',
		'headerLineHeight' => '',
	);
	private $footer = array(
		'footerString' => '',
		'footerFont' => '',
		'footerFontSize' => 18,
		'footerFontColor' => '',
		'footerImage' => '',
		'footerImageWidth' => '',
		'footerLineHeight' => '',
	);
	private $body = array(
		'bodyString' => '',
		'bodyFont' => '',
		'bodyFontSize' => 16,
		'bodyFontColor' => '',
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

		$this->imageHeight = $this->calc_image_height();
/*		echo $this->header['headerLineHeight'] . '<br><br>';
		echo $this->body['bodyLineHeight'] . '<br><br>';
		echo $this->footer['footerLineHeight'] . '<br><br>';*/

		$this->make_image();
	}

	//generates the image from the data given to the class
	public function make_image() {

		$this->theImage = imagecreatetruecolor( $this->imageWidth, $this->imageHeight );
		$black = imagecolorallocate($this->theImage, 0, 0, 0);
		$white = imagecolorallocate($this->theImage, 255, 255, 255);
		imagefill($this->theImage, 0, 0, $white);

		$lineHeight = $this->verticalImageMargin + $this->header['headerFontSize'] + ( $this->footer['footerFontSize'] / 2 );
		//echo $lineHeight;
		foreach ( $this->header['headerString'] as $s ) {
			imagettftext($this->theImage, $this->header['headerFontSize'], 0, $this->horizontalImageMargin, $lineHeight, $black, $this->header['headerFont'], $s);
			$lineHeight += $this->header['headerLineHeight'];
		}

		$lineHeight += $this->verticalImageMargin;
		foreach ( $this->body['bodyString'] as $s ) {
			imagettftext($this->theImage, $this->body['bodyFontSize'], 0, $this->horizontalImageMargin, $lineHeight, $black, $this->body['bodyFont'], $s);
			$lineHeight += $this->body['bodyLineHeight'];
		}

		$lineHeight += $this->verticalImageMargin;
		foreach ( $this->footer['footerString'] as $s ) {
			imagettftext($this->theImage, $this->footer['footerFontSize'], 0, $this->horizontalImageMargin, $lineHeight, $black, $this->footer['footerFont'], $s);
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

		return $stringHeight + $totalMargin;
	}

}
