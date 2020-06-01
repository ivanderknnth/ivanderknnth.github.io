<?php

/*
* Stop execution if someone tried to get file directly.
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
//======================================================================
// Code for the admin funcionality of Feed Them All
//======================================================================
class FTA_Admin
{
    /* Intitializing $adminurl .*/
    var  $adminurl ;
    /*
     * __construct initialize all function of this class.
     * Returns nothing. 
     * Used action_hooks to get things sequentially.
     */
    function __construct()
    {
        /*
         * admin_menu hooks fires on wp admin load.
         * Add the menu page in wp admin area.
         */
        add_action( 'admin_menu', array( $this, 'fta_menu' ) );
        /*
         * admin_enqueue_scripts hooks fires for enqueing custom script and styles.
         * Css file will be include in admin area.
         */
        add_action( 'admin_enqueue_scripts', array( $this, 'fta_admin_style' ) );
        /*
         * wp_enqueue_scripts hooks fires for enqueing custom script and styles.
         * Css file will be include in frontend area.
         */
        add_action( 'wp_enqueue_scripts', array( $this, 'fta_frontend_style' ) );
        /*
         * fta_plugin_status hooks fires on Ajax call.
         * fta_plugin_status method will be call when user change status of plugin.
         */
        add_action( 'wp_ajax_fta_plugin_status', array( $this, 'fta_plugin_status' ) );
        /*
         * fta_remove_at hooks fires on Ajax call.
         * fta_remove_at method will remove the access token and all data.
         */
        add_action( 'wp_ajax_fta_remove_at', array( $this, 'fta_remove_at' ) );
        /*
         * admin_notices hooks fires for displaying admin notice.
         * fta_admin_notice method will be call.
         */
        add_action( 'admin_notices', array( $this, 'fta_admin_notice' ) );
        /*
         * admin_footer_text hooks fires for displaying admin footer text.
         * fta_admin_footer_text method will be call.
         */
        add_filter( 'admin_footer_text', array( $this, 'fta_admin_footer_text' ) );
        /*
         * wp_ajax_mif_supported hooks fires on Ajax call.
         * wp_ajax_mif_supported method will be call on click of supported button in admin notice.
         */
        add_action( 'wp_ajax_fta_supported', array( $this, 'fta_supported_func' ) );
        /*
         * wp_ajax_mif_supported hooks fires on Ajax call.
         * wp_ajax_mif_supported method will be call on click of supported button in admin notice.
         */
        add_action( 'wp_ajax_fta_upgraded_msg_dismiss', array( $this, 'fta_upgraded_msg_dismiss' ) );
        add_action( 'admin_head', array( $this, 'esf_hide_notices' ) );
        add_action( 'pre_get_posts', array( $this, 'esf_exclude_demo_pages' ), 1 );
    }
    
    /* __construct Method ends here. */
    public function esf_hide_notices()
    {
        $screen = get_current_screen();
        if ( $screen->base == 'admin_page_esf_welcome' ) {
            remove_all_actions( 'admin_notices' );
        }
        echo  "<style>.toplevel_page_feed-them-all .wp-menu-image img{padding-top: 4px!important;}</style>" ;
        // echo "<pre>"; print_r($screen->base);exit();
    }
    
    /*
     * fta_frontend_style will enqueue style and js files.
     */
    public function fta_frontend_style()
    {
        /*
         * Esf Custom Fonts Css.
         */
        wp_enqueue_style( 'esf-fonts', FTA_PLUGIN_URL . 'assets/css/esf-custom-fonts.css' );
    }
    
    /* fta_frontend_style Method ends here. */
    /*
     * fta_admin_style will enqueue style and js files.
     * Returns hook name of the current page in admin.
     * $hook will contain the hook name.
     */
    public function fta_admin_style( $hook )
    {
        // exit( $hook);
        /*
         * Following files should load only on fta page in backend.
         */
        if ( 'toplevel_page_feed-them-all' !== $hook && 'easy-social-feed_page_mif' !== $hook && 'easy-social-feed_page_easy-facebook-likebox' !== $hook && 'admin_page_esf_welcome' !== $hook && 'easy-social-feed_page_esf_recommendations' !== $hook ) {
            return;
        }
        /*
         * Base css file for admin area.
         */
        wp_enqueue_style( 'materialize.min', FTA_PLUGIN_URL . 'assets/css/materialize.min.css' );
        /*
         * Css file for admin area.
         */
        wp_enqueue_style( 'fta_animations', FTA_PLUGIN_URL . 'assets/css/fta_animations.css' );
        /*
         * Css file for admin area.
         */
        wp_enqueue_style( 'fta_admin_style', FTA_PLUGIN_URL . 'assets/css/fta_admin_style.css' );
        /*
         * Base script file for admin area.
         */
        wp_enqueue_script( 'materialize.min', FTA_PLUGIN_URL . 'assets/js/materialize.min.js', array( 'jquery' ) );
        /*
         * For sliding animations.
         */
        wp_enqueue_script( 'jquery-effects-slide' );
        /*
         * Copy To Clipboard script file for admin area.
         */
        wp_enqueue_script( 'clipboard' );
        /*
         * Custom scripts file for admin area.
         */
        wp_enqueue_script( 'fta_admin_jquery', FTA_PLUGIN_URL . 'assets/js/fta-admin.js', array( 'jquery' ) );
        // echo "<pre>";
        // print_r(admin_url('admin-ajax.php'));exit();
        wp_localize_script( 'fta_admin_jquery', 'fta', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'fta-ajax-nonce' ),
        ) );
        wp_enqueue_script( 'media-upload' );
        wp_enqueue_media();
    }
    
    /* fta_admin_style Method ends here. */
    /*
     * fta_menu will add admin page.
     * Returns nothing.
     */
    public function fta_menu()
    {
        /*
         * URL of the plugin icon.
         */
        $icon_url = FTA_PLUGIN_URL . 'assets/images/plugin_icon.png';
        /*
         * add_menu_page will add menu into the page.
         * string $page_title 
         * string $menu_title 
         * string $capability 
         * string $menu_slug
         * callable $function 
         */
        add_menu_page(
            __( 'Easy Social Feed', 'easy-facebook-likebox' ),
            __( 'Easy Social Feed', 'easy-facebook-likebox' ),
            'administrator',
            'feed-them-all',
            array( $this, 'fta_page' ),
            $icon_url
        );
        /*
         * Add wellcome page but not visible in menu
         */
        add_submenu_page(
            null,
            __( 'Welcome', 'easy-facebook-likebox' ),
            __( 'Welcome', 'easy-facebook-likebox' ),
            'administrator',
            'esf_welcome',
            array( $this, 'esf_welcome_page' )
        );
        if ( efl_fs()->is_free_plan() ) {
            /*
             * Add Recommendations page.
             */
            add_submenu_page(
                'feed-them-all',
                __( 'Recommendations', 'easy-facebook-likebox' ),
                __( 'Recommendations', 'easy-facebook-likebox' ),
                'administrator',
                'esf_recommendations',
                array( $this, 'esf_recommendations_page' ),
                4
            );
        }
    }
    
    /* fta_menu Method ends here. */
    /*
     * esf_welcome_page contains the html/markup of the  welcome page.
     * Returns Html.
     */
    function esf_welcome_page()
    {
        $fta_class = new Feed_Them_All();
        $fta_settings = $fta_class->fta_get_settings();
        $returner = null;
        /*
         * Welcome page html.
         * esf_welcome_page_html filter can be used to customize base html of setting page.
         */
        $returner .= sprintf(
            '<div class="esf_loader_wrap">
                <div class="esf_loader_inner">
                <div class="loader esf_welcome_loader"></div>
                </div>
            </div>
            <div class="fta_wrap z-depth-1 esf_wc_wrap">
                <div class="fta_wrap_inner">
                    <div class="fta_tab_c_holder">
                        <div class="row">
                            <div class="esf_wc_header">
                                <div class="esf_wc_header_top">
                                  
                                    <h1>%11$s</h1>
                                </div>                               
                                <p>%1$s</p>
                            </div>                       
                            <div class="esf_wc_boxes_wrap">
                                <div class="esf_wc_box">
                                    <div class="esf_wc_box_img">
                                        <img src="' . FTA_PLUGIN_URL . '/assets/images/likebox-icon.png" />
                                    </div>
                                    <div class="esf_wc_box_content">
                                        <h5>%2$s</h5>
                                        <p>%3$s</p>
                                        <div class="esf_wc_box_btns_holder">
                                            <a class="waves-effect waves-light btn" href="' . admin_url( 'admin.php?page=easy-facebook-likebox&sub_tab=efbl-likebox-use#efbl-general' ) . '">%4$s</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="esf_wc_box">
                                    <div class="esf_wc_box_img">
                                        <img src="' . FTA_PLUGIN_URL . '/assets/images/facebook-feed-icon.png" />
                                    </div>
                                    <div class="esf_wc_box_content">
                                        <h5>%5$s</h5>
                                        <p>%6$s</p>
                                        <div class="esf_wc_box_btns_holder">
                                            <a class="waves-effect waves-light btn" href="' . admin_url( 'admin.php?page=easy-facebook-likebox' ) . '">%4$s</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="esf_wc_box">
                                    <div class="esf_wc_box_img">
                                        <img src="' . FTA_PLUGIN_URL . '/assets/images/popup-icon.png" />
                                    </div>
                                    <div class="esf_wc_box_content">
                                        <h5>%7$s</h5>
                                        <p>%8$s</p>
                                        <div class="esf_wc_box_btns_holder">
                                            <a class="waves-effect waves-light btn" href="' . admin_url( 'admin.php?page=easy-facebook-likebox#efbl-auto-popup' ) . '">%4$s</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="esf_wc_box">
                                    <div class="esf_wc_box_img">
                                        <img src="' . FTA_PLUGIN_URL . '/assets/images/instagram-feed-icon.png" />
                                    </div>
                                    <div class="esf_wc_box_content">
                                        <h5>%9$s</h5>
                                        <p>%10$s</p>
                                        <div class="esf_wc_box_btns_holder">
                                            <a class="waves-effect waves-light btn" href="' . admin_url( 'admin.php?page=mif' ) . '">%4$s</a>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                            <div class="esf-quick-setup-wrap">                 
                             <h5>%12$s</h5>
                             <iframe height="400" src="https://www.youtube.com/embed/HES9pa98x_8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div> 
                        </div> 
                    </div>  
                </div>
            </div>',
            /* Variables starts here. */
            __( "Easy Social Feed plugin have following four key fetaures. ", $fta_class->fta_slug ),
            __( "Facebook Page Plugin (Like box)", $fta_class->fta_slug ),
            __( "Displays a Facebook Page Plugin. The Facebook Page Plugin is a social plugin that enables Facebook Page owners to attract and gain Likes from their own website.", $fta_class->fta_slug ),
            __( "Use this feature", $fta_class->fta_slug ),
            __( "Custom Facebook Feed", $fta_class->fta_slug ),
            __( "Display a customizable, responsive and SEO friendly feed of your Facebook posts on your site. Supports all types of posts, including images, videos, status and events.", $fta_class->fta_slug ),
            __( "Auto PopUp", $fta_class->fta_slug ),
            __( "Display Page Pugin (like box) or anything in the popup/lightbox. It supports, HTML and shortcode.", $fta_class->fta_slug ),
            __( "Custom Instagram Feed", $fta_class->fta_slug ),
            __( "Display your stunning photos and videos from your Instagram account on your site. It’s responsive), highly customizable and SEO friendly.", $fta_class->fta_slug ),
            __( "Welcome", $fta_class->fta_slug ),
            __( "Quick start video", $fta_class->fta_slug )
        );
        // echo "<pre>"; print_r($returner);exit();
        echo  $returner = apply_filters( 'esf_welcome_page_html', $returner ) ;
    }
    
    /* esf_welcome_page method ends here. */
    /*
     * esf_recommendations_page contains the html/markup of the  welcome page.
     * Returns Html.
     */
    function esf_recommendations_page()
    {
        $fta_class = new Feed_Them_All();
        $fta_settings = $fta_class->fta_get_settings();
        $returner = null;
        /*
         * Welcome page html.
         * esf_welcome_page_html filter can be used to customize base html of setting page.
         */
        $returner .= sprintf(
            '<div class="fta_wrap z-depth-1 fl-recomend-tab-holder">
                <div class="fta_wrap_inner">
                    <div class="fl-recomend-plugins-holder">
                    <div class="fl-recomend-head">
                        <h4>%1$s</h4>
                        <p>%2$s</p> 
                    </div>  
                    <div class="row">

                        <div class="col s3 fl-recomend-wraper">
                            <h4>%12$s</h4>
                            <p>%13$s</p>
                            <div class="fl-recomend-meta-wraper">
                                <p>%14$s</p>
                                <p><span title="%6$s" style="color: #ffb900;" class="stars">&#9733; &#9733; &#9733; &#9733; &#9733; </span></p>
                            </div>  
                            <div class="fl-recomends-action">
                                <a  href="' . $this->esf_get_plugin_install_link( 'wpoptin' ) . '">%7$s</a>
                                <a class="right" href="https://wordpress.org/plugins/wpoptin/" target="_blank">%8$s</a>
                            </div>
                        </div>

                         <div class="col s3 fl-recomend-wraper">
                            <h4>%9$s</h4>
                            <p>%10$s</p>
                            <div class="fl-recomend-meta-wraper">
                                <p>%11$s</p>
                                <p><span title="%6$s" style="color: #ffb900;" class="stars">&#9733; &#9733; &#9733; &#9733; &#9733; </span></p>
                            </div>  
                            <div class="fl-recomends-action">
                                <a  href="' . $this->esf_get_plugin_install_link( 'floating-links' ) . '" >%7$s</a>
                                <a class="right" href="https://wordpress.org/plugins/floating-links/" target="_blank">%8$s</a>
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col s3 fl-recomend-wraper">
                            <h4>%15$s</h4>
                            <p>%16$s</p>
                            <div class="fl-recomend-meta-wraper">
                                <p>%17$s</p>
                                <p><span title="%6$s" style="color: #ffb900;" class="stars">&#9733; &#9733; &#9733; &#9733; &#9733; </span></p>
                            </div>  
                            <div class="fl-recomends-action">
                                <a  href="' . $this->esf_get_plugin_install_link( 'my-instagram-feed' ) . '">%7$s</a>
                                <a class="right" href="https://wordpress.org/plugins/my-instagram-feed/" target="_blank">%8$s</a>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="fl-recomend-partner">
                        <div class="fl-recomend-head">
                            <h4>%18$s</h4>
                        </div>
                        <div class="row">
                            <div class="col s3 fl-recomend-wraper">
                                <img src="' . FTA_PLUGIN_URL . '/assets/images/cloudways-logo.png" />
                                <p>%19$s</p>
                                <p>%20$s</p>
                                <div class="fl-recomends-action">
                                    <a  href="https://www.cloudways.com" rel="noopener noreferrer" target="_blank">%8$s</a>
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>',
            /* Variables starts here. */
            /* Variables starts here. */
            __( "Love this plugin?", $fta_class->fta_slug ),
            __( "Then why not try our other awesome and FREE plugins.", $fta_class->fta_slug ),
            __( "Easy Social Post Feed", $fta_class->fta_slug ),
            __( "The easiest and beginner-friendly plugin for <b>Custom Instagram Feed</b>(Display photos, gallery and videos), <b>Custom Facebook Feed</b> (posts, links, status, photos, videos, events), <b>Facebook Page Plugin</b> (previously Facebook Like Box) and <b>Auto PopUp.</b>", $fta_class->fta_slug ),
            __( "Active Installs: 100,000+", $fta_class->fta_slug ),
            __( "5-Star Rating", $fta_class->fta_slug ),
            __( "Install now", $fta_class->fta_slug ),
            __( "More Info", $fta_class->fta_slug ),
            __( "Floating Links", $fta_class->fta_slug ),
            __( "Displays Fancy Floating Back To Top And Go To Bottom Links Along With Go To Next Post, Previous Post, Home Icon And Random Post Links On Post Detail Pages.", $fta_class->fta_slug ),
            __( "Active Installs: 800+", $fta_class->fta_slug ),
            __( "WPOptin", $fta_class->fta_slug ),
            __( "The easiest and beginner friendly <b>opt-in</b> plugin to grow email <b>subscribers</b> list, <b>sell more</b>, get more <b>phone calls</b>, increase <b>Facebook fan page likes</b> and get more <b>leads</b> faster than ever.", $fta_class->fta_slug ),
            __( "Just Released", $fta_class->fta_slug ),
            __( "Easy Social Photos Gallery", $fta_class->fta_slug ),
            __( "Display photos and videos from a non-private Instagram account in responsive and customizable layout.", $fta_class->fta_slug ),
            __( "Active Installs: 100+", $fta_class->fta_slug ),
            __( "Our Partners", $fta_class->fta_slug ),
            __( "We simplify hosting experiences because we believe in empowering individuals, teams and businesses. We set high standards of <b>performance</b>, commit to complete freedom of <b>choice</b> coupled with <b>simplicity</b> and agility in every process.", $fta_class->fta_slug ),
            __( "Backed by an <b>innovative</b> approach, our platform is built on <b>best-of-breed technologies</b> and industry-leading infrastructure providers that creates smooth managed cloud hosting experiences. And, we do this by investing in the right talent and by organizing the perfect <b>teams</b>.", $fta_class->fta_slug )
        );
        // echo "<pre>"; print_r($returner);exit();
        echo  $returner = apply_filters( 'esf_welcome_page_html', $returner ) ;
    }
    
    /* esf_recommendations_page method ends here. */
    /* fta_menu Method ends here. */
    /*
     * feed-them-all-content contains the html/markup of the page.
     * Returns nothing.
     */
    function fta_page()
    {
        $fta_class = new Feed_Them_All();
        $fta_settings = $fta_class->fta_get_settings();
        $current_user = wp_get_current_user();
        $returner = null;
        /*
         * Base html.
         * fta_base_html filter can be used to customize base html of setting page.
         */
        $returner .= sprintf(
            '<div class="esf_loader_wrap">
                <div class="esf_loader_inner">
                <div class="loader"></div>
                 </div>
               </div>
               <h1 class="esf-main-heading">Easy Social Feed (Previously Easy Facebook Likebox)</h1>
                <div class="fta_wrap z-depth-1">
				<div class="fta_wrap_inner">
				<div class="fta_tabs_holder">
					<div class="fta_tabs_header">
						<div class="fta_sliders_wrap">
                         <div id="fta_sliders">
                              <span>
                                <div class="box"></div>
                              </span>
                              <span>
                                <div class="box"></div>
                              </span>
                              <span>
                                <div class="box"></div>
                              </span>
                            </div>

                      </div>
					</div>
					<div class="fta_tab_c_holder">
                        <div class="row">
                       <h5>%1$s %2$s</h5>
                        <p>%3$s</p>                       
                        %4$s
					</div>
				</div>	
				</div>	
				</div>
			 </div>',
            /* Variables starts here. */
            __( "Welcome", $fta_class->fta_slug ),
            __( "to the modules management page.", $fta_class->fta_slug ),
            __( "You can disable or enable the modules you are not using to only include resources you need. If you are using all features, then keep these active.", $fta_class->fta_slug ),
            $this->fta_plugins_listing()
        );
        if ( efl_fs()->is_free_plan() ) {
            $returner .= '<div class="espf-upgrade">
                <h2>' . __( 'Easy Social Feed <b>Pro</b>', 'easy-facebook-likebox' ) . '</h2>
                 <p>' . __( 'Unlock all premium features such as Advanced PopUp, More Fancy Layouts, Post filters like events, images, videos, and albums, gallery in the PopUp and above all top notch priority support.', 'easy-facebook-likebox' ) . '</p>
                  <p>' . __( 'Upgrade today and get a 10% discount! On the checkout click on "Have a promotional code?" and enter <code>espf10</code>', 'easy-facebook-likebox' ) . '</p>
                   <a href="' . efl_fs()->get_upgrade_url() . '" class="waves-effect waves-light btn"><i class="material-icons right">lock_open</i>' . __( 'Upgrade To Pro', 'easy-facebook-likebox' ) . '</a>
                 </div>';
        }
        // echo "<pre>"; print_r($returner);exit();
        echo  $returner = apply_filters( 'fta_base_html', $returner ) ;
    }
    
    /* fta_page method ends here. */
    /*
     * fta_plugins_listing contains the html/markup of the listings in dashboard.
     * Returns HTML.
     */
    private function fta_plugins_listing()
    {
        /*
         * Getting main class.
         */
        $FTA = new Feed_Them_All();
        // echo "<pre>"; print_r($FTA->fta_get_settings());exit();
        /*
         * Getting All FTA plugins.
         */
        $fta_all_plugs = $FTA->fta_plugins();
        /*
         * Holds all the HTML.
         */
        $returner = '<div class="fta_all_plugs col s12">';
        /*
         * IF plugins exists loop thorugh it and make html.
         */
        if ( isset( $fta_all_plugs ) ) {
            foreach ( $fta_all_plugs as $fta_plug ) {
                $fta_settings_url = admin_url( 'admin.php?page=' . $fta_plug['slug'] );
                // echo "<pre>"; print_r($fta_settings_url);exit();
                /*
                 * Getting Image URL.
                 */
                $img_url = FTA_PLUGIN_URL . 'assets/images/' . $fta_plug['img_name'] . '';
                /*
                 * Making Slug.
                 */
                $slug = $fta_plug['activate_slug'];
                /*
                 * Making Button Label.
                 */
                
                if ( $fta_plug['status'] == 'activated' ) {
                    $btn = __( 'Deactivate', $FTA->fta_slug );
                } else {
                    $btn = __( 'Activate', $FTA->fta_slug );
                }
                
                $returner .= sprintf(
                    '<div class="card col fta_single_plug s5 fta_plug_%5$s fta_plug_%4$s">
                        <div class="card-image waves-effect waves-block waves-light">
                          <img src="%2$s">
                        </div>
                        <div class="card-content">
                          <span class="card-title  grey-text text-darken-4">%1$s</span>
                         </div>
                                <hr>
                          <div class="fta_cta_holder">
                          %3$s
                              <a class="btn waves-effect fta_plug_activate waves-light" data-status="%4$s" data-plug="%5$s" href="javascript:void(0);">%6$s</a>
                              <a class="btn waves-effect fta_setting_btn right waves-light" href="%8$s">%7$s</a>
                          </div>

                        <div class="card-reveal">
                          <span class="card-title grey-text text-darken-4">%1$s<i class="material-icons right">close</i></span>
                          <p>%3$s</p>
                        </div>
                      </div>',
                    /* Variables starts here. */
                    $fta_plug['name'],
                    $img_url,
                    $fta_plug['description'],
                    $fta_plug['status'],
                    $slug,
                    $btn,
                    __( "Settings", $FTA->fta_slug ),
                    $fta_settings_url
                );
            }
        }
        return $returner .= '</div>';
    }
    
    /* fta_plugins_listing method ends here. */
    /*
     * fta_plugin_status on ajax.
     * Returns the Success or Error Message. 
     * Change Plugin Status
     */
    function fta_plugin_status()
    {
        /*
         *  Getting the Plugin Name. 
         */
        $fta_plugin = sanitize_text_field( $_POST['plugin'] );
        /*
         *  Getting the Plugin status. 
         */
        $fta_plug_status = sanitize_text_field( $_POST['status'] );
        /*
         *  Getting the Plugin main object. 
         */
        $Feed_Them_All = new Feed_Them_All();
        /*
         *  Getting the FTA Plugin settings. 
         */
        $fta_settings = $Feed_Them_All::fta_get_settings();
        /*
         *  Chaning status accroding to selected option of specific plugin. 
         */
        $fta_settings['plugins'][$fta_plugin]['status'] = $fta_plug_status;
        if ( wp_verify_nonce( $_POST['fta_nonce'], 'fta-ajax-nonce' ) ) {
            if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
                /*
                 *  Updating the settings back into DB
                 */
                $status_updated = update_option( 'fta_settings', $fta_settings );
            }
        }
        
        if ( $fta_plug_status == 'activated' ) {
            $status = __( ' Activated', $Feed_Them_All->fta_slug );
        } else {
            $status = __( ' Deactivated', $Feed_Them_All->fta_slug );
        }
        
        /*
         *  If status is successfully changed
         */
        
        if ( isset( $status_updated ) ) {
            /*
             *  Sending back the success message
             */
            echo  wp_send_json_success( __( ucfirst( $fta_plugin ) . $status . ' Successfully', $Feed_Them_All->fta_slug ) ) ;
            die;
        } else {
            /*
             *  Sending back the error message
             */
            echo  wp_send_json_error( __( 'Something Went Wrong! Please try again.', $Feed_Them_All->fta_slug ) ) ;
            die;
        }
        
        exit;
    }
    
    /* fta_plugin_status method ends here. */
    /*
     * fta_remove_at on ajax.
     * Returns the Success or Error Message. 
     * Remove access token and data
     */
    function fta_remove_at()
    {
        /*
         *  Getting the Plugin main object. 
         */
        $Feed_Them_All = new Feed_Them_All();
        /*
         *  Getting the FTA Plugin settings. 
         */
        $fta_settings = $Feed_Them_All->fta_get_settings();
        if ( wp_verify_nonce( $_POST['fta_nonce'], 'fta-ajax-nonce' ) ) {
            
            if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
                $access_token = $fta_settings['plugins']['facebook']['access_token'];
                unset( $fta_settings['plugins']['facebook']['approved_pages'] );
                unset( $fta_settings['plugins']['facebook']['access_token'] );
                $fta_settings['plugins']['instagram']['selected_type'] = 'personal';
                /*
                 *  Updating the settings back into DB
                 */
                $delted_data = update_option( 'fta_settings', $fta_settings );
                $response = wp_remote_request( 'https://graph.facebook.com/v4.0/me/permissions?access_token=' . $access_token . '', array(
                    'method' => 'DELETE',
                ) );
                $body = wp_remote_retrieve_body( $response );
            }
        
        }
        /*
         *  If status is successfully changed
         */
        
        if ( isset( $delted_data ) ) {
            /*
             *  Sending back the success message
             */
            echo  wp_send_json_success( __( 'Deleted', $Feed_Them_All->fta_slug ) ) ;
            die;
        } else {
            /*
             *  Sending back the error message
             */
            echo  wp_send_json_error( __( 'Something Went Wrong! Please try again.', $Feed_Them_All->fta_slug ) ) ;
            die;
        }
        
        exit;
    }
    
    /* fta_remove_at method ends here. */
    /**
     * Display a nag to ask rating.
     */
    public function fta_admin_notice()
    {
        if ( !current_user_can( 'install_plugins' ) ) {
            return;
        }
        global  $pagenow ;
        $Feed_Them_All = new Feed_Them_All();
        $install_date = $Feed_Them_All->fta_get_settings( 'installDate' );
        $display_date = date( 'Y-m-d h:i:s' );
        $datetime1 = new DateTime( $install_date );
        $datetime2 = new DateTime( $display_date );
        $diff_intrval = round( ($datetime2->format( 'U' ) - $datetime1->format( 'U' )) / (60 * 60 * 24) );
        // echo '<pre>'; print_r();exit();
        
        if ( $diff_intrval >= 6 && get_site_option( 'fta_supported' ) != "yes" ) {
            $html = sprintf(
                '<div style="position:relative;padding-right:80px;" class="update-nag fta_msg fta_review">
                   
                        <p>%s<b>%s</b>%s</p>
                        <p>%s<b>%s</b>%s</p>
                        <p>%s</p>
                        <p>%s</p>
                       <p style="margin-left:5px;">~Danish Ali Malik (@danish-ali)</p>
                       <div class="fl_support_btns">
                    <a href="https://wordpress.org/support/plugin/easy-facebook-likebox/reviews/?filter=5#new-post" class="fta_HideRating button button-primary" target="_blank">
                        %s  
                    </a>
                    <a href="javascript:void(0);" class="fta_HideRating button" >
                    %s  
                    </a>
                    <br>
                    <a style="margin-top:5px;float:left;" href="javascript:void(0);" class="fta_HideRating" >
                    %s  
                    </a>
                    <div class="fta_HideRating" style="position:absolute;right:10px;cursor:pointer;top:4px;color: #029be4;"> <div style="font-weight:bold;" class="dashicons dashicons-no-alt"></div><span style="margin-left: 2px;">%s</span></div>
                        </div>
                        </div>',
                __( 'Awesome, you have been using ', 'easy-facebook-likebox' ),
                __( 'Easy Social Feed ', 'easy-facebook-likebox' ),
                __( 'for more than 1 week.', 'easy-facebook-likebox' ),
                __( 'May I ask you to give it a ', 'easy-facebook-likebox' ),
                __( '5-star ', 'easy-facebook-likebox' ),
                __( 'rating on Wordpress? ', 'easy-facebook-likebox' ),
                __( 'This will help to spread its popularity and to make this plugin a better one.', 'easy-facebook-likebox' ),
                __( 'Your help is much appreciated. Thank you very much. ', 'easy-facebook-likebox' ),
                __( 'I Like Easy Social Feed - It increased engagement on my site', 'easy-facebook-likebox' ),
                __( 'I already rated it', 'easy-facebook-likebox' ),
                __( 'No, not good enough, I do not like to rate it', 'easy-facebook-likebox' ),
                __( 'Dismiss', 'easy-facebook-likebox' )
            );
            $script = ' <script>
                jQuery( document ).ready(function( $ ) {

                jQuery(\'.fta_HideRating\').click(function(){
                   var data={\'action\':\'fta_supported\'}
                         jQuery.ajax({
                    
                    url: "' . admin_url( 'admin-ajax.php' ) . '",
                    type: "post",
                    data: data,
                    dataType: "json",
                    async: !0,
                    success: function(e ) {
                        
                        if (e=="success") {
                            jQuery(\'.fta_msg\').slideUp(\'fast\');
                           
                        }
                    }
                     });
                    })
                
                });
    </script>';
            echo  $html . $script ;
        }
        
        
        if ( get_site_option( 'fta_upgraded_notice' ) != "yes" ) {
            $screen = get_current_screen();
            $arr = array( 'easy-social-feed_page_mif', 'toplevel_page_feed-them-all', 'easy-social-feed_page_easy-facebook-likebox' );
            
            if ( in_array( $screen->id, $arr ) ) {
                $html = sprintf(
                    '<div class="update-nag fta_upgraded_msg" style=" background-color: #ed6d62;color: #fff;position:relative;padding-right:100px;">
                       <h5 style="color: #fff;">%s</h5>
                        <p>%s</p>
                        <ol>
                        <li>%s</li>
                        <li>%s</li>
                        <li>%s <a style=" color: #fff; text-decoration: underline;" href="' . admin_url( 'admin.php?page=easy-facebook-likebox#efbl-cached' ) . '">%s</a></li>
                        <li>%s</li>
                        </ol>
                       <div class="fl_support_btns">
                    <a href="javascript:void(0);" class="fta_HideUpgradedMsg button button-primary">
                        %s  
                    </a>
                    <div class="fta_HideUpgradedMsg" style="position:absolute;right:10px;cursor:pointer;top:10px;"> <div style="font-weight:bold;" class="dashicons dashicons-no-alt"></div><span style="margin-left: 2px;">%s</span></div>
                        </div>
                        </div>',
                    __( "Easy Social Feed (previously Easy Facebook Likebox) plugin notice", 'easy-facebook-likebox' ),
                    __( "If you just updated to 5.0 please don't forget to follow the steps below:", 'easy-facebook-likebox' ),
                    __( "Deactivate the plugin and activate again", 'easy-facebook-likebox' ),
                    __( 'Click on the authentication button to authenticate the app again', 'easy-facebook-likebox' ),
                    __( 'Clear the cache from', 'easy-facebook-likebox' ),
                    __( 'cache page', 'easy-facebook-likebox' ),
                    __( 'Opionally clear the browser cache.', 'easy-facebook-likebox' ),
                    __( "Hide this notice", 'easy-facebook-likebox' ),
                    __( 'Dismiss', 'easy-facebook-likebox' )
                );
                $script = ' <script>
                jQuery( document ).ready(function( $ ) {

                jQuery(\'.fta_HideUpgradedMsg\').click(function(){
                   var data={\'action\':\'fta_upgraded_msg_dismiss\'}
                         jQuery.ajax({
                    
                    url: "' . admin_url( 'admin-ajax.php' ) . '",
                    type: "post",
                    data: data,
                    dataType: "json",
                    async: !0,
                    success: function(e ) {
                        
                        if (e=="success") {
                            jQuery(\'.fta_upgraded_msg\').slideUp(\'fast\');
                           
                        }
                    }
                     });
                    })
                
                });
    </script>';
                echo  $html . $script ;
            }
        
        }
    
    }
    
    /**
     * Save the notice closed option.
     */
    public function fta_supported_func()
    {
        update_site_option( 'fta_supported', 'yes' );
        echo  json_encode( array( "success" ) ) ;
        exit;
    }
    
    public function fta_upgraded_msg_dismiss()
    {
        update_site_option( 'fta_upgraded_notice', 'yes' );
        echo  json_encode( array( "success" ) ) ;
        exit;
    }
    
    /**
     * Add powered by text in admin footer
     *
     * @param string  $text  Default footer text.
     *
     * @return string
     */
    function fta_admin_footer_text( $text )
    {
        $screen = get_current_screen();
        $arr = array(
            'easy-social-feed_page_mif',
            'toplevel_page_feed-them-all',
            'easy-social-feed_page_feed-them-all-account',
            'easy-social-feed_page_feed-them-all-contact',
            'easy-social-feed_page_feed-them-all-pricing',
            'easy-social-feed_page_easy-facebook-likebox',
            'easy-social-feed_page_esf_recommendations'
        );
        // echo $screen->id;
        
        if ( in_array( $screen->id, $arr ) ) {
            $fta_class = new Feed_Them_All();
            $text = '<i><a href="' . admin_url( '?page=feed-them-all' ) . '" title="' . __( 'Visit Easy Social Feed page for more info', 'easy-facebook-likebox' ) . '">ESPF</a> v' . $fta_class->version . '. Please <a target="_blank" href="https://wordpress.org/support/plugin/easy-facebook-likebox/reviews/?filter=5#new-post" title="Rate the plugin">rate the plugin <span style="color: #ffb900;" class="stars">&#9733; &#9733; &#9733; &#9733; &#9733; </span></a> to help us spread the word. Thank you from the Easy Social Feed team!</i><div style="margin-left:5px;top: 1px;" class="fb-like" data-href="https://www.facebook.com/easysocialfeed" data-width="" data-layout="button" data-action="like" data-size="small" data-share="false"></div><div id="fb-root"></div><script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v6.0&appId=1983264355330375&autoLogAppEvents=1"></script><style>#wpfooter{background-color: #fff;padding: 15px 20px;-webkit-box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.12), 0 1px 5px 0 rgba(0,0,0,.2);box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.12), 0 1px 5px 0 rgba(0,0,0,.2);}.fb_iframe_widget{float:left;}#wpfooter a{text-decoration:none;}</style>';
        }
        
        return $text;
    }
    
    // admin_footer_text
    private function esf_get_plugin_install_link( $slug )
    {
        $action = 'install-plugin';
        $esf_wpoptin_install = wp_nonce_url( add_query_arg( array(
            'action' => $action,
            'plugin' => $slug,
        ), admin_url( 'update.php' ) ), $action . '_' . $slug );
        return $esf_wpoptin_install;
    }
    
    function esf_exclude_demo_pages( $query )
    {
        if ( !is_admin() ) {
            return $query;
        }
        global  $pagenow ;
        
        if ( 'edit.php' == $pagenow && (get_query_var( 'post_type' ) && 'page' == get_query_var( 'post_type' )) ) {
            $fta_class = new Feed_Them_All();
            $fta_settings = $fta_class->fta_get_settings();
            if ( isset( $fta_settings['plugins']['facebook']['default_page_id'] ) ) {
                $fb_id = $fta_settings['plugins']['facebook']['default_page_id'];
            }
            if ( isset( $fta_settings['plugins']['instagram']['default_page_id'] ) ) {
                $insta_id = $fta_settings['plugins']['instagram']['default_page_id'];
            }
            if ( $fb_id || $insta_id ) {
                $query->set( 'post__not_in', array( $fb_id, $insta_id ) );
            }
            // array page ids
        }
        
        return $query;
    }

}
/* FTA_Admin Class ends here. */
$FTA_Admin = new FTA_Admin();