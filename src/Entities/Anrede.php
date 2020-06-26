<?php

namespace ITHilbert\LaravelKit\Entities;

use Illuminate\Database\Eloquent\Model;
use ITHilbert\LaravelKit\Traits\VueComboBox;

class Anrede extends Model
{
    protected $table ='anrede';
    protected $fillable = ['anrede'];
    public $timestamps = false;

    use VueComboBox;

    public function getCbCaptionAttribute(){
        return $this->anrede;
    }
}
