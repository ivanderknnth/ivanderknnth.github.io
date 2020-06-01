<?php
/*
* Stop execution if someone tried to get file directly.
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
		
/*
* Returns the Instagram API type to get data
*/
if(!function_exists('mif_instagram_type')):

	function mif_instagram_type(){

		$mif_personal_connected_accounts = 'personal';

		/*
        *  Getting the Plugin main object. 
        */
        $Feed_Them_All = new Feed_Them_All();

        /*
        *  Getting the FTA Plugin settings. 
        */
        $fta_settings = $Feed_Them_All->fta_get_settings();

        if(isset($fta_settings['plugins']['instagram']['selected_type'])){
        	$mif_personal_connected_accounts = $fta_settings['plugins']['instagram']['selected_type'];
        }else{
             
            if( isset($fta_settings['plugins']['facebook']['approved_pages']) && !empty($fta_settings['plugins']['facebook']['approved_pages']) ){
                $mif_has_business_insta = false;

                $approved_pages = $fta_settings['plugins']['facebook']['approved_pages'];

                if($approved_pages)
                foreach ($approved_pages as $key => $approved_page):
                    if( array_key_exists('instagram_connected_account', $approved_page) ) $mif_has_business_insta = true;
                endforeach;   

                 if($mif_has_business_insta){
                    $mif_personal_connected_accounts = 'business';
                 }   
            }

        }

        return $mif_personal_connected_accounts;
	}

endif;

/*
* Returns the personal Instagram accounts
*/
if(!function_exists('mif_instagram_personal_accounts')):

function mif_instagram_personal_accounts(){

		$mif_personal_connected_accounts = '';

		/*
        *  Getting the Plugin main object. 
        */
        $Feed_Them_All = new Feed_Them_All();

        /*
        *  Getting the FTA Plugin settings. 
        */
        $fta_settings = $Feed_Them_All->fta_get_settings();

        if( isset($fta_settings['plugins']['instagram']['instagram_connected_account']) && !empty($fta_settings['plugins']['instagram']['instagram_connected_account']) ){
        	$mif_personal_connected_accounts = $fta_settings['plugins']['instagram']['instagram_connected_account'];
        }

        return $mif_personal_connected_accounts;
	}
endif;	