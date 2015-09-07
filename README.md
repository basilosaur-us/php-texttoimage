# php-texttoimage
A PHP class that takes text and turns it into a PNG file.  This will ultimately be appropriate for generating memes (just to offer one example).

This is very much a work in progress.  Right now, it will produce images that have black text on a white background.  Still to implement are:
* header and footer background images
* image height limiter

##The Image_create class takes the following array as its parameter:

$arr = 	array (
	'header' => array(
		'headerString' => '', //required
		'headerFont' => '', //required
		'headerFontSize' => 18,
		'headerFontColor' => '#000',
		'headerImage' => '',
		'headerImageWidth' => '',
	),
	'footer' => array(
		'footerString' => '', //required
		'footerFont' => '', //required
		'footerFontSize' => 18,
		'footerFontColor' => '#000',
		'footerImage' => '',
		'footerImageWidth' => '',
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

Members with defaults can be left out when you're setting up the class.  With the exception of 'Image,' 'ImageWidth,' and 'imageMaxHeight' entries, members that are blank are required.
