{{-- resources/views/admin/dashboard.blade.php --}}

@extends('layouts.vue')

@section('title', 'Vue Components')

@section('content_header')
    <h1>Vue Components</h1>
@stop

@section('content')
    <card title="Buttons">
        <button-create route="#"></button-create>
        <button-create>Button-Create</button-create>

        <button-show>Button-Show</button-show>

        <button-edit>Button-Edit</button-edit>
        <button-delete>Button-Delete</button-delete>

        <button-save>Button-Save</button-save>
        <button-cancel>Button-Cancel</button-cancel>
        <button-back>Button-Back</button-back>

        <button-submit>Button-Submit</button-submit>
        <button-reset>Button-Reset</button-reset>

        <button-delete>Button-Delete</button-delete>

        <hbutton>hbutton</hbutton>
   </card>

   <hform action="{{route('vue-submit')}}">
    <card title="Input">
        <hlabel for="search">search</hlabel><search name="search" :options="{{ $roles }}"></search><br>
        <hlabel for="combobox">combobox</hlabel><combobox name="combobox" :options="{{ $roles }}"></combobox><br>
        <hlabel for="myhidden">input-hidden</hlabel><input-hidden name="myhidden" value="1"></input-hidden><br><br>
        <hlabel for="input-text">input-text</hlabel><input-text value="input-text"></input-text><br>
        <hlabel for="input-euro">input-euro</hlabel><input-euro value="1.25" ></input-euro><br>
        <hlabel for="input-percent">input-percent</hlabel><input-percent value="input-percent"></input-percent><br>
        <hlabel for="input-int">input-int</hlabel><input-int value="input-int"></input-int><br>
        <hlabel for="input-number">input-number</hlabel><input-number value="input-number"></input-number><br>
        input-date: <input-date value="1955-11-25"></input-date><br>
        input-password: <input-password value="input-password"></input-password><br>
        <hlabel for="ta1">text-area</hlabel>
        <text-area id="ta1">text-area</text-area>

        <checkbox id="cb1">checkbox 1</checkbox>
        <checkbox id="cb2" checked="true">checkbox 2</checkbox>

        <radiobox id="rb1" name="rbg" checked="true">RadioBox 1</radiobox>
        <radiobox id="rb2" name="rbg">RadioBox 2</radiobox>

        <br>
        <button-submit>Submit</button-submit>
    </card>
</hform>

@stop

@section('css')
    <!--<link rel="stylesheet" href="/css/admin_custom.css">-->
@stop

@section('js')

@stop
