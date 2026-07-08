<?php

// ===== MULTILANGUE =====
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'fr';
$_SESSION['lang'] = $lang;
$translations = [];
if (file_exists(__DIR__ . '/lang/' . $lang . '.php')) {
    $translations = require_once __DIR__ . '/lang/' . $lang . '.php';
}
function __($key) {
    global $translations;
    return $translations[$key] ?? $key;
}

?><?php
// suppliers/BigBuy.php - Interface avec BigBuy

class BigBuySupplier {
    private $api_key;
    private $api_url = 'https://api.bigbuy.eu/rest/v1/';
    
    public function __construct($api_key) {
        $this->api_key = $api_key;
    }
    
    public function getProducts($page = 1, $limit = 100) {
        $url = $this->api_url . 'products?page=' . $page . '&limit=' . $limit;
        return $this->call($url);
    }
    
    public function createOrder($order_data) {
        $url = $this->api_url . 'orders';
        return $this->call($url, 'POST', $order_data);
    }
    
    public function getTracking($order_id) {
        $url = $this->api_url . 'orders/' . $order_id . '/tracking';
        return $this->call($url);
    }
    
    private function call($url, $method = 'GET', $data = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) throw new Exception('Erreur API BigBuy : ' . $error);
        return json_decode($response, true);
    }
}
?>
