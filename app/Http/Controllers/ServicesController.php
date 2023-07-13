<?php

namespace App\Http\Controllers;

use App\Models\Services;
use Illuminate\Http\Request;

class ServicesController extends Controller {
    function ServiceIndex() {
        return view( 'Services' );
    }

    function getServiceData() {
        $result = Services::all();

        return json_encode( $result );
    }

    function deleteServices( Request $req ) {
        //$result = Student::find( 1 );
        //return $result::Delete();
        $id = $req->input( 'id' );
        $result = Services::where( 'id', $id )->delete();
        if ( $result == true ) {
            echo 'Data Deleted Successfully';
        } else {
            echo 'Data Not Deleted';
        }
    }

    function getServiceDetails( Request $req ) {
        $id = $req->input( 'id' );
        // return Services::find( 1 );
        $result = Services::where( 'id', $id )->get();
        return json_encode( $result );
    }

    function updateServices( Request $req ) {
        $id = $req->input( 'id' );
        $name = $req->input( 'name' );
        $description = $req->input( 'description' );
        $img = $req->input( 'img' );
        $result = Services::where( 'id', $id )->update(['service_name' => $name, 'service_des' => $description,'service_img' => $img]);

    }
}
