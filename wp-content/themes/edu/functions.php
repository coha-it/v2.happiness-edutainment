<?php
/**
 * Edutainment 2016 functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Edutainment_2016
 */

if ( ! function_exists( 'edu_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function edu_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Edutainment 2016, use a find and replace
	 * to change 'edu' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'edu', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'edu' ),
	) );
	
		register_nav_menus( array(
		'subnary' => __( 'Subnary Menu', 'edu' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	 
	 /*
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );
	
	/*
	
		/*
	 * Remove unnecessary meta tags from WordPress header
	 */
	  remove_action( 'wp_head', 'wp_generator' ) ;
      remove_action( 'wp_head', 'wlwmanifest_link' ) ;
      remove_action( 'wp_head', 'rsd_link' ) ;


	// Set up the WordPress core custom background feature.
	/*
	add_theme_support( 'custom-background', apply_filters( 'edu_custom_background_args', array(
		'default-color' => 'F9F9F6',
		'default-image' => '',
	) ) );
	*/
	
}
endif;
add_action( 'after_setup_theme', 'edu_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function edu_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'edu_content_width', 794 );
}
add_action( 'after_setup_theme', 'edu_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function edu_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'edu' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	
		register_sidebar( array(
		'name'          => __( 'Sidebar Startseite', 'edu' ),
		'id'            => 'sidebar-start',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'edu_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function edu_scripts() {
	wp_enqueue_style( 'edu-style', get_stylesheet_uri() );

	wp_enqueue_script( 'edu-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'edu-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'edu_scripts', 999 );

/**
 * Implement the Custom Header feature.
 */
 /*
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
 /*
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';


/**
 ********************************************
//// THEME SETTING FINISHED ////////////////////
  *******************************************
 */

 /**
 ******************************************************************************************
 * Disable WordPress Login Hints
  ******************************************************************************************
 */

function no_wordpress_errors(){
  return 'Versuchen Sie es noch einmal';
}
add_filter( 'login_errors', 'no_wordpress_errors' );

/**
 ******************************************************************************************
 * Activate own style-login.css for LOGIN FORM
 * https://codex.wordpress.org/Customizing_the_Login_Form
  ******************************************************************************************
 */

function my_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_template_directory_uri() . '/style_login.css' );
}

add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );

/**
 ******************************************************************************************
 * ADDITIONAL LOGIN FORM HOOKS: https://css-tricks.com/snippets/wordpress/customize-login-page/
 ******************************************************************************************
 */

 
function change_wp_login_title() {
	return get_option('blogname');
}
add_filter('login_headertitle', 'change_wp_login_title');
 
function new_wp_login_url() {
	return home_url();
}
add_filter('login_headerurl', 'new_wp_login_url');
 
/**
 ******************************************************************************************
 * execute PHP scripts in text widget of wordpress by default
  ******************************************************************************************
 */


function php_execute($html){
if(strpos($html,"<"."?php")!==false){ ob_start(); eval("?".">".$html);
$html=ob_get_contents();
ob_end_clean();
}
return $html;
}
add_filter('widget_text','php_execute',100);

/**
 ******************************************************************************************
 * Restricting users to view only media library items they upload
  ******************************************************************************************
 */

function my_files_only( $wp_query ) {
    if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/upload.php' ) !== false ) {
        if ( !current_user_can( 'level_5' ) ) {
            global $current_user;
            $wp_query->set( 'author', $current_user->id );
        }
    }
}

add_filter('parse_query', 'my_files_only' );

/**
 ******************************************************************************************
 * Remove menu items from WordPress admin bar
 * http://www.catswhocode.com/blog/wordpress-dashboard-hacks-for-developers-and-freelancers
 ******************************************************************************************
 */

function wps_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('about');
    $wp_admin_bar->remove_menu('wporg');
    $wp_admin_bar->remove_menu('documentation');
    $wp_admin_bar->remove_menu('support-forums');
    $wp_admin_bar->remove_menu('feedback');
    $wp_admin_bar->remove_menu('view-site');
}
add_action( 'wp_before_admin_bar_render', 'wps_admin_bar' );

/**
 ******************************************************************************************
 * Remove dashboard menus for trainer
 * http://www.wpmayor.com/how-to-remove-menu-items-in-admin-depending-on-user-role/
 ******************************************************************************************
 */

add_action( 'admin_menu', 'remove_menus' );
function remove_menus(){
$user = wp_get_current_user();
if ( in_array( 'trainer', (array) $user->roles ) ) {
    //The user has the "author" role
		remove_submenu_page( 'themes.php' , 'nav-menus.php' );
		remove_submenu_page( 'themes.php' , 'customize.php?return=%2Fwp-admin%2Findex.php' );
		remove_submenu_page( 'themes.php' , 'themes.php' );
		remove_submenu_page( 'myCRED' , 'myCRED_page_hooks' );
		remove_submenu_page( 'myCRED' , 'myCRED_page_addons' );
		remove_submenu_page( 'myCRED' , 'myCRED_page_settings' );
		remove_menu_page( 'edit.php?post_type=sidebar' );
	}
}
 

/**
 ******************************************************************************************
 * Disable admin bar on the frontend of your website
 * for subscribers.
 ******************************************************************************************
 */
 
function themeblvd_disable_admin_bar() { 
	if( ! current_user_can('edit_posts') )
		add_filter('show_admin_bar', '__return_false');	
}
add_action( 'after_setup_theme', 'themeblvd_disable_admin_bar' );

/**
 ******************************************************************************************
 * ADD ONW STANDARD AVATAR
 * http://crunchify.com/how-to-change-default-avatar-in-wordpress/
 ******************************************************************************************
 */

add_filter( 'avatar_defaults', 'ourowngravatar' );
 
function ourowngravatar ($avatar_defaults) {
$myavatar = get_bloginfo('template_directory') . '/img/stand_avatar_coha_user.png';
$avatar_defaults[$myavatar] = "CoHa Avatar";
return $avatar_defaults;
}


/**
 ******************************************************************************************
* prevent editor from deleting, editing, or creating an administrator
* only needed if the editor was given right to edit users 
* http://isabelcastillo.com/editor-role-manage-users-wordpress
 ******************************************************************************************
 */
 
class ISA_User_Caps {
 
  // Add our filters
  function ISA_User_Caps(){
    add_filter( 'editable_roles', array(&$this, 'editable_roles'));
    add_filter( 'map_meta_cap', array(&$this, 'map_meta_cap'),10,4);
  }
  // Remove 'Administrator' from the list of roles if the current user is not an admin
  function editable_roles( $roles ){
    if( isset( $roles['administrator'] ) && !current_user_can('administrator') ){
      unset( $roles['administrator']);
    }
    return $roles;
  }
  // If someone is trying to edit or delete an
  // admin and that user isn't an admin, don't allow it
  function map_meta_cap( $caps, $cap, $user_id, $args ){
    switch( $cap ){
        case 'edit_user':
        case 'remove_user':
        case 'promote_user':
            if( isset($args[0]) && $args[0] == $user_id )
                break;
            elseif( !isset($args[0]) )
                $caps[] = 'do_not_allow';
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) ){
                if(!current_user_can('administrator')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        case 'delete_user':
        case 'delete_users':
            if( !isset($args[0]) )
                break;
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) ){
                if(!current_user_can('administrator')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        default:
            break;
    }
    return $caps;
  }
 
}
 
$isa_user_caps = new ISA_User_Caps();



 /**
 ******************************************************************************************
 * Show content only to group members https://buddypress.org/support/topic/show-content-for-specific-bp-groups-only/
  ******************************************************************************************
 */

add_shortcode( 'group', 'group_check_shortcode' );
// usage: [group id = YOUR_GROUP_ID_HERE] content [/group]

function group_check_shortcode( $atts, $content = null ) {
	extract(shortcode_atts(array( 'id' => '', ), $atts));
     
        if( groups_is_user_member( bp_loggedin_user_id(), $id ) ) { return do_shortcode( $content ); }
          
	if( $id == 1 ) {
		return '';
	} else { 
		$group = groups_get_group( array( 'group_id' => $id ) );
		//Put your User Groups Page Slug in the next code row.
		//Usually this is "groups", but in my case it is different.
		$url = home_url( $path = 'gruppen/' . $group->slug . '/', $scheme = relative );
		return '' ; 
}
} /*---------------------------------------------------------*/

  /* BUDDYPRESS Non Member User Shown START*/

function im_GetNonMemberUsers()
{
  global $wpdb;
  $imUserId = get_current_user_id();
  $imResult = $wpdb->get_results( 'SELECT * FROM wpedu_bp_groups_members WHERE user_id = '.$imUserId.'', OBJECT );  
  $imResultCount =  count($imResult);
  return $imResultCount ;
}


/* CRED Leaderboard Login User Assign Group According END*/

function GetCredGroupUsers()
{
  global $wpdb;
   $imCId = get_current_user_id();
  $imCResult = $wpdb->get_results( 'SELECT group_id FROM wpedu_bp_groups_members WHERE user_id = '.$imCId.'', OBJECT );  
  $imCResultCount =  count($imCResult);
  $imGroupUsers = array();
  if($imCResultCount>0)
      {
      foreach ( $imCResult as $imCRes => $imCGroupId )
          {
     
          $imCUserResult = $wpdb->get_results( 'SELECT user_id FROM wpedu_bp_groups_members WHERE group_id = '.$imCGroupId->group_id.'', OBJECT );  
          $imCUserResultCount =  count($imCUserResult);
          if($imCUserResultCount > 0)
              {
              
               foreach ( $imCUserResult as $imCUserRes => $imCUserId )
                   {
                      $imGroupUsers[$imCUserId->user_id] =  $imCUserId->user_id;
                   }
              
              }
          
          }
      }
  
    return $imGroupUsers ;
}


if ( ! function_exists( 'im_mycred_render_shortcode_leaderboard' ) ) :
	function im_mycred_render_shortcode_leaderboard( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'number'       => 25,
			'order'        => 'DESC',
			'offset'       => 0,
			'type'         => MYCRED_DEFAULT_TYPE_KEY,
			'based_on'     => 'balance',
			'wrap'         => 'li',
			'template'     => '#%position% %user_profile_link% %cred_f%',
			'nothing'      => 'Leaderboard is empty',
			'current'      => 0,
			'exclude_zero' => 1,
			'timeframe'    => ''
		), $atts ) );

		if ( ! MYCRED_ENABLE_LOGGING ) return '';

		if ( ! mycred_point_type_exists( $type ) )
			$type = MYCRED_DEFAULT_TYPE_KEY;

		if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) )
			$order = 'DESC';

		if ( $number != '-1' )
			$limit = 'LIMIT ' . absint( $offset ) . ',' . absint( $number );
		else
			$limit = '';

		$mycred = mycred( $type );

		global $wpdb;

		// Option to exclude zero balances
		$excludes = '';
		if ( $exclude_zero == 1 ) {

			$balance_format = '%d';
			if ( isset( $mycred->format['decimals'] ) && $mycred->format['decimals'] > 0 ) {
				$length         = 65 - $mycred->format['decimals'];
				$balance_format = 'CAST( %f AS DECIMAL( ' . $length . ', ' . $mycred->format['decimals'] . ' ) )';
			}

			$excludes = $wpdb->prepare( "AND um.meta_value != {$balance_format}", $mycred->zero() );

		}

		$based_on = sanitize_text_field( $based_on );

		// Leaderboard based on balance
		if ( $based_on == 'balance' ) {

			if ( mycred_centralize_log() ){
				$query = $wpdb->prepare( "SELECT DISTINCT u.ID, um.meta_value AS cred FROM {$wpdb->users} u INNER JOIN {$wpdb->usermeta} um ON ( u.ID = um.user_id ) WHERE um.meta_key = %s {$excludes} ORDER BY um.meta_value+0 {$order} {$limit};", mycred_get_meta_key( $type ) );

			// Multisite support
                        }else {
                          
				$blog_id = absint( $GLOBALS['blog_id'] );
				$query   = $wpdb->prepare( "
					SELECT DISTINCT u.ID, um.meta_value AS cred 
					FROM {$wpdb->users} u 
					INNER JOIN {$wpdb->usermeta} um ON ( u.ID = um.user_id ) 
					LEFT JOIN {$wpdb->usermeta} cap ON ( u.ID = cap.user_id AND cap.meta_key = 'cap.wp_{$blog_id}_capabilities' ) 
					WHERE um.meta_key = %s 
					{$excludes} 
					ORDER BY um.meta_value+0 
					{$order} {$limit};", mycred_get_meta_key( $type ) );
			}

		}

		// Leaderboard based on reference
		else {

			$time_filter = '';
			$now         = current_time( 'timestamp' );
			if ( $timeframe != '' ) {

				// Start of the week based of our settings
				$week_starts = get_option( 'start_of_week' );
				if ( $week_starts == 0 )
					$week_starts = 'sunday';
				else
					$week_starts = 'monday';

				// Filter: Daily
				if ( $timeframe == 'today' )
					$time_filter = $wpdb->prepare( "AND log.time BETWEEN %d AND %d", strtotime( 'today midnight', $now ), $now );

				// Filter: Weekly
				elseif ( $timeframe == 'this-week' )
					$time_filter = $wpdb->prepare( "AND log.time BETWEEN %d AND %d", strtotime( $week_starts . ' this week', $now ), $now );

				// Filter: Monthly
				elseif ( $timeframe == 'this-month' )
					$time_filter = $wpdb->prepare( "AND log.time BETWEEN %d AND %d", strtotime( 'Y-m-01', $now ), $now );

				else
					$time_filter = $wpdb->prepare( "AND log.time BETWEEN %d AND %d", strtotime( $timeframe, $now ), $now );

				$time_filter = apply_filters( 'mycred_leaderboard_time_filter', $time_filter, $based_on, $user_id, $ctype );

			}

			if ( mycred_centralize_log() )
				$query = $wpdb->prepare( "SELECT DISTINCT log.user_id AS ID, SUM( log.creds ) AS cred FROM {$mycred->log_table} log WHERE log.ref = %s {$time_filter} GROUP BY log.user_id ORDER BY SUM( log.creds ) {$order} {$limit};", $based_on );

			// Multisite support
			else {
				$blog_id = absint( $GLOBALS['blog_id'] );
				$query   = $wpdb->prepare( "
					SELECT DISTINCT log.user_id AS ID, SUM( log.creds ) AS cred 
					FROM {$mycred->log_table} log 
					LEFT JOIN {$wpdb->usermeta} cap ON ( log.user_id = cap.user_id AND cap.meta_key = 'cap.wp_{$blog_id}_capabilities' ) 
					WHERE log.ref = %s 
					{$time_filter} 
					GROUP BY log.user_id 
					ORDER BY SUM( log.creds ) 
					{$order} {$limit};", $based_on );
			}

		}




		$leaderboard  = $wpdb->get_results( apply_filters( 'mycred_ranking_sql', $query, $atts ), 'ARRAY_A' );
		$output       = '';
		$in_list      = false;
		$current_user = wp_get_current_user();

   $imCredGroupUser = GetCredGroupUsers();             
   $imleaderboard = array();
   $imCount = 0 ;
   
  foreach ( $leaderboard as $imPosition => $imUser ) 
      {
      if (in_array($imUser['ID'], $imCredGroupUser))
        {
       $imleaderboard[$imCount]['ID'] = $imUser['ID'] ;
       $imleaderboard[$imCount]['cred'] = $imUser['cred'] ;
        $imCount++;
        }
       
      }
      
  $leaderboard = $imleaderboard ;
		if ( ! empty( $leaderboard ) ) {

			// Check if current user is in the leaderboard
			if ( $current == 1 && is_user_logged_in() ) {

				// Find the current user in the leaderboard
				foreach ( $leaderboard as $position => $user ) {
					if ( $user['ID'] == $current_user->ID ) {
						$in_list = true;
						break;
					}
				}

			}

			// Load myCRED
			$mycred = mycred( $type );

			// Wrapper
			if ( $wrap == 'li' )
				$output .= '<ol class="myCRED-leaderboard list-unstyled">';

			// Loop
			foreach ( $leaderboard as $position => $user ) {

				// Prep
				$class = array();

				// Position
				if ( $offset != '' && $offset > 0 )
					$position = $position + $offset;

				// Classes
				$class[] = 'item-' . $position;
				if ( $position == 0 )
					$class[] = 'first-item';

				if ( $user['ID'] == $current_user->ID )
					$class[] = 'current-user';

				if ( $position % 2 != 0 )
					$class[] = 'alt';

				$row_template = $template;
				if ( ! empty( $content ) )
					$row_template = $content;

				// Template Tags
				$layout = str_replace( array( '%ranking%', '%position%' ), $position+1, $row_template );

				$layout = $mycred->template_tags_amount( $layout, $user['cred'] );
				$layout = $mycred->template_tags_user( $layout, $user['ID'] );

				// Wrapper
				if ( ! empty( $wrap ) )
					$layout = '<' . $wrap . ' class="%classes%">' . $layout . '</' . $wrap . '>';

				$layout = str_replace( '%classes%', apply_filters( 'mycred_ranking_classes', implode( ' ', $class ) ), $layout );
				$layout = apply_filters( 'mycred_ranking_row', $layout, $template, $user, $position+1 );

				$output .= $layout . "\n";

			}

			// Current user is not in list but we want to show his position
			if ( ! $in_list && $current == 1 && is_user_logged_in() ) {

				// Flush previous query
				$wpdb->flush();

				$current_position = mycred_render_shortcode_leaderbaord_position( array(
					'based_on'  => $based_on,
					'user_id'   => 'current',
					'timeframe' => $timeframe,
					'ctype'     => $type
				), $content );

				$row_template = $template;
				if ( ! empty( $content ) )
					$row_template = $content;

				// Template Tags
				$layout = str_replace( array( '%ranking%', '%position%' ), $current_position, $row_template );

				$layout = $mycred->template_tags_amount( $layout, $mycred->get_users_balance( $current_user->ID, $type ) );
				$layout = $mycred->template_tags_user( $layout, false, $current_user );

				// Wrapper
				if ( ! empty( $wrap ) )
					$layout = '<' . $wrap . ' class="%classes%">' . $layout . '</' . $wrap . '>';

				$layout = str_replace( '%classes%', apply_filters( 'mycred_ranking_classes', implode( ' ', $class ) ), $layout );
				$layout = apply_filters( 'mycred_ranking_row', $layout, $template, $current_user, $current_position );

				$output .= $layout . "\n";

			}

			if ( $wrap == 'li' )
				$output .= '</ol>';

		}

		// No result template is set
		else {

			$output .= '<p class="mycred-leaderboard-none">' . $nothing . '</p>';

		}

		return do_shortcode( apply_filters( 'im_mycred_leaderboard', $output, $atts ) );

	}
endif;
add_shortcode( 'im_mycred_leaderboard', 'im_mycred_render_shortcode_leaderboard' );

/* CRED Leaderboard Login User Assign Group According END*/

 function GetNonMemberUsersGroup($atts, $content = null)
    {
         $imContent = "";
          if(im_GetNonMemberUsers() == 0){  $imContent = do_shortcode($content); }

         return $imContent;
    }
add_shortcode('groups', 'GetNonMemberUsersGroup');

/* BUDDYPRESS Non Member User Shown END*/
 
 /**
 ******************************************************************************************
 * BUDDYPRESS exclude User
 V1  http://buddydev.com/buddypress/exclude-users-from-members-directory-on-a-buddypress-based-social-network/
 here V2 http://buddydev.com/buddypress/hiding-users-on-buddypress-based-site/
  ******************************************************************************************
 */
 
 add_filter( 'bp_after_has_members_parse_args', 'buddydev_exclude_users_by_role' );
 
function buddydev_exclude_users_by_role( $args ) {
    //do not exclude in admin
    if( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return $args;
    }
    
    $excluded = isset( $args['exclude'] )? $args['exclude'] : array();
 
    if( !is_array( $excluded ) ) {
        $excluded = explode(',', $excluded );
    }
    
    $role = 'bbp_blocked';//change to the role to be excluded
    $user_ids1 =  get_users( array( 'role' => $role ,'fields'=>'ID') );
	$role = 'administrator';//change to the role to be excluded
    $user_ids2 =  get_users( array( 'role' => $role ,'fields'=>'ID') );
	    
    $excluded = array_merge( $excluded, $user_ids1, $user_ids2 );
    
    $args['exclude'] = $excluded;
    
    return $args;
}

 
/**
 ******************************************************************************************
 * BBPRESS HACKS https://codex.bbpress.org/layout-and-functionality-examples-you-can-use/
  ******************************************************************************************
 */


//create vertical list & Remove the topic/reply count from the forum list
function custom_bbp_sub_forum_list() {
$args['separator'] = '<br>';
$args['show_topic_count'] = false;
$args['show_reply_count'] = false;
$args['count_sep'] = '';
 return $args;
 }
 
 add_filter('bbp_before_list_forums_parse_args', 'custom_bbp_sub_forum_list' );
 
 // turns on WordPress’s visual editor and ONLY the visuell version
 // https://codex.bbpress.org/enable-visual-editor/
/*
 
function bbp_enable_visual_editor( $args = array() ) {
    $args['tinymce'] = true;
    $args['quicktags'] = false;
    return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );

*/


/**
 ******************************************************************************************
 * Videos in WordPress Responsive einbetten http://wphafen.com/wordpress-youtube-videos-responsive-einbetten/
  ******************************************************************************************
 */
add_filter('embed_oembed_html', 'my_embed_oembed_html', 99, 4);
function my_embed_oembed_html($html, $url, $attr, $post_id) {
	return '<div class="video-container">' . $html . '</div>';
}

 
/**
 ******************************************************************************************
myCRED HOOKS: -->>
 */
  /**
 ******************************************************************************************
 https://wordpress.org/support/topic/how-to-include-user-avatar-image-in-leaderboard-widget-sidebar?replies=11
 ******************************************************************************************
 */
 
add_filter( 'mycred_ranking_row', 'my_custom_ranking_rows', 10, 4 );
function my_custom_ranking_rows( $layout, $template, $row, $position )
{
	$avatar = get_avatar( $row['ID'], 32 );
	return str_replace( '%avatar%', $avatar, $layout );
}
 
 /**
 ******************************************************************************************
Rank progress Shortcode
https://gist.github.com/codelion7/9cbd4dcf33d1716397e7#file-mycred-users-rank-progress-shortcode
 ******************************************************************************************
 */

 
// Get user's rank progress
function get_mycred_users_rank_progress( $user_id, $show_rank ) {
	global $wpdb;
 
	if ( ! function_exists( 'mycred' ) ) return '';
	
	// Change rank data to displayed user when on a user's profile
	if ( function_exists( 'bp_is_user' ) && bp_is_user() && empty( $user_id ) ) {
		$user_id = bp_displayed_user_id();
	}
 
	// Load myCRED
	$mycred = mycred();
 
	// Ranks are based on a total
	if ( $mycred->rank['base'] == 'total' )
		$key = $mycred->get_cred_id() . '_total';
			
	// Ranks are based on current balance
	else
		$key = $mycred->get_cred_id();
 
	// Get Balance
	$users_balance = $mycred->get_users_cred( $user_id, $key );
   
	// Rank Progress
	
	// Get the users current rank post ID
	$users_rank = mycred_get_users_rank_id( $user_id, $type_id );

	
	// Get the name of the users current rank
	$users_rank_name = get_the_title( $users_rank );
   
	// Get the ranks set max
	$max = get_post_meta( $users_rank, 'mycred_rank_max', true );
	
	$tabl_name = $wpdb->prefix . 'postmeta';
	
	// Get the users next rank post ID
	$next_ranks = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$tabl_name} WHERE meta_key = %s AND meta_value > %d ORDER BY meta_value * 1 LIMIT 1;", 'mycred_rank_min', $max ) );
 
    foreach( $next_ranks as $next_rank ) {
 
        $next_rank = $next_rank->post_id;
    }
	
	// Get the name of the users next rank
	$next_rank_name = get_the_title( $next_rank );
	
	// Get the ranks set min
	$next_rank_min = get_post_meta( $next_rank, 'mycred_rank_min', true );
   
	// Calculate progress. We need a percentage with 1 decimal
	$progress = number_format( ( ( $users_balance / $max ) * 100 ), 0 );
 
	// Display rank progress bar
	echo '<div class="mycred-rank-progress">';	
		echo '<h3 class="rank-progress-label" style="font-weight:bold;padding-bottom:1%;">Ihr Fortschritt ('. $progress .'%)</h3>';
		echo 'bis zum nächsten Rang </br>';
		echo '<progress max="'. $max .'" value="'. $users_balance .'" class="rank-progress-bar">';
		echo '</progress>';	
		if( $show_rank == 'yes' ){
			echo '<span class="current-rank" style="float:left;padding-top:1%;">'. $users_rank_name .'</span>';	
			echo '<span class="next-rank" style="float:right;padding-top:1%;">'. $next_rank_name .'</span>';
			echo '<span class="points-progress" style="width:100%;float:left;margin-top: -3.5%;padding-top:1%;text-align:center;">'. $users_balance .' von '. $next_rank_min .'</span>';
		}
	echo '</div>';	
}
 
/**
 * myCRED Shortcode: mycred_users_rank_progress
 * @since 1.0
 * @version 1.0
 */
function mycred_users_rank_progress( $atts ){
	extract( shortcode_atts( array(
		'user_id' => get_current_user_id(),
		'show_rank' => 'no'
	), $atts ) );
 
	ob_start();
	
	get_mycred_users_rank_progress( $user_id, $show_rank );
 
	$output = ob_get_contents();
	ob_end_clean();
 
	return $output;
 
}
add_shortcode( 'mycred_users_rank_progress', 'mycred_users_rank_progress' );
 
 
/**
 ******************************************************************************************
* myCRED Hook for LearnDash
https://gist.github.com/codelion7/96efe35ad56bd27b3d5f
* @since 1.0
* @version 1.0
 ******************************************************************************************
*/
 
/**
* Register Custom myCRED Hook
* @since 1.0
* @version 1.0
*/
add_filter( 'mycred_setup_hooks', 'Learndash_myCRED_Hook' );
function Learndash_myCRED_Hook( $installed ) {
 
$installed['hook_learndash'] = array(
'title' => __( 'LearnDash', 'mycred' ),
'description' => __( 'Awards %_plural% for LearnDash actions.', 'mycred' ),
'callback' => array( 'myCRED_Hook_Learndash' )
);
return $installed;
}
 
/**
* Hook for LearnDash
* @since 1.0
* @version 1.0
*/
 
if ( ! class_exists( 'myCRED_Hook_Learndash' ) && class_exists( 'myCRED_Hook' ) ) {
class myCRED_Hook_Learndash extends myCRED_Hook {
/**
* Construct
*/
function __construct( $hook_prefs, $type = 'mycred_default' ) {
parent::__construct( array(
'id' => 'hook_learndash',
'defaults' => array(
'course_completed' => array(
'creds' => 0,
'log' => '%plural% for Completing a Course'
),
'lesson_completed' => array(
'creds' => 0,
'log' => '%plural% for Completing a Lesson'
),
'topic_completed' => array(
'creds' => 0,
'log' => '%plural% for Completing a Topic'
),
'quiz_completed' => array(
'creds' => 0,
'log' => '%plural% for Completing a Quiz'
)
)
), $hook_prefs, $type );
}
/**
* Run
* @since 1.0
* @version 1.1
*/
public function run() {
// Course Completed
if ( $this->prefs['course_completed']['creds'] != 0 )
add_action( 'learndash_course_completed', array( $this, 'course_completed' ), 5, 1 );
// Lesson Completed
if ( $this->prefs['lesson_completed']['creds'] != 0 )
add_action( 'learndash_lesson_completed', array( $this, 'lesson_completed' ), 5, 1 );
// Topic Completed
if ( $this->prefs['topic_completed']['creds'] != 0 )
add_action( 'learndash_topic_completed', array( $this, 'topic_completed' ), 5, 1 );
// Quiz Completed
if ( $this->prefs['quiz_completed']['creds'] != 0 )
add_action( 'learndash_quiz_completed', array( $this, 'quiz_completed' ), 5, 1 );
add_filter( 'mycred_all_references', array( $this, 'add_references' ) );
}
/**
* Course Completed
* @since 1.0
* @version 1.1
*/
public function course_completed( $data ) {
$course_id = $data['course']->ID;
// Must be logged in
if ( ! is_user_logged_in() ) return;
 
$user_id = get_current_user_id();
// Check if user is excluded
if ( $this->core->exclude_user( $user_id ) ) return;
 
// Make sure this is unique event
if ( $this->core->has_entry( 'course_completed', $course_id, $user_id ) ) return;
 
// Execute
$this->core->add_creds(
'course_completed',
$user_id,
$this->prefs['course_completed']['creds'],
$this->prefs['course_completed']['log'],
$course_id,
array( 'ref_type' => 'post' ),
$this->mycred_type
);
}
 
/**
* Lesson Completed
* @since 1.0
* @version 1.1
*/
public function lesson_completed( $data ) {
$lesson_id = $data['lesson']->ID;
// Must be logged in
if ( ! is_user_logged_in() ) return;
 
$user_id = get_current_user_id();
// Check if user is excluded
if ( $this->core->exclude_user( $user_id ) ) return;
 
// Make sure this is unique event
if ( $this->core->has_entry( 'lesson_completed', $lesson_id, $user_id ) ) return;
 
// Execute
$this->core->add_creds(
'lesson_completed',
$user_id,
$this->prefs['lesson_completed']['creds'],
$this->prefs['lesson_completed']['log'],
$lesson_id,
array( 'ref_type' => 'post' ),
$this->mycred_type
);
}
 
/**
* Topic Completed
* @since 1.0
* @version 1.1
*/
public function topic_completed( $data ) {
$topic_id = $data['topic']->ID;
// Must be logged in
if ( ! is_user_logged_in() ) return;
 
$user_id = get_current_user_id();
// Check if user is excluded
if ( $this->core->exclude_user( $user_id ) ) return;
 
// Make sure this is unique event
if ( $this->core->has_entry( 'topic_completed', $topic_id, $user_id ) ) return;
 
// Execute
$this->core->add_creds(
'topic_completed',
$user_id,
$this->prefs['topic_completed']['creds'],
$this->prefs['topic_completed']['log'],
$topic_id,
array( 'ref_type' => 'post' ),
$this->mycred_type
);
}
 
/**
* Quiz Completed
* @since 1.0
* @version 1.1
*/
public function quiz_completed( $data ) {
$quiz_id = $data['quiz']->ID;
// Must be logged in
if ( ! is_user_logged_in() ) return;
 
$user_id = get_current_user_id();
// Check if user is excluded
if ( $this->core->exclude_user( $user_id ) ) return;
 
// Make sure this is unique event
if ( $this->core->has_entry( 'quiz_completed', $quiz_id, $user_id ) ) return;
 
// Execute
$this->core->add_creds(
'quiz_completed',
$user_id,
$this->prefs['quiz_completed']['creds'],
$this->prefs['quiz_completed']['log'],
$quiz_id,
array( 'ref_type' => 'post' ),
$this->mycred_type
);
}
 
/**
* Register Custom myCRED References
* @since 1.0
* @version 1.0
*/
public function add_references( $references ) {
 
// LearnDash References
$references['course_completed'] = 'Completed Course';
$references['lesson_completed'] = 'Completed Lesson';
$references['topic_completed'] = 'Completed Topic';
$references['quiz_completed'] = 'Completed Quiz';
return $references;
}
 
/**
* Preferences for LearnDash
* @since 1.1
* @version 1.0
*/
public function preferences() {
$prefs = $this->prefs; ?>
 
<label class="subheader" for="<?php echo $this->field_id( array( 'course_completed' => 'creds' ) ); ?>"><?php _e( 'Completing a Course', 'mycred' ); ?></label>
<ol>
<li>
<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'course_completed' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'course_completed' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['course_completed']['creds'] ); ?>" size="8" /></div>
</li>
</ol>
<label class="subheader" for="<?php echo $this->field_id( array( 'course_completed' => 'log' ) ); ?>"><?php _e( 'Log Template', 'mycred' ); ?></label>
<ol>
<li>
<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'course_completed' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'course_completed' => 'log' ) ); ?>" value="<?php echo esc_attr( $prefs['course_completed']['log'] ); ?>" class="long" /></div>
<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
</li>
</ol>
 
<label class="subheader" for="<?php echo $this->field_id( array( 'lesson_completed' => 'creds' ) ); ?>"><?php _e( 'Completing a Lesson', 'mycred' ); ?></label>
<ol>
<li>
<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'lesson_completed' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'lesson_completed' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['lesson_completed']['creds'] ); ?>" size="8" /></div>
</li>
</ol>
<label class="subheader" for="<?php echo $this->field_id( array( 'lesson_completed' => 'log' ) ); ?>"><?php _e( 'Log Template', 'mycred' ); ?></label>
<ol>
<li>
<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'lesson_completed' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'lesson_completed' => 'log' ) ); ?>" value="<?php echo esc_attr( $prefs['lesson_completed']['log'] ); ?>" class="long" /></div>
<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
</li>
</ol>
 
<label class="subheader" for="<?php echo $this->field_id( array( 'topic_completed' => 'creds' ) ); ?>"><?php _e( 'Completing a Topic', 'mycred' ); ?></label>
<ol>
<li>
<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'topic_completed' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'topic_completed' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['topic_completed']['creds'] ); ?>" size="8" /></div>
</li>
</ol>
<label class="subheader" for="<?php echo $this->field_id( array( 'topic_completed' => 'log' ) ); ?>"><?php _e( 'Log Template', 'mycred' ); ?></label>
<ol>
<li>
<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'topic_completed' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'topic_completed' => 'log' ) ); ?>" value="<?php echo esc_attr( $prefs['topic_completed']['log'] ); ?>" class="long" /></div>
<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
</li>
</ol>
 
<label class="subheader" for="<?php echo $this->field_id( array( 'quiz_completed' => 'creds' ) ); ?>"><?php _e( 'Completing a Quiz', 'mycred' ); ?></label>
<ol>
<li>
<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'quiz_completed' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'quiz_completed' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['quiz_completed']['creds'] ); ?>" size="8" /></div>
</li>
</ol>
<label class="subheader" for="<?php echo $this->field_id( array( 'quiz_completed' => 'log' ) ); ?>"><?php _e( 'Log Template', 'mycred' ); ?></label>
<ol>
<li>
<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'quiz_completed' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'quiz_completed' => 'log' ) ); ?>" value="<?php echo esc_attr( $prefs['quiz_completed']['log'] ); ?>" class="long" /></div>
<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
</li>
</ol>
<?php
}
}
} 
?>
