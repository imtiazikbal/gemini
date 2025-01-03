# Gemini Intregation for File

This project is a Laravel-based solution to upload documents and generate summaries using the Gemini API. Users can upload documents (PDF, TXT, HTML, CSS, CSV, XML, RTF) and get summaries based on the provided prompt. The system stores user interactions and responses in the database for future reference.

## Prerequisites

Before you begin, ensure that you have the following installed:

- [PHP >= 8.2](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Laravel 11.x or higher](https://laravel.com/)
- [MySQL](https://www.mysql.com/) or another supported database

## Installation

1. **Install Gemini API package**

    The project uses the Gemini API package by [imtiaz/gemini](https://github.com/imtiaz/gemini). Install it via Composer:

    ```bash
    composer require imtiaz/gemini
    ```

3. **Create GOOGLE_API_KEY in  `.env` file**


    ```bash
    GOOGLE_API_KEY=your_api
    API_BASE_URL=https://generativelanguage.googleapis.com

    ```

4. **Create `gemini.php` in  confiq **
```
<?php

return [

    'api_key' => env('GOOGLE_API_KEY', 'laravel'),

];
```
4. **Create model and migrations**

    Generate the application key:

    ```bash
    php artisan make:model Chat -mc
`
5. Migrations file 
```
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->longText('prompt');
            $table->longText('response');
            $table->string('file_url');
```
6. Update model
```
 protected $fillable = [
        'user_id',
        'prompt',
        'response',
        'file_url'
    ];
```
5. **Run the migrations**

    Create the necessary database tables by running:

    ```bash
    php artisan migrate
    ```

7. Route example:

 ```
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/view',[App\Http\Controllers\GeminiController::class, 'view']);
Route::get('/getUserDocumentsResponses',[App\Http\Controllers\GeminiController::class, 'documentsResponses']);

Route::post('/documents/summarize',action: [App\Http\Controllers\GeminiController::class, 'summarizeSingleDocument'])->name('documents');


```
7. **View file**

```
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload with Prompt</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        form div {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="file"],
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="file"]:focus,
        textarea:focus {
            border-color: #007BFF;
        }

        textarea {
            resize: none;
            height: 100px;
        }

        button {
            background: #007BFF;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        button:active {
            transform: scale(0.98);
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            form {
                padding: 15px;
            }

            button {
                width: 100%;
            }
        }
        .container {
            display: flex;
            gap: 20px;
            
        }
    </style>
</head>

<body class="container">


    <form action="{{ route('documents') }}" method="POST" enctype="multipart/form-data">
        @csrf <!-- Laravel's CSRF protection token -->
         <h1>Multiple PDF with Prompt and Trained Model</h1>
 
 
         <!-- File Input -->
         <div>
            <label for="files">Upload a file (Allowed: pdf, txt, text/plain, html, css, csv, xml, rtf, jpeg, png, jpg, webp, heic, heif - Max size: 10MB)</label>
            <input type="file" name="files[]" id="files"  multiple>
         </div>
         
 
         <div>
 
             <label for="files">Specify Gemini Trained Models</label>
             <a href="https://ai.google.dev/gemini-api/docs/models/gemini-models">Gemini Models</a>
             <br>
             <select name="model" id="models" class="form-control">
                 <option value="gemini-1.5-flash">Gemini 1.5 Flash</option>
                 <option value="gemini-2.0-flash-exp">Gemini 2.0 Flash</option>
                 <option value="gemini-exp-1206">Gemini gemini-exp-1206</option>
                 <option value="learnlm-1.5-pro-experimental">LearnLM 1.5 Pro Experimental</option>
                 <option value="gemini-exp-1121">Gemini gemini-exp-1121</option>
                 <option value="gemini-exp-1121">Gemini gemini-exp-1121</option>
                 <option value="gemini-1.5-pro-exp-0827">Gemini 1.5 Pro gemini-1.5-pro-exp-0827</option>
                 <option value="gemini-1.5-pro-exp-0801">Gemini 1.5 Pro</option>
                 <option value="gemini-1.5-flash-8b-exp-0924">Gemini 1.5 Flash-8B</option>
                 <option value="gemini-1.5-flash-8b-exp-0827">Gemini 1.5 Flash-8B</option>
             </select>
         </div>
 
 
         <!-- Prompt Input -->
         <div>
             <label for="prompt">Prompt</label>
 
             <textarea name="prompt" id="prompt" required placeholder="Enter your prompt here"></textarea>
         </div>
 
         <!-- Submit Button -->
         <div>
             <button type="submit">Submit</button>
         </div>
     </form>
</body>
</html>



```
8. # Controller
```
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
            'files.*' => 'required|mimes:pdf,txt,text/plain,html,css,csv,xml,rtf,jpeg,png,jpg,webp,heic,heif|mimetypes:text/rtf,application/rtf|max:10240', 
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
            'application/pdf', 'text/plain', 'text/html', 'text/css', 'text/csv', 'application/xml', 'application/rtf','text/rtf','application/rtf'
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









```

   
---

## API Endpoints

### 1. **GET /view**
   - **Description**: Returns the view for interacting with the Gemini service.
   - **Method**: GET
   - **Response**: A view (HTML form) for document upload.

### 2. **POST /summarizeDocument**
   - **Description**: Upload a document and receive a summary based on the provided prompt.
   - **Method**: POST
   - **Parameters**:
     - `file` (required): The document to summarize (PDF, TXT, HTML, CSS, CSV, XML, RTF).
     - `prompt` (required): A string that describes the summarization task.
   - **Response**:
     - `status`: The status of the request (`success` or `error`).
     - `data`: The summary returned from the Gemini API.
     - `status_code`: HTTP status code.
   - **Example Request**:

     ```bash
     curl -X POST -F "file=@path/to/document.pdf" -F "prompt=Summarize this document" http://localhost:8000/summarizeDocument
     ```

### 3. **GET /getUserDocumentsResponses**
   - **Description**: Retrieves the user's previous documents and responses stored in the database.
   - **Method**: GET
   - **Response**:
     - `status`: The status of the request (`success` or `error`).
     - `chats`: A list of documents and responses for the authenticated user.
     - `status_code`: HTTP status code.
   - **Example Request**:

     ```bash
     curl http://localhost:8000/getUserDocumentsResponses
     ```

---

## Code Explanation

### **GeminiController**

The controller is responsible for handling the document upload, summarization, and storing of responses.

- **view()**: Returns the view for uploading documents.
- **summarizeDocument(Request $request)**: Validates the file and prompt, sends them to the Gemini API for summarization, and stores the response in the database.
- **documentsResponses()**: Retrieves all previous document interactions for a user.
- **storeResponse($data, $prompt, $file_url, $user_id)**: Stores the summary and interaction data in the `chats` table.


