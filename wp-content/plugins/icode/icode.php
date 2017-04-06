<?php /*
Plugin Name:  ICode
Plugin URI:   http://utterlyrational.com
Description:  Allows you to structure your programming experience and use it in a theme
Version:      1.0.0
Author:       Odys
Author URI:   http://utterlyrational.com
*/
class ICode
{    
    public function __construct()
    {
        add_action('init', array($this, 'registerPostTypes'), 0 );
        
        require_once 'ShortCodeProjects.php';
        $shortcodeProjects = new ShortCodeProjects();
        $shortcodeProjects->init();
    }
    
    public function registerPostTypes()
    {
        // Set labels for custom post types: Project and Component
        $projectLabels = array(
                'name'                => __( 'Projects'),
                'singular_name'       => __( 'Project'),
                'menu_name'           => __( 'Projects'),
                'parent_item_colon'   => __( 'Parent Project'),
                'all_items'           => __( 'All Projects'),
                'view_item'           => __( 'View Project'),
                'add_new_item'        => __( 'Add New Project'),
                'add_new'             => __( 'Add New'),
                'edit_item'           => __( 'Edit Project'),
                'update_item'         => __( 'Update Project'),
                'search_items'        => __( 'Search Project'),
                'not_found'           => __( 'Not Found'),
                'not_found_in_trash'  => __( 'Not found in Trash'),
        );
        $componentLabels = array(
                'name'                => __( 'Components'),
                'singular_name'       => __( 'Component'),
                'menu_name'           => __( 'Components'),
                'parent_item_colon'   => __( 'Parent Component'),
                'all_items'           => __( 'All Components'),
                'view_item'           => __( 'View Component'),
                'add_new_item'        => __( 'Add New Component'),
                'add_new'             => __( 'Add New'),
                'edit_item'           => __( 'Edit Component'),
                'update_item'         => __( 'Update Component'),
                'search_items'        => __( 'Search Component'),
                'not_found'           => __( 'Not Found'),
                'not_found_in_trash'  => __( 'Not found in Trash'),
        );
        $componentTypeTaxLabels = array(
                'name' => __('Component types'),
                'singular_name' => __('Component type'),
                'search_items' =>  __( 'Search Component types' ),
                'popular_items' => __( 'Popular Component types' ),
                'all_items' => __( 'All Component types' ),
                'parent_item' => null,
                'parent_item_colon' => null,
                'edit_item' => __( 'Edit Component type' ),
                'update_item' => __( 'Update Component type' ),
                'add_new_item' => __( 'Add New Component type' ),
                'new_item_name' => __( 'New Component type Name' ),
                'separate_items_with_commas' => __( 'Separate component types with commas' ),
                'add_or_remove_items' => __( 'Add or remove component types' ),
                'choose_from_most_used' => __( 'Choose from the most used ones' ),
                'menu_name' => __( 'Component types' ),
        ); 
        
        // Set other options for custom post types
        $projectArgs = array(
                'label'               => __( 'projects'),
                'description'         => __( 'Stuff that you have done or used'),
                'labels'              => $projectLabels,
                'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 6,
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'page',
        );
        
        $componentArgs = array(
                'label'               => __( 'components'),
                'description'         => __( 'Things used in projects'),
                'labels'              => $componentLabels,
                'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
                'taxonomies'          => array('components'),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 7,
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'page',
        );
        
        $componentTypeTaxArgs = array(
                'hierarchical' => false,
                'labels' => $componentTypeTaxLabels,
                'show_ui' => true,
                'show_admin_column' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array( 'slug' => 'type' ),
        );
        
        // Registering your Custom Post Type
        register_taxonomy('component_type', 'component', $componentTypeTaxArgs);
        register_post_type('component', $componentArgs);
        register_post_type( 'project', $projectArgs );
    }

}

global $icode;
$icode = new ICode();