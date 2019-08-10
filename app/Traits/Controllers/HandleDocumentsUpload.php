<?php
namespace App\Traits\Controllers;

use App\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

trait HandleDocumentsUpload {
    // 處理 documents.inputs blade 產的 inputs 上傳的檔案
    // 上傳的檔案會與 $modelInstance 與 Document model 建立關聯
    //
    protected function handleDocumentsUpload(Model $modelInstance, array $documentTypes) {
        foreach($documentTypes as $documentType) {
            $existedFiles = Request::input("documents.{$documentType}.*");
            $existedFiles = is_array($existedFiles) ? $existedFiles : [];

            // 刪除標記為 delete 的檔案
            foreach ($existedFiles as $existedFile) {
                if ($existedFile['_delete'] == '1') {
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
}
