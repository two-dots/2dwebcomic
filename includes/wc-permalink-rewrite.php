<?php

// In case we're running standalone, for some odd reason
if (function_exists('add_action'))
{
	register_activation_hook(__FILE__, 'comic_rewrite_activate');
	register_deactivation_hook(__FILE__, 'comic_rewrite_deactivate');

	// Setup filters
	add_filter('generate_rewrite_rules', 'comic_rewrite_generate_rules');
	add_filter('post_link', 'comic_rewrite_post_link', 10, 2);
	
	global $comic_rules;
	$comic_rules = array();
}

function comic_rewrite_activate()
{
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function comic_rewrite_deactivate()
{
	// Remove the filters so we don't regenerate the wrong rules when we flush
	remove_filter('generate_rewrite_rules', 'comic_rewrite_generate_rules');
	remove_filter('post_link', 'comic_rewrite_post_link');

	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function comic_rewrite_generate_rules($wp_rewrite)
{
	//global $comic_rules;
	global $wp_rewrite;
	$comic_rules = array (
		'(comic)/([^/]+)/trackback/?$' => 'index.php?category_name=' . $wp_rewrite->preg_index(1) . '&name=' . $wp_rewrite->preg_index(2) . '&tb=1',
    	'(comic)/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?category_name=' . $wp_rewrite->preg_index(1) . '&name=' . $wp_rewrite->preg_index(2) . '&feed=' . $wp_rewrite->preg_index(3),
    	'(comic)/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?category_name=' . $wp_rewrite->preg_index(1) . '&name=' . $wp_rewrite->preg_index(2) . '&feed=' . $wp_rewrite->preg_index(3),
    	'(comic)/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?category_name=' . $wp_rewrite->preg_index(1) . '&name=' . $wp_rewrite->preg_index(2) . '&paged=' . $wp_rewrite->preg_index(3),
    	'(comic)/([^/]+)/comment-page-([0-9]{1,})/?$' => 'index.php?category_name=' . $wp_rewrite->preg_index(1) . '&name=' . $wp_rewrite->preg_index(2) . '&cpage=' . $wp_rewrite->preg_index(3),
    	'(comic)/([^/]+)(/[0-9]+)?/?$' => 'index.php?category_name=' . $wp_rewrite->preg_index(1) . '&name=' . $wp_rewrite->preg_index(2) . '&page=' . $wp_rewrite->preg_index(3)
    	);
	$wp_rewrite->rules = $comic_rules + $wp_rewrite->rules;
}

function comic_rewrite_post_link($post_link, $post_id)
{
	return preg_replace('|/comic/\d{4}/\d{2}|', '/comic', $post_link, 1);
}
