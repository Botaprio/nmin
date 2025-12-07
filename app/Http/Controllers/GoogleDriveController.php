<?php

namespace App\Http\Controllers;

use App\Models\GoogleDriveFile;
use App\Models\Card;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Drive;

class GoogleDriveController extends Controller
{
    private function getDriveService()
    {
        $user = auth()->user();

        if (!$user->google_token) {
            abort(403, 'No has conectado tu cuenta de Google Drive');
        }

        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken($user->google_token);

        // Refresh token if expired
        if ($client->isAccessTokenExpired() && $user->google_refresh_token) {
            $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            $user->update(['google_token' => $client->getAccessToken()]);
        }

        return new Drive($client);
    }

    private function getOrCreateFolder($service, $folderName, $parentId = null)
    {
        // Search for existing folder
        $query = "name='{$folderName}' and mimeType='application/vnd.google-apps.folder' and trashed=false";
        if ($parentId) {
            $query .= " and '{$parentId}' in parents";
        }

        $results = $service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)',
        ]);

        if (count($results->getFiles()) > 0) {
            return $results->getFiles()[0]->getId();
        }

        // Create folder
        $fileMetadata = new Drive\DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
        ]);

        if ($parentId) {
            $fileMetadata->setParents([$parentId]);
        }

        $folder = $service->files->create($fileMetadata, [
            'fields' => 'id',
        ]);

        return $folder->getId();
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'card_id' => 'required|exists:cards,id',
            'file' => 'required|file|max:102400', // 100MB max
            'file_type' => 'required|in:asset,music,final_video,other',
        ]);

        $card = Card::with('board')->findOrFail($validated['card_id']);
        $service = $this->getDriveService();

        try {
            // Create folder structure: Board Name / Card ID / File Type
            $boardFolderId = $this->getOrCreateFolder($service, $card->board->name);
            $cardFolderId = $this->getOrCreateFolder($service, "Card-{$card->id}", $boardFolderId);
            $typeFolderId = $this->getOrCreateFolder($service, ucfirst($validated['file_type']), $cardFolderId);

            // Upload file
            $file = $request->file('file');
            $fileMetadata = new Drive\DriveFile([
                'name' => $file->getClientOriginalName(),
                'parents' => [$typeFolderId],
            ]);

            $content = file_get_contents($file->getRealPath());
            $uploadedFile = $service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $file->getMimeType(),
                'uploadType' => 'multipart',
                'fields' => 'id, name, webViewLink, mimeType, size',
            ]);

            // Save to database
            $driveFile = GoogleDriveFile::create([
                'card_id' => $card->id,
                'file_name' => $uploadedFile->getName(),
                'file_type' => $validated['file_type'],
                'drive_file_id' => $uploadedFile->getId(),
                'drive_url' => $uploadedFile->getWebViewLink(),
                'mime_type' => $uploadedFile->getMimeType(),
                'file_size' => $uploadedFile->getSize(),
                'uploaded_by' => auth()->id(),
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'board_id' => $card->board_id,
                'card_id' => $card->id,
                'action' => 'file_uploaded',
                'description' => auth()->user()->name . " subió el archivo: {$driveFile->file_name}",
            ]);

            return back()->with('success', 'Archivo subido correctamente a Google Drive');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al subir archivo: ' . $e->getMessage());
        }
    }

    public function show(GoogleDriveFile $googleDriveFile)
    {
        // Return file details with drive URL for viewing
        return response()->json([
            'file' => $googleDriveFile,
            'drive_url' => $googleDriveFile->drive_url,
        ]);
    }

    public function destroy(GoogleDriveFile $googleDriveFile)
    {
        $service = $this->getDriveService();

        try {
            // Delete from Google Drive
            $service->files->delete($googleDriveFile->drive_file_id);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'board_id' => $googleDriveFile->card->board_id,
                'card_id' => $googleDriveFile->card_id,
                'action' => 'file_deleted',
                'description' => auth()->user()->name . " eliminó el archivo: {$googleDriveFile->file_name}",
            ]);

            // Delete from database
            $googleDriveFile->delete();

            return back()->with('success', 'Archivo eliminado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar archivo: ' . $e->getMessage());
        }
    }
}
