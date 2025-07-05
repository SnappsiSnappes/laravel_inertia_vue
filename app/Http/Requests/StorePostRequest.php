<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        // Разрешить доступ только аутентифицированным пользователям
        return Auth::check();

        // Или использовать политику:
        // return Gate::allows('create', \App\Models\Post::class);

        // Или всегда разрешать запрос:
        // return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'preview_text' =>['required','string','max:1000'],
            'preview_image'=>['nullable','image','max:99000']
        ];
    }
}