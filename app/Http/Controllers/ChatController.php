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
use App\Models\Message;
use Illuminate\Http\Request;
use App\Services\PostService as ServicesPostService;

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
            ->orderBy('updated_at', 'DESC')
            ->get();

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

        $messages = Message::select('id', 'user_id', 'message', 'created_at', 'updated_at', 'is_seen')
            ->where('chat_id', $chat->id)
            ->orderBy('created_at', 'asc')
            ->get();

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(chat $chat)
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

        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $postId = $request->post_id;
        $receiverId = $request->receiver_id;

        // Determine roles
        $buyerId = $user->id;
        $sellerId = $receiverId;

        // Check if chat already exists for this post + buyer + seller
        $chat = Chat::firstOrCreate([
            'buyer_id' => $buyerId,
            'seller_id' => $sellerId,
            'post_id' => $postId,
        ]);

        // Store the message
        $message = $chat->messages()->create([
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        // Optional: update chat timestamp
        $chat->touch();

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

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
}
