<?
class Kamoney
{
    public static $public_key = '';
    public static $secret_key = '';
    private static $api = "https://homolog-api2.kamoney.com.br/v2";

    private static function request_public($endpoint, $data = [], $type = 'GET')
    {
        $headers = ['Content-Type: application/json'];
        $url = self::$api . "/public" . $endpoint;
        
        $ch = curl_init();

        if ($type == 'POST') {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            $data_query = http_build_query(self::query_mounted($data), '', '&');
            curl_setopt($ch, CURLOPT_URL, $url . (count($data) > 0 ? '?' . $data_query : ''));
            curl_setopt($ch, CURLOPT_POST, 0);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $data = curl_exec($ch);

        return json_decode($data);
    }
    
    public static function query_mounted(array $req, String $comp = '') {
        $req_data_query = [];
        
        foreach ($req as $key => $value) {
            if(is_array($value)) {
                $req_data_query = array_merge($req_data_query, self::query_mounted($value, "{$comp}{$key}"));
            } else {
                $req_data_query[$comp.$key] = $value;
            }
        }
        
        return $req_data_query;
    }
    
    private static function request_private($endpoint, $data = array(), $type = 'GET')
    {
        $mt = explode(' ', microtime());
        $data['nonce'] = $mt[1] . substr($mt[0], 2, 6);
        $data_query = http_build_query(self::query_mounted($data), '', '&');
        $sign = hash_hmac('sha512', $data_query, self::$secret_key);

        $headers = array(
            'public: ' . self::$public_key,
            'sign: ' . $sign,
            'Content-Type: application/json'
        );

        $url = self::$api . "/private" . $endpoint;

        $ch = curl_init();

        if ($type == 'POST') {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $data_query);
            curl_setopt($ch, CURLOPT_POST, 0);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $data = curl_exec($ch);

        return json_decode($data);
    }

    public static function public_currency()
    {
        return self::request_public("/currency");
    }

    public static function public_system_info()
    {
        return self::request_public("/system/info");
    }

    public static function merchant_create(String $asset, String $network, float $amount, String $email, String $callback)
    {
        return self::request_private("/merchant", [
            "asset" => $asset,
            "network" => $network,
            "amount" => $amount,
            "email" => $email,
            "callback" => $callback,
        ], 'POST');
    }
}
