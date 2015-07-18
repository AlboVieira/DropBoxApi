<?php
/**
 * Created by PhpStorm.
 * User: Albo
 * Date: 05/06/2015
 * Time: 19:24
 */


// ================================================================================

class UtilDropBox{

    public function store_token($token, $name)
    {
        file_put_contents("tokens/$name.token", serialize($token));
    }

    public function load_token($name)
    {
        if(!file_exists("tokens/$name.token")) return null;
        return @unserialize(@file_get_contents("tokens/$name.token"));
    }

    public function delete_token($name)
    {
        @unlink("tokens/$name.token");
    }
    // ================================================================================

    public function handle_dropbox_auth($dropbox)
    {
        // first try to load existing access token
        $access_token = $this->load_token("access");

        if(!empty($access_token)) {
            $dropbox->SetAccessToken($access_token);
        }
        elseif(!empty($_GET['auth_callback'])) // are we coming from dropbox's auth page?
        {
            // then load our previosly created request token
            $request_token = $this->load_token($_GET['oauth_token']);
            if(empty($request_token)) die('Request token not found!');

            // get & store access token, the request token is not needed anymore
            $access_token = $dropbox->GetAccessToken($request_token);
            $this->store_token($access_token, "access");
            $this->delete_token($_GET['oauth_token']);
        }

        // checks if access token is required
        if(!$dropbox->IsAuthorized())
        {
            // redirect user to dropbox auth page
            $return_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?auth_callback=1";
            $auth_url = $dropbox->BuildAuthorizeUrl($return_url);
            $request_token = $dropbox->GetRequestToken();

            $this->store_token($request_token, $request_token['t']);

            return $auth_url;
        }

        return 'conectado';
    }
}