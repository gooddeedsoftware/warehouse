
<?php

/**
 * [formatqueryString description]
 * @param  [type] $query_string [description]
 * @return [type]               [description]
 */
function formatqueryString($query_string)
{
    $exploded = explode("&", str_replace("?", "", $query_string));
    if ($exploded) {
        $finalquery = [];
        foreach ($exploded as $key => $query) {
            $ext = explode("=", $query);
            if (@trim(strtolower($ext[0])) != 'page') {
                $finalquery[] = $query;
            }
        }
        $query_string = implode("&", $finalquery);
    }
    return $query_string;
}

/**
 * [formateCreatedDate description]
 * @param  [type] $string [description]
 * @return [type]         [description]
 */
function formatSearchDate($string)
{
    if ($string) {
        $string = str_replace('.', '-', str_replace('/', '-', $string));
        $time   = false;
        if (strpos($string, ':') !== false) {
            $time = true;
        }
        $datelength = strlen($string);
        if ($datelength == 16) {
            $string = date('Y-m-d H:i', strtotime($string));
        }
        if ($datelength == 10) {
            $string = date('Y-m-d', strtotime($string));
        }
        if ($datelength == 7) {
            $string = date('Y-m', strtotime($string . '-' . date('d')));
        }
        if ($datelength == 5) {
            $string = $time ? date('H:i', strtotime(date('Y-m-d') . ' ' . $string)) : date('m-d', strtotime($string . '-' . date('Y')));
        }
        if ($datelength == 4) {
            $string = date('Y', strtotime($string . '-' . date('m') . '-' . date('d')));
        }
    }
    return $string;
}

/**
 * [replaceDotWithComma description]
 * @param  [type] $number [description]
 * @return [type]         [description]
 */
function replaceDotWithComma($number)
{
    if ($number) {
        return Number_format($number, "2", ",", "");
    }
}

function replaceCommaWithDot($number)
{
    if ($number) {
        return str_replace(',', ".", $number);
    }
}
/**
 * [formatDateFromDatabase description]
 * @param  [type] $date [description]
 * @return [type]         [description]
 */
function formatDateFromDatabase($date)
{
    if ($date) {
        return date('d.m.Y', strtotime($date));
    }
}

/**
 * [storeExceptionLog description]
 * @param  [type] $date [description]
 * @return [type]         [description]
 */
function storeExceptionLog($file_name = false, $log_message = false)
{
    try {
        $file_name = storage_path() . '/uploads/' . strtolower($file_name) . '.log';
        $fd        = fopen($file_name, "a");
        fwrite($fd, $log_message . "\n");
        // close file
        fclose($fd);
    } catch (Exception $e) {
        var_dump($e);
    }
}

/**
 * [restApi description]
 * @param  [type] $url         [description]
 * @param  [type] $method      [description]
 * @param  array  $header      [description]
 * @param  string $post_fields [description]
 * @return [type]              [description]
 */
function restApi($url, $method, $header = array(), $post_fields = "")
{
    try {
        $data = array();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $post_fields,
            CURLOPT_HTTPHEADER     => $header,
        ));
        $data['response'] = curl_exec($curl);
        $data['err']      = curl_error($curl);
        curl_close($curl);
        return $data;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * [findPercentage description]
 * @param  integer $value      [description]
 * @param  integer $percentage [description]
 * @return [type]              [description]
 */
function findPercentage($value = 0, $percentage = 0)
{
    $result = $value * $percentage / 100;
    return $result;
}

/**
 * [createImageAsBase64 description]
 * @param  [type] $path [description]
 * @return [type]       [description]
 */
function createImageAsBase64($path)
{
    if (!File::exists($path)) {
        abort(404);
    }

    $type   = pathinfo($path, PATHINFO_EXTENSION);
    $data   = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    return $base64;
}

/**
 * [r_collect description]
 * @param  [type] $array [description]
 * @return [type]        [description]
 */
function r_collect($array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $value       = r_collect($value);
            $array[$key] = $value;
        }
    }

    return collect($array);
}


/**
 * [register_error_log description]
 * @param  boolean $log_message [description]
 * @param  [type]  $file_name   [description]
 * @return [type]               [description]
 */
function register_error_log($log_message = false, $file_name)
{
    try {
        $file_name = base_path() . '/storage/logs/' . $file_name;
        $fd        = fopen($file_name, "a");
        fwrite($fd, $log_message . "\n");
        fclose($fd);
    } catch (\Exception $e) {
        var_dump($e);
    }
}