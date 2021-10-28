<?php

namespace App\Http\Requests\Channel;

use App\Models\Channel;
use Illuminate\Foundation\Http\FormRequest;

class ChannelUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     * @throws \Exception
     */
    public function authorize()
    {
        $channel = Channel::find($this->route('channel'));


        if(!$channel){
            abort(400, 'Channel does not exists');
        }

        return $channel && $this->user()->can('update-channel', $channel);

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
            'id' => 'required'
        ];
    }
}
