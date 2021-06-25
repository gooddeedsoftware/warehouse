<?php
/**
 * [checkUNITokenExists description]
 * @return [type] [description]
 */
function checkUNITokenExists()
{
    return App\Models\UniRefreshToken::first();
}

/**
 * [curlPostRequest description]
 * @param  boolean $url         [description]
 * @param  boolean $post_fields [description]
 * @param  boolean $headers     [description]
 * @return [type]               [description]
 */
function curlPostRequest($url = false, $post_fields = false, $headers = false)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => "POST",
        CURLOPT_POSTFIELDS     => $post_fields,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    $response = curl_exec($curl);
    $err      = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
        // Log the error
        return null;
    }
    return json_decode($response);
}

/**
 * [curlGetRequest description]
 * @param  boolean $url    [description]
 * @param  boolean $header [description]
 * @return [type]          [description]
 */
function curlGetRequest($url = false, $header = false)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_PORT           => "443",
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => "GET",
        CURLOPT_HTTPHEADER     => $header,
    ]);
    $response = curl_exec($curl);
    $err      = curl_error($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
        return null;
    }
    return json_decode($response);
}


/**
 * [curlPutRequest description]
 * @param  boolean $url         [description]
 * @param  boolean $post_fields [description]
 * @param  boolean $headers     [description]
 * @return [type]               [description]
 */
function curlPutRequest($url = false, $post_fields = false, $headers = false)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => "PUT",
        CURLOPT_POSTFIELDS     => $post_fields,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    $response = curl_exec($curl);
    $err      = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
        // Log the error
        return null;
    }
    return json_decode($response);
}