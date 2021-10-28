<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserUpdateRequest;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
        try{
            $user = User::where('id',$id)
                ->with(['userMessages', 'userChannels', 'userMentions'])
                ->first();

            if(!$user){
                throw new \Exception("User not found", 400);
            }

            return response([
                "data" => $user,
                "message" => 'success',
            ], 201);
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
     * @param UserUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request, $id)
    {
        //

        User::where('id', $id)->update([
            'first_name'=>$request['first_name'],
            'last_name'=>$request['last_name'],
        ]);

        return response()->json([
            "data" => '',
            "message" => 'Successfully updated!',
        ]);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
