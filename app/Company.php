<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $fillable = ['id','cin','registration_number','company_name','status','build_year','email','other_details'];
}
