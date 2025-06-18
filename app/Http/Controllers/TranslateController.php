<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Translate\V2\TranslateClient;

class TranslateController extends Controller
{
    public function translate(Request $request)
    {
        $request->validate([
            'texts' => 'required|array',
            'texts.*' => 'string',
            'target' => 'required|string',
        ]);

        $translate = new TranslateClient([
            'key' => config('services.google_translate.key'),
        ]);

        $texts = $request->input('texts');
        $target = $request->input('target');
        $translations = [];

        foreach ($texts as $text) {
            $result = $translate->translate($text, ['target' => $target]);
            $translations[] = $result['text'];
        }

        return response()->json(['translations' => $translations]);
    }
}
