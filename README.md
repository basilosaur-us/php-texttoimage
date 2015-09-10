# php-texttoimage
A PHP class that takes text and turns it into a PNG file.  This will ultimately be appropriate for generating memes (just to offer one example).

This is very much a work in progress.  Right now, it will produce images that have black text on a white background.  Still to implement is:
* image height limiter

##The Image_create class takes the following array as its parameter:

	$arr = 	array (
		'header' => array(
			'string' => '',
			'font' => '', //required
			'fontSize' => 18,
			'fontColor' => '#b0000b',
			'bgImage' => '',
			'scalebgImage' => false, //scaling only works in PHP 5.5 and above
			'padding' => 0, //padding between the header and body
		),
		'footer' => array(
			'string' => '',
			'font' => '', //required
			'fontSize' => 18,
			'fontColor' => '#b0000b',
			'bgImage' => '',
			'scalebgImage' => false, //scaling only works in PHP 5.5 and above
			'padding' => 0, //padding between the body and footer
		),
		'body' => array(
			'string' => '', //required
			'font' => '', //required
			'fontSize' => 16,
			'fontColor' => '#000',
		),
		'lineHeight' => 1.5,
		'imageWidth' => 700,
		'imageMaxHeight' => '',
		'verticalImageMargin' => 10,
		'horizontalImageMargin' => 10,
		'backgroundColor' => '#fff'
	);


Members with defaults can be left out when you're setting up the class. Required fields are as marked.  Still on my to do list is to make it so that headerFont and footerFont are not required when there is no headerString and/or footerString defined.  But that's next, not now.

###Here is how to instantiate the class:
	$img = Image_create($arr); //where $arr is the array above;

###To use the class, try:
	$img->image_to_browser(); //to output directly to the browser as a PNG
	$img->image_to_file( $pathToImage ); //to export the image to a file.
