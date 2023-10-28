<?php

namespace App\Http\Controllers;

class OtherController extends Controller
{


    
public function other_controller_data()
{
    $helperController = new HelperController();
    $values = $helperController->getValues();

    $key1Value = $values['key1'];
    $key2Value = $values['key2'];

    return response()->json(['data1' => $key1Value, 'data2' => $key2Value], 200);
}



}
