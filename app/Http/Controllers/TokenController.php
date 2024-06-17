<?php

namespace App\Http\Controllers;
use App\Models\Token;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function index(){
        return token::all();
    }


    //consultar usuario
    public function show($id){
return token::find($id);

    }

    public function store(Request $request){
       
        $this->validate($request,[
            
            'token' =>'required'
        ]);
           $not = new token;
           $not->fill($request->all());
           $not->save();
        return  $not;
            }

            public function update(Request $request,$id){
                
                $not = token::find($id);
                if(!$not) return response('',404);
                $not->update($request->all());
                $not->save();
                return $not;
                    }

                    public function destroy($id){
                        $not = token::find($id);
                        if(!$not) return response('',404);
                        $not->delete();
                        return $not;
                            }
}
