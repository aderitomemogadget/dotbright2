<?php

if (!function_exists('rma_loan_block_message')) {
    /**
     * Resultado específico usado para distinguir um bloqueio funcional de
     * empréstimo de uma gravação bem-sucedida devolvida pelo PDO.
     */
    function rma_loan_block_message()
    {
        return 'Não é possivel finalizar RMA sem devolver equipamento de empréstimo!';
    }
}

if (!function_exists('rma_is_loan_block_result')) {
    function rma_is_loan_block_result($result)
    {
        return is_string($result) && $result === rma_loan_block_message();
    }
}

if (!function_exists('rma_loan_blocks_finalization')) {
    /**
     * Uma RMA só fica bloqueada quando existe um equipamento de empréstimo
     * identificado por IMEI e ainda marcado como entregue ao cliente.
     */
    function rma_loan_blocks_finalization($rma_state, $loan_state, $loan_imei)
    {
        $final_states = array(15, 16, 17, 18, 19, 20);

        return in_array((int) $rma_state, $final_states, true)
            && (int) $loan_state === 2
            && trim((string) $loan_imei) !== '';
    }
}

if (!function_exists('rma_normalize_loan_submission')) {
    /**
     * Sem IMEI selecionado não existe empréstimo. Limpa dados residuais que
     * possam ter ficado no formulário após desmarcar o equipamento.
     *
     * @return array{imei:string,marca:string,modelo:string,estado:int}
     */
    function rma_normalize_loan_submission($imei, $marca, $modelo, $estado)
    {
        $imei = trim((string) $imei);
        if ($imei === '') {
            return array('imei' => '', 'marca' => '', 'modelo' => '', 'estado' => 1);
        }

        return array(
            'imei' => $imei,
            'marca' => trim((string) $marca),
            'modelo' => trim((string) $modelo),
            'estado' => (int) $estado,
        );
    }
}
