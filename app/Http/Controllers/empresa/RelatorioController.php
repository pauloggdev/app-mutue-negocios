<?php

namespace App\Http\Controllers\empresa;

use App\Models\empresa\CentroCusto;
use App\Models\empresa\Factura;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class RelatorioController extends Component
{
    public $venda;
    public $centroCusto;

    use LivewireAlert;


    public function mount($uuid)
    {
        $this->centroCusto =  CentroCusto::where('uuid', $uuid)->where('empresa_id', auth()->user()->empresa_id)->first();
        if (!$this->centroCusto) {
            return redirect()->back();
        }
    }
    public function boot()
    {

        $this->venda['dataFim'] = NULL;
        $this->venda['dataInicio'] = NULL;
    }
    public function render()
    {

        return view('empresa.relatorios.index', [
            'centroCusto' => $this->centroCusto
        ]);
    }
    public function printRelatorioVendaPdf($format)
    {
        $this->printRelatorioVenda($format);
    }
    public function printRelatorioVendaXls($format)
    {


        $request = $this->venda;
        $rules = [
            'venda.dataInicio' => ["required", function ($attribute, $value, $fail) use ($request) {
                if ($request['dataInicio'] > $request['dataFim']) {
                    $fail('data inicial é maior que a final');
                    return;
                }
            }],
            'venda.dataFim' => 'required',
        ];
        $messages = [
            'venda.dataInicio.required' => 'Informe as duas datas',
            'venda.dataFim.required' => 'Informe as duas datas',
        ];

        $this->validate($rules, $messages);


        $dataInicio = str_replace("T", " ", $this->venda['dataInicio']);

        $dataFim = str_replace("T", " ", $this->venda['dataFim']);
        $dataInicioFormat = date_format(date_create($dataInicio), 'd/m/Y H:i');
        $dataFinalFormat = date_format(date_create($dataFim), 'd/m/Y H:i');

        $factura = Factura::where('empresa_id', auth()->user()->empresa_id)
            ->where('anulado', 1)->where('tipo_documento', 1)->whereBetween('created_at', [$dataInicio, $dataFim])->get();

        if (count($factura) > 0) {



            $operador = "Todos operadores";
            $filename = 'relatoriosDeVendaXls';
            $logotipo = public_path() . "/upload//" . auth()->user()->empresa->logotipo;

            $reportController = new ReportShowController('xls');
            $report = $reportController->show(
                [
                    'report_file' => $filename,
                    'report_jrxml' => $filename . '.jrxml',
                    'report_parameters' => [
                        'empresa_id' => auth()->user()->empresa_id,
                        'data_inicio' => $dataInicio,
                        'data_fim' => $dataFim,
                        'user_id' => 0,
                        'dataInicioFormat' => $dataInicioFormat,
                        'dataFinalFormat' => $dataFinalFormat,
                        'operador' => $operador,
                        'logotipo' => $logotipo
                    ]

                ]
            );

            dd($report['filename']);
            $this->dispatchBrowserEvent('printLink', ['data' => $report['filename']]);
            // unlink($report['filename']);
            // flush();

        } else {
            $this->confirm('Não existe documento neste intervalo/ou documento anulado neste intervalo', [
                'showConfirmButton' => 'OK',
                'showCancelButton' => false,
                'icon' => 'warning',
            ]);
            return;
        }
    }

    public function printRelatorioVenda($format)
    {
        $request = $this->venda;
        $rules = [
            'venda.dataInicio' => ["required", function ($attribute, $value, $fail) use ($request) {
                if ($request['dataInicio'] > $request['dataFim']) {
                    $fail('data inicial é maior que a final');
                    return;
                }
            }],
            'venda.dataFim' => 'required',
        ];
        $messages = [
            'venda.dataInicio.required' => 'Informe as duas datas',
            'venda.dataFim.required' => 'Informe as duas datas',
        ];

        $this->validate($rules, $messages);


        $dataInicio = str_replace("T", " ", $this->venda['dataInicio']);

        $dataFim = str_replace("T", " ", $this->venda['dataFim']);
        $dataInicioFormat = date_format(date_create($dataInicio), 'd/m/Y H:i');
        $dataFinalFormat = date_format(date_create($dataFim), 'd/m/Y H:i');

        // dd($dataFinalFormat);


        $factura = Factura::where('empresa_id', auth()->user()->empresa_id)
            ->where('anulado', 1)->where('tipo_documento', 1)->whereBetween('created_at', [$dataInicio, $dataFim])->get();

        if (count($factura) > 0) {

            $operador = "Todos operadores";
            $filename = $format == 'pdf' ? 'relatoriosDeVenda' : 'relatoriosDeVendaXls';
            $logotipo = public_path() . "/upload//" . auth()->user()->empresa->logotipo;


            $reportController = new ReportShowController();
            $report = $reportController->show(
                [
                    'report_file' => $filename,
                    'report_jrxml' => $filename . '.jrxml',
                    'report_parameters' => [
                        'empresa_id' => auth()->user()->empresa_id,
                        'data_inicio' => $dataInicio,
                        'data_fim' => $dataFim,
                        'user_id' => 0,
                        'dataInicioFormat' => $dataInicioFormat,
                        'dataFinalFormat' => $dataFinalFormat,
                        'operador' => $operador,
                        'logotipo' => $logotipo
                    ]

                ]
            );
            $this->dispatchBrowserEvent('printPdf', ['data' => base64_encode($report['response']->getContent())]);
            unlink($report['filename']);
            flush();

            /*
            $reportController = new ReportsController($format);

            $file = $reportController->show(
                [
                    'report_file' => $filename,
                    'report_jrxml' => $filename . '.jrxml',
                    'report_parameters' => [
                        'empresa_id' => auth()->user()->empresa_id,
                        'data_inicio' => $dataInicio,
                        'data_fim' => $dataFim,
                        'user_id' => 0,
                        'dataInicioFormat' => $dataInicioFormat,
                        'dataFinalFormat' => $dataFinalFormat,
                        'operador' => $operador,
                        'logotipo' => $logotipo
                    ]
                ]
            );


            return response()->streamDownload(function () use ($file) {
                echo response()::file($file)->getContent();
            }, 'myFile.pdf');
            */
        } else {
            $this->confirm('Não existe documento neste intervalo/ou documento anulado neste intervalo', [
                'showConfirmButton' => 'OK',
                'showCancelButton' => false,
                'icon' => 'warning',
            ]);
            return;
        }


        //158
        // DATA INICIO =  2022-09-05 13:05
        // DATA FIM =  2022-09-05 13:05

        // DATA INICIO =  05/09/2022 13:05
        // DATA FIM =  05/09/2022 13:05
        // Todos operadores
        // C:\laragon\www\appmutuenegociosv1\public/upload//utilizadores/cliente/LSiFuIFEP1qhJ4mcpYcXpfidlenFnrmTKDq7Lvm1.jpg




    }




    public function printRelatorioExistenciaStock()
    {
        $reportController = new ReportsController();

        $logotipo = public_path() . "/upload//" . auth()->user()->empresa->logotipo;


        return $reportController->show(
            [
                'report_file' => 'relatoriosExistenciaStocks',
                'report_jrxml' => 'relatoriosExistenciaStocks.jrxml',
                'report_parameters' => [
                    'empresa_id' => auth()->user()->empresa_id,
                    'logotipo' => $logotipo

                ]

            ]
        );

        return response()->streamDownload(function () use ($file) {
            echo response()::file($file)->getContent();
        }, 'myFile.pdf');
    }
}
