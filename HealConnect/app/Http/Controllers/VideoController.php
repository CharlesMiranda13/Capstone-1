<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\VideoCallStarted;
use App\Events\MessageSent;
use App\Models\User;
use App\Models\Message;

class VideoController extends Controller
{
    /**
     * Create a Daily.co room and start a video call
     */
    public function createRoom(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'receiver_id' => 'required|exists:users,id'
            ]);

            $receiverId = $request->receiver_id;
            $caller = auth()->user();
            $receiver = User::find($receiverId);

            // Log the attempt
            Log::info('Video call initiated', [
                'caller_id' => $caller->id,
                'caller_name' => $caller->name,
                'receiver_id' => $receiverId,
                'receiver_name' => $receiver->name
            ]);

            // Generate unique room name
            $roomName = 'healconnect-' . $caller->id . '-' . $receiverId . '-' . time();

            // Check if API key is configured
            $apiKey = config('services.daily.api_key') ?? env('DAILY_API_KEY');
            
            if (!$apiKey) {
                Log::error('Daily.co API key not configured');
                return response()->json([
                    'success' => false,
                    'message' => 'Video service not configured. Please check your .env file for DAILY_API_KEY'
                ], 500);
            }

            Log::info('Creating Daily.co room', [
                'room_name' => $roomName,
                'api_key_present' => !empty($apiKey)
            ]);

            // Create Daily.co room via API with proper settings
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.daily.co/v1/rooms', [
                'name' => $roomName,
                'privacy' => 'public',
                'properties' => [
                    'enable_screenshare' => true,
                    'enable_chat' => false,
                    'enable_prejoin_ui' => false,        
                    'enable_knocking' => false,          
                    'start_video_off' => false,
                    'start_audio_off' => false,
                    'enable_people_ui' => true,
                    'enable_pip_ui' => false,
                    'enable_emoji_reactions' => false,
                    'enable_hand_raising' => false,
                    'enable_network_ui' => false,
                    'enable_noise_cancellation_ui' => true,
                    'max_participants' => 2,
                    'owner_only_broadcast' => false,     
                    'enable_advanced_chat' => false,
                    'enable_recording' => false,
                    'exp' => time() + 7200,             
                    'nbf' => time() - 60,                
                ]
            ]);

            // Log the response
            Log::info('Daily.co API response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $roomData = $response->json();
                
                // Verify URL exists
                if (!isset($roomData['url'])) {
                    Log::error('Room created but no URL returned', ['response' => $roomData]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Room created but URL not provided',
                        'debug' => $roomData
                    ], 500);
                }
                
                Log::info('Room created successfully', [
                    'room_name' => $roomName,
                    'room_url' => $roomData['url']
                ]);

                // Create meeting tokens for both caller and receiver
                $callerToken = $this->createMeetingToken($apiKey, $roomName, $caller->name, true);
                $receiverToken = $this->createMeetingToken($apiKey, $roomName, $receiver->name, false);

                // Broadcast to receiver that call is starting
                try {
                    broadcast(new VideoCallStarted([
                        'caller' => [
                            'id' => $caller->id,
                            'name' => $caller->name,
                            'role' => $caller->role,
                        ],
                        'receiver_id' => $receiverId,
                        'room' => $roomName,
                        'room_url' => $roomData['url'],
                        'token' => $receiverToken
                    ]))->toOthers();

                    Log::info('Broadcast sent successfully');
                } catch (\Exception $e) {
                    Log::error('Broadcast failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Continue even if broadcast fails
                }

                return response()->json([
                    'success' => true,
                    'room_name' => $roomName,
                    'room_url' => $roomData['url'],
                    'token' => $callerToken,
                    'redirect' => route('video.room', ['room' => $roomName, 'token' => $callerToken])
                ]);
            }

            // If Daily.co API failed
            Log::error('Daily.co room creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create video room. Status: ' . $response->status(),
                'error' => $response->json()
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Video call error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating video room: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Handle call ended - save message with call duration
     */
    public function callEnded(Request $request)
    {
        try {
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'duration' => 'required|string'
            ]);

            Log::info('Call ended', [
                'caller_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'duration' => $request->duration
            ]);

            // Create message with call_ended type
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => "Video chat ended - {$request->duration}",
                'type' => 'call_ended',
                'message_type' => 'call_ended', // Add this if your table has both columns
            ]);

            // Broadcast the message to both users
            broadcast(new MessageSent($message))->toOthers();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Call ended error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save call end message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a meeting token for a user
     */
    private function createMeetingToken($apiKey, $roomName, $userName, $isOwner = false)
    {
        try {
            $tokenResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.daily.co/v1/meeting-tokens', [
                'properties' => [
                    'room_name' => $roomName,
                    'user_name' => $userName,
                    'is_owner' => $isOwner,
                    'enable_screenshare' => true,
                    'start_video_off' => false,
                    'start_audio_off' => false,
                    'exp' => time() + 7200, // 2 hours
                ]
            ]);

            if ($tokenResponse->successful()) {
                $tokenData = $tokenResponse->json();
                $token = $tokenData['token'] ?? null;
                
                if ($token) {
                    Log::info('Meeting token created', [
                        'user_name' => $userName,
                        'is_owner' => $isOwner,
                        'token_preview' => substr($token, 0, 20) . '...'
                    ]);
                    return $token;
                }
            }

            Log::warning('Failed to create meeting token', [
                'user_name' => $userName,
                'status' => $tokenResponse->status()
            ]);
            
            return null;

        } catch (\Exception $e) {
            Log::error('Token creation error', [
                'user_name' => $userName,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Show the video call room page
     */
    public function showRoom(Request $request, $room)
    {
        $user = auth()->user();
        
        Log::info('User joining room', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'room' => $room
        ]);

        // Get token from query parameter
        $token = $request->query('token');
        
        if (!$token) {
            $apiKey = config('services.daily.api_key') ?? env('DAILY_API_KEY');
            
            if ($apiKey) {
                $token = $this->createMeetingToken($apiKey, $room, $user->name, false);
            }
        }

        return view('video.room', [
            'room' => $room,
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Delete a Daily.co room
     */
    public function deleteRoom($roomName)
    {
        try {
            $apiKey = config('services.daily.api_key') ?? env('DAILY_API_KEY');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key not configured'
                ], 500);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->delete("https://api.daily.co/v1/rooms/{$roomName}");

            Log::info('Room deletion attempt', [
                'room_name' => $roomName,
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

            return response()->json([
                'success' => $response->successful(),
                'message' => $response->successful() ? 'Room deleted' : 'Failed to delete room'
            ]);

        } catch (\Exception $e) {
            Log::error('Room deletion error', [
                'room_name' => $roomName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}