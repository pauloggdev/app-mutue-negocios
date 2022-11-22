<?php

namespace App\Http\Controllers\empresa\Facturas;


use App\Http\Controllers\empresa\ReportShowController;
use App\Repositories\Empresa\FacturaRepository;
use App\Repositories\Empresa\ParametroRepository;
use App\Traits\Empresa\TraitEmpresaAutenticada;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;



class FacturasIndexController extends Component
{

    use TraitEmpresaAutenticada;
    use WithPagination;



    public $search;
    private $facturaRepository;
    private $parametroRepository;




    public function boot(FacturaRepository $facturaRepository, ParametroRepository $parametroRepository)
    {
        $this->facturaRepository = $facturaRepository;
        $this->parametroRepository = $parametroRepository;
    }

    public function render()
    {
        $infoNotificacao = $this->alertarActivacaoLicenca();
        $data['alertaAtivacaoLicenca'] = $infoNotificacao;

        if ($infoNotificacao['diasRestantes'] === 0) {
            return redirect("empresa/home");
        }
        $data['facturas'] = $this->facturaRepository->listarfacturas($this->search);
        return view('empresa.facturas.facturasIndex', $data);
    }


    public function imprimirFactura($facturaId)
    {


        $factura = $this->facturaRepository->listarFactura($facturaId);
        $numeroViaImpressao = $this->parametroRepository->numeroViasImpressao();

        $filename = "Winmarket";

        if ($factura['anulado'] == 2) {


            $logotipo = public_path() . '/upload//' . auth()->user()->empresa->logotipo;
            $DIR_SUBREPORT = "/upload/documentos/empresa/modelosFacturas/a4/";
            $DIR = public_path() . "/upload/documentos/empresa/modelosFacturas/a4/";



            $reportController = new ReportShowController('pdf', $DIR_SUBREPORT);
            $report = $reportController->show(
                [
                    'report_file' => 'WinmarketAnulado',
                    'report_jrxml' => 'WinmarketAnulado.jrxml',
                    'report_parameters' => [
                        "empresa_id" => auth()->user()->empresa_id,
                        "logotipo" => $logotipo,
                        "facturaId" => $facturaId,
                        "viaImpressao" => 2,
                        "dirSubreportBanco" => $DIR,
                        "dirSubreportTaxa" => $DIR,
                        "CaminhomarcaAgua" => $DIR,
                        "tipo_regime" => auth()->user()->empresa->tipo_regime_id
                    ]

                ]
            );
        } else if ($factura['retificado'] == 'Sim') {

            $filename = "WinmarketFacturaRetificada";

            $logotipo = public_path() . '/upload//' . auth()->user()->empresa->logotipo;
            $DIR_SUBREPORT = "/upload/documentos/empresa/modelosFacturas/a4/";
            $DIR = public_path() . "/upload/documentos/empresa/modelosFacturas/a4/";



            $reportController = new ReportShowController('pdf', $DIR_SUBREPORT);
            $report = $reportController->show(
                [
                    'report_file' => $filename,
                    'report_jrxml' => $filename . '.jrxml',
                    'report_parameters' => [
                        "empresa_id" => auth()->user()->empresa_id,
                        "logotipo" => $logotipo,
                        "facturaId" => $facturaId,
                        "viaImpressao" => 2,
                        "dirSubreportBanco" => $DIR,
                        "dirSubreportTaxa" => $DIR,
                        "tipo_regime" => auth()->user()->empresa->tipo_regime_id
                    ]

                ]
            );
        } else {


            $logotipo = public_path() . '/upload//' . auth()->user()->empresa->logotipo;
            $DIR_SUBREPORT = "/upload/documentos/empresa/modelosFacturas/a4/";
            $DIR = public_path() . "/upload/documentos/empresa/modelosFacturas/a4/";



            $reportController = new ReportShowController('pdf', $DIR_SUBREPORT);
            $report = $reportController->show(
                [
                    'report_file' => $filename,
                    'report_jrxml' => $filename . '.jrxml',
                    'report_parameters' => [
                        "empresa_id" => auth()->user()->empresa_id,
                        "logotipo" => $logotipo,
                        "facturaId" => $facturaId,
                        "viaImpressao" => 2,
                        "dirSubreportBanco" => $DIR,
                        "dirSubreportTaxa" => $DIR,
                        "tipo_regime" => auth()->user()->empresa->tipo_regime_id
                    ]

                ]
            );
        }




        $this->dispatchBrowserEvent('printPdf', ['data' => base64_encode($report['response']->getContent())]);
        // $this->dispatchBrowserEvent('printPdf', ['data' => base64_encode($report['response']->getContent())]);
        unlink($report['filename']);
        flush();
       
    }
}
