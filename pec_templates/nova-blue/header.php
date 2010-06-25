<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>

    <title>
    	<?php $pecio->out('homepage_main_title'); ?> &raquo; 
    	<?php 
    		if ($pecio->get('site_view') == SITE_VIEW_ARTICLE) {
    			echo $pecio->get('article')->get_title();
    		}
    		elseif ($pecio->get('site_view') == SITE_VIEW_BLOG) {
    			echo 'Blog';
    		}
    		elseif ($pecio->get('site_view') == SITE_VIEW_BLOGPOST) {
    			echo $pecio->get('blogpost')->get_title();
    		}
    		elseif ($pecio->get('site_view') == SITE_VIEW_BLOGCATEGORY) {
    			echo 'Category: ' . $pecio->get('blogcategory')->get_name();
    		}
    		elseif ($pecio->get('site_view') == SITE_VIEW_BLOGTAG) {
    			echo 'Tag: ' . $pecio->get('blogtag')->get_name();
    		}
    		elseif ($pecio->get('site_view') == SITE_VIEW_BLOGARCHIVE) {
			    echo 'Blog Archive';
    		}
    		elseif ($pecio->get('site_view') == SITE_VIEW_HOME) {
    			echo 'Home';
    		}
    	?>
    </title>
    
    <?php $pecio->head_data(); ?>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <link rel="stylesheet" type="text/css" href="<?php $pecio->out('template_path'); ?>/css/layout.css"/>
    <link rel="stylesheet" type="text/css" href="<?php $pecio->out('template_path'); ?>/css/format.css"/>
    <link rel="stylesheet" type="text/css" href="<?php $pecio->out('template_path'); ?>/css/menu.css"/>
    <link rel="stylesheet" type="text/css" href="<?php $pecio->out('template_path'); ?>/css/blog.css"/>
     
  </head>

  <body>
    <div id="main_wrapper"> 
        
        <div id="header"><h1><a href="<?php $pecio->out('root_path'); ?>"><?php $pecio->out('homepage_main_title'); ?></a></h1></div>
        
        <div id="navi">
            <div id="menu">
                <?php $pecio->out('complete_menu'); ?>
                <div style="clear: left;"></div>
            </div>
            
            <div id="search"><?php $pecio->out('search_form'); ?></div>
            
            <div class="cleardiv"></div>
        </div>
        
        <div id="content_wrapper">
            <div id="left_content">
