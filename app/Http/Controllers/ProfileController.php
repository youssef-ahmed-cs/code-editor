<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AzureBlobStorage;
use App\Http\Resources\UserResource;
use App\Http\Requests\ImageUploadRequest;
use App\Http\Requests\UpdateProfileRequest;
class ProfileController extends Controller
{
    // public function getProfile()
    // {
    //     $user = auth()->user();
    //     return response()->json([
    //         'user' => new UserResource($user),
    //     ]);
    // }

    // public function updateProfile(UpdateProfileRequest $request){
    //     $user = auth()->user();

    //     $user->update($request->all());
    // }

    public function uploadAvatarImage(ImageUploadRequest $request, AzureBlobStorage $azureService)
    {
        $file = request()->file('file');

        $authuser = auth()->user();

        if (!$file) {
            return response()->json(['error' => 'No file provided'], 400);
        }

        $filePath = $azureService->uploadImage($file, 'avatar');
        $authuser->update(['avatar_url' => $filePath]);

        if (!$filePath) {
            return response()->json(['error' => 'Failed to upload file to Azure Blobs'], 500);
        }

        return response()->json([
            'message' => 'Avatar image uploaded successfully!',
            'avatar_url' => $filePath,
        ]);
    }
}
