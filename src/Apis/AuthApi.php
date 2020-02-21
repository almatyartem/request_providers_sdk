<?php

namespace ApiSdk;

use ApiSdk\Contracts\RequestProvider;

class AuthApi
{
    /**
     * @var RequestProvider
     */
    public $provider;

    public $api;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $oauthCallback;

    /**
     * @var string
     */
    public $env;

    /**
     * @var string
     */
    public $app;

    /**
     * AuthApi constructor.
     * @param RequestProvider $provider
     * @param string $clientId
     * @param string $clientSecret
     * @param string $oauthCallback
     * @param string $env
     * @param string $app
     * @param null $api
     */
    public function __construct(RequestProvider $provider, string $clientId, string $clientSecret, string $oauthCallback, string $env, string $app, $api = null)
    {
        $this->provider = $provider;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->oauthCallback = $oauthCallback;
        $this->env = $env;
        $this->app = $app;
        $this->api = $api ?? 'auth';
    }

    /**
     * @param $code
     * @return string|null
     */
    public function getClientToken($code) : ?string
    {
        try
        {
            $data = $this->provider->request($this->api , 'post','oauth/token',  [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->oauthCallback,
                'code' => $code,
            ], []);


            if($data and isset($data['access_token']) and $data['access_token'])
            {
                return $data['access_token'];
            }
        }
        catch(RequestProviderException $exception){}

        return null;
    }

    /**
     * @param $email
     * @param $password
     * @return string|null
     */
    public function getClientTokenByAuth($email, $password) : ?string
    {
        try
        {
            $data = $this->provider->request($this->api,'post','oauth/token',  [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username' => $email,
                'password' => $password,
                'scope' => '',
            ]);
        }
        catch(RequestProviderException $exception){}

        if($data and isset($data['access_token']) and $data['access_token'])
        {
            return $data['access_token'];
        }

        return null;
    }

    /**
     * @param $token
     * @return mixed
     */
    public function getUserByToken($token) : ?array
    {
        try
        {
            $data = $this->provider->request($this->api, 'get','api/user?env='.$this->env.'&app='.$this->app,  [], [
                'Authorization' => 'Bearer ' .$token
            ]);

            if(is_array($data))
            {
                return $data;
            }
        }
        catch(RequestProviderException $exception){}

        return null;
    }

    /**
     * @param $token
     * @param null $name
     * @param null $email
     * @param null $password
     * @return array|null
     */
    public function editUserByToken($token, $name = null, $email = null, $password = null) : ?array
    {
        try
        {
            $data = $this->provider->request($this->api , 'post','api/user', [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ], [
                'Authorization' => 'Bearer ' .$token
            ]);

            if(is_array($data))
            {
                return $data;
            }
        }
        catch(RequestProviderException $exception){}

        return null;
    }

    /**
     * @param $email
     * @param $name
     * @param $password
     * @return array|null
     */
    public function createUser($email, $name, $password) : ?array
    {
        try
        {
            $data = $this->provider->request($this->api , 'post','api/register', [
                'email' => $email,
                'name' => $name,
                'password' => $password
            ]);

            if(is_array($data))
            {
                return $data;
            }
        }
        catch(RequestProviderException $exception){}

        return null;
    }
}
