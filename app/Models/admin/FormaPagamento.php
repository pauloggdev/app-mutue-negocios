<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\admin\Factura;
use App\Models\admin\Empresa;

class FormaPagamento extends Model
{
    protected $connection = 'mysql';
    protected $table ='formas_pagamentos';

    
}
