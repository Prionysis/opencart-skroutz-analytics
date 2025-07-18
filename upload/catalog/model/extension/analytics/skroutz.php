<?php

/**
 * Skroutz Analytics
 * @author Dionysis Pasenidis
 * @link https://github.com/Prionysis
 * @version 1.3
 *
 * @property DB $db
 */
class ModelExtensionAnalyticsSkroutz extends Model
{
    public function getOrder(int $order_id): array
    {
        $query = $this->db->query("
            SELECT 
                o.order_id, 
                o.payment_code, 
                o.payment_method,
                MAX(CASE WHEN ot.code = 'tax' THEN ot.value END) AS tax,
                MAX(CASE WHEN ot.code = 'shipping' THEN ot.value END) AS shipping,
                MAX(CASE WHEN ot.code = 'total' THEN ot.value END) AS revenue
            FROM `" . DB_PREFIX . "order` o
                LEFT JOIN `" . DB_PREFIX . "order_total` ot USING (order_id)
            WHERE o.order_id = {$order_id} 
        ");

        return $query->row;
    }

    public function getOrderProducts(int $order_id): array
    {
        $query = $this->db->query("
            SELECT 
                op.product_id, 
                op.quantity, 
        	    CONCAT(op.name, COALESCE(CONCAT(' - ', GROUP_CONCAT(DISTINCT oo.value ORDER BY oo.value SEPARATOR ', ')), '')) AS name,
        	    (op.price + op.tax) AS price
            FROM `" . DB_PREFIX . "order_product` op
        	    LEFT JOIN `" . DB_PREFIX . "order_option` oo USING (order_product_id)
            WHERE op.order_id = {$order_id} 
            GROUP BY op.product_id
        ");

        return $query->rows;
    }
}