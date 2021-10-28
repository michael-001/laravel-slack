<?php

namespace App\Http\Controllers;

use App\Http\Requests\Channel\ChannelDeleteRequest;
use App\Http\Requests\Channel\ChannelUpdateRequest;
use App\Models\Channel;
use App\Models\Mention;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        try{
            $channels = Channel::all();

            if($channels->isEmpty()){
                throw new \Exception("No channel exists");
            }

            return response([
                "data" => $channels,
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
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        //
        try{
            $request->validate([
                'name' => 'required|unique:channels'
            ]);

            $channel = Channel::create([
                'name' =>  $request->name,
                'owner_id' => auth('api')->id()
            ]);

            return response([
                "data" => $channel,
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
        try{
            $channel = Channel::where('id', $id)
                ->with('messages')
                ->first();

            if(!$channel){
                throw new \Exception("Channel not found");
            }

            return response([
                "data" => $channel,
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
     * @param ChannelUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ChannelUpdateRequest $request, $id)
    {
        //
        try {
            $channel = Channel::find($id);

            if (!$channel) {
                throw new \Exception("Channel not found");
            }
            if (!auth('api')->id() == $channel->owner_id) {
                throw new \Exception("You are not authorized to perform this task", 403);
            }

            $channel->update([
                'name'=>$request->name
            ]);
            return response()->json([
                "data" => '',
                "message" => 'Successfully updated!',
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
     * @param ChannelDeleteRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|JsonResponse|Response
     */
    public function destroy($id)
    {
        //
        try{
            $channel = Channel::find($id);

            if(!$channel){
                throw new \Exception("Channel not found");
            }
            if(!auth('api')->id() == $channel->owner_id){
                throw new \Exception("You are not authorized to perform this task", 403);
            }

            // delete messages in channel first
            Mention::where('channel_id', $channel->id)->delete();
            Message::where('channel_id', $channel->id)->delete();
            $channel->delete();

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
}
