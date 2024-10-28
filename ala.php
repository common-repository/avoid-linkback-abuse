<?php
/**
 * Plugin Name: Avoid Linkback Abuse (ALA)
 * Plugin URI: http://www.mandsconsulting.com
 * Description: Remove the URL field from the comments form to prevent linkback spam
 * Version: 1.0
 * Author: Blake Willard
 * License: GPL2
 */

abstract class ALA_Settings_Abstract {

    ////
    // Bootstrap
    ////

    public $title; 
    public $page; 

    function __construct($title, $page) {
        $this->title = $title;
        $this->page = $page;
        add_action('admin_init', array($this, 'setup'));
    }

    public function setup() {
        // make sure setup is called once
        // throughout the instance's life
        static $called = false;
        if ($called) return;
        $called = true;

        $this->init();
    }

    abstract protected function init();

    ////
    // Utility
    ////

    //TODO: these may all be better off in a specialize utility class
    //      which is instantiated by this abstract class
    static public function titleToID($title = null) {
        if (is_null($title)) return '';
        return preg_replace('/[^A-Za-z0-9]/', '-', strtolower($title));
    }

    static public function apply($callback, $args_arr) {
        foreach ($args_arr as $args) {
            call_user_func_array($callback, array_values($args));
        }
    }

    static public function keyMatchFilter($needle, $haystack) {
        if (is_array($haystack[0])) {
            // assume multidimensional array
            $return = array();
            foreach ($haystack as $hay) {
                $return[] = self::keyMatchFilter($needle, $hay);
            }
            return $return;
        }
        return array_intersect_key($haystack, array_flip($needle));
    }

    static protected function ql($head = '', $var = '') {
        echo "<pre><p>$head</p>";
        print_r($var);
        echo "</pre>";
    }

    static public function kmsort(&$arrays, $sort_flags = SORT_REGULAR) {
        foreach($arrays as &$array) {
            ksort($array);
        }
    }

    static public function kmrsort(&$arrays, $sort_flags = SORT_REGULAR) {
        foreach($arrays as &$array) {
            krsort($array);
        }
    }

    //TODO: dirty
    static public function load_template($args) {
        $template =
            isset($args['template'])
            ? $args['template'].'.php'
            : 'default.php';
        if (stat(plugin_dir_path(__FILE__) . $template) !== FALSE) {
            ob_start();
            require(plugin_dir_path(__FILE__) . $template);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
        return "Couldn't find $template in plugin directory.";
    }
}

class ALA_Settings extends ALA_Settings_Abstract {

    protected function init() {
        $this->addSettingsSections();
        $this->addSettingsFields();
        //see note at end for why this is commented out
        //$this->selectMode();
    }

    //TODO: add public method to inject more sections before
    //      running bootstrap steps (i.e. init and setup)
    private function addSettingsSections() {
        $sections = array();
        $sections[] = array(
            'id' => $this->titleToID($this->title),
            'title' => $this->title,
            'callback' => array($this, 'printSettingsSection'),
            'page' => $this->page,
        );
        $this->apply('add_settings_section', $sections);
    }

    //TODO: convert fields and sections arrays to classes
    private function addSettingsFields() {
        $fields = array();
        $fields[] = array(
            'id' => 'ala-mode',
            'title' => 'ALA Mode of Operation',
            'callback' => array($this, 'printSettingsField'),
            'page' => $this->page,
            'section' => $this->titleToID($this->title),
            'args' => array(
                'template' => 'ala_mode'
            ),
        );
        // add additional fields here

        // add newest addition to fields to itself
        // for access within its template. TODO: clean this
        end($fields);
        $fields[key($fields)]['args']['self'] = current($fields);
        reset($fields);

        $this->apply('add_settings_field', $fields);
        $register = $this->keyMatchFilter(array('page', 'id'), $fields);
        $this->kmrsort($register); //so page appears before id to match param order
        $this->apply('register_setting', $register);
    }

    public function printSettingsSection($section) {
        //$section here comes from WP
        echo $this->getSettingsSection($section);
    }

    public function getSettingsSection($section) {
        //TODO: how can we do this dynamically? like args in $fields
        return $this->load_template(array('template' => 'main_settings'));
    }

    public function printSettingsField($args) {
        //$args here comes from WP
        echo $this->getSettingsField($args);
    }
 
    public function getSettingsField($args) {
        return $this->load_template($args);
    }

    //TODO: this is lazy, modes should be pluggable, options dynamic
    private function selectMode() {
        $this->mode = "default";
        switch(get_option('ala-mode')) {
            case "strict":
                error_log('About to add strict filter');
                add_filter('comment_form_field_url', array($this, 'strictMode')); 
                break;
            default:
                add_filter('get_comment_author_link', array($this, 'defaultMode')); 
        }
    }

    //TODO: Modes should live somewhere outside of Settings class?
    public function strictMode() {
        $doesntexist->test;
        return '';
    }

    public function defaultMode() {
        $id = get_comment_ID();
        return get_comment_author($id);
    }
}

class ALA_Settings_Page extends ALA_Settings {
    protected function init() {}
    //TODO: If we want our own page at some point
}

//TODO: should be able to configure and add settings before kicking off boostrap
$settings = new ALA_Settings("Avoid Linkback Abuse (ALA)", "discussion");

//BUG: For some reason I could not do any of this inside the class,
//like there was some sort of race condition; the filter would
//just disappear from $wp_filter by the time it reached apply_filter()
//
//To reproduce, try creating a class that only calls add_filter
if (get_option("ala-mode") === "strict") {
    add_filter('comment_form_field_url', array($settings, 'strictMode')); 
}
add_filter('get_comment_author_link', array($settings, 'defaultMode')); 
