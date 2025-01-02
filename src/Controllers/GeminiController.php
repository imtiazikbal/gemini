<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Imtiaz\LaravelGemini\Gemini\GeminiApi;
use Imtiaz\LaravelGemini\Gemini\MultiPdfUpload;
use Imtiaz\LaravelGemini\Gemini\MultipleImage;


use App\Models\Chat;
use Illuminate\Support\Facades\Storage;

class GeminiController extends Controller
{

    public function view(){
        return view("gemini-file");
    }
    public function summarizeSingleDocument(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'files' => 'required|array', // Expect an array of files
            'files.*' => 'required|mimes:pdf,txt,text/plain,html,css,csv,xml,rtf,jpeg,png,jpg,webp,heic,heif|max:10240', // File validation rules
            'prompt' => 'required|string',
            'model' => 'required|string'
        ]);
    
        if ($validator->fails()) {
            Log::error('Validation failed: ' . json_encode($validator->errors()));
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        // Check if files are uploaded
        if (!$request->hasFile('files')) {
            Log::error('No file was uploaded.');
            return response()->json(['error' => 'No file was uploaded'], 400);
        }
    
        // Get the files
        $files = $request->file('files');
    
        // Check if the uploaded files are valid
        foreach ($files as $file) {
            if (!$file->isValid()) {
                Log::error('Uploaded file is not valid: ' . $file->getErrorMessage());
                return response()->json(['error' => 'Uploaded file is not valid'], 400);
            }
    
            // Log the file details (for debugging purposes)
            Log::info('Uploaded file name: ' . $file->getClientOriginalName());
            Log::info('Uploaded file size: ' . $file->getSize());
            Log::info('Uploaded file MIME type: ' . $file->getMimeType());
        }
    
        $prompt = $request->input('prompt', 'Summarize this document');
        $model = $request->input('model');
    
        // Define the allowed MIME types for documents
        $allowedMimeTypes = [
            'application/pdf', 'text/plain', 'text/html', 'text/css', 'text/csv', 'application/xml', 'application/rtf'
        ];
    
        // Define the allowed MIME types for images
        $allowedImageMimeTypes = [
            'image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/heif'
        ];
    
        // Check if the files array contains multiple files
        if (count($files) > 1) {
            // Check if the files are all documents (PDF, TXT, etc.)
            $allDocuments = true;
            foreach ($files as $file) {
                if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                    $allDocuments = false;
                    break;
                }
            }
    
            // Handle multiple PDFs if all files are documents
            if ($allDocuments) {
                try {
                    $summary = MultiPdfUpload::handleUpload($files, $prompt, $model);
                    $this->storeResponse($summary->getOriginalContent(), $prompt,'file_url',1);

                    return response()->json([
                        'status' => 'success',
                        'data' => $summary->getOriginalContent(),
                        'status_code' => 200
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to generate summary for multiple PDFs. ' . $e->getMessage()], 400);
                }
            }
    
            // Handle multiple images if all files are images
            $allImages = true;
            foreach ($files as $file) {
                if (!in_array($file->getMimeType(), $allowedImageMimeTypes)) {
                    $allImages = false;
                    break;
                }
            }
    
            if ($allImages) {
                try {
                    $summary = MultipleImage::handleImageUpload($files, $prompt, $model);
                    $this->storeResponse($summary->getOriginalContent(), $prompt,'file_url',1);

                    return response()->json([
                        'status' => 'success',
                        'data' => $summary->getOriginalContent(),
                        'status_code' => 200
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to generate summary for multiple images. ' . $e->getMessage()], 400);
                }
            }
    
            // If not all files are images or documents, return an error
            return response()->json(['error' => 'Mixed file types are not allowed. Please upload either all images or all documents.'], 400);
        }
    
        // Handle single file upload if there is only one file
        // Check if the file is a valid document or image
        $file = $files[0];
        $mimeType = $file->getMimeType();
    
        if (in_array($mimeType, $allowedMimeTypes)) {
            // Handle document summarization (PDF, TXT, etc.)
            try {
                $summary = GeminiApi::summarizeDocument($file, $prompt, $model);
                $this->storeResponse($summary, $prompt, 'file_url', 1);

                return response()->json([
                    'status' => 'success',
                    'data' => $summary,
                    'status_code' => 200
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to summarize the document. ' . $e->getMessage()], 400);
            }
        } elseif (in_array($mimeType, $allowedImageMimeTypes)) {
            // Handle image summarization (JPEG, PNG, etc.)
            try {
                $summary = MultipleImage::handleImageUpload([$file], $prompt, $model);
                $this->storeResponse($summary->getOriginalContent(), $prompt, 'file_url', 1);

                return response()->json([
                    'status' => 'success',
                    'data' => $summary->getOriginalContent(),
                    'status_code' => 200
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to summarize the image. ' . $e->getMessage()], 400);
            }
        } else {
            return response()->json(['error' => 'Invalid file type. Only documents or images are allowed.'], 400);
        }
    }
    private function storeResponse($data,$prompt,$file_url,$user_id){
        try{

            $chat = Chat::create([
                'prompt' => $prompt,
                'response' => $data,
                'user_id'=> $user_id,
                'file_url'=> $file_url
            ]);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 400);
        }

    }
}




