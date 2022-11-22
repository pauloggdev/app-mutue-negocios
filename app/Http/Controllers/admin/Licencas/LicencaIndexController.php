<?php

namespace App\Http\Controllers\admin\Licencas;

use App\Http\Controllers\admin\Traits\TraitEmpresa;
use App\Http\Controllers\admin\Traits\TraitPathRelatorio;
use App\Repositories\Admin\BancoRepository;
use App\Repositories\Admin\LicencaRepository;
use Livewire\Component;

class LicencaIndexController extends Component
{




    private $licencaRepository;
    public $search;

    public function boot(LicencaRepository $licencaRepository)
    {
        $this->licencaRepository = $licencaRepository;
    }

    public function render()
    {
        $licencas = $this->licencaRepository->getLicencas($this->search);
        return view('admin.licencas.index', [
            'licencas' => $licencas
        ])->layout('layouts.appAdmin');
    }
}
