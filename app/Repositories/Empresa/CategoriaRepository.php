<?php

namespace App\Repositories\Empresa;

use App\Models\empresa\Categoria;
use App\Models\empresa\Cliente;
use App\Models\empresa\Factura;
use App\Models\empresa\Marca;
use App\Models\empresa\NotaCredito;
use App\Models\empresa\NotaDebito;
use App\Models\empresa\Recibos;
use App\Models\empresa\Statu;
use Hamcrest\Type\IsNumeric;
use Illuminate\Support\Facades\DB;


class CategoriaRepository
{
    protected $entity;

    public function __construct(Categoria $categoria)
    {
        $this->entity = $categoria;
    }


    public function getCategorias()
    {
        $categoria = $this->entity = Categoria::with(['statuGeral', 'empresa'])
            ->where('empresa_id', auth()->user()->empresa_id)->get();
        return $categoria;
    }

    public function getCategoria(int $id)
    {
       
        $categoria = $this->entity::where('empresa_id', auth()->user()->empresa_id)
        ->where('id', $id)
        ->first();
        return $categoria;

    }

    public function store($data)
    {
        $categorias = $this->entity::create([
            'designacao' => $data['designacao'],
            'user_id' => auth()->user()->id,
            'empresa_id' => auth()->user()->empresa_id,
            'status_id' => $data['status_id'],
            'canal_id' => $data['canal_id'] ? $data['canal_id'] : 2,

        ]);

        return $categorias;
    }

    public function update($data)
    {
        
        $categoria = $this->entity::where('empresa_id', auth()->user()->empresa_id)
            ->where('id', $data['id'])
            ->update([
           
            'user_id' => auth()->user()->id,
            'empresa_id' => auth()->user()->empresa_id,
            'status_id' => $data['status_id'],
            'designacao' => $data['designacao'],
            'canal_id' => $data['canal_id'] ? $data['canal_id'] : 2,
     
        ]);

        return $categoria;
    }
}
