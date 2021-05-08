<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Repositories\Contracts\IChat;
use App\Repositories\Contracts\IMessage;
use App\Repositories\Eloquent\Criteria\WithTrashed;
use Illuminate\Http\Request;

class ChatsController extends Controller
{
    protected $chat;
    protected $message;

    public function __construct(IChat $chat, IMessage $message)
    {
        $this->chats = $chat;
        $this->messages = $message;
    }
    //send message to user
    public function sendMessage(Request $request)
    {
        //validate first
        $this->validate($request, [
            'recipient' => ['required'],
            'body' => ['required']
        ]);
        $recipient = $request->recipient;
        $user = auth()->user();
        $body = $request->body;

        //check if there is existing chat beetwen user and recipient
        $chat = $user->getChatWithUser($recipient);

        if (!$chat) {
            $chat = $this->chats->create([]);
            $this->chats->createParticipants($chat->id, [$user->id, $recipient]);
        }

        // add the message to the chat
        $message = $this->messages->create([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'body' => $body,
            'last_read' => null
        ]);

        return new MessageResource($message);
    }


    //Get chat from user
    public function getUserChats()
    {
        $chat = $this->chats->getUserChats();
        return ChatResource::collection($chat);
    }

    //Get message from chat
    public function getChatMessages($id)
    {
        $message = $this->messages->withCriteria([
            new WithTrashed()
        ])->findWhere('chat_id', $id);

        return MessageResource::collection($message);
    }

    //Mark as read message
    public function  markAsRead($id)
    {
        $chat = $this->chats->find($id);
        $chat->markAsReadForUser(auth()->id());
        return response()->json(['message' => 'successful'], 200);
    }

    //Delete mesage
    public function destroy($id)
    {
        $message = $this->messages->find($id);
        $this->authorize('delete', $message);
        $message->delete();
    }
}
