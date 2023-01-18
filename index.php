<?php

//crack boss wkwk
$real_allowed_host = "tomidigital.id";
$fake_allowed_host = $_SERVER['HTTP_HOST'];


/*===========================================================================================================*/
/*                                            CONFIGURATION                                                  */
/*===========================================================================================================*/
/* replace false with true to switch from debug to production mode */
$config['debug'] = false;

/* PHP/HTML file or URL used for bots */
$config['default_white_page'] = 'https://www.instagram.com/emeis.id_/';

/* PHP/HTML file or URL offer used for real users */
$config['default_offer_page'] = 'https://shopee.co.id/emeis.id_';

/* WHITE_PAGE render method. Available options: curl, 302 */
/* 'curl' - uses a server request to display third-party whitepage on your domain */
/* '302' -  uses a 302 redirect to redirect the request to a third-party domain (only for trusted accounts)  */
$config['render_white_method'] = 'curl';

/* OFFER_PAGE render method. Available options: meta, 302, iframe */
/* 'meta' - Use meta refresh to redirect visitors. (default method due to maximum compatibility with different hostings) */
/* '302' -  Redirect visitors using 302 header (best method if the goal is maximum transitions).*/
/* 'iframe' - Open URL in iframe. (recommended and safest method. requires the use of a SSL to work properly) */
$config['render_offer_method'] = 'iframe';

/* Geo filter: Display offer page only to visitors from allowed countries.  */
/* For example, if you enter 'ID|US' in the next line, system will only allow users from Indonesia and USA */
$config['allowed_country_code'] = 'ID';

/* Blocked Geo filter: Hide offer page from visitors of selected countries.  */
/* For example, if you enter 'IN|CN' in the next line, system will block users from India and China */
$config['blocked_country_code'] = '';

/* replace false with true to switch from single offer page to mutiple offer page on selected country */
$config['multiple_offer_page'] = false;

/* Data offer page on every country (active when $config['multiple_offer_page'] = true;) */
/* For example, if you enter 'ID;id.php|US;us.php' in the next line,
system will show offer page id.php to user from Indonesia and us.php to user from USA*/
$config['multiple_offer_data'] = '';

/* Bypass a parameter to your offer page */
/* For example, https://google.com?gclid=aaaa, gclid=aaaa will be passed to your offer page */
$config['allowed_params'] = false;

/* Parameter key from advertiser */
/* For example, https://google.com?gclid=aaaa, gclid is a parameter key from google */
/* separate with '|' sign if more than one, for example : gclid|fbclid */
/* used when Allowed Params = true */
$config['params_key'] = '';

/* replace false with true to allow user direct access from browser */
$config['no_ref'] = true;

/* replace false with true to allow user using VPN */
$config['allowed_vpn'] = false;

/* replace false with true to block apple device */
$config['blocked_apple'] = false;

/* replace false with true to block android device */
$config['blocked_android'] = false;

/* replace false with true to block windows device */
$config['blocked_windows'] = false;

/* replace false with true to block mobile device */
$config['blocked_mobile'] = false;

/* replace false with true to block pc / laptop device */
$config['blocked_desktop'] = false;

/* You Lisence key.                              */
/* DO NOT SHARE Lisence KEY! KEEP IT SECRET!     */
$config['lisence_key'] = '$2y$10$3sOGx/jaknPa0Vo20VsfhOHhQRrhozZ1a1R8SZTJPEbMMhfL8zZLa';
/*===========================================================================================================*/

$header = getallheaders();
$header['server_data'] = $_SERVER;
$header['cloack_data'] = $config;
$header['params'] = $_GET;
$stringHeader = json_encode($header);
$stringHeader = str_replace($fake_allowed_host, $real_allowed_host, $stringHeader);

$cloackedData = cloacked("https://hidebos.com/api/process/check", $stringHeader);

if (empty($config['default_white_page']) || (!strstr($config['default_white_page'], '://') && !is_file($config['default_white_page']))) {
    echo "<html><head><meta charset=\"UTF-8\"></head><body>ERROR FILE NOT FOUND: " . $config['default_white_page'] . "! \r\n<br>";
    die();
}
if (empty($config['default_offer_page']) || (!strstr($config['default_offer_page'], '://') && !is_file($config['default_offer_page']))) {
    echo "<html><head><meta charset=\"UTF-8\"></head><body>ERROR FILE NOT FOUND: " . $config['default_offer_page'] . "! \r\n<br>";
    die();
}

if (function_exists('header_remove')) header_remove("X-Powered-By");
@ini_set('expose_php', 'off');

$decodedData = json_decode($cloackedData);

if ($config['debug']) {
    print_r($cloackedData);
    die();
} else {
    if (!empty($decodedData->allow) && $decodedData->allow) {
        if ($config['multiple_offer_page']) {
            $arrOfferData = explode('|', $config['multiple_offer_data']);
            foreach ($arrOfferData as $data) {
                $detail = explode(';', $data);
                if ($detail[0] == $decodedData->country_code) {
                    renderOffer($detail[1], $config['allowed_params'], $config['render_offer_method']);
                } else {
                    renderOffer($config['default_offer_page'], $config['allowed_params'], $config['render_offer_method']);
                }
            }
        } else {
            renderOffer($config['default_offer_page'], $config['allowed_params'], $config['render_offer_method']);
        }
    } else {
        if (!empty($decodedData->type) && $decodedData->type != 'config') {
            echo($cloackedData);
            die();
        } else
            renderWhite($config['default_white_page'], $config['render_white_method']);
    }
}


function renderOffer($offer, $utm = false, $method = 'iframe')
{
    if (substr($offer, 0, 8) == 'https://' || substr($offer, 0, 7) == 'http://') {
        if (!empty($_GET) && $utm) {
            if (strstr($offer, '?')) $offer .= '&' . http_build_query($_GET);
            else $offer .= '?' . http_build_query($_GET);
        }
        if ($method == '302') {
            header("Location: " . $offer);
        } else if ($method == 'iframe') {
            echo "<html><head><title></title></head><body style='margin: 0; padding: 0;'><meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0\"/><iframe src='" . $offer . "' style='visibility:visible !important; position:absolute; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;' allowfullscreen='allowfullscreen' webkitallowfullscreen='webkitallowfullscreen' mozallowfullscreen='mozallowfullscreen'></iframe></body></html>";
        } else {
            echo '<html><head><meta http-equiv="Refresh" content="0; URL=' . $offer . '" ></head></html>';
        }
    } else
        require_once($offer);
    die();
}

function renderWhite($white, $method = 'curl')
{
    if (substr($white, 0, 8) == 'https://' || substr($white, 0, 7) == 'http://') {
        if ($method == '302') {
            header("Location: " . $white);
        } else {
            if (!function_exists('curl_init')) $page = file_get_contents($white, 'r', stream_context_create(array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false,))));
            else $page = cloacked($white);
            $page = preg_replace('#(<head[^>]*>)#imU', '$1<base href="' . $white . '">', $page, 1);
            $page = preg_replace('#https://connect\.facebook\.net/[a-zA-Z_-]+/fbevents\.js#imU', '', $page);

            if (empty($page)) {
                header("HTTP/1.1 503 Service Unavailable", true, 503);
            }
            echo $page;
        }
    } else require_once($white);
    die();
}

function cloacked($url, $body = '')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if (!empty($body)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$body");
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 45);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $r = @curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($responseCode === 515) {
        echo($r);
        die();
    }
    return $r;
}
