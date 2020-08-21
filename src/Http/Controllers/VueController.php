<?php

namespace ITHilbert\LaravelKit\Http\Controllers;

use Illuminate\Http\Request;
use ITHilbert\UserAuth\Entities\Role;
use Illuminate\Routing\Controller;

class VueController extends Controller
{

    public function vue(Request $request){
        $roles = Role::getComboBoxData();
        return view('vue')->with(compact('roles'));
    }


    public function vuesubmit(Request $request){
        return view('vue-submit')->with(compact('request'));
    }
}
