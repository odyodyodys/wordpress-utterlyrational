<?php
/**
 * Understrap functions and definitions
 *
 * @package understrap
 */
class ToThePoint
{
    public $aboutPageEngId = 134;
    
    public function __construct()
    {
        $templateDirectory = get_template_directory();
        
        $includes = [];
        $includes []= '/inc/setup.php'; // Theme setup and custom theme supports.
        $includes []= '/inc/widgets.php'; // Register widget area. @link http://codex.wordpress.org/Function_Reference/register_sidebar
        $includes []= '/inc/security.php'; // functions to secure your WP install.
        $includes []= '/inc/enqueue.php'; // Enqueue scripts and styles.
        $includes []= '/inc/template-tags.php'; // Template tags for this theme.
        $includes []= '/inc/pagination.php'; // Template tags for this theme.
        $includes []= '/inc/extras.php'; // Functions that act independently of the theme templates.
        $includes []= '/inc/customizer.php'; // Customizer additions.
        $includes []= '/inc/custom-comments.php'; // Customizer additions.
        $includes []= '/inc/jetpack.php'; // Jetpack compatibility file.
        $includes []= '/inc/bootstrap-wp-navwalker.php'; // custom nav walker.
        //$includes []= '/inc/woocommerce.php'; // WooCommerce functions.
        $includes []= '/inc/editor.php'; // Editor functions.
        
        foreach ($includes as $incl):
            require_once $incl;
        endforeach;
    }    
}

global $theme;
$theme = new ToThePoint();