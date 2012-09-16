<?php
/**
 * @version		0.1
 * @package		JQuery Masonry (plugin)
 * @author              ctrlxctrlv (Cut & Paste) - http://whitelabelextensions.com
 * @copyright           Copyright (c)  2012 ctrlxctrlv. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgContentInfiniteScroll extends JPlugin {


	function plgContentInfiniteScroll( &$subject, $params ){
		parent::__construct( $subject, $params );
	}

	function onBeforeSurgeContentRender(){
            ////get document
            $doc =& JFactory::getDocument();
            //get params
            $params = $this->params;           
            //load jQuery
            switch ($params->get('jquery_loading'))
            {
                case 0:
                    $doc->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js");
                    break;
                case 1:
                    $doc->addScript(JURI::root(true) . '/plugins/content/infinite_scroll/js/jquery.min.js');
                    break;
                case 2:
                    break;
            }
            //TODO -->  NO SCROLL ON BODY CONTAINER (EG ITS LESS THAT THE HEIGHT OF THE SCREEN) 
            //          WILL CAUSE A FAIL IN THE LOADING OF NEW CONTENT
            
            //$doc->addScript( JURI::root(true) . '/plugins/content/InfiniteScroll/js/jquery.infinitescroll.js' );                       
            $content="               
                jQuery('body').ready(function(){
                     console.log(jQuery('div.list-footer'));
                    var container = jQuery('" . $params->get('container_selector') . "');                       
                    container.infinitescroll({
                    navSelector:'" . $params->get('nav_selector') ."',
                    nextSelector:'" . $params->get('next_selector') . "',
                    itemSelector:'" . $params->get('item_selector') . "',
                    bufferPx     : 400,
                    isAnimated:false,  
                    debug:true,
                    loadingImg:'" . JURI::base() . "media/com_surgeContent/img/blank.gif',
                    extractLink: true
                    }, function(newElements){
                            var \$surgeTarget = jQuery('#loading_indicator'); 
                            \$surgeTarget.removeClass('show_loader').addClass('hide_loader');
                            newElems = jQuery(newElements);
                            container.isotope( 'appended', newElems );
                            });
                });
            ";
            $doc->addScriptDeclaration($content); 
	}

	

} // End class
