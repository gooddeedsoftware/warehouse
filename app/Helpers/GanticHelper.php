<?php
namespace App\Helpers;

use GuzzleHttp\Client;
use Image;
use Mail;
use Session;

class GanticHelper
{
    public static function gen_uuid()
    {
        return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x', // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), // 16 bits for "time_mid"
            mt_rand(0, 0xffff), // 16 bits for "time_hi_and_version", // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000, // 16 bits, 8 bits for "clk_seq_hi_res", // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000, // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }

    public static function send_mail($type, $to, $data, $subject)
    {
        Mail::send('emails.' . $type, $data, function ($message) use (&$to, &$subject) {
            $message->to($to)->subject($subject)->bcc(array('venkatesangee@gmail.com'));
        });
        return Mail::failures();
    }
    public static function sendmail()
    {
        Mail::send([], [], function ($message) {
            $message->to('saravananshc@gmail.com')->subject('test')->bcc(array('venkatesangee@gmail.com'))->setBody('Hi, welcome user!');
        });
        return Mail::failures();
    }

    public static function base64_to_jpeg($base64_string)
    {
        $output_file = static::createTempFile('png');
        $data        = explode(',', $base64_string);
        if (isset($data[1])) {
            $ifp = fopen($output_file, "wb");
            fwrite($ifp, base64_decode($data[1]));
            fclose($ifp);
        }
        return $output_file;
    }

    public static function createTempFile($ext)
    {
        $tmp      = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
        $fileName = static::gen_uuid() . ".$ext";
        return $tmp . "/$fileName";
    }

    public static function resizeImage($file, $width = 200, $height = 200, $brightness = 0, $extension = false)
    {
        if ($extension) {
            $target = static::createTempFile($extension);
        } else {
            $target = static::createTempFile(pathinfo($file, PATHINFO_EXTENSION));
        }

        try {
            if (file_exists($file) && filesize($file) > 0) {
                $img = Image::make($file)->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->brightness($brightness);
                $img->save($target);
            }
        } catch (Exception $e) {

        }
        return $target;
    }

    public static function upload_file($input, $path = "uploads")
    {
        $files = array();
        if ($input) {
            foreach ($input as $file) {
                if ($file) {
                    $fileSize        = $file->getSize();
                    $destinationPath = storage_path() . "/$path/";
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }
                    $filename                        = $file->getClientOriginalName();
                    $extension                       = $file->getClientOriginalExtension();
                    $fileName                        = NoorsiHelper::gen_uuid() . '.' . $extension;
                    $file_object['fileName']         = $fileName;
                    $file_object['filePath']         = "/$path/" . $fileName;
                    $file_object['fileSize']         = $file->getSize();
                    $file_object['fileType']         = $file->getMimeType();
                    $file_object['fileExtension']    = $extension;
                    $file_object['fileOriginalName'] = $filename;
                    $file->move($destinationPath, $destinationPath . $fileName, 0777);
                    chmod($destinationPath . $fileName, 0777);
                    $files[] = $file_object;
                }
            }
        }
        return $files;
    }

    public static function format_hours($hour)
    {
        if ($hour) {
            $hour = rtrim($hour, "00");
            $hour = ltrim($hour, 0);

            if (substr($hour, -1) == ",") {
                $hour = substr_replace($hour, "", -1);
            }
        }
        return $hour;
    }

    // get norway holidays
    public static function getHolidays($year)
    {
        $holiday_dates = array();
        try {
            // get the list of holidays
            $client  = new Client(['verify' => false]);
            $request = $client->request('GET', 'https://webapi.no/api/v1/holydays/' . $year);

            $code     = $request->getStatusCode();
            $result   = $request->getBody()->getContents();
            $holidays = json_decode($result);

            if (@$holidays->data) {
                foreach ($holidays->data as $key => $value) {
                    $date                                 = MaskinstyringHelper::formatDate($value->date, 'Y-m-d');
                    $vacation_description                 = $value->description;
                    $holiday_dates[$vacation_description] = $date;
                }
            }
        } catch (Exception $e) {
        }

        return $holiday_dates;
    }

    // limit the text to display
    public static function characterLimiter($str, $limit = 100)
    {
        $result = "";
        if ($str && strlen($str) > $limit) {
            for ($i = 0; $i < $limit; $i++) {
                $result .= $str[$i];
            }
            return $result;
        } else {
            return $str;
        }

    }

    // format date to norway format
    public static function formatDate($date, $format = 'd.m.Y')
    {
        try {
            if ($date) {
                $date = new \DateTime($date);
                return $date->format($format);
            }
        } catch (Exception $e) {
            return $date;
        }
    }

    // time format (If user enters 5,5 then we are convert like 5:00)
    public static function convertCommaToTime($time)
    {
        try {
            if ($time && strpos($time, ',') !== false) {
                $time = str_replace(",", ":", $time);
            } else {
                $time = $time . ":00";
            }
        } catch (Exception $e) {
        }
        return $time;
    }

    // get start and end date from week number
    public static function getStartAndEndDateFromWeekNumber($week, $year = false)
    {
        $date = new \DateTime();
        if ($year) {
            $year = date($year);
        } else {
            $year = date('Y');
        }
        $date->setISODate($year, $week);
        $week_date['week_start'] = $date->format('Y-m-d');
        $date->modify('+6 days');
        $week_date['week_end'] = $date->format('Y-m-d');
        return $week_date;
    }

    // get start and end date from week number
    public static function getDatesFromWeekNumber($week, $year = false)
    {
        $date = new \DateTime();
        // $year = date('Y');
        if ($year) {
            $year = date($year);
        } else {
            $year = date('Y');
        }
        for ($i = 0; $i < 7; $i++) {
            $date->setISODate($year, $week);
            $date->modify('+' . $i . ' days');
            $week_date[$date->format('Y-m-d')] = $date->format('Y-m-d');
        }
        return $week_date;
    }

    /**
     * [offerLog description]
     * @param boolean $log_message [description]
     */
    public static function errorLog($log_message = false)
    {
        try {
            $file_name = storage_path() . '/uploads/errorLog.log';
            $fd        = fopen($file_name, "a");
            fwrite($fd, $log_message . "\n");
            // close file
            fclose($fd);
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * [createSearchArray description]
     * @param  [type] $input              [description]
     * @param  [type] $module_search_name [description]
     * @return [type]                     [description]
     */
    public function createSearchArray($input, $module_search_name)
    {
        $data = @Session::get($module_search_name) ? @Session::get($module_search_name) : [];
        @$input ? Session::put($module_search_name, array_merge($data, $input)) : '';
        return session::get($module_search_name);
    }
}
