<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';
    protected $fillable = ['first_name', 'last_name', 'employee_id', 'department_id'];
}
