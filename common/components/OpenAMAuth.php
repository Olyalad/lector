<?php

namespace common\components;

/**
 * Class OpenAMAuth
 * Name: OpenAM REST Authentication Library
 *
 * Description: This library is used to authenticate users using OpenAM.
 * The library uses REST calls to the OpenAM.
 * The required REST APIs are: /json/authenticate; /json/users/ and /json/sessions.
 * Therefore you need OpenAM 11.0 and above.
 *
 * Version: 0.1.0
 * Author: Eugene Peregudov
 * Email:  joniknsk@gmail.com
 * Date:   28.05.2015
 */
class OpenAMAuth
{
    /** @var OpenAmParams */
    private $param;

    /** @var OpenAmConfig */
    private $config;

    /** @var OpenAmDesc */
    private $desc;

    private $session = null;

    /**
     * OpenAMAuth constructor.
     * @param OpenAmConfig $config
     * @param OpenAmParams $params
     * @param OpenAmDesc $desc
     */
    public function __construct($config = null, $params = null, $desc = null)
    {
        // default openam parameters
        $this->param = $params ? $params : new OpenAmParams();

        // default openam configurations
        $this->config = $config != null ? $config : new OpenAmConfig();

        // openam configurations descriptions
        $this->desc = $desc ? $desc : new OpenAmDesc();

        $this->logMessage(get_class($this) . " construct: config=" . print_r($this->config->getAttributes(), true));
    }

    public function getParam($param)
    {
        return $this->param[$param];
    }

    public function getConf($conf)
    {
        return $this->config[$conf];
    }


    /**
     * Authenticate with redirect or login/password
     * @param bool $redirect
     * @param string $login_url
     * @param string $username
     * @param string $password
     * @return null|string - token
     */
    public function openam_login($redirect = false, $login_url = null, $username = '', $password = '')
    {
        // Check if redirect to login page required
        $this->probeHttpRedirectAndDie($redirect, $this->openam_login_url($login_url));

        // If username and password presents, then we are starting here
        if ($username != '' and $password != '') {
            $token = $this->authenticateWithOpenAM($username, $password);
            if ($token) {
                // User exist, returns token
                $this->logMessage("openam_login: openam authentication successful! token=" . $token);
                return $token;
            }
            $this->logMessage("openam_login: the combination username/password was not correct");
        }
        // User does not exist, send back an error message
        $this->logMessage("openam_login: authentication failed");
        return null;
    }


    public function openam_logout($redirect = false, $logout_url = null)
    {
        // Check if redirect to logout page required
        $this->probeHttpRedirectAndDie($redirect, $this->openam_logout_url($logout_url));
        // Invalidate token with openam request
        $response = $this->invalidateWithOpenAM($this->openam_token());
        return $response;
    }

    public function openam_login_url($goto_url = null)
    {
        return $this->createOpenAMParamURL($this->config['url'] . $this->param['login'], $goto_url);
    }

    public function openam_logout_url($goto_url = null)
    {
        $base_url = $this->config['url'] . $this->param['logout'];
        // Process goto url parameter
        if ($goto_url != '') {
            $base_url .= (stripos($base_url, '?') ? '&' : '?');
            $base_url .= $this->param['goto'] . "=" . urlencode($goto_url);
        }
        return $base_url;
    }

    public function openam_current_url()
    {
        // by default - scheme is http
        $url = 'http';
        // set scheme if proxy request
        if (isset ($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $url = $_SERVER['HTTP_X_FORWARDED_PROTO'];
            // set scheme if direct request
        } elseif (isset($_SERVER['HTTPS'])) {
            $url = ($_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        }
        // add delimeter
        $url .= "://";

        // set host if proxy request
        if (isset ($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $url .= $_SERVER['HTTP_X_FORWARDED_HOST'];
            // set host if direct request
        } elseif (isset ($_SERVER['HTTP_HOST'])) {
            $url .= $_SERVER['HTTP_HOST'];
        } else {
            // set host from server settings
            $url .= $_SERVER['SERVER_NAME'];
        }
        // set request uri from variable or set /
        $url .= (isset ($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '/';
        return $url;
    }

    public function openam_token()
    {
        return (isset ($_COOKIE[$this->config['cookie']]) ? $_COOKIE[$this->config['cookie']] : null);
    }

    public function openam_session()
    {
        return $this->session;
    }

    public function openam_validate($token)
    {
        $this->logMessage("openam_validate: token=" . $token);
        // decode openam session information
        $am_session = json_decode($this->validateWithOpenAM($token), true);
        return $am_session;
    }

    public function openam_authenticate($redirect, $goto_url = null)
    {
        // log and start authentication
        $this->logMessage("openam_authenticate: redirect=" . ($redirect ? "true" : "false") . " goto_url=" . $goto_url);
        $authenticated = false;
        // start or resume current php session
        @session_start();
        // get token
        $token = $this->openam_token();
        // check token in cache
        $this->session = $this->getCachedPHPSession($token);
        // if returns from cache
        if (isset ($this->session) and is_array($this->session)) {
            // AUTHENTICATED FROM SESSION CACHE!
            $authenticated = true;
            $this->logMessage("openam_authenticate: AUTHENTICATED FROM CACHE user = " . $this->session['uid']);
        } else {
            // clear cached or expired session
            $this->clearCachedPHPSession();
            // validate token with openam request and redirect to login page if enabled
            $this->session = $this->openam_validate($token);

            if ($this->session['valid']) {
                // AUTHENTICATED FROM OPENAM!
                $authenticated = true;
                $this->logMessage("openam_authenticate: AUTHENTICATED FROM OPENAM user = " . $this->session['uid']);
                // save validated token with timestamp to php session
                $this->saveCachedPHPSession($this->session, $token);
            } else {
                // NOT AUTHENTICATED!
                $this->logMessage("openam_authenticate: NOT AUTHENTICATED!");
                // try to login
                $this->openam_login($redirect, ($goto_url == null ? $this->openam_current_url() : $goto_url));
            }
        }
        $this->logMessage("openam_authenticate: authenticated=" . ($authenticated ? "true" : "false") . " result session=" . print_r($this->session, true));
        return $authenticated;
    }

    public function openam_info()
    {
        echo "<code>\n<table border='1'>\n\t<caption><h3>OpenAM REST Authentication Library Settings</h3></caption>\n\t<thead style='font-weight: bold; background: #9ac0cd;'>\n\t\t<tr><td>Parameter</td><td>Value</td><td>Short Name</td><td>Description</td></tr>\n\t</thead>\n";
        $bold = false;
        foreach ($this->config->getAttributes() as $key => $value) {
            echo "\t<tr" . ($bold ? " style='background: #ebebeb;'" : "") . "><td>" . $key . "</td><td>" . $value . "</td><td>" . $this->desc[$key][0] . "</td><td>" . $this->desc[$key][1] . "</td></tr>\n";
            $bold = !$bold;
        }
        echo "\t<tr style='font-weight: bold; background: #f0e68c;' ><td colspan='2'>Current token values:</td><td>" . $this->config['cookie'] . "</td><td>" . $this->openam_token() . "</td></tr>\n</table>\n</code>";
    }


    /**
     * Generate URL to OpenAM
     * @param $base_url
     * @param null $goto_url
     * @return string
     */
    private function createOpenAMParamURL($base_url, $goto_url = null)
    {
        // Process realm parameter
        if ($this->config['realm'] != '') {
            $base_url .= "?" . $this->param['realm'] . "=" . $this->config['realm'];
        }
        // Process authentication module or service chain parameters
        if ($this->config['module'] != '') {

            $base_url .= (stripos($base_url, '?') ? '&' : '?');
            $base_url .= $this->param['module'] . "=" . $this->config['module'];
        } else {
            if ($this->config['service'] != '') {
                $base_url .= (stripos($base_url, '?') ? '&' : '?');
                $base_url .= $this->param['service'] . "=" . $this->config['service'];
            }
        }
        // Process goto url parameter
        if ($goto_url != '') {
            $base_url .= (stripos($base_url, '?') ? '&' : '?');
            $base_url .= $this->param['goto'] . "=" . urlencode($goto_url);
        }
        return $base_url;
    }


    /**
     * Authenticate with login/password
     * @param string $username
     * @param string $password
     * @return string
     */
    private function authenticateWithOpenAM($username, $password)
    {
        // Configure authentication url and make http request
        $authentication_url = $this->createOpenAMParamURL($this->config['url'] . $this->param['auth']);
        $this->logMessage("authenticateWithOpenAM: authentication_url=" . $authentication_url);
        $headers = "X-OpenAM-Username: " . $username . "\r\n" .
            "X-OpenAM-Password: " . $password . "\r\n";
        $response = $this->sendHttpRequest($authentication_url, $headers);
        $this->logMessage("authenticateWithOpenAM: auth_response=" . $response);
        // decode actual response and set cookie if token present
        $am_response = json_decode($response, true);
        if (isset ($am_response['tokenId'])) {
            setcookie($this->config['cookie'], $am_response['tokenId'], 0, '/', $this->config['domain']);
            $this->logMessage("validateWithOpenAM: set session cookie=['" . $this->config['cookie'] . "', '" . $am_response['tokenId'] . "', 0, '/', '" . $this->config['domain'] . "']");
        }
        return $response;
    }


    /**
     * Validate OpenAM token
     * @param string $token
     * @return string
     */
    private function validateWithOpenAM($token)
    {
        // configure session validation url and make http request
        $session_url = $this->config['url'] . $this->param['sess'];
        $session_url .= isset($token) ? $token : "NULL";
        $session_url .= "?_action=validate";
        $this->logMessage("validateWithOpenAM: token=" . $token . "\nsession_url=" . $session_url);
        // check actual openam session associated with token
        $response = $this->sendHttpRequest($session_url);
        $this->logMessage("validateWithOpenAM: validate token with openam, response=" . $response);
        return $response;
    }


    private function invalidateWithOpenAM($token)
    {
        // configure session logout url and make http request
        $session_url = $this->config['url'] . $this->param['sess'];
        $session_url .= "?_action=logout";
        $this->logMessage("invalidateWithOpenAM: token=" . $token, "\nsession_url=" . $session_url);
        // actual logout with openam instance
        $response = $this->sendHttpRequest($session_url, $this->config['cookie'] . ": " . (isset($token) ? $token : "NULL") . "\r\n");
        $this->logMessage("invalidateWithOpenAM: logout with openam, response=" . $response);
        setcookie($this->config['cookie'], '', time() - $this->config['timeout'], '/', $this->config['domain']);
        $this->logMessage("invalidateWithOpenAM: set remove cookie=['" . $this->config['cookie'] . "', '', " . (time() - $this->config['timeout']) . ", '/', '" . $this->config['domain'] . "']");
        return $response;
    }


    private function saveCachedPHPSession($am_session, $token)
    {
        // store cached entry to php session array
        $cached_session = array();
        $cached_session['token'] = $token;
        $cached_session['timestamp'] = time();
        $cached_session['session'] = $am_session;
        $_SESSION[$this->config['cookie']] = $cached_session;
        $this->logMessage("savePHPSession: \$_SESSION['" . $this->config['cookie'] . "']=" . print_r($_SESSION[$this->config['cookie']], true));
    }

    private function getCachedPHPSession($token)
    {
        // validate not empty token and session and get cached entry
        $am_session = null;
        $this->logMessage("getCachedPHPSession: search token=" . $token);
        if (isset ($token) and
            isset ($_SESSION[$this->config['cookie']]) and
            is_array($_SESSION[$this->config['cookie']])
        ) {
            $cached_session = $_SESSION[$this->config['cookie']];
            $current_time = time();
            $this->logMessage("getCachedPHPSession: current_time=" . $current_time . " timestamp=" . $cached_session['timestamp'] .
                " diff=" . ($current_time - $cached_session['timestamp']) . " timeout=" . $this->config['timeout'] . " cached_session=" . print_r($cached_session, true));
            // if token == session token and timestamp + timeout > current time
            if ($token == $cached_session['token'] and
                ($cached_session['timestamp'] + $this->config['timeout'] > $current_time)
            ) {
                $am_session = $cached_session['session'];
            }
        }
        $this->logMessage("getCachedPHPSession: result am_session=" . print_r($am_session, true));
        return $am_session;
    }

    private function clearCachedPHPSession()
    {
        // clear stored entry in php session array if exists
        if (isset ($_SESSION[$this->config['cookie']]))
            unset($_SESSION[$this->config['cookie']]);
        $this->logMessage("clearPHPSession: clear php session array \$_SESSION['" . $this->config['cookie'] . "']");
    }


    private function sendHttpRequest($url, $headers = '')
    {
        // Configure http context
        $context = array(
            "http" => array(
                "method" => "POST",
                "ignore_errors" => true,
                "header" =>
                    "Content-Type: application/json\r\n" .
                    "Connection: close\r\n" . $headers,
            ),
            "ssl" => array(
                "allow_self_signed" => true,
                "verify_peer" => false,
            ),
        );
        $this->logMessage("sendHttpRequest: request=" . $url . " headers=\r\n[\r\n" . $context['http']['header'] . "]");
        // Send http request, verify and return result
        $response = file_get_contents($url, false, stream_context_create($context));
        $this->logMessage("sendHttpRequest: result=" . $http_response_header[0] . " response=" . $response);
        return $response;
    }

    /**
     * Redirect to login page
     * @param boolean $redirect
     * @param string $url
     */
    private function probeHttpRedirectAndDie($redirect, $url)
    {
        if ($redirect and $this->config['redirect']) {
            $this->logMessage("probeHttpRedirectAndDie: redirect to location: " . $url);
            //header("Access-Control-Allow-Origin: http://ciu.nstu.ru");
            header("Location: " . $url);
            //echo "<pre> Location: " . $url . "\n\n </pre>";
            die();
        }
    }


    /**
     * Add a message in log file
     * @param string $message
     */
    private function logMessage($message)
    {
        if ($this->config['debug']) {
            if ($this->config['file'] != '') {
                error_log(date('Y-m-d H:i:s ') . $this->config['name'] . " " . $message . "\n", 3, $this->config['file']);
            } else {
                error_log(date('Y-m-d H:i:s ') . $this->config['name'] . " " . $message . "\n", 0);
            }
        }
    }


    private function isChecked($checked, $current = true, $echo = true)
    {
        return $this->isCheckedHelper($checked, $current, $echo, 'checked');
    }


    private function isCheckedHelper($helper, $current, $echo, $type)
    {
        if ((string)$helper === (string)$current)
            $result = " $type='$type'";
        else
            $result = '';
        if ($echo)
            echo $result;
        return $result;
    }


    public function openam_check()
    {
        $redirect = false; // РёР»Рё РїРµСЂРµРґР°РІР°С‚СЊ РµРіРѕ РІ С„СѓРЅРєС†РёСЋ РІ РєРѕРЅС†Рµ РєРѕРЅС†РѕРІ, РёРЅР°С‡Рµ РІ РѕС‚Р»Р°РґРѕС‡РЅРѕРј СЂРµР¶РёРјРµ Р»РµР·РµС‚ РїСЂРµРґСѓРїСЂРµР¶РґРµРЅРёРµ (Р–РµРЅСЏ Р”.)
        // log and start authentication
        $this->logMessage("openam_check: redirect=" . ($redirect ? "true" : "false") . " current_url=" . $this->openam_current_url());
        $authenticated = false;
        // start or resume current php session
        @session_start();
        // get token
        $token = $this->openam_token();
        // check token in cache
        $this->session = $this->getCachedPHPSession($token);
        // if returns from cache
        if (isset ($this->session) and is_array($this->session)) {
            // AUTHENTICATED FROM SESSION CACHE!
            $authenticated = true;
            $this->logMessage("openam_check: AUTHENTICATED FROM CACHE user = " . $this->session['uid']);
        } else {
            // clear cached or expired session
            $this->clearCachedPHPSession();
            // validate token with openam request and redirect to login page if enabled
            $this->session = $this->openam_validate($token);

            if ($this->session['valid']) {
                // AUTHENTICATED FROM OPENAM!
                $authenticated = true;
                $this->logMessage("openam_check: AUTHENTICATED FROM OPENAM user = " . $this->session['uid']);
                // save validated token with timestamp to php session
                $this->saveCachedPHPSession($this->session, $token);
            } else {
                // NOT AUTHENTICATED!
                $this->logMessage("openam_check: NOT AUTHENTICATED!");
                // try to login
                //$this->openam_login( $redirect, $this->openam_current_url() );
            }
        }
        $this->logMessage("openam_authenticate: authenticated=" . ($authenticated ? "true" : "false") . " result session=" . print_r($this->session, true));
        return $authenticated;
    }


}

?>


