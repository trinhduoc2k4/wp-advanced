<?php
require get_theme_file_path( '/inc/rest-api.php' );
//add field api wp
function registerField() {
    register_rest_field( 'post', 'authorName', array(
        'get_callback' => function() {
            return get_author_name();
        }
    ) );

    register_rest_field( 'page', 'authorName', array(
        'get_callback' => function() {
            return get_author_name();
        }
    ) );
}

add_action('rest_api_init', 'registerField');

function load_assets()
{
    wp_enqueue_style("font", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i", array(), "1.0", "all");
    wp_enqueue_style("bootstrapcss", "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css", array(), "1.1", "all");
    wp_enqueue_style("maincss", get_theme_file_uri() . "/build/index.css", array(), "1.0.2", "all");
    wp_enqueue_style("mainstylecss", get_theme_file_uri() . "/build/style-index.css", array(), "1.0.3", "all");

    wp_enqueue_script("scripts", get_theme_file_uri() . "/build/index.js", array('jquery'), "1.02", true);

    wp_localize_script( 'scripts', 'universityData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
    ) );
}

add_action("wp_enqueue_scripts", "load_assets");


function add_menu()
{
    add_theme_support("menus");
    register_nav_menus(array(
        'themeLocationOne' => 'Footer Menu One',
        'themeLocationTwo' => 'Footer Menu Two'
    ));
}

add_action("init", "add_menu");

//
function wpdocs_custom_excerpt_length($length)
{
    return 20;
}
add_filter('excerpt_length', 'wpdocs_custom_excerpt_length');

function add_author_support_to_posts()
{
    add_post_type_support('event', 'author');
}
add_action('init', 'add_author_support_to_posts');

//
function university_create_query($query)
{
    if (!is_admin() and is_post_type_archive('programmes') and $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }
    if (!is_admin() and is_post_type_archive('event') and $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('post_type', 'event');
        $query->set('meta_key', 'events_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key' => 'events_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric'
            )
        ));
    }
}
add_action('pre_get_posts', 'university_create_query');


//handle image
function wpdocs_theme_setup() {
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrail', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}
add_action('after_setup_theme', 'wpdocs_theme_setup');

//getbanner function
function getBanner() {
    $title = get_the_title();
    $subtitle = get_field('page_banner_subtitle');
    $pageBanner = empty(get_field('page_banner_background_image')) ? get_theme_file_uri( '/images/ocean.jpg' ) : get_field('page_banner_background_image')['sizes']["pageBanner"];
    ?>
        <div class="page-banner">
            <div class="page-banner__bg-image" style="background-image: url(<?php echo $pageBanner ?>);"></div>
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php echo $title ?></h1>
                <div class="page-banner__intro">
                    <p><?php echo $subtitle ?></p>
                </div>
            </div>
        </div>
    <?php
}

//redirect homepage when guest login
function redirectHomePage() {
    $guests = wp_get_current_user();
    if(count($guests->roles) == 1 AND $guests->roles[0] == 'subscriber') {
        wp_redirect( site_url('/') );
    }
}

add_action( 'admin_init', 'redirectHomePage' );

// hide admin bar 
function noAdminBar() {
    $guests = wp_get_current_user();
    if(count($guests->roles) == 1 AND $guests->roles[0] == 'subscriber') {
        show_admin_bar( false );
    }
}

add_action('wp_loaded', 'noAdminBar');

// redirect home when click logo wp

function logoWPClicked() {
    return esc_url(site_url('/'));
}

add_filter('login_headerurl', 'logoWPClicked');

//load css for login page

function login_loading_assets() {
    wp_enqueue_style("font", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i", array(), "1.0", "all");
    wp_enqueue_style("bootstrapcss", "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css", array(), "1.1", "all");
    wp_enqueue_style("maincss", get_theme_file_uri() . "/build/index.css", array(), "1.0.2", "all");
    wp_enqueue_style("mainstylecss", get_theme_file_uri() . "/build/style-index.css", array(), "1.0.3", "all");
}

add_action('login_enqueue_scripts', 'login_loading_assets');


// change login title
function change_title_login($headertext) {
    return get_bloginfo( 'name' );
}

add_filter('login_headertext', 'change_title_login');

function makeNotePrivate($data,$postarr) {
  if ($data['post_type'] == 'note') {
    if(count_user_posts(get_current_user_id(), 'note') >= 4 AND !$postarr['ID']) {
      die("You have reached your note limit.");
    }

    $data['post_content'] = sanitize_textarea_field($data['post_content']);
    $data['post_title'] = sanitize_text_field($data['post_title']);
  }

  if($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
    $data['post_status'] = "private";
  }
  
  return $data;
}

add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);
