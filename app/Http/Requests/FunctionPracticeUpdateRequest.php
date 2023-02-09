<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FunctionPracticeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'PracticeTitle' => ['required', 'string'],
            'PracticeTitleAr' => ['required', 'string'],
            'FunctionId' => ['required', 'integer'],
            'Status' => ['required'],
        ];
    }
}
