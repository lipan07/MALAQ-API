<?php

namespace App\Http\Controllers;

use App\Events\MessageSeen;
use App\Http\Requests\StorechatRequest;
use App\Http\Requests\UpdatechatRequest;
use App\Models\chat;
use App\Models\Post;
use App\Events\MessageSent;
use App\Http\Resources\ChatResource;
use App\Http\Resources\PostResource;
use App\Jobs\SendFcmNotification;
use App\Models\DeviceToken;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Services\PostService as ServicesPostService;
use App\Services\FcmService;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $chats = Chat::with([
            'post:id,category_id,title,status,post_time',
            'buyer:id,name,status,last_activity',
            'seller:id,name,status,last_activity',
            'messages' => function ($query) {
                $query->select('id', 'chat_id', 'user_id', 'message', 'created_at', 'is_seen')
                    ->latest()
                    ->limit(1);
            }
        ])
            ->where(function ($query) use ($user) {
                $query->where('seller_id', $user->id)
                    ->orWhere('buyer_id', $user->id);
            })
            ->where('deleted_at', null)
            ->orderBy('updated_at', 'DESC')
            ->get();

        // Filter out chats with no messages
        $chats = $chats->filter(function ($chat) {
            return $chat->messages->isNotEmpty();
        })->values();

        // Wrap chats in resource and add last_message and other_person
        $data = $chats->map(function ($chat) use ($user) {
            $resource = new ChatResource($chat);
            $array = $resource->toArray(request());

            $lastMessage = $chat->messages->first();
            $array['last_message'] = $lastMessage ? [
                'id' => $lastMessage->id,
                'message' => $lastMessage->message,
                'created_at' => $lastMessage->created_at,
                'is_seen' => $lastMessage->is_seen,
            ] : null;

            // Determine other person
            if ($user->id === $chat->seller_id) {
                $otherUser = $chat->buyer;
            } else {
                $otherUser = $chat->seller;
            }
            $array['other_person'] = [
                'id' => $otherUser->id ?? null,
                'name' => $otherUser->name ?? null,
                'status' => $otherUser->status ?? null,
                'last_activity' => $otherUser->last_activity ?? null,
            ];

            return $array;
        });

        return response()->json([
            'data' => $data,
            'message' => 'Chats fetched successfully'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorechatRequest $request)
    {
        $user = auth()->user();
        $chat = Chat::updateOrCreate([
            'post_id' => $request->post_id,
            'seller_id' => $request->sender_id,
            'buyer_id' => $request->receiver_id,
        ], [
            'seller_id' => $request->sender_id,
            'buyer_id' => $request->receiver_id,
        ]);
        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($chat->id, $message))->toOthers();

        return response()->json(['status' => '200', 'message' => 'Chat created successfully', 'data' => [$chat->id, $message->id]]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Chat $chat)
    {
        $authUser = auth()->user();

        // Determine the other person's ID
        if ($authUser->id === $chat->seller_id) {
            $otherUser = $chat->buyer;
        } else {
            $otherUser = $chat->seller;
        }

        $perPage = $request->input('per_page', 10);
        $messages = Message::select('id', 'user_id', 'message', 'created_at', 'updated_at', 'is_seen')
            ->where('chat_id', $chat->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'message' => 'Chat messages fetched successfully',
            'chats' => $messages,
            'other_person' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'status' => $otherUser->status,
                'last_activity' => $otherUser->last_activity,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatechatRequest $request, chat $chat)
    {
        //
    }

    public function openChat(Request $request)
    {
        \Log::info($request->all());
        $buyerId = $request->input('buyer_id');
        $sellerId = $request->input('seller_id');
        $postId = $request->input('post_id');

        // Check if chat already exists
        $chat = Chat::where('buyer_id', $buyerId)
            ->where('seller_id', $sellerId)
            ->where('post_id', $postId)
            ->first();

        if ($chat) {
            // Return existing chat and messages
            $messages = Message::where('chat_id', $chat->id)->get();
            return response()->json(['chat' => $chat, 'messages' => $messages]);
        } else {
            // Create new chat if it doesn't exist
            $chat = Chat::create([
                'buyer_id' => $buyerId,
                'seller_id' => $sellerId,
                'post_id' => $postId,
            ]);
            return response()->json(['chat' => $chat, 'messages' => []]);
        }
    }

    public function sendMessage(Request $request)
    {
        $user = auth()->user();

        // Determine if this is an existing chat or a new one
        $hasChatId = $request->filled('chat_id');

        // Dynamic validation
        $rules = [
            'message' => 'required|string|max:1000',
        ];

        if ($hasChatId) {
            $rules['chat_id'] = 'required|exists:chats,id';
        } else {
            $rules['post_id'] = 'required|exists:posts,id';
            $rules['receiver_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        if ($hasChatId) {
            $chat = Chat::select('id', 'buyer_id', 'seller_id') // select only needed cols
                ->findOrFail($request->chat_id);
        } else {
            $chat = Chat::firstOrCreate(
                [
                    'buyer_id' => $user->id,
                    'seller_id' => $request->receiver_id,
                    'post_id'  => $request->post_id,
                ],
                ['created_at' => now()] // prevent extra DB write
            );
        }

        // Store the message
        $message = $chat->messages()->create([
            'user_id' => $user->id,
            'message' => $validated['message'],
        ]);

        // Broadcast to others
        broadcast(new MessageSent($message))->toOthers();

        // Update chat updated_at
        $chat->touch();

        // 🔔 Send FCM Push to receiver
        $receiverId = $hasChatId ? ($chat->buyer_id === $user->id ? $chat->seller_id : $chat->buyer_id) : $request->receiver_id;
        \Log::info("Receiver ID: $receiverId");

        // Dispatch job to queue - returns immediately
        SendFcmNotification::dispatch(
            $receiverId,
            'New Message',
            $request->message,
            ['chat_id' => $chat->id]
        )->afterResponse(); // This sends the response first, then processes the job


        return response()->json([
            'chat_id' => $chat->id,
            'message' => $message,
        ]);
    }

    public function markMessagesAsSeen(Request $request, $messageId)
    {
        $user = auth()->user();
        $message = Message::find($messageId);

        if ($message && $message->user_id !== $user->id) { // Ensure it's not the user's own message
            $message->is_seen = true;
            $message->save();

            // Optionally broadcast this change to other users
            broadcast(new MessageSeen($message));

            return response()->json(['message' => 'Message marked as seen', 'data' => $message]);
        }

        return response()->json(['message' => 'Message not found or already seen'], 404);
    }

    public function destroy(Request $request, $chatId)
    {
        $user = auth()->user();

        // Validate chat exists
        $chat = Chat::where('id', $chatId)
            ->where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            })
            ->firstOrFail();

        // Update deleted_at
        $chat->update(['deleted_at' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Chat marked as deleted successfully.',
            'chat_id' => $chat->id,
            'deleted_at' => $chat->deleted_at,
        ]);
    }
}
