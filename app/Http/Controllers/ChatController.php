<?php

namespace App\Http\Controllers;

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
        $chats = Chat::with('post', 'buyer')->where('seller_id', $user->id)->orWhere('buyer_id', $user->id)->orderBy('updated_at', 'DESC')->get();
        foreach ($chats as $chat) {
            $chat->post = ServicesPostService::fetchSinglePostData($chat->post);
        }

        $chats = ChatResource::collection($chats);
        return response()->json(['chats' => $chats]);
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

        return response()->json(['status' => 'Message Sent!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $chats = Message::where('chat_id', $id)->orderBy('created_at', 'asc')->get();

        return response()->json([
            'message' => 'Chat messages fetched successfully',
            'chats' => $chats,
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
        $chatId = $request->input('chat_id');
        $messageText = $request->input('message');

        // Store the message
        $message = Message::create([
            'user_id' => $user->id,
            'chat_id' => $chatId,
            'message' => $messageText,
        ]);
        Chat::where('id', $chatId)->update(['updated_at' => now()]);

        // Broadcast the message using Laravel Broadcasting (Pusher)
        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['message' => $message]);
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
