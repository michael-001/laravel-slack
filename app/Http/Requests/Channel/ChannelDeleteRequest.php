<?php

namespace App\Http\Requests\Channel;

use App\Models\Channel;
use Illuminate\Foundation\Http\FormRequest;

class ChannelDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function authorize()
    {
        try{
            $channel = Channel::find($this->route('channel'));

            if(!$channel){
                abort("Channel not found", 400);
            }

            if($this->user()->id == $channel->owner_id){
                return true;
            }
            return false;

        } catch (\Exception $e) {
            return response([
                "data" => '',
                "message" => $e->getMessage(),
            ], 400);
        }





    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
