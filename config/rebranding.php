<?php
/**
Initial configs: 28/06/2012 by Howard Mei
Modified: 25/10/2013 by Howard Mei
*/

if ( !defined('_ROOT_DIR_') ) {
			exit();
}

if ( defined('ABSPATH') ) {
/** The first Admin user account. Don't simply use 'admin', because it's been targeted by hacking robots. 
    If not defined here, the default user name will be 'NetAdmin' */
define('_NETBRAND_NAME_',  'WhitelabelEcms');                   
define('_NETBRAND_HOME_LINK_',  '//whitelabelecms.com');                                  
define('_NETBRAND_SLOGAN_', 'White Label Enterprise CMS, A Optimized Fork Of WordPress For the Cloud & Various SaaS Applications.');
define('_NETABOUT_TITLE_', 'About WhitelabelEcms');                     
define('_NETBRAND_INTRO_', 'Create your own SaaS with the WhitelabelEcms to harness the power of WordPress plugins & themes market.');                 
// About page header introduction text.
define('_ENGCODEX_TITLE_', 'User Manual');                              
define('_ENGCODEX_LINK_', _NETBRAND_HOME_LINK_ . '/usermanual');    
define('_NETSUPPORT_TITLE_', 'Support Forum');                         
define('_NETSUPPORT_LINK_', _NETBRAND_HOME_LINK_ . '/supportforum');                  
define('_NETFEEDBACK_TITLE_', 'Direct Feedback');                            
define('_NETFEEDBACK_LINK_', _NETBRAND_HOME_LINK_ . '/directfeedback');                         	   
define('_NETBRAND_LOGO_',  _NETBRAND_HOME_LINK_ . '/logo.png');    
define('_NETBRAND_BADGE_',  _NETBRAND_HOME_LINK_ . '/badge.png');                          
define('_NETBRAND_HOME_TLINK_', '<a href="' . _NETBRAND_HOME_LINK_ . '" target=_blank>' . _NETBRAND_NAME_ . '</a>');	  
define('_NETPOWERED_BY_TITLE_',  'Powered by ' . _NETBRAND_NAME_);           
define('_NETPOWERED_BY_TLINK_', 'Powered by ' . _NETBRAND_HOME_TLINK_);        
define('_NETCROSS_PROMO_', _NETBRAND_HOME_LINK_ . '/crosspromotion');
define('_NETBLOG_LINK_',   _NETBRAND_HOME_LINK_ . '/blog');
define('_NETNEWS_LINK_',   _NETBRAND_HOME_LINK_ . '/news');

define('_ENGINE_VENDOR_LINK_',  '//www.mubiic.com/cmsaas');  
define('_ENGINE_NAME_',  'CMSaaS');  
define('_ENGINE_VER_',  '2013A');
define('_ENGINE_LOGO_', _ENGINE_VENDOR_LINK_ . '/logo.png'); 
define('_ENGINE_SLOGAN_', 'CMSaaS is the first White Label Enterprise Grade CMS optimized for Cloud and SaaS Applications.');
define('_ENGPOWERED_BY_TITLE_',  'Powered by ' . _ENGINE_NAME_);  
define('_ENGPOWERED_BY_TLINK_', 'Powered by <a href="' . _ENGINE_VENDOR_LINK_ . '" target=_blank>' . _ENGINE_NAME_ . '</a>');
define('_ENGINE_SHOWCODEORIGIN_', false);
define('_ENGINE_PLUGINCREDITS_', '<a href="' . _ENGINE_VENDOR_LINK_ . '/attributions_licenses_credits" target=_blank>Attributions, credits and licenses</a>'); 
define('_ENGINE_THEMECREDITS_', '<a href="' . _ENGINE_VENDOR_LINK_ . '/attributions_licenses_credits" target=_blank>Attributions, credits and licenses</a>'); 
define('_ENGEXTEND_LINK_', _ENGINE_VENDOR_LINK_ . '/extend');
define('_ENGSUPPORT_LINK_', _ENGINE_VENDOR_LINK_ . '/support');
define('_ENGFEEDBACK_LINK_', _ENGINE_VENDOR_LINK_ . '/feedback');
define('_ENGBLOG_LINK_', _ENGINE_VENDOR_LINK_ . '/blog');
define('_ENGNEWS_LINK_', _ENGINE_VENDOR_LINK_ . '/news');
define('_ENGCROSS_PROMO_', _ENGINE_VENDOR_LINK_ . '/crosspromotion');
}

