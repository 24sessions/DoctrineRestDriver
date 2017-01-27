<?php

namespace DoctrineRestDriver\Security;

use DoctrineRestDriver\Types\Request;
use League\OAuth2\Client\Token\AccessToken;

class OAuth2Authentication implements AuthStrategy {

    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function transformRequest(Request $request) {
        /** @var $accessToken AccessToken */
        $accessToken = $_SESSION['_sf2_attributes']['access_token']; // from Symfony session

        $options  = $request->getCurlOptions();
        $headers  = !empty($options[CURLOPT_HTTPHEADER]) ? $options[CURLOPT_HTTPHEADER] : [];
        $headers[] = "Accept: application/ld+json";
        $headers[] = 'Authorization: Bearer ' . $accessToken->getToken();
        $options[CURLOPT_HTTPHEADER] = $headers;

        return $request->setCurlOptions($options);
    }
}