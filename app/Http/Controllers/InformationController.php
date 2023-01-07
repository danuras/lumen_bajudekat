<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\Information;
use DB;

class InformationController  extends Controller
{
    public function __construct()
    {
        $this->middleware('autha', ['only'=>['update']]);
    }
    
    public function show(Request $request){

        $information = DB::select('select short_description, description, no_telp, email, link_market_place, link_tiktok, link_instagram, link_facebook, link_twitter, link_pinterest, link_linkedin, link_youtube, city, address from informations limit 1');

        if(!$information){
            try {$information = Information::create([
                'short_description'=>'none',
                'description'=>'none',
                'no_telp'=>'none',
                'email'=>'none',
                'link_market_place'=>'none',
                'link_tiktok'=>'none',
                'link_instagram'=>'none',
                'link_facebook'=>'none',
                'link_twitter'=>'none',
                'link_pinterest'=>'none',
                'link_linkedin'=>'none',
                'link_youtube'=>'none',
                'city'=>'1',
                'address'=>'none',
            ]);
    
            } catch(\Exception $e) {
                echo "<pre>";
                echo $e;
                echo "</pre>";
            }
            
        
        }

        if($information){
            return response()->json([
                'success' => true,
                'message' => 'Information Found!',
                'data' => $information
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Information Not Found!',
                'data' => ''
            ], 400);
        }
    }
    
    public function update(Request $request){

        try {
            
        $information = DB::update('update informations set short_description = ?, description = ?, no_telp = ?, email = ?, link_market_place = ?, link_tiktok = ?, link_instagram = ?, link_facebook = ?, link_twitter = ?, link_pinterest = ?, link_linkedin = ?, link_youtube = ?, city = ?, address = ?', [
            $request->input('short_description'), 
            $request->input('description'), 
            $request->input('no_telp'), 
            $request->input('email'), 
            $request->input('link_market_place'), 
            $request->input('link_tiktok'), 
            $request->input('link_instagram'), 
            $request->input('link_facebook'), 
            $request->input('link_twitter'), 
            $request->input('link_pinterest'), 
            $request->input('link_linkedin'), 
            $request->input('link_youtube'), 
            $request->input('city'), 
            $request->input('address')
        
        ]);

        } catch(\Exception $e) {
            echo "<pre>";
            return response()->json([ $e]);
            echo "</pre>";
        }

        if($information){
            return response()->json([
                'success' => true,
                'message' => 'Information Updated!',
                'data' => $information
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Information Not updated!',
                'data' => ''
            ], 400);
        }
    }
}
