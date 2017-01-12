<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;

class LoginController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function processLogin(Request $request)
    {

        //login with facebook
        if($this->loginWithFacebook($request)){

            $response = $this->urlToResponse($this->generateFacebookMeUrl($request));

            if (isset($response)){
                $response = [
                    'id' => $response['id'],
                    'facebook' => [
                        'name' => $response['name']
                    ]
                ];

                return view('auth.dashboard', compact('response'));
            }

        }

        //login with account kit
        if($this->csrfPassed($request)){ //// CSRF check

            $response = $this->urlToResponse( $this->generateAccountKitAccessTokenGetterUrl($request));

            if (isset($response)){ //get user access token

                $response = $this->urlToResponse($this->generateAccountKitMeUrl($response));

                if (isset($response)) {

                    return view('auth.dashboard', compact('response'));
                }
            }
        }

        return redirect()->to('/')->withErrors(['Login could not be handled']);
    }


    private function getResponse($url)
    {
        $client = new Guzzle();
        $res = $client->request('GET', $url);
        return $res;
    }

    private function responseToArray($response)
    {
        $body = $response->getBody();
        $array = json_decode($body, TRUE);
        return $array;
    }

    private function urlToResponse($url)
    {
        $res = $this->getResponse($url);

        if ($res->getStatusCode()==200) {

            $response = $this->responseToArray($res);

            return $response;
        }

        return null;
    }

    private function csrfPassed($request)
    {
        return $request->_token == $request->csrf_nonce;
    }

    private function loginWithFacebook($request)
    {
        return $request->fb_login == "true";
    }



    //url generators

    private function generateFacebookMeUrl($request)
    {
        $graph_api_version = env('GRAPH_API_VERSION', 'v2.8');
        $url = 'https://graph.facebook.com/'. $graph_api_version .'/me?fields=id,name&access_token=' . $request->fb_access_token;
        return $url;
    }

    private function generateAccountKitMeUrl($response)
    {
        $user_access_token = $response['access_token'];
        $url = 'https://graph.accountkit.com/v1.0/me?access_token=' . $user_access_token;
        return $url;
    }

    private function generateAccountKitAccessTokenGetterUrl($request)
    {
        $facebook_app_id = env('FACEBOOK_APP_ID', 'facebook_app_id');
        $account_kit_secret = env('ACCOUNT_KIT_SECRET', 'account_kit_secret');
        $account_kit_version =  env('ACCOUNT_KIT_VERSION', 'v1.0');

        $app_access_token = 'AA|' . $facebook_app_id  . '|' . $account_kit_secret ;
        $url = 'https://graph.accountkit.com/'. $account_kit_version .'/access_token?grant_type=authorization_code&code=';

        $url = $url . $request->code .'&access_token='. $app_access_token;
        return $url;
    }

}
