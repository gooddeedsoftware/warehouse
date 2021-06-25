<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\GanticHelper;

class Files extends Model
{
    use SoftDeletes;
    protected $table = 'files';
    public $timestamps = true;
    protected $fillable = array('id', 'organization_id', 'obj_type', 'obj_id', 'file_object', 'file_size', 'comment', 'added_by', 'version');

    public static function upload_file($file, $files = false,$filepath = "/uploads/")
    {
        if ($file) {
            $destinationPath = storage_path() . $filepath;
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = NoorsiHelper::gen_uuid() . '.' . $extension;
            $return['fileName'] = $fileName;
            $return['filePath'] = '/uploads/equipment/' . $fileName;
            $return['fileSize'] = $file->getSize();
            $return['fileType'] = $file->getMimeType();
            $return['fileExtension'] = $extension;
            $return['fileOriginalName'] = $filename;
            $file->move($destinationPath, $destinationPath . $fileName, 0777);
            chmod($destinationPath . $fileName, 0777);
            $files = json_encode($return);
        }
        return $files;
    }

    // To fetch the file details
    public static function getFileDetails($id)
    {
        $imageRecord = Files::where('id', '=', $id)->first();
        $fileDetails = json_decode($imageRecord->file_object);
        $file['fileOriginalName'] = $fileDetails->fileOriginalName;
        $file['fileName'] = $fileDetails->fileName;
        $file['filePath'] = storage_path().$fileDetails->filePath;
        return $file;
    }

       /**
     * [uploadCompanyLogo description]
     * @return [type] [description]
     */
    public static function uploadCompanyLogo($logo = false) {
        try {
            if (@$logo && $logo->getSize() > 0) {
                $old_image_path = public_path() . "/images/maskinstyring_report_logo.png";  // Value is not URL but directory file path
                 if (file_exists($old_image_path)) {
                   @unlink($old_image_path);
                }
                $destinationPath = public_path() . "/images";
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $fileName = "maskinstyring_report_logo.png";
                $logo->move($destinationPath, $fileName, 0777);
                return 1;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            echo $e;
            exit;
        }

    }
}
