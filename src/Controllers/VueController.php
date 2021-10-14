<?php

namespace ITHilbert\LaravelKit\Controllers;

use Illuminate\Http\Request;
use ITHilbert\UserAuth\Entities\Role;
use Illuminate\Routing\Controller;

class VueController extends Controller
{

    public function vue(Request $request){
        $roles = Role::getComboBoxData();
        return view('vue::vue')->with(compact('roles'));
    }


    public function vuesubmit(Request $request){
        return view('vue::vue-submit')->with(compact('request'));
    }
}
