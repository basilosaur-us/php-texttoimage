# php-texttoimage
A PHP class that takes text and turns it into a PNG file.  This will ultimately be appropriate for generating memes (just to offer one example).

This is very much a work in progress.  Right now, it will produce images that have black text on a white background.  Still to implement are:
* header and footer background images
* image height limiter

##The Image_create class takes the following array as its parameter:

	$arr = 	array (
		'header' => array(
			'headerString' => '',
			'headerFont' => '', //required
			'headerFontSize' => 18,
			'headerFontColor' => '#000',
			'headerImage' => '',
			'scaleHeaderImage' => false,
			'headerPadding' => 0, //adds padding between the header and body text
		),
		'footer' => array(
			'footerString' => '',
			'footerFont' => '', //required
			'footerFontSize' => 18,
			'footerFontColor' => '#000',
			'footerImage' => '',
			'scaleFooterImage' => false,
			'footerPadding' => 0, //adds padding between the body and footer text
		),
		'body' => array(
			'bodyString' => '', //required
			'bodyFont' => '', //required
			'bodyFontSize' => 16,
			'bodyFontColor' => '#000',
		),
		'lineHeight' => 1.5,
		'maxWordLength' => 27,
		'imageWidth' => 700,
		'imageMaxHeight' => '',
		'verticalImageMargin' => 10,
		'horizontalImageMargin' => 10,
		'backgroundColor' => '#FFF'
	);

Members with defaults can be left out when you're setting up the class. Required fields are as marked.  Still on my to do list is to make it so that headerFont and footerFont are not required when there is no headerString and/or footerString defined.  But that's next, not now.

###Here is how to instantiate the class:
	$img = Image_create($arr); //where $arr is the array above;

###To use the class, try:
	$img->image_to_browser(); //to output directly to the browser as a PNG
	$img->image_to_file( $pathToImage ); //to export the image to a file.
