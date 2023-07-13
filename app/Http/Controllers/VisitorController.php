<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller {

    function VisitorIndex() {
        $VisitorData =json_decode(Visitor::orderBy('id','desc')->take(10)->get());
        return view( 'Visitor', [ 'VisitorData' => $VisitorData ] );
    }
}
