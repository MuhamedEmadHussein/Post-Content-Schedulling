<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userPlatforms = Auth::user()->platforms->pluck('id');
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'scheduled_time' => 'required|date|after:now',
            'platforms' => 'required|array|min:1',
            'platforms.*' => Rule::in($userPlatforms),
        ];
    }
}