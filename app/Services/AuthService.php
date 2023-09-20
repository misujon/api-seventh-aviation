<?php

namespace App\Services;
use GuzzleHttp\Client;
use App\Models\ApiCredential;
use App\Constants\AppConstants;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class AuthService
{
    private $url = '';
    private $token = '';
    private $apiKey = '';
    private $apiSecret = '';

    public function __construct()
    {
        $apiCred = ApiCredential::where('name', AppConstants::AMADEUS_API)->where('status', AppConstants::STATUS_ACTIVE)->first();
        if ($apiCred)
        {
            $this->url = $apiCred->base_url;
            $this->apiKey = $apiCred->api_key;
            $this->apiSecret = $apiCred->api_secret;
            $this->token = $apiCred;
        }
    }

    public function auth(bool $new = false, int $retry=0)
    {
        try
        {
            if ($retry === 3) return false;

            $client = new Client();
            if (!empty($this->token) && !empty($this->token->token) && $new === false)
            {
                if (strtotime($this->token->token_expiration) > time())
                {
                    return $this->token;
                }
                else
                {
                    return $this->auth(true, ($retry+1));
                }
            }

            if (empty($this->apiKey) || empty($this->apiSecret))
            {
                Log::error("Api keys not found!");
                return false;
            }

            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ];
            
            $options = [
                'form_params' => [
                    'client_id' => $this->apiKey,
                    'client_secret' => $this->apiSecret,
                    'grant_type' => 'client_credentials'
                ]
            ];
            
            $request = new Request('POST', $this->url.'/v1/security/oauth2/token', $headers);
            $res = $client->sendAsync($request, $options)->wait();

            $jsonResponse = json_decode($res->getBody()->getContents(), true);

            $this->token->token = $jsonResponse['access_token'];
            $this->token->token_expiration = date('Y-m-d H:i:s', (time()+$jsonResponse['expires_in']));
            $this->token->save();
            return $this->token;
        }
        catch (\GuzzleHttp\Exception\RequestException $e)
        {
            Log::info("Authentication api request exception! (Reauth) ===================");
            if ($e->hasResponse() && $e->getResponse()->getReasonPhrase() == "Unauthorized")
            {
                return $this->auth(true, ($retry+1));
            }

            return false;
        }
        catch(Exception $e)
        {
            Log::error("Api authentication error!", [$e]);
            return false;
        }
    }
}