<?php

    //  ============== Enquque JS ========================
    
    function dr_custom_scripts() {
        wp_enqueue_script('my-script', get_stylesheet_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
    }
    add_action('wp_enqueue_scripts', 'dr_custom_scripts');


    // ============== Enqueue Stylesheet =================
    
    function dr_enqueue_styles() {
        wp_enqueue_style('custom-stylesheet', get_stylesheet_uri());
    }
    add_action('wp_enqueue_scripts', 'dr_enqueue_styles');

    
   

    // ================ Modify Elementor site-logo widget for SEO ======================

    function modify_elementor_theme_site_logo_output($content, $widget) {
        if ($widget->get_name() === 'theme-site-logo') { // Correct widget name
            $site_name = get_bloginfo('name'); // Get site title
            $home_url = home_url('/'); // Get homepage URL
    
            // Extract the <img> tag from the existing content
            preg_match('/<img[^>]+>/i', $content, $img_tag);
    
            if (!empty($img_tag[0]) && is_front_page()) {
                // Replace the default Site Logo widget output our custom markup
                return '<h1 class="custom-logo-header">
                            <a href="' . esc_url($home_url) . '" title="' . esc_attr($site_name) . '">
                                ' . $img_tag[0] . '
                            </a>
                        </h1>';
            }
            elseif(!empty($img_tag[0]) && !is_front_page()) {
                return '<div class="custom-logo-header">
                            <a href="' . esc_url($home_url) . '" title="' . esc_attr($site_name) . '">
                                ' . $img_tag[0] . '
                            </a>
                        </div>';
            }
    
            // If no logo is set, return the site name as a link
            return '<h1 class="custom-logo-header">
                        <a href="' . esc_url($home_url) . '">' . esc_html($site_name) . '</a>
                    </h1>';
        }
        return $content;
    }
    add_filter('elementor/widget/render_content', 'modify_elementor_theme_site_logo_output', 10, 2);

    // =========== Office Settings ===================

    function office_settings_init() {
        add_settings_section('office_info_section', 'Dental Revenue | Office Information', null, 'general');
    
        $fields = [
            'office_name' => 'Office Name',
            'street_address' => 'Street Address',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'Zip Code',
            'account_id' => 'Account ID',
            'gtm_id' => 'GTM ID',
            'recaptcha_key' => 'Recaptcha Key',
            'new_lead_number' => 'Lead Number',
            'current_customer_number' => 'Customer Number',
            'email' => 'Email',
            'cc_email' => 'CC Email',
            'google_review_url' => 'Google Review URL',
            'google_place_id' => 'Google Place ID'
        ];
    
        foreach ($fields as $field => $label) {
            register_setting('general', $field);
            add_settings_field(
                $field,
                $label,
                function () use ($field) {
                    $value = get_option($field, '');
                    echo '<input type="text" name="' . esc_attr($field) . '" value="' . esc_attr($value) . '" class="regular-text">';
                },
                'general',
                'office_info_section'
            );
        }
    }
    add_action('admin_init', 'office_settings_init');

    // ============ Activate shortcode for DR Office Info ====================

    function get_client_info_shortcode($atts) {
        $atts = shortcode_atts(['field' => ''], $atts);
        $field = $atts['field']; // Assign the field variable
        $value = get_option($field, 'Not Available'); // Get the field value from WP settings
    
        // Check if the field is 'new_lead_number' and wrap it in a span with class
        if ($field === 'new_lead_number') {
            return '<span class="tracknum">' . esc_html($value) . '</span>';
        }
    
        // Return normal text for other fields
        return esc_html($value);
    }
    add_shortcode('client_info', 'get_client_info_shortcode');


     // ============= Google Tag Manager =================
    
     function hook_javascript() {
        ?>
           <!-- Google Tag Manager --> 
           <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= get_option('gtm_id', 'N/A') ?>');
            </script>   

            <!-- End Google Tag Manager -->
        <?php
    }
    add_action('wp_head', 'hook_javascript');


    function add_script_after_body_open() {
        ?>
       <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= get_option('gtm_id', 'N/A') ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->

        <?php
    }
    add_action('wp_body_open', 'add_script_after_body_open');


// Add custom fields on every page and post by default

    add_action('admin_head', function() {
        $screen = get_current_screen();
        if ($screen->post_type === 'page') {
            add_filter('default_hidden_meta_boxes', function($hidden, $screen) {
                if ($screen->id === 'page') {
                    return array_diff($hidden, ['postcustom']);
                }
                return $hidden;
            }, 10, 2);
        }
    });
    

        // ============= Dash AccountID ===============

    function add_accountID_after_body_open() {
        ?>
        <script>
            const DRAccountId ="<?= get_option('account_id', 'N/A') ?>";
        </script>
        <?php
        }
        add_action('wp_body_open', 'add_accountID_after_body_open');
        
    // =============== Form ===================

    function request_appointment_form_shortcode() {
        ob_start();
        ?>
        <form action="https://ws.dentalrevenue.com/ws/forms/ProcessLeadCertV5.aspx" method="post" name="form-schedule" autocomplete="nope">
            <div class="user-info">
                <div class="name">
                    <label for="firstname">First name:</label>
                    <input type="text" name="FirstName" id="firstname" class="s-input">
                    <label for="lastname">Last name:</label>
                    <input type="text" name="LastName" id="lastname" class="s-input">
                </div>
                <div class="contact-info">
                    <label for="phone">Phone:</label> 
                    <input type="text" name="Phone" id="phone" class="s-input">
                    <label for="email">Email:</label> 
                    <input type="text" name="EmailName" id="email" class="s-input">
                    <input type="text" name="RepeatEmailName" placeholder="Retype Email" autocomplete="nope">
                </div>
            </div>
    
            <div class="user-info">
                <div class="info-select">
                    <label for="contact-method">I prefer to be contacted by:</label> 
                    <select name="AdsNext-PreferredContactMethod" id="contact-method" class="s-input">
                        <option selected="" value="Select One">Select One</option>
                        <option value="Phone">Phone</option>
                        <option value="Email">Email</option>
                    </select>
    
                    <label for="patient">Are you a new Patient?</label>
                    <select name="AdsNext-AreYouNewPatient" id="patient" class="s-input">
                        <option selected="" value="Select One">Select One*</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
    
                <div class="info-select">
                    <label for="range">I would like to schedule a visit</label> 
                    <select name="AdsNext-ScheduleRange" id="range" class="s-input">
                        <option selected="" value="Select One">Select One</option>
                        <option value="Less than 1 month">Less than 1 month</option>
                        <option value="1 - 3 months">in 1 - 3 months</option>
                        <option value="3 - 6 months">in 3 - 6 months</option>
                        <option value="6 - 12 months">in 6 - 12 months</option>
                        <option value="12 months or more">in 12 months+</option>
                    </select>
    
                    <label for="day-time">What time of day would you prefer?</label> 
                    <select name="AdsNext-PreferredTimeOfDay" id="day-time" class="s-input">
                        <option selected="" value="Select One">Select One</option>
                        <option value="Morning">Morning</option>
                        <option value="Mid-day">Mid-day</option>
                        <option value="Afternoon">Afternoon</option>
                    </select>
                </div>
            </div>
    
            <div class="bottom-info">
                <fieldset>
                    <legend>What day of the week would you like to schedule your consultation (select all that apply)</legend>
                    <input type="checkbox" value="Monday" name="AdsNext-PreferredDayMonday" id="monday"><label for="monday">Monday</label>
                    <input type="checkbox" value="Tuesday" name="AdsNext-PreferredDayTuesday" id="tuesday"><label for="tuesday">Tuesday</label>
                    <input type="checkbox" value="Wednesday" name="AdsNext-PreferredDayWednesday" id="wednesday"><label for="wednesday">Wednesday</label>
                    <input type="checkbox" value="Thursday" name="AdsNext-PreferredDayThursday" id="thursday"><label for="thursday">Thursday</label>
                    <input type="checkbox" value="Friday" name="AdsNext-PreferredDayFriday" id="friday"><label for="friday">Friday</label>
                </fieldset>
    
                <div>
                    <label for="comments">Comments</label><br>
                    <textarea name="Comments" id="comments" class="s-input" rows="2"></textarea>
                </div>
            </div>
    
            <br>
    
            <button class="g-recaptcha btn solid" data-sitekey="<?= get_option('recaptcha_key', 'N/A') ?>" data-callback="onSubmit" data-action="submit" type="submit">Submit</button>

    
            <!-- Dashboard Account Info -->
            <input name="Subject" type="hidden" value="<?= get_option('office_name', 'N/A') ?> Schedule Appointment Form">
            <input name="Campaign" type="hidden" value="Schedule Appointment Form">
            <input name="AccountID" type="hidden" value="<?= get_option('account_id', 'N/A') ?>">
            <input name="RedirectPageFullURL" type="hidden" value="/thank-you">
            <input name="EmailRecipient" type="hidden" value="<?= get_option('email', 'N/A') ?>">

            <?php if ($cc_email = get_option('cc_email')): ?>
                <input name="EmailCC" type="hidden" value="<?= esc_attr($cc_email) ?>">
            <?php endif; ?>
            
        </form>
    
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <!-- <script>
        function onSubmit(token) {
            document.forms['form-schedule'].submit();
            }
        </script> -->

        <?php
        return ob_get_clean();
    }
    add_shortcode('dr_form', 'request_appointment_form_shortcode');
    
//   Inner Page form

    function inner_page_form_shortcode() {
        ob_start();
        ?>
        <form action="https://ws.dentalrevenue.com/ws/forms/ProcessLeadCertV5.aspx" method="post" name="form-schedule" autocomplete="nope">
            <div class="user-info">
                <div class="name">
                    <label for="firstname">First name:</label>
                    <input type="text" name="FirstName" id="firstname" class="s-input">
                    <label for="lastname">Last name:</label>
                    <input type="text" name="LastName" id="lastname" class="s-input">
                </div>
                <div class="contact-info">
                    <label for="phone">Phone:</label> 
                    <input type="text" name="Phone" id="phone" class="s-input">
                    <label for="email">Email:</label> 
                    <input type="text" name="EmailName" id="email" class="s-input">
                    <input type="text" name="RepeatEmailName" placeholder="Retype Email" autocomplete="nope">
                    <textarea name="Comments" id="comments" placeholder="What are your oral health concerns?" class="s-input" rows="2" spellcheck="false"></textarea>
                </div>
            </div>
    
    
            <br>
            <button class="g-recaptcha btn solid" data-sitekey="<?= get_option('recaptcha_key', 'N/A') ?>" data-callback="onSubmit" data-action="submit" type="submit">Submit</button>

            <!-- Dashboard Account Info -->
            <input name="Subject" type="hidden" value="<?= get_option('office_name', 'N/A') ?> Schedule Appointment Form">
            <input name="Campaign" type="hidden" value="Schedule Appointment Form">
            <input name="AccountID" type="hidden" value="<?= get_option('account_id', 'N/A') ?>">
            <input name="RedirectPageFullURL" type="hidden" value="/thank-you">
            <input name="EmailRecipient" type="hidden" value="<?= get_option('email', 'N/A') ?>">
            

            <?php if ($cc_email = get_option('cc_email')): ?>
                <input name="EmailCC" type="hidden" value="<?= esc_attr($cc_email) ?>">
            <?php endif; ?>

        </form>
    
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
        <?php
        return ob_get_clean();
    }
    add_shortcode('dr_form-innerpage', 'inner_page_form_shortcode');






//   Inner Page form

    function minimal_form_shortcode() {
        ob_start();
        ?>
        <form action="https://ws.dentalrevenue.com/ws/forms/ProcessLeadCertV5.aspx" method="post" name="form-schedule" autocomplete="nope" id="mini-form">

        
                
           
                <div class="contact-info mini-contact-info">
                        <label class="elementor-screen-only" for="firstname">Name:</label>
                    <input type="text" name="Name" id="firstname" class="s-input" placeholder="Name">
                    <label class="elementor-screen-only" for="phone">Phone:</label> 
                    <input type="text" name="Phone" id="phone" class="s-input" placeholder="Phone">
                    <label class="elementor-screen-only" for="email">Email:</label> 
                    <input type="text" name="EmailName" id="email" class="s-input" placeholder="Email">
                    <input type="text" name="RepeatEmailName" placeholder="Retype Email" autocomplete="nope">
                                <button class="g-recaptcha btn solid" data-sitekey="<?= get_option('recaptcha_key', 'N/A') ?>" data-callback="onSubmit" data-action="submit" type="submit">Submit</button>

               
                
                </div>
    
    
            <br>

            <!-- Dashboard Account Info -->
            <input name="Subject" type="hidden" value="<?= get_option('office_name', 'N/A') ?> Schedule Appointment Form">
            <input name="Campaign" type="hidden" value="Schedule Appointment Form">
            <input name="AccountID" type="hidden" value="<?= get_option('account_id', 'N/A') ?>">
            <input name="RedirectPageFullURL" type="hidden" value="/thank-you">
            <input name="EmailRecipient" type="hidden" value="<?= get_option('email', 'N/A') ?>">
            

            <?php if ($cc_email = get_option('cc_email')): ?>
                <input name="EmailCC" type="hidden" value="<?= esc_attr($cc_email) ?>">
            <?php endif; ?>

        </form>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <style>
            form#mini-form {
    display: flex;
    width: 100%;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    margin: 1rem 0;
}
.contact-info.mini-contact-info {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}
.contact-info.mini-contact-info > input {
    margin-bottom: 0;
    margin-right: 1rem;
}
#mini-form > div > button {
    width: 100%;
}
@media(max-width: 767px) {
    #mini-form, .contact-info.mini-contact-info  {
        flex-direction: column;
        align-items: baseline;
    }
   #mini-form > div > button {
    margin-top: 1rem;
}
.contact-info.mini-contact-info > input {
    margin-bottom: 15px;}

    
}
        </style>
    
    
    
        <?php
        return ob_get_clean();
    }
    add_shortcode('minimal_form', 'minimal_form_shortcode');
    


    //exclude noindex pages from sitemap
function exclude_no_indexes_from_wp_list_pages( $pages, $args ) {	
    if( array_key_exists( 'walker', $args ) ) {
        foreach ( $pages as $key => $item ) {
            $_yoast_wpseo_meta_robots_noindex = get_post_meta( $item->ID, '_yoast_wpseo_meta-robots-noindex', true );
            if( $_yoast_wpseo_meta_robots_noindex == 1 ) unset( $pages[$key] );
        }
    }
    return $pages;
}
add_filter( 'get_pages', 'exclude_no_indexes_from_wp_list_pages', 10, 2 );



add_filter('acf/settings/remove_wp_meta_box', '__return_false');





