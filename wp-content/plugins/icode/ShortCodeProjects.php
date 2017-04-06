<?php
/**
 * Shortcode [projects] that displays the code stuff: Projects, components and graphs
 */
class ShortCodeProjects
{
    function __construct()
    {
    }
    
    public function init()
    {
        add_action('init', array($this, 'registerShortCode'));        
    }
    
    public function registerShortCode()
    {
        add_shortcode('projects', array($this, 'theProjects'));
        add_action( 'wp_enqueue_scripts', array($this, 'enqueueScripts') );
    }
    
    public function enqueueScripts()
    {
        wp_enqueue_script('icode-shortcode-projects', plugins_url('projects.js', __FILE__), array('jquery'));
        wp_enqueue_style('icode-shortcode-projects', plugins_url('projects.css', __FILE__));
    }
    
    /**
     * The shortcode markup generator
     */
    public function theProjects()
    {
        // prepare data
        query_posts(array('post_type' => 'project', 'nopaging' => true));
        
        ob_start();
        if (have_posts()):?>
            <section class="projects"><?php
                // loop projects
                while (have_posts()) : the_post();?>
                    <span class="project">
                        <span class="name"><?php the_title();?></span>
                        <span class="description"><?php the_content();?></span>
                        <?php
                        $startDate = new DateTime(get_field('project_start_date', false, false));
                        $endDate = new DateTime(get_field('project_end_date', false, false));?>
                        <time datetime="<?php echo $startDate->format('Y-m-d');?>"><?php echo $startDate->format('j M Y'); ?></time>
                        <time datetime="<?php echo $startDate->format('Y-m-d');?>"><?php echo $endDate->format('j M Y');?></time><?php
                        // loop components. When one has a language include it
                        if(have_rows('project_components')):?>
                            <span class="components"><?php
                                $components = [];
                                while(have_rows('project_components')): the_row();
                                    $component = get_sub_field('project_component_item');
                                    $components []= array($component, get_sub_field('project_component_weight'));
                                    
                                    // if the component contains a language (also a component) loop on both
                                    $componentLangs = get_field('component_lang', $component->ID);
                                    if($componentLangs):
                                        foreach ($componentLangs as $lang):
                                            $components []= array($lang, 0);
                                        endforeach;
                                    endif;
                                endwhile;
                                
                                // loop through components. Sets of component-weight
                                foreach ($components as $componentData):
                                    // get component info
                                    $component = $componentData[0];
                                    $componentWeight = $componentData[1];
                                    $componentUrl = get_field('component_url', $component->ID);                                    
                                    // component type
                                    $componentTypes = get_the_terms($component->ID, 'component_type');
                                    ?>
                                    <span class="component" data-weight="<?php echo $componentWeight;?>">
                                        <span class="name"><?php echo get_the_title($component->ID)?></span>
                                        <span class="description"><?php echo get_the_content();?></span>
                                        <a href="<?php echo $componentUrl;?>" target="blank"><?php _e('url', 'icode');?></a><?php
                                        if($componentTypes):?>
                                            <span class="component-types"><?php
                                            foreach ($componentTypes as $componentType):?>
                                                <span class="component-type">
                                                    <span class="id"><?php echo $componentType->term_id;?></span>
                                                    <span class="name"><?php echo $componentType->name;?></span>
                                                    <span class="slug"><?php echo $componentType->slug;?></span>
                                                </span><?php
                                            endforeach;?>
                                            </span><?php
                                        endif;?>
                                    </span><?php
                                endforeach;?>
                            </span><?php
                        endif;?>
                    </span><?php                
                endwhile;?>
            </section><?php 
        endif;
        wp_reset_query();
        
        $markup = ob_get_contents();
        ob_end_clean();
        return $markup;
    }
    
}






