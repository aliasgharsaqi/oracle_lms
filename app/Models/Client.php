<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Client extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable=['name','sur_name','email','contact_no','refer_person','dob','passport_pic','created_by','staff_id'];
}
