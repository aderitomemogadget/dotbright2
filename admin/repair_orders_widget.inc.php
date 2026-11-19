<?php
require_once __DIR__.'/../core/repair_orders.php';

/** Separate dashboard panel; never queries the shop order tables or confidential access data. */
function dotbright_repair_orders_dashboard($admin_id, $permissions, $shop, PDO $db = null)
{
    if (!$shop || !dotbright_repair_order_can_manage($admin_id, $permissions)) return '';
    $rows = array();
    $error = false;
    try {
        $db = $db ?: dotbright_repair_orders_db();
        $q = $db->query('SELECT id,legacy_id,created_at,customer_name,product_id,product_name,product_reference,total_cents,status FROM repair_orders ORDER BY id DESC LIMIT 10');
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $error = true;
    }
    $escape = 'dotbright_repair_order_escape';
    $labels = dotbright_repair_order_statuses();
    $url = isset($GLOBALS['site_http_url']) ? rtrim($GLOBALS['site_http_url'], '/').'/admin/products/repair_orders.php' : 'products/repair_orders.php';
    $html = '<section class="panel PC" aria-labelledby="repair-dashboard-heading"><div class="panel-heading"><h4 class="panel-title" id="repair-dashboard-heading">Últimos pedidos de reparação</h4></div>';
    if ($error) {
        $html .= '<div class="panel-body"><p role="alert">Não foi possível carregar os pedidos de reparação. Confirme a configuração e a tabela repair_orders.</p></div>';
    } else {
        $html .= '<div class="table-responsive"><table class="table table-bordered table-hover table-striped table-td-valign-middle"><thead><tr class="inverse"><th scope="col">Pedido</th><th scope="col">Data UTC</th><th scope="col">Cliente</th><th scope="col">Reparação</th><th scope="col">Total estimado</th><th scope="col">Estado</th><th scope="col">Detalhe</th></tr></thead><tbody>';
        foreach ($rows as $row) {
            $detail = $escape($url.'?id='.(int)$row['id']);
            $html .= '<tr><td><a href="'.$detail.'">REP-'.(int)$row['id'].'</a>'.($row['legacy_id'] ? '<br>Antigo #'.(int)$row['legacy_id'] : '').'</td>';
            $html .= '<td>'.$escape($row['created_at'] ?: 'Data original desconhecida').'</td><td>'.$escape($row['customer_name']).'</td>';
            $html .= '<td>'.$escape($row['product_name'] ?: 'Produto #'.$row['product_id']).($row['product_reference'] !== '' ? '<br>'.$escape($row['product_reference']) : '').'</td>';
            $html .= '<td>'.($row['total_cents'] === null ? '—' : $escape(number_format($row['total_cents']/100, 2, ',', '.').' €')).'</td>';
            $html .= '<td>'.$escape($labels[$row['status']] ?? $row['status']).'</td><td><a class="btn btn-default btn-sm" href="'.$detail.'">Ver pedido</a></td></tr>';
        }
        if (!$rows) $html .= '<tr><td colspan="7">Ainda não existem pedidos de reparação registados nesta área.</td></tr>';
        $html .= '</tbody></table></div>';
    }
    return $html.'<div class="panel-footer text-center"><a class="btn btn-primary" href="'.$escape($url).'">Ver todos os pedidos de reparação</a></div></section>';
}
