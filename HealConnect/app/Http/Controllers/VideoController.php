<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Events\VideoCallStarted;
use App\Models\User;

class VideoController extends Controller
{
    /**
     * Create a Daily.co room and start a video call
     * This is called when a therapist/clinic clicks "Start Call"
     */
    public function createRoom(Request $request)
    {
        try {
            // Validate that we have a receiver (patient)
            $request->validate([
                'receiver_id' => 'required|exists:users,id'
            ]);

            $receiverId = $request->receiver_id;
            $caller = auth()->user();
            $receiver = User::find($receiverId);

            // Security check: Only therapists and clinics can initiate calls
            if (!in_array($caller->role, ['therapist', 'clinic'])) {
                Log::warning('Unauthorized video call attempt', [
                    'user_id' => $caller->id,
                    'role' => $caller->role
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Only therapists and clinics can initiate video calls'
                ], 403);
            }

            // Log the attempt
            Log::info('🎥 Video call initiated', [
                'caller_id' => $caller->id,
                'caller_name' => $caller->name,
                'caller_role' => $caller->role,
                'receiver_id' => $receiverId,
                'receiver_name' => $receiver->name,
                'receiver_role' => $receiver->role
            ]);

            // Generate unique room name
            $roomName = 'healconnect-' . $caller->id . '-' . $receiverId . '-' . time();

            // Get Daily.co API key
            $apiKey = config('services.daily.api_key') ?? env('DAILY_API_KEY');
            
            if (!$apiKey) {
                Log::error('Daily.co API key not configured');
                return response()->json([
                    'success' => false,
                    'message' => 'Video service not configured. Please contact administrator.'
                ], 500);
            }

            Log::info('Creating Daily.co room', [
                'room_name' => $roomName,
                'api_key_present' => !empty($apiKey)
            ]);

            // Create Daily.co room via API
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

            // Log the API response
            Log::info('Daily.co API response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->json()
            ]);

            // Check if room creation was successful
            if ($response->successful()) {
                $roomData = $response->json();
                
                // Verify URL exists
                if (!isset($roomData['url'])) {
                    Log::error('Room created but no URL returned', ['response' => $roomData]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Room created but URL not provided'
                    ], 500);
                }
                
                Log::info(' Room created successfully', [
                    'room_name' => $roomName,
                    'room_url' => $roomData['url']
                ]);

                // Create meeting tokens for both caller and receiver
                $callerToken = $this->createMeetingToken($apiKey, $roomName, $caller->name, true);
                $receiverToken = $this->createMeetingToken($apiKey, $roomName, $receiver->name, false);

                //  Broadcast ONLY to the specific receiver
                try {
                    Log::info(' Broadcasting video call to receiver', [
                        'receiver_id' => $receiverId,
                        'channel' => 'healconnect-chat.' . $receiverId
                    ]);

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
                    Log::error(' Broadcast failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Continue even if broadcast fails - caller can still join
                }

                // Return success response to the caller
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
                'body' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create video room. Please try again.'
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error(' Video call error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating video room. Please try again.'
            ], 500);
        }
    }

    /**
     * Create a meeting token for a user
     * This gives them permission to join the specific room
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
                    'exp' => time() + 7200, // Token valid for 2 hours
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
            Log::error(' Token creation error', [
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
        
        Log::info('👤 User joining room', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'room' => $room
        ]);

        // Get token from query parameter (passed from createRoom or broadcast)
        $token = $request->query('token');
        
        // If no token provided
        if (!$token) {
            Log::info('No token provided, creating new token');
            $apiKey = config('services.daily.api_key') ?? env('DAILY_API_KEY');
            
            if ($apiKey) {
                $token = $this->createMeetingToken($apiKey, $room, $user->name, false);
            } else {
                Log::error('Cannot create token - API key missing');
            }
        }

        // Return the video call page
        return view('video.room', [
            'room' => $room,
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Delete a Daily.co room (cleanup)
     * Called when call ends or for cleanup
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

            Log::info(' Room deletion attempt', [
                'room_name' => $roomName,
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

            return response()->json([
                'success' => $response->successful(),
                'message' => $response->successful() ? 'Room deleted' : 'Failed to delete room'
            ]);

        } catch (\Exception $e) {
            Log::error(' Room deletion error', [
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