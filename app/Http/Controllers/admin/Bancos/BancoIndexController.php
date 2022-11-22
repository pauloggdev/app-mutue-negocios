<?php

namespace App\Http\Controllers\admin\Bancos;

use App\Http\Controllers\admin\Traits\TraitEmpresa;
use App\Http\Controllers\admin\Traits\TraitPathRelatorio;
use App\Repositories\Admin\BancoRepository;
use App\Repositories\Admin\UserRepository;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BancoIndexController extends Component
{

    use WithPagination;
    use TraitEmpresa;
    use TraitPathRelatorio;


    public $search = null;
    public $byStatus = null;
    public $perpage = 15;

    private $bancoRepository;

    public function boot(BancoRepository $bancoRepository)
    {
        $this->bancoRepository = $bancoRepository;
    }

    public function render()
    {
        $bancos = $this->bancoRepository->getBancos($this->byStatus, $this->search, $this->perpage);

        // dd($bancos);
        return view('admin.bancos.index', [
            'bancos' => $bancos
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
