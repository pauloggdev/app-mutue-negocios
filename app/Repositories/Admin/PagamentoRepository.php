<?php

namespace App\Repositories\Admin;

use App\Models\admin\Pagamento;

class PagamentoRepository
{

    protected $entity;

    public function __construct(Pagamento $entity)
    {
        $this->entity = $entity;
    }

    public function getPagamentos()
    {
    }
    public function getPagamento(int $pagamentoId, int $empresaId)
    {
        $pagamento = $this->entity::where('id', $pagamentoId)->where('empresa_id', $empresaId)->first();
        return $pagamento;
    }
    public function alterarStatuPagamentoAtivo(int $pagamentoId, int $empresaId, $dataValicacao)
    {
        $pagamento = Pagamento::where('id', $pagamentoId)->where('empresa_id', $empresaId)->first();
        $pagamento->data_validacao = $dataValicacao;
        $pagamento->status_id = Pagamento::ATIVO;
        return $pagamento->save();
    }
}
