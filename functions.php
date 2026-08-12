<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

// Load .env mula sa theme folder mismo
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require get_theme_file_path('/inc/search-routing.php');

function university_custom_rest_api() {
  register_rest_field('post','authorName',array(
    'get_callback' => function() { 
      return get_the_author(); 
      }
  ));
}
add_action('rest_api_init', 'university_custom_rest_api');  


function pageBanner($args = []){
  
  $defaults = [
    'title'    => get_the_title(),
    'subtitle' => get_field('page_banner_subtitle'),
    'photo'    => get_field('page_banner_background_image') ? get_field('page_banner_background_image')['sizes']['pageBanner'] : get_theme_file_uri('/images/ocean.jpg')

    ];
$args = wp_parse_args($args, $defaults);

  ?>
  <div class="page-banner">
      <div class="page-banner__bg-image" style="background-image: url(<?php echo esc_url($args['photo']); ?> )"></div>
      <div class="page-banner__content container container--narrow">

        <h1 class="page-banner__title"><?php 
   if (!empty($args['title'])) {
            // If plain text (no HTML tags) → escape
            if ($args['title'] === strip_tags((string)$args['title'])) {
              echo esc_html($args['title']);
            } else {
              echo $args['title']; // raw output (WP functions with markup)
            }
          }
        
        ?></h1>
        <div class="page-banner__intro">
          <p><?php 

if (!empty($args['subtitle'])) {
              if ($args['subtitle'] === strip_tags((string)$args['subtitle'])) {
                echo esc_html($args['subtitle']);
              } else {
                echo $args['subtitle']; // raw output (WP functions with markup)
              }
            }
          
          ?></p>
        </div>
      </div>
    </div>
    <!-- <?php //endif; ?> -->
<?php }

function university_files(){
  wp_enqueue_script('googleMAp','https://maps.googleapis.com/maps/api/js?key=' . $_ENV['GOOGLE_MAPS_API_KEY'], NULL, '1.0', true);
  wp_enqueue_script('main-university-javascript',get_theme_file_uri('build/index.js'), array('jquery'), '1.0', true);
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');  
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('uniaversity_extra_styles', get_theme_file_uri('/build/index.css'));

  wp_localize_script('main-university-javascript','universityDataRootUrl', array(
    'root_url' => get_site_url()
  ));

  }

add_action('wp_enqueue_scripts','university_files');

function university_features(){
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_image_size('professorLandscape', 400, 260, true);
  add_image_size('professorPortrait', 480, 650, true);
  add_image_size('pageBanner', 1500, 350, true);
}

add_action('after_setup_theme','university_features');


function university_adjust_queries($query){
if(!is_admin() AND is_post_type_archive('campus') AND $query->is_main_query()){
  $query->set('posts_per_page', -1);
}

if(!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()){
  $query->set('orderby', 'title');
  $query->set('order', 'ASC');
  $query->set('posts_per_page', -1);
}

      if(!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()){
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
          array(
            'key'=> 'event_date',
            'compare' => '>=',
            'value'=> $today,
            'type' => 'numeric'
          )
         ));
      }
}
add_action('pre_get_posts','university_adjust_queries');

function universityMapKey($api){
  $api['key'] = $_ENV['GOOGLE_MAPS_API_KEY'];
  return $api;  
}

add_filter('acf/fields/google_map/api', 'universityMapKey');

//Redirect subscriber accounts out of admin and onto homepage
add_action('admin_init', 'redirectSubsToFrontend');

function redirectSubsToFrontend(){
  $ourCurrentUser = wp_get_current_user();

  if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber'){
    wp_redirect(site_url('/'));
    exit;
  }
}

add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar(){
  $ourCurrentUser = wp_get_current_user();

  if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber'){
    show_admin_bar(false);
  }
}


// Customize login screen
add_filter('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl(){
  return esc_url(site_url('/'));
}

add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS(){
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i'); 
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}

add_filter('login_headertext', 'ourLoginTitle');

function ourLoginTitle(){
  return get_bloginfo('name');
}