<?php
add_action( 'after_setup_theme', 'ucwTheme_setup' );
function ucwTheme_setup() {
load_theme_textdomain( 'ucwTheme', get_template_directory() . '/languages' );
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'responsive-embeds' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'html5', array( 'search-form' ) );
add_theme_support( 'woocommerce' );
global $content_width;
if ( !isset( $content_width ) ) { $content_width = 1920; }
register_nav_menus( array( 'main-menu' => esc_html__( 'Main Menu', 'ucwTheme' ) ) );
}
add_action( 'admin_notices', 'ucwTheme_admin_notice' );
function ucwTheme_admin_notice() {
$user_id = get_current_user_id();
if ( !get_user_meta( $user_id, 'ucwTheme_notice_dismissed_4' ) && current_user_can( 'manage_options' ) )
echo '<div class="notice notice-info"><p>' . __( '<big><strong>ucwTheme</strong>:</big> Help keep the project alive! <a href="?notice-dismiss" class="alignright">Dismiss</a> <a href="https://calmestghost.com/donate" class="button-primary" target="_blank">Make a Donation</a>', 'ucwTheme' ) . '</p></div>';
}
add_action( 'admin_init', 'ucwTheme_notice_dismissed' );
function ucwTheme_notice_dismissed() {
$user_id = get_current_user_id();
if ( isset( $_GET['notice-dismiss'] ) )
add_user_meta( $user_id, 'ucwTheme_notice_dismissed_4', 'true', true );
}
add_action( 'wp_enqueue_scripts', 'ucwTheme_enqueue' );
function ucwTheme_enqueue() {
wp_enqueue_style( 'ucwTheme-style', get_stylesheet_uri() );
wp_enqueue_script( 'jquery' );
}
add_action( 'wp_footer', 'ucwTheme_footer' );
function ucwTheme_footer() {
?>
<script>
jQuery(document).ready(function($) {
var deviceAgent = navigator.userAgent.toLowerCase();
if (deviceAgent.match(/(iphone|ipod|ipad)/)) {
$("html").addClass("ios");
}
if (navigator.userAgent.search("MSIE") >= 0) {
$("html").addClass("ie");
}
else if (navigator.userAgent.search("Chrome") >= 0) {
$("html").addClass("chrome");
}
else if (navigator.userAgent.search("Firefox") >= 0) {
$("html").addClass("firefox");
}
else if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
$("html").addClass("safari");
}
else if (navigator.userAgent.search("Opera") >= 0) {
$("html").addClass("opera");
}
});
</script>
<?php
}
add_filter( 'document_title_separator', 'ucwTheme_document_title_separator' );
function ucwTheme_document_title_separator( $sep ) {
$sep = '|';
return $sep;
}
add_filter( 'the_title', 'ucwTheme_title' );
function ucwTheme_title( $title ) {
if ( $title == '' ) {
return '...';
} else {
return $title;
}
}
add_filter( 'nav_menu_link_attributes', 'ucwTheme_schema_url', 10 );
function ucwTheme_schema_url( $atts ) {
$atts['itemprop'] = 'url';
return $atts;
}
if ( !function_exists( 'ucwTheme_wp_body_open' ) ) {
function ucwTheme_wp_body_open() {
do_action( 'wp_body_open' );
}
}
add_action( 'wp_body_open', 'ucwTheme_skip_link', 5 );
function ucwTheme_skip_link() {
echo '<a href="#content" class="skip-link screen-reader-text">' . esc_html__( 'Skip to the content', 'ucwTheme' ) . '</a>';
}
add_filter( 'the_content_more_link', 'ucwTheme_read_more_link' );
function ucwTheme_read_more_link() {
if ( !is_admin() ) {
return ' <a href="' . esc_url( get_permalink() ) . '" class="more-link">' . sprintf( __( '...%s', 'ucwTheme' ), '<span class="screen-reader-text">  ' . esc_html( get_the_title() ) . '</span>' ) . '</a>';
}
}
add_filter( 'excerpt_more', 'ucwTheme_excerpt_read_more_link' );
function ucwTheme_excerpt_read_more_link( $more ) {
if ( !is_admin() ) {
global $post;
return ' <a href="' . esc_url( get_permalink( $post->ID ) ) . '" class="more-link">' . sprintf( __( '...%s', 'ucwTheme' ), '<span class="screen-reader-text">  ' . esc_html( get_the_title() ) . '</span>' ) . '</a>';
}
}
add_filter( 'big_image_size_threshold', '__return_false' );
add_filter( 'intermediate_image_sizes_advanced', 'ucwTheme_image_insert_override' );
function ucwTheme_image_insert_override( $sizes ) {
unset( $sizes['medium_large'] );
unset( $sizes['1536x1536'] );
unset( $sizes['2048x2048'] );
return $sizes;
}
add_action( 'widgets_init', 'ucwTheme_widgets_init' );
function ucwTheme_widgets_init() {
register_sidebar( array(
'name' => esc_html__( 'Sidebar Widget Area', 'ucwTheme' ),
'id' => 'primary-widget-area',
'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
'after_widget' => '</li>',
'before_title' => '<h3 class="widget-title">',
'after_title' => '</h3>',
) );
}
add_action( 'wp_head', 'ucwTheme_pingback_header' );
function ucwTheme_pingback_header() {
if ( is_singular() && pings_open() ) {
printf( '<link rel="pingback" href="%s" />' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
}
}
add_action( 'comment_form_before', 'ucwTheme_enqueue_comment_reply_script' );
function ucwTheme_enqueue_comment_reply_script() {
if ( get_option( 'thread_comments' ) ) {
wp_enqueue_script( 'comment-reply' );
}
}
function ucwTheme_custom_pings( $comment ) {
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo esc_url( comment_author_link() ); ?></li>
<?php
}
add_filter( 'get_comments_number', 'ucwTheme_comment_count', 0 );
function ucwTheme_comment_count( $count ) {
if ( !is_admin() ) {
global $id;
$get_comments = get_comments( 'status=approve&post_id=' . $id );
$comments_by_type = separate_comments( $get_comments );
return count( $comments_by_type['comment'] );
} else {
return $count;
}
}