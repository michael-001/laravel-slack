<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\MessageStoreRequest;
use App\Http\Requests\Message\MessageUpdateRequest;
use App\Http\Resources\MessageResource;
use App\Models\Channel;
use App\Models\Mention;
use App\Models\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MessageController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param $channel
     * @return Response
     */
    public function index($channel)
    {
        try{

            $channel = Channel::find($channel);

            if(!$channel){
                throw new \Exception("Channel not found", 400);
            }

            $messages = Message::with('channel')->get();

            if($messages->isEmpty()){
                throw new \Exception("No message exists");
            }

            $this->setLanguageInMessageCollection($messages);

            return response([
                "data" => $messages,
                "message" => 'success',
            ]);
        } catch (\Exception $e) {
            return response([
                "data" => '',
                "message" => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param MessageStoreRequest $request
     * @param $channel
     * @return \Illuminate\Http\Response
     */
    public function store(MessageStoreRequest $request, $channel)
    {
        //

        try {
            $channelObject = Channel::find($channel);

            if(empty($channelObject)){
                throw new \Exception("Channel not found", 400);
            }

            $message = Message::create([
               'user_id' => auth('api')->id(),
               'channel_id' => $channelObject->id,
               'text' => $request->text
            ]);

            $this->setUserMentions($request->text, $message->id, $channelObject->id);

            return response([
                "data" => $message,
                "message" => 'success',
            ]);

        } catch(\Exception $e) {
            return response([
               'data' => '',
               "message" => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($channel, $message)
    {
        //
        try{
            $channel = Channel::find($channel);

            if(!$channel){
                throw new \Exception("Channel not found");
            }

            $messageObject = Message::where('id', $message)->with('channel')->first();

            if(!$messageObject){
                throw new \Exception("Message not found");
            }

            $this->setLanguageInMessageObject($messageObject);

            return response([
                "data" => $messageObject,
                "message" => 'success',
            ]);
        } catch (\Exception $e) {
            return response([
                "data" => '',
                "message" => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param MessageUpdateRequest $request
     * @param $channel
     * @param $message
     * @return \Illuminate\Http\Response
     */
    public function update(MessageUpdateRequest $request, $channel, $message)
    {
        //
        try{
            $channel = Channel::find($channel);

            if(!$channel){
                throw new \Exception("Channel not found");
            }

            $message = Message::find($message);

            if(!$message){
                throw new \Exception("Message not found");
            }

            $message->update([
                   'text' => $request->text
                ]);

            return response([
                "data" => '',
                "message" => 'Successfully updated message',
            ]);
        } catch (\Exception $e) {
            return response([
                "data" => '',
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $category
     * @param $message
     * @return \Illuminate\Http\Response
     */
    public function destroy($category, $message)
    {
        //
        try{
            $channel = Channel::find($category);

            if(!$channel){
                throw new \Exception("Channel not found");
            }

            $message = Message::find($message);

            if(!$message){
                throw new \Exception("Message not found");
            }

            if (!auth('api')->id() == $message->user_id) {
                throw new \Exception("You are not authorized to perform this task", 403);
            }

            // delete messages in channel first
            $message->delete();

            return response([
                "data" => '',
                "message" => 'Successfully deleted',
            ]);
        } catch (\Exception $e) {
            return response([
                "data" => '',
                "message" => $e->getMessage(),
            ], 400);
        }
    }


    /******************************************************
     * Mentions
     * ****************************************************
     */


    private function extractHandlesFromText($text)
    {
        $textArray =  explode(" ", $text);
        return array_values(preg_filter('/@+/', '', $textArray));
    }

    private function getUsersByHandle($extractedHandles)
    {
        return User::whereIn('handle', $extractedHandles)
            ->pluck('id');
    }

    private function loopInsertForMentions($userHandles, $messageId, $channelId)
    {

        info($userHandles);

        for($i = 0, $j = count($userHandles); $i<$j; $i++)
        {
            if(auth('api')->id() != $userHandles[$i]){
                Mention::create([
                    'user_id' => $userHandles[$i],
                    'message_id' => $messageId,
                    'channel_id' => $channelId
                ]);
            }

        }
    }

    protected function setUserMentions($text, $messageId, $channelId)
    {
        $extractedHandles = $this->extractHandlesFromText($text);
        info("extract");
        info($extractedHandles);
        $userHandles = $this->getUsersByHandle($extractedHandles);
        info($userHandles);
        $this->loopInsertForMentions($userHandles, $messageId, $channelId);

    }


    /********************
     * Accept Language
     * ************************************
     */


    /**
     * @param $messageObject
     * @return mixed
     */
    protected function setLanguageInMessageObject($messageObject)
    {
        if(!is_null(request()->header('Accept-Language'))){
            $header = request()->header('Accept-Language');
            $messageCollection = collect([$messageObject]);
            $newMessageCollection = $messageCollection->transform(function($item) use($header){
                return $item->text = $item->text." (".$header.") ";
            });
            return $newMessageCollection[0];
        } else {
            return $messageObject;
        }
    }

    /**
     * @param $messageArr
     * @return mixed
     */
    protected function setLanguageInMessageCollection($messageArr)
    {
        if(!is_null(request()->header('Accept-Language'))){
            $header = request()->header('Accept-Language');
            $messageCollection = collect($messageArr);
            return $messageCollection->transform(function($item) use($header){
                return $item->text = $item->text." (".$header.") ";
            });
        } else {
            return $messageArr;
        }
    }

}
