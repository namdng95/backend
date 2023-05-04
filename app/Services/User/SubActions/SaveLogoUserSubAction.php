<?php

namespace App\Services\User\SubActions;

use App\Services\Action;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SaveLogoUserSubAction extends Action
{
    /**
     * Execute sub action.
     *
     * @param array $data Data
     *
     * @return array
     */
    public function handle(array $data = []): array
    {
        $file = $data['file'];
        $fileName =  substr(uniqid(),rand(1,5),8) . "_" . $file->getClientOriginalName();
        $filePath = "files/logo/{$data['code']}";
        $fileFullPath = "{$filePath}/{$fileName}";

        Storage::put($fileFullPath, file_get_contents($file), 'private');

        // Save file logo local
        Storage::disk('local')->putFileAs($filePath, $file, $fileName);

        $zipName = "logo_user_{$data['code']}.zip";

        // Save zip in local
        $zipFilePath = Storage::disk('local')->path($zipName);
        // or $zipFilePath = storage_path("app/{$zipName}");
        $exists = Storage::disk('local')->exists($zipName);

        // Save zip to AWS
//        $zipFilePath = Storage::disk('zip')->path($zipName);
//        $exists = Storage::disk('zip')->exists($zipName);

        $zip = new \ZipArchive();

        if (!$exists) {
            if ($zip->open($zipFilePath, \ZipArchive::CREATE) === true) {
                if (Storage::exists($fileFullPath)) {
                    $this->checkFileNameAndZipFile($zip, $file);
                }
            }
        }

        if ($exists) {
            if ($zip->open($zipFilePath) === true) {
                if (Storage::exists($fileFullPath)) {
                    $this->checkFileNameAndZipFile($zip, $file);
                }
            }
        }

        return [
            'email'     => $data['email'],
            'code'      => $data['code'],
            'name'      => $data['name'],
            'file_path' => $fileFullPath,
            'zip_name'  => $zipName
        ];
    }

    /**
     * Check File Name
     *
     * @param \ZipArchive  $zip  Zip file
     * @param UploadedFile $file File
     *
     * @return void
     */
    private function checkFileNameAndZipFile(\ZipArchive $zip, UploadedFile $file): void
    {
        $fileName = $file->getClientOriginalName();
        $extension = $file->extension();
        $onlyName = substr($fileName, 0, -strlen($extension) + (-1)); // Get extension
        $i = 0;

        while ($zip->locateName($fileName) !== false) {
            $fileName = $onlyName . "_(" . ++$i . ").{$extension}";
        }

        $zip->addFromString($fileName, file_get_contents($file));
        $zip->close();
    }
}
