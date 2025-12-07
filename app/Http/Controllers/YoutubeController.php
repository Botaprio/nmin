<?php

namespace App\Http\Controllers;

use App\Models\YoutubeVideo;
use App\Models\Card;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Google\Client;
use Google\Service\YouTube;

class YoutubeController extends Controller
{
    private function getYoutubeService()
    {
        $user = auth()->user();

        if (!$user->google_token) {
            abort(403, 'No has conectado tu cuenta de YouTube');
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

        return new YouTube($client);
    }

    public function analytics(Request $request)
    {
        $youtube = $this->getYoutubeService();

        try {
            // Get user's channel
            $channelsResponse = $youtube->channels->listChannels('snippet,statistics', [
                'mine' => true,
            ]);

            if (empty($channelsResponse->getItems())) {
                return back()->with('error', 'No se encontró un canal de YouTube asociado');
            }

            $channel = $channelsResponse->getItems()[0];
            $channelId = $channel->getId();

            // Get recent videos
            $searchResponse = $youtube->search->listSearch('snippet', [
                'channelId' => $channelId,
                'maxResults' => 50,
                'order' => 'date',
                'type' => 'video',
            ]);

            $videos = [];
            foreach ($searchResponse->getItems() as $searchResult) {
                $videoId = $searchResult->getId()->getVideoId();

                // Get video statistics
                $videoResponse = $youtube->videos->listVideos('snippet,statistics', [
                    'id' => $videoId,
                ]);

                if (!empty($videoResponse->getItems())) {
                    $video = $videoResponse->getItems()[0];
                    $stats = $video->getStatistics();

                    $videos[] = [
                        'video_id' => $videoId,
                        'title' => $video->getSnippet()->getTitle(),
                        'published_at' => $video->getSnippet()->getPublishedAt(),
                        'views' => $stats->getViewCount() ?? 0,
                        'likes' => $stats->getLikeCount() ?? 0,
                        'comments' => $stats->getCommentCount() ?? 0,
                    ];
                }
            }

            return Inertia::render('Youtube/Analytics', [
                'channel' => [
                    'id' => $channelId,
                    'title' => $channel->getSnippet()->getTitle(),
                    'subscriber_count' => $channel->getStatistics()->getSubscriberCount(),
                    'video_count' => $channel->getStatistics()->getVideoCount(),
                    'view_count' => $channel->getStatistics()->getViewCount(),
                ],
                'videos' => $videos,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al obtener analytics: ' . $e->getMessage());
        }
    }

    public function linkVideo(Request $request)
    {
        $validated = $request->validate([
            'card_id' => 'required|exists:cards,id',
            'youtube_video_id' => 'required|string',
        ]);

        $card = Card::findOrFail($validated['card_id']);
        $youtube = $this->getYoutubeService();

        try {
            // Get video details from YouTube
            $videoResponse = $youtube->videos->listVideos('snippet,statistics', [
                'id' => $validated['youtube_video_id'],
            ]);

            if (empty($videoResponse->getItems())) {
                return back()->with('error', 'Video no encontrado en YouTube');
            }

            $video = $videoResponse->getItems()[0];
            $stats = $video->getStatistics();

            // Create or update YouTube video record
            $youtubeVideo = YoutubeVideo::updateOrCreate(
                [
                    'card_id' => $card->id,
                ],
                [
                    'youtube_video_id' => $validated['youtube_video_id'],
                    'title' => $video->getSnippet()->getTitle(),
                    'published_at' => $video->getSnippet()->getPublishedAt(),
                    'view_count' => $stats->getViewCount() ?? 0,
                    'like_count' => $stats->getLikeCount() ?? 0,
                    'comment_count' => $stats->getCommentCount() ?? 0,
                    'watch_time_minutes' => 0, // Not available in API v3
                ]
            );

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'board_id' => $card->board_id,
                'card_id' => $card->id,
                'action' => 'youtube_linked',
                'description' => auth()->user()->name . " vinculó el video de YouTube: {$youtubeVideo->title}",
            ]);

            return back()->with('success', 'Video vinculado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al vincular video: ' . $e->getMessage());
        }
    }

    public function refreshAnalytics(YoutubeVideo $youtubeVideo)
    {
        $youtube = $this->getYoutubeService();

        try {
            $videoResponse = $youtube->videos->listVideos('statistics', [
                'id' => $youtubeVideo->youtube_video_id,
            ]);

            if (empty($videoResponse->getItems())) {
                return back()->with('error', 'Video no encontrado');
            }

            $video = $videoResponse->getItems()[0];
            $stats = $video->getStatistics();

            $youtubeVideo->update([
                'view_count' => $stats->getViewCount() ?? 0,
                'like_count' => $stats->getLikeCount() ?? 0,
                'comment_count' => $stats->getCommentCount() ?? 0,
            ]);

            return back()->with('success', 'Estadísticas actualizadas');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
}
