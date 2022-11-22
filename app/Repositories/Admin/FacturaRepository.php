<?php

namespace App\Repositories\Admin;

use App\Models\admin\Factura;

class FacturaRepository
{

    protected $entity;

    public function __construct(Factura $entity)
    {
        $this->entity = $entity;
    }

    public function alterarStatuFacturaParaDivida(int $facturaId, int $empresaId)
    {
        $factura = $this->entity::where('empresa_id', $empresaId)->find($facturaId);
        $factura->statusFactura = $this->entity::STATUDIVIDA;
        $factura->save();
    }
    public function alterarStatuFacturaParaPago(string $referenciaFactura, int $empresaId): bool
    {
        $factura = Factura::where('faturaReference', $referenciaFactura)->where('empresa_id', $empresaId)->first();
        $factura->statusFactura = Factura::STATUPAGO;
        return $factura->save();
    }
}
