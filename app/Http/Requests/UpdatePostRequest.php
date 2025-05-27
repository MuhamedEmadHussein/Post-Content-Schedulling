<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userPlatforms = Auth::user()->platforms->pluck('id');
        return [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'image' => 'nullable|image|max:2048',
            'scheduled_time' => 'sometimes|date|after:now',
            'platforms' => 'sometimes|array|min:1',
            'platforms.*' => Rule::in($userPlatforms),
        ];
    }
}