<?php

namespace App\Traits\Controllers;

use App\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Request;

trait HandleDocumentsUpload
{
    // 處理 documents.inputs blade 產的 inputs 上傳的檔案
    // 上傳的檔案會與 $modelInstance 與 Document model 建立關聯
    //
    protected function handleDocumentsUpload(Model $modelInstance, array $documentTypes)
    {
        foreach ($documentTypes as $documentType) {
            $existedFiles = Request::input("documents.{$documentType}.*");
            $existedFiles = is_array($existedFiles) ? $existedFiles : [];

            // 刪除標記為 delete 的檔案
            foreach ($existedFiles as $existedFile) {
                if (isset($existedFile['_delete']) && $existedFile['_delete'] == '1') {
                    Document::destroy($existedFile['id']);
                }
            }

            // 處理新增的檔案
            $newFiles = Request::file("documents.{$documentType}.*");
            $newFiles = is_array($newFiles) ? $newFiles : [];
            foreach ($newFiles as $newFile) {
                $document = new Document();

                $document->attachable_type = get_class($modelInstance);
                $document->attachable_id = $modelInstance->id;
                $document->document_type = $documentType;
                $document->filename = $newFile->getClientOriginalName();
                $document->path = $newFile->store('documents');

                $document->save();
            }
        }
    }

    protected function handleMultiDocumentsUpload(Model $modelInstance, array $documentTypes, int $formIndex)
    {
        foreach ($documentTypes as $documentType) {
            $existedFiles = request()->all("documents.{$documentType}");
            $existedFiles = is_array($existedFiles) ? $existedFiles : [];

            // 刪除標記為 delete 的檔案
            foreach ($existedFiles as $existedFile) {
                foreach ($existedFile as $file) {
                    if (isset($file[$formIndex]['_delete']) && $file[$formIndex]['_delete'] == '1') {
                        Document::destroy($existedFile[$formIndex]['id']);
                    }
                }
            }

            // 處理新增的檔案
            $newFiles = request()->file("documents.{$documentType}");

            if (isset($newFiles[$formIndex])) {
                foreach ($newFiles as $newFormFiles) {
                    /** @var UploadedFile $newFormFile */
                    foreach ($newFormFiles as $newFormFile) {
                        $document = new Document();

                        $document->attachable_type = get_class($modelInstance);
                        $document->attachable_id = $modelInstance->id;
                        $document->document_type = $documentType;
                        $document->filename = $newFormFile->getClientOriginalName();
                        $document->path = $newFormFile->store('documents');

                        $document->save();
                    }
                }
            }
        }
    }

    protected function handleDocumentsUploadByArray(Model $modelInstance, array $documents = [], $documentType = 'undefined')
    {
        $existedFiles = $documents;
        $existedFiles = is_array($existedFiles) ? $existedFiles : [];

        // 刪除標記為 delete 的檔案
        foreach ($existedFiles as $existedFile) {
            if (isset($existedFile['_delete']) && $existedFile['_delete'] == '1') {
                Document::destroy($existedFile['id']);
            }
        }

        // 處理新增的檔案
        $newFiles = $documents;
        $newFiles = is_array($newFiles) ? $newFiles : [];
        foreach ($newFiles as $newFile) {
            if (is_file($newFile['id'])) {
                $newFile = $newFile['id'];

                $document = new Document();
                $document->attachable_type = get_class($modelInstance);
                $document->attachable_id = $modelInstance->id;
                $document->document_type = $documentType;
                $document->filename = $newFile->getClientOriginalName();
                $document->path = $newFile->store('documents');
                $document->save();
            }
        }
    }
}
