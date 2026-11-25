<?php
/** Shared legacy m_origem contract; independent of company IDs and config scope. */
function dotbright_rma_origins()
{
    $origins=array(1=>'Online',2=>'Loja de Matosinhos',3=>'Loja de Maia');
    foreach (($GLOBALS['lojas_rmas'] ?? array()) as $id=>$label) {
        if (ctype_digit((string)$id) && (int)$id>0 && is_string($label) && trim($label)!=='') $origins[(int)$id]=trim($label);
    }
    return $origins;
}
