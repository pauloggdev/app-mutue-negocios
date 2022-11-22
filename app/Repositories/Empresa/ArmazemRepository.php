<?php

namespace App\Repositories\Empresa;

use App\Models\empresa\Armazen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Keygen\Keygen;

class ArmazemRepository
{

    protected $entity;

    public function __construct(Armazen $armazen)
    {
        $this->entity = $armazen;
    }

    public function getArmazens($search = null)
    {
        $armazen = $this->entity::with(['statuGeral'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->search(trim($search))
            ->paginate(10);
        return $armazen;
    }

    public function getArmazen(int $id)
    {
        $armazen = $this->entity::where('empresa_id', auth()->user()->empresa_id)
            ->where('id', $id)
            ->first();
        return $armazen;
    }
    public function store($data)
    {
        $armazem = $this->entity::create([
            'designacao' => $data['designacao'],
            'sigla' => $data['sigla']??NULL,
            'localizacao' => $data['localizacao'],
            'codigo' => mb_strtoupper(Keygen::numeric(9)->generate()),
            'empresa_id' => auth()->user()->empresa_id,
            'status_id' => $data['status_id'],
            'user_id' => auth()->user()->id
        ]);
        return $armazem;
    }

    public function update($armazem)
    {
        $armazem = $this->entity::where('id', $armazem['id'])->update([
            'designacao' => $armazem['designacao'],
            'sigla' => $armazem['sigla']??NULL,
            'localizacao' => $armazem['localizacao']
        ]);
        return $armazem;
    }
    public function deletarArmazem($armazemId)
    {
        return $this->entity::where('id', $armazemId)
            ->where('empresa_id', auth()->user()->empresa_id)
            ->delete();
    }
}
