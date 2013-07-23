<?php

// no direct access
defined('_JEXEC') or die;

//START INFINITE SCROLL ----->
class plgSystemInfinite_Scroll extends JPlugin
{
    protected $count;

    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->count = 0;
    }


    public function onBeforeRender()
    {
        $doc = JFactory::getDocument();
        $app = JFactory::getApplication();
        $params = $this->params;

        $cats = $params->get('categories');
        $layout = $app->input->get('layout');
        $use_plugin = false;


        if ($app->isSite()) {
            $use_plugin = true;
        }

        //Only run if the layout is set to blog
        if ($layout == "blog" && $use_plugin) {
            $cat_id = $app->input->get('id');
            //Use the plugin if this category is selected or if no categories are selected
            $use_plugin = (in_array($cat_id, $cats) || (count($cats) == 0)) ? true : false;
        }

        if (!$use_plugin) {
            return false;
        } else {

            $inputs = $app->input->getArray(array(
                'Itemid' => 'int',
                'option' => 'string',
                'view' => 'string',
                'layout' => 'string',
                'id' => 'int',
                //'start' => 'int'
            ));

            $buffer = $doc->getBuffer();
            $context = $inputs['option'] . "." . $inputs['layout'];


            $limit = $app->getUserStateFromRequest($context . '.limit', 'limit', 5, 'uint');
            $limitStart = 0;

            $urlsegs = array();

            foreach ($inputs as $k => $v) {
                if ($v) {
                    $urlsegs[] = "$k=$v";
                }
            }

            $url = JURI::root() . "?" . implode('&', $urlsegs) . "&start=";

            $buffer['component'][""] .= "<div class='infiniteNavigation'></div><a href='$url' class='infiniteScrollNextLink'>&nbsp;</a></div>";

            $doc->setBuffer($buffer['component'][""], 'component', '');



            if ($params->get('preset') == "Beez5") {
                $config['container_selector']   = "body";
                $config['item_selector']        = ".items-row";
                $config['content_selector']     = ".blog";
            } elseif ($params->get('preset') == "Beez2") {
                $config['container_selector']   = "body";
                $config['item_selector']        = ".item";
                $config['content_selector']     = ".blog";
            } else {
                $config['container_selector']   = $params->get('container_selector');
                $config['item_selector']        = $params->get('item_selector');
                $config['content_selector']     = $params->get('content_selector');
            }


            if ($this->count === 0) {
                switch ($params->get('jquery_loading')) {
                    case 0:
                        $doc->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js");
                        $doc->addScriptDeclaration('jQuery.noConflict();');
                    case 1:
                        $doc->addScript(JURI::root() . '/plugins/system/infinite_scroll/js/jquery.min.js');
                        $doc->addScriptDeclaration('jQuery.noConflict();');
                        break;
                    case 2:
                        break;
                }
                $doc->addScript(JURI::root(true) . '/plugins/system/infinite_scroll/js/jquery.infinitescroll.min.js');
                $content = "
                window.InfiniteConfig = {
                    container   : '" . $config['container_selector'] . "',
                    nextSelector: '" . $params->get('next_selector') . "',
                    itemSelector: '" . $config['item_selector'] . "',
                    contentSelector: '" . $config['content_selector'] . "',
                    baseURL     : '/localhost',
                    url         : '$url',
                    limitStart  : $limitStart,
                    limit       : $limit,
                    finishedMsg : '" . $params->get('end_msg') ."',
                    msgText     : '" . $params->get('loading_msg') ."'
                }

            ";
                $doc->addScriptDeclaration($content);
            }

            $this->count++;

            return true;
        }
    }
}