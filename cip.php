<?php
/***********************************************

  "cip.php"

  Created by Michael Cheng on 04/16/2014 14:40
            http://michaelcheng.us/
            michael@michaelcheng.us
            --All Rights Reserved--

***********************************************/

/**
 * credentials.php should look like this:
 * 
 * <?php
 * //this is your cpanel url
 * $ddns_cpanel_url = "https://yourhost.com:2083";
 *
 * //this is the username to your cpanel
 * $ddns_cpanel_user = "username";
 *
 * //this is the password to your cpanel
 * $ddns_cpanel_pass = "password123";
 *
 * //this is the domain of your ddns domain
 * $ddns_ddns_domain = "yourdomain.com";
 *
 * //this is the url you want to visit to get redirected to your vnc server or something similar
 * $ddns_ddns_subdomain = "vnc.yourdomain.com";
 * ?>
 *
 * You can also just set these variables inline (below).
 */
require("credentials.php");
require("CpanelDDNS.php");





$cpanel = new Cpanel();
$cpanel
	->setUrl($ddns_cpanel_url)
	->setUser($ddns_cpanel_user)
	->setPass($ddns_cpanel_pass);
$cpanel->updateDdns($ddns_ddns_subdomain, $ddns_ddns_domain); //wasn't that fun? :)






echo "IP is <strong>" . $_SERVER['REMOTE_ADDR'] . "</strong>";
?>