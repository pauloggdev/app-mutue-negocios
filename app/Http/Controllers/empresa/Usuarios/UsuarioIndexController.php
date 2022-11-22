<?php

namespace App\Http\Controllers\empresa\Usuarios;

use App\Http\Controllers\empresa\ReportShowController;
use App\Repositories\Empresa\UserRepository;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class UsuarioIndexController extends Component
{

    use LivewireAlert;

    public $banco;
    public $search = null;
    public $utilizadorId;
    private $userRepository;
    protected $listeners = ['deletarUtilizador'];




    public function boot(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function render()
    {
        $data['users'] = $this->userRepository->getUsers($this->search);
        return view('empresa.usuarios.index', $data);
    }
    public function modalDel($utilizadorId)
    {
        $this->utilizadorId = $utilizadorId;
        $this->confirm('Deseja apagar o item', [
            'onConfirmed' => 'deletarUtilizador',
            'cancelButtonText' => 'Não',
            'confirmButtonText' => 'Sim',
        ]);
    }
    public function deletarUtilizador($data)
    {

        if ($data['value']) {
            try {
                $this->userRepository->deletarUtilizador($this->utilizadorId);
                $this->confirm('Operação realizada com sucesso', [
                    'showConfirmButton' => false,
                    'showCancelButton' => false,
                    'icon' => 'success'
                ]);
            } catch (\Throwable $th) {
                $this->alert('warning', 'Não permitido eliminar, altera o status como desativo');
            }
        }
    }

    public function imprimirUtilizadores()
    {

        $logotipo = public_path() . '/upload//' . auth()->user()->empresa->logotipo;
        $filename = "utilizadores";

        $reportController = new ReportShowController();
        $report = $reportController->show(
            [
                'report_file' => $filename,
                'report_jrxml' => $filename . '.jrxml',
                'report_parameters' => [
                    'empresa_id' => auth()->user()->empresa_id,
                    'logotipo' => $logotipo,
                ]

            ]
        );

        $this->dispatchBrowserEvent('printPdf', ['data' => base64_encode($report['response']->getContent())]);
        unlink($report['filename']);
        flush();
    }
}
