<?
/*
Plugin Name: MX Individual CSS/JS
Description: Allows to use individual CSS/JS files for different types of pages. It allows to reduce the size of CSS/JS code of the certain page by using only relevant parts of CSS/JS.
Version: 1.0
Author: MX Studio
Author URI: http://mxsite.ru
Plugin URI: https://github.com/mx-studio/mx-individual-css-js
Text Domain: mx-individual-css-js
Domain Path: /languages
*/

class MXIndividualCSSJS {

    var $scriptNamesA=array();
    var $allowedExtensions=array('css','js');
    var $languageDomain='mx-individual-css-js';
    var $cssFolderUri, $jsFolderUri;
    var $cssFolder, $jsFolder;

    public function __construct() {
        $this->add_actions();
        register_activation_hook( __FILE__, array($this,'activate'));
        $this->cssFolder=get_template_directory().'/'.str_replace('//','/',get_option('mx_individual_cssjs_cssFolder').'/');
        $this->cssFolderUri=get_template_directory_uri().'/'.str_replace('//','/',get_option('mx_individual_cssjs_cssFolder').'/');
        $this->jsFolder=get_template_directory().'/'.str_replace('//','/',get_option('mx_individual_cssjs_jsFolder').'/');
        $this->jsFolderUri=get_template_directory_uri().'/'.str_replace('//','/',get_option('mx_individual_cssjs_jsFolder').'/');
    }

    /**
     * Activate plugin event (setting default settings options during this event)
     */
    public function activate() {
        $defaultOptions=array(
            'mx_individual_cssjs_combineCSS'=>'1',
            'mx_individual_cssjs_combineJS'=>'1',
            'mx_individual_cssjs_cssFolder'=>'css',
            'mx_individual_cssjs_jsFolder'=>'js',
        );
        foreach($defaultOptions as $key=>$value) {
            if (get_option($key)===false) {
                update_option($key,$value);
            }
        }
    }

    /**
     * Initialize wp action
     */
    private function add_actions() {
        add_action('plugins_loaded',array($this,'loadLanguage'));
        add_action('wp',array($this,'attachScripts'));
        add_filter('query_vars',array($this,'registerQueryVariables'));
        add_action('wp',array($this,'generateCSSJS'));
        add_action('admin_menu',array($this,'settingsMenu'));
        add_action('init',array($this,'updateSettings'));
    }

    /**
     * Update settings of the plugin
     */
    public function updateSettings() {
        $allowedSettingsOptionsA=array('mx_individual_cssjs_combineCSS','mx_individual_cssjs_combineJS','mx_individual_cssjs_cssFolder','mx_individual_cssjs_jsFolder');
        if (isset($_POST['mx_individual_cssjs_options'])) {
            $optionsA=$_POST['mx_individual_cssjs_options'];
            foreach($optionsA as $key=>$value) {
                $key='mx_individual_cssjs_'.$key;
                if (in_array($key,$allowedSettingsOptionsA)) {
                    update_option($key, $value);
                }
            }
            update_option('mx_individual_cssjs_notice',__('Settings updated',$this->languageDomain));
            wp_redirect('/wp-admin/options-general.php?page=mx-individual-css-js.php');
            exit;
        }
    }

    /**
     * Load language files
     */
    public function loadLanguage() {
        load_plugin_textdomain( $this->languageDomain, false, basename( dirname( __FILE__ ) ) . '/languages' );
    }

    /**
     * Add admin menu for plugin settings
     */
    public function settingsMenu() {
        add_options_page(__('MX Individual CSS/JS',$this->languageDomain),__('MX Individual CSS/JS',$this->languageDomain), 'manage_options', basename(__FILE__),array ($this, 'settingsPage') );
    }

    /**
     * Generate settings page
     */
    public function settingsPage() {
        $combineCSS=get_option('mx_individual_cssjs_combineCSS');
        $combineJS=get_option('mx_individual_cssjs_combineJS');
        $CssFolder=get_option('mx_individual_cssjs_cssFolder');
        $JsFolder=get_option('mx_individual_cssjs_jsFolder');
        ?>
        <h1><?=__('MX Individual CSS/JS Settings',$this->languageDomain)?></h1>
        <?
        if ($notice=get_option('mx_individual_cssjs_notice')) {
            delete_option('mx_individual_cssjs_notice');
            ?>
            <div id="message" class="updated notice notice-success is-dismissible"><p><?=$notice?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button></div>
            <?
        }
        ?>
        <form action="" method="post">
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row"><?=__('Combine',$this->languageDomain)?></th>
                    <td>
                        <input type="hidden" name="mx_individual_cssjs_options[combineCSS]" value="0">
                        <input type="hidden" name="mx_individual_cssjs_options[combineJS]" value="0">
                        <label for="chkCombineCSS"><input <?=$combineCSS?'checked="checked"':''?> value="1" type="checkbox" name="mx_individual_cssjs_options[combineCSS]" id="chkCombineCSS"> <?=__('all attached CSS files into one union file',$this->languageDomain)?></label>
                        <br/>
                        <label for="chkCombineJS"><input <?=$combineJS?'checked="checked"':''?> value="1" type="checkbox" name="mx_individual_cssjs_options[combineJS]" id="chkCombineJS"> <?=__('all attached JS files into one union file',$this->languageDomain)?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="inpCssFolder"><?=__('Path to CSS files',$this->languageDomain)?></label></th>
                    <td>
                        <input type="text" class="regular-text" name="mx_individual_cssjs_options[cssFolder]" id="inpCssFolder" value="<?=$CssFolder?>"/>
                        <p class="description"><?=__('Path should be specified relative to the theme folder',$this->languageDomain)?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="inpJsFolder"><?=__('Path to JS files',$this->languageDomain)?></label></th>
                    <td>
                        <input type="text" class="regular-text" name="mx_individual_cssjs_options[jsFolder]" id="inpJsFolder" value="<?=$JsFolder?>"/>
                        <p class="description"><?=__('Path should be specified relative to the theme folder',$this->languageDomain)?></p>
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" value="<?=__('Update changes',$this->languageDomain)?>"/>
            </p>
        </form>
        <?
    }

    /**
     * Register required request variables
     * @param $variables
     * @return array
     */
    public function registerQueryVariables($variables) {
        $variables[]='mx-individual-css-js-combined';
        return $variables;
    }

    /**
     * Generate union CSS or JS file using filenames trasfered in the request parameter
     */
    public function generateCSSJS() {
        $scripts=trim(get_query_var('mx-individual-css-js-combined'));
        if ($scripts) {
            $scriptsA=explode(',',$scripts);
            foreach($scriptsA as $index=>$script) {
                $ext=strtolower(preg_replace('|^.*\.([^\.]+)$|','$1',$script));
                if ($index==0) {
                    if ($ext=='css') {
                        header("Content-Type: text/css");
                    } elseif ($ext=='js') {
                        header("Content-Type: script/javascript");
                    }
                }
                if (in_array($ext,$this->allowedExtensions)) {
                    echo file_get_contents(get_template_directory_uri().'/'.$ext.'/'.$script).PHP_EOL;
                }
            }
            exit;
        }
    }

    /**
     * Initial function for attaching CSS/JS
     */
    public function attachScripts() {
        $this->defineRelevantScripts();
        $this->attachRelevantScripts();
    }

    /**
     * Defining the set of CSS/JS scripts relevant to the current page
     */
    private function defineRelevantScripts() {
        $queriedObject = get_queried_object();
        if (is_admin()) {
            $this->scriptNamesA[] = 'admin';
        } else {
            if (is_home()) {
                $this->scriptNamesA[] = 'home';
            }
            if (is_search()) {
                $this->scriptNamesA[] = 'search';
            }
            if (is_404()) {
                $this->scriptNamesA[] = '404';
            }
            if (is_singular()) {
                $this->scriptNamesA[] = 'singular';
                if (is_single()) {
                    $this->scriptNamesA[] = 'single';
                    if ($post_type = get_post_type()) {
                        $this->scriptNamesA[] = 'single-' . $post_type;
                    }
                } elseif (is_page()) {
                    $this->scriptNamesA[] = 'page';
                    $this->scriptNamesA[] = 'page-' . $queriedObject->ID;
                    $this->scriptNamesA[] = 'page-' . $queriedObject->post_name;
                }
            } elseif (is_archive()) {
                $this->scriptNamesA[] = 'archive';
                if (is_category()) {
                    $this->scriptNamesA[] = 'category';
                    $this->scriptNamesA[] = 'category-' . $queriedObject->term_id;
                    $this->scriptNamesA[] = 'category-' . $queriedObject->slug;
                }
            }
        }
    }

    /**
     * Attaching found set of relevant CSS/JS scripts
     */
    private function attachRelevantScripts() {
        $this->attachRelevantCSS();
        $this->attachRelevantJS();
    }

    /**
     * Attaching found set of relevant CSS scripts
     */
    private function attachRelevantCSS() {
        $scriptA=array();
        $combineCSS=get_option('mx_individual_cssjs_combineCSS');
        foreach($this->scriptNamesA as $scriptName) {
            $filePath=$this->cssFolder.$scriptName.'.min.css';
            if (file_exists($filePath)) {
                if ($combineCSS) {
                    $scriptA[]=$scriptName.'.min.css';
                } else {
                    wp_enqueue_style($scriptName,$this->cssFolderUri.$scriptName.'.min.css');
                }
            } else {
                $filePath = $this->cssFolder . $scriptName . '.css';
                if (file_exists($filePath)) {
                    if ($combineCSS) {
                        $scriptA[] = $scriptName . '.css';
                    } else {
                        wp_enqueue_style($scriptName, $this->cssFolderUri . $scriptName . '.css');
                    }
                }
            }
        }
        if ($combineCSS && count($scriptA)) {
            wp_enqueue_style('mx-individual-css-js-combined', site_url() . '?mx-individual-css-js-combined=' . implode(',', $scriptA));
        }
    }

    /**
     * Attaching found set of relevant JS scripts
     */
    private function attachRelevantJS() {
        $scriptA=array();
        $combineJS=get_option('mx_individual_cssjs_combineJS');
        foreach($this->scriptNamesA as $scriptName) {
            $filePath=$this->jsFolder.$scriptName.'.min.js';
            if (file_exists($filePath)) {
                if ($combineJS) {
                    $scriptA[]=$scriptName.'.min.js';
                } else {
                    wp_enqueue_script($scriptName,$this->jsFolderUri.$scriptName.'.min.js');
                }
            } else {
                $filePath = $this->jsFolder . $scriptName . '.js';
                if (file_exists($filePath)) {
                    if ($combineJS) {
                        $scriptA[] = $scriptName . '.js';
                    } else {
                        wp_enqueue_script($scriptName, $this->jsFolderUri . $scriptName . '.js');
                    }
                }
            }
        }
        if ($combineJS && count($scriptA)) {
            wp_enqueue_script('mx-individual-css-js-combined', site_url() . '?mx-individual-css-js-combined=' . implode(',', $scriptA));
        }
    }

}

$MXIndividualCSSJS = new MXIndividualCSSJS();