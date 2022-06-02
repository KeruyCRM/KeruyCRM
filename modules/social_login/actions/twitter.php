<?php

require 'includes/libs/social_login/Twitter/src/Config.php';
require 'includes/libs/social_login/Twitter/src/Response.php';
require 'includes/libs/social_login/Twitter/src/SignatureMethod.php';
require 'includes/libs/social_login/Twitter/src/HmacSha1.php';
require 'includes/libs/social_login/Twitter/src/Consumer.php';
require 'includes/libs/social_login/Twitter/src/Util.php';
require 'includes/libs/social_login/Twitter/src/Request.php';
require 'includes/libs/social_login/Twitter/src/TwitterOAuthException.php';
require 'includes/libs/social_login/Twitter/src/Token.php';
require 'includes/libs/social_login/Twitter/src/Util/JsonDecoder.php';
require 'includes/libs/social_login/Twitter/src/TwitterOAuth.php';

if (isset($_REQUEST['oauth_token']) && isset($_REQUEST['oauth_verifier'])) {
} else {
    /*
     * http://support.heateor.com/how-to-get-twitter-api-key-and-secret/
     */

    $connection = new Abraham\TwitterOAuth\TwitterOAuth(CFG_TWITTER_APP_ID, CFG_TWITTER_SECRET_KEY);
    $requestToken = $connection->oauth('oauth/request_token', ['oauth_callback' => url_for('social_login/twitter')]);

    if ($connection->getLastHttpCode() == 200) {
        $url = $connection->url('oauth/authorize', ['oauth_token' => $requestToken['oauth_token']]);
        header('Location: ' . $url);
    }
}

exit();
