<?php

namespace App\Models\empresa;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $connection = 'mysql2';
    protected $table ='categorias';
    protected $primarykey = 'id';
    protected $guard = 'id';
    protected $fillable = ['designacao','created_at','updated_at','empresa_id','status_id','user_id','canal_id'];
    public function empresa(){
        return $this->belongsTo(Empresa_Cliente::class, 'empresa_id');
    }

    public function statuGeral()
    {
        return $this->belongsTo(Statu::class, 'status_id');
    }
}
