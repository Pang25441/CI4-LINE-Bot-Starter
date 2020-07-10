<?php

namespace App\Libraries;

class Liff
{
	protected $line;
	protected $accessToken;
    
    function __construct()
    {
        // $this->line = new \Config\Line();
    }

    function verifyIdToken($idToken)
    {
        $response = $this->httpClient('POST', 'https://api.line.me/oauth2/v2.1/verify', 'id_token='.$idToken, 'verifyIdToken');
        if($response)
        {
            if(isset($response->error))
            {
                $error_msg = "verifyIdToken $response->error: ($response->error_description)";
			    log_message('error', $error_msg);
                return false;
            }
            else
            {
                return $response;
            }
        }
        return false;
	}
	
	function verifyAccessToken($accessToken)
    {
        $response = $this->httpClient('GET', 'https://api.line.me/oauth2/v2.1/verify'. '?access_token='.$accessToken,'', 'verifyAccessToken');
        if($response)
        {
            if(isset($response->error))
            {
                $error_msg = "verifyIdToken $response->error: ($response->error_description)";
			    log_message('error', $error_msg);
                return false;
            }
            else
            {
                return $response;
            }
        }
        return false;
	}
	
	function getProfile($accessToken)
	{
		$this->accessToken = $accessToken;
		$response = $this->httpClient('GET', 'https://api.line.me/v2/profile', '', 'getProfile');
		return $response ? $response : false;
	}

    private function httpClient($method='POST',$endpoint,$JSONBody,$origin_method = 'httpClient') 
	{
		$curl = curl_init();

		$header = [
			"cache-control: no-cache",
			"content-type: application/json"
		];
		if($this->accessToken)
		{
			$header[] = "authorization: Bearer " . $this->accessToken;
		}
		
		curl_setopt_array($curl, [
			CURLOPT_URL 			=> $endpoint,
			CURLOPT_RETURNTRANSFER 	=> true,
			CURLOPT_MAXREDIRS 		=> 10,
			CURLOPT_TIMEOUT 		=> 30,
			CURLOPT_HTTP_VERSION 	=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 	=> $method,
			CURLOPT_POSTFIELDS 		=> $JSONBody,
			CURLOPT_HTTPHEADER 		=> $header,
		]);

		$response 	= curl_exec($curl);
		$err 		= curl_error($curl);
		$status 	= curl_getinfo($curl);
		$http_code 	= $status['http_code'];

		curl_close($curl);

		if ($http_code !== 200) 
		{
			$error_msg = "$origin_method Failed ($http_code)";
			log_message('error', $error_msg);
			log_message('info', $response);
			return false;
		} 
		else 
		{
			return json_decode($response);
		}
	}
}