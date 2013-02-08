<?php

// no direct access
defined('_JEXEC') or die;

//START INFINITE SCROLL ----->
class plgContentInfinite_Scroll extends JPlugin
{

    public function __construct(& $subject, $config) {
        parent::__construct($subject, $config);
        $this->count = 0;
    }


    public function onContentPrepare($context, &$article, &$params, $page = 0) {
        $doc    =& JFactory::getDocument();
        $app    =& JFactory::getApplication();
        $params = $this->params;
        if ($this->count === 0) {
            switch ($params->get('jquery_loading')) {
                case 0:
                    $doc->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js");
                    break;
                case 1:
                    $doc->addScript(JURI::root(true) . '/plugins/content/infinite_scroll/js/jquery.min.js');
                    break;
                case 2:
                    break;
            }
            $doc->addScript(JURI::root(true) . '/plugins/content/infinite_scroll/js/jquery.infinitescroll.min.js');
            $content = "
                window.InfiniteConfig = {
                    container   : '" . $params->get('container_selector') . "',
                    navSelector : '" . $params->get('nav_selector') . "',
                    nextSelector: '" . $params->get('next_selector') . "',
                    itemSelector: '" . $params->get('item_selector') . "',
                    contentSelector: '" . $params->get('content_selector') . "',
                    baseURL     : '" . JURI::base(true) . "'
                }
                
            ";
            $doc->addScriptDeclaration($content);
        }

        $this->count++;
        return true;
    }
}

// END INFINITE SCROLL <-----
?>


