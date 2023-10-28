<?php

namespace App\Http\Controllers;

class HelperController extends Controller
{
    public function getValues()
    {
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        return $data;
    }
}