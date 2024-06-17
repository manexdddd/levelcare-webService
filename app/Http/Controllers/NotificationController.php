<?php

namespace App\Http\Controllers;
use App\Models\notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(){
        return notification::all();
    }


    //consultar usuario
    public function show($id){
return notification::find($id);

    }

    public function store(Request $request){
       
        $this->validate($request,[
            
            'title' =>'required',
            'subtitle' =>'required',
            'date' =>'required'
        ]);
           $not = new notification;
           $not->fill($request->all());
           $not->save();
        return  $not;
            }

            public function update(Request $request,$id){
                
                $not = notification::find($id);
                if(!$not) return response('',404);
                $not->update($request->all());
                $not->save();
                return $not;
                    }

                    public function destroy($id){
                        $not = notification::find($id);
                        if(!$not) return response('',404);
                        $not->delete();
                        return $not;
                            }
}