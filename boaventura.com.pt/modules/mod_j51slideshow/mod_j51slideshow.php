<?php
/**
* @title	    J51 Slideshow
* @version		1.0
* @website		http://www.joomla51.com
* @copyright	Copyright (C) 2012 Joomla51.com. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$document 			= JFactory::getDocument();

$baseurl    		= JURI::base();
$width				= $params->get('thumbwidth');
$height				= $params->get('thumbheight');
$transfx			= $params->get('transfx');
$transdur			= $params->get('transdur');
$slideint			= $params->get('slideint');
$loadjquery	    	= $params->get('loadjquery');
$showarrows	    	= $params->get('showarrows');
$shownav	    	= $params->get('shownav');
$autoplay	    	= $params->get('autoplay');
$borderradius    	= $params->get('borderradius');
$bordercolor    	= $params->get('bordercolor');
$borderwidth    	= $params->get('borderwidth');
$navposition    	= $params->get('navposition');
$width100    		= $params->get('width100');



$document->addStyleSheet('modules/mod_j51slideshow/css/slideshow.css');

$style = '
	/* Set slideshow height/width */
		.slideshowcontainer {
			max-width:'. $width .'px;
			max-height:'. $height .'px;
		}

	/* Set slideshow border-radius*/
		.slideshowcontainer, .slidesjs-container, .slidesjs-container img {
			border: '. $borderwidth .'px solid '. $bordercolor .';
			border-radius: '. $borderradius .'px;
		}

	/* Navigation Position */
	.slidesjs-pagination {
		float: '. $navposition .';
	}
';

$document->addStyleDeclaration( $style );

/* 100% Width */
if($width100 == "true") : 
	$style = '
		.slideshowcontainer {
			max-width:100%;
		}
		.slideshowcontainer #slides {
			max-height: '. $height .'px;
		}
		#container_slideshow .wrapper960 {
			width:100%;
		}
		#body_bg {
    		box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.2);
		}
		#container_slideshow, #slideshow.block_holder {
			padding:0px !important;
			z-index: 0;
		}
		.slideshowcontainer, .slidesjs-container, .slidesjs-container img {
			border-radius: 0px;
		}		
	';
endif;

/* Prev/Next Toggle */
if($showarrows == "no") :
	$style = '
		.slidesjs-previous.slidesjs-navigation, .slidesjs-next.slidesjs-navigation {
			display:none;
		}
	';
endif;

$document->addStyleDeclaration( $style );

/* Navigation Toggle */
if($shownav == "no") : 
	$style = '
		.slideshowcontainer {
			max-height: '. $height .'px;
		}
		.slidesjs-pagination {
			display:none;
		}
	';
endif;

$document->addStyleDeclaration( $style );

require_once ('helper.php');

$list = SlideshowHelper::getimgList($params);

?>

<div class="slideshowcontainer">
	<a class="slidesjs-previous slidesjs-navigation" href="#" title="Previous"><i class="fa fa-chevron-left"></i></a>
	<div id="slides">
		<?php foreach($list as $item) { ?>	
				<img src="<?php echo $item->image ?>" alt="<?php echo $item->title ?>" />
		<?php } ?>
	</div>
</div>

<!-- SlidesJS Required: Link to jquery.slides.js -->
<script type="text/javascript" src="modules/mod_j51slideshow/js/jquery.slides.min.js" ></script>
<!-- End SlidesJS Required -->

<!-- SlidesJS Required: Initialize SlidesJS with a jQuery doc ready -->
<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function($) {

    $(function() {
      $('#slides').slidesjs({
        width: <?php echo $width; ?>,
        height: <?php echo $height; ?>,
        play: {
	        active: true,
	        effect: "<?php echo $transfx; ?>",
	        auto: <?php echo $autoplay; ?>,
	        interval: <?php echo $slideint; ?>,
	        swap: true
	    },
		<?php if($transfx == "fade") : ?> // Fade Effect Toggle
	        navigation: {
	        	effect: "fade"
	        },
	        pagination: {
	        	effect: "fade"
	        },
	        effect: {
	        	fade: {
	        	speed: <?php echo $transdur; ?>
         	}
		<?php endif; ?>

		<?php if($transfx == "slide") : ?> // Slide Effect Toggle
			effect: {
  				slide: {
    			speed: <?php echo $transdur; ?>,
  			}
		<?php endif; ?>

        }
      });
    });

});

</script>
<!-- End SlidesJS -->


