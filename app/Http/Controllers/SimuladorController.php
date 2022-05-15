<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimuladorController extends Controller
{
    private $dadosSimulador;
    private $simulacao = [];

    public function simular(Request $request)
    {
        $this->carregarArquivoDadosSimulador()
            ->simularEmprestimo($request->valor_emprestimo)
            ->filtrarInstituicao($request->instituicoes);
            //->filtrarConvenio($request->convenios)
            //->filtrarParcelas($request->parcelas)
        return \response()->json($this->simulacao);
    }

    private function carregarArquivoDadosSimulador(): self
    {
        $this->dadosSimulador = json_decode(\File::get(storage_path("app/public/simulador/taxas_instituicoes.json")));
        return $this;
    }

    private function simularEmprestimo(float $valorEmprestimo): self
    {
        foreach ($this->dadosSimulador as $dados) {
            $this->simulacao[$dados->instituicao][] = [
                "taxa"            => $dados->taxaJuros,
                "parcelas"        => $dados->parcelas,
                "valor_parcela"    => $this->calcularValorDaParcela($valorEmprestimo, $dados->coeficiente),
                "convenio"        => $dados->convenio,
            ];
        }
        return $this;
    }

    private function calcularValorDaParcela(float $valorEmprestimo, float $coeficiente): float
    {
        return round($valorEmprestimo * $coeficiente, 2);
    }


// // // // // // // ESTOU DEIXANDO AS FUNCOES FILTRARCONVENIO E FILTRARPARCELAS COMENTADAS
// // // // // // // POIS NAO CONSEGUI TERMINA-LAS EM TEMPO HÁBIL :C


    // private function filtrarConvenio(array $convenios): self
    // {
       
    //     $arrayAux = [];
    //     foreach ($convenios as $key => $convenio) {
    //         if (\array_key_exists($convenio, $this->simulacao)) {
    //                 $arrayAux[$convenio] = $this->simulacao[$convenio];
    //             }
    //         $this->simulacao = $arrayAux;
    //         }
        
    //     return $this;
    // }


    // private function filtrarParcelas(array $parcelas): self
    // {
       
    //     $arrayAux = [];
    //     foreach ($parcelas as $key => $parcela) {
    //         if (\array_key_exists($parcela, $this->simulacao)) {
    //                 $arrayAux[$parcela] = $this->simulacao[$parcela];
    //             }
    //         $this->simulacao = $arrayAux;
    //         }
        
    //     return $this;
    // }


    private function filtrarInstituicao(array $instituicoes): self
    {
        if (\count($instituicoes)) {
            $arrayAux = [];
            foreach ($instituicoes as $key => $instituicao) {
                // print_r($instituicao);
                if (\array_key_exists($instituicao, $this->simulacao)) {

                    $arrayAux[$instituicao] = $this->simulacao[$instituicao];
                }
            }
            $this->simulacao = $arrayAux;
        }
        return $this;
    }
}
