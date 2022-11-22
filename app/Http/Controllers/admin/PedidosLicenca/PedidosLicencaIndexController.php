<?php

namespace App\Http\Controllers\admin\PedidosLicenca;

use App\Http\Controllers\admin\Traits\TraitEmpresa;
use App\Http\Controllers\admin\Traits\TraitPathRelatorio;
use App\Repositories\Admin\PedidosLicencaRepository;
use Livewire\Component;
use Livewire\WithPagination;

class PedidosLicencaIndexController extends Component
{

    use WithPagination;
    use TraitEmpresa;
    use TraitPathRelatorio;


    public $search = null;
    public $byStatus = null;
    public $byLicencas = null;
    public $perpage = 10;

    private $pedidosLicencaRepository;

    public function boot(PedidosLicencaRepository $pedidosLicencaRepository)
    {
        $this->pedidosLicencaRepository = $pedidosLicencaRepository;
    }

    public function render()
    {
        $pedidosLicenca = $this->pedidosLicencaRepository->getPedidoLicencas($this->byStatus, $this->byLicencas, $this->search, $this->perpage);
        return view('admin.pedidosLicenca.index', [
            'pedidosLicenca' => $pedidosLicenca
        ])->layout('layouts.appAdmin');
    }
    // public function imprimirUtilizadores()
    // {

    //     // return redirect()->route('admin.home');
    //     $pathLogoCompany = $this->getPathCompany();
    //     $pathRelatorio = $this->getPathRelatorio();

    //     $data =  $this->relatorioRepository->print('utilizadores', [
    //         'dirSubreportEmpresa' => $pathRelatorio,
    //         'diretorio' => $pathLogoCompany
    //     ]);
    //     return $data;
    // }
}
