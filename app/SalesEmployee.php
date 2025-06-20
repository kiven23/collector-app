<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesEmployee extends Model
{
    public function salesQueries()
    {
        return $this->hasMany('App\SalesQueries', 'Salesman', 'employee_id');
  
    }
}
