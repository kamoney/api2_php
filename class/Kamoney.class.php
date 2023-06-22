<?
class Kamoney
{
    private static $public_key = 'YOUR_PUBLIC';
    private static $secret_key = 'YOUR_SECRET';
    private static $api = "https://sandbox-api2.kamoney.com.br/v2";

    private static function request_public($endpoint)
    {
        $mt = explode(' ', microtime());
        $req['nonce'] = $mt[1] . substr($mt[0], 2, 6);
        $data_query = http_build_query($req, '', '&');
        $sign = hash_hmac('sha512', $data_query, self::$secret_key);

        $headers = [
            'public: ' . self::$public_key,
            'sign: ' . $sign,
        ];

        $url = self::$api . "/public" . $endpoint;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $data = curl_exec($ch);

        return $data;
    }
    
    private static function request_private($endpoint, $data = array(), $type = 'GET')
    {
        $mt = explode(' ', microtime());
        $req['nonce'] = $mt[1] . substr($mt[0], 2, 6);

        foreach ($data as $key => $value) {
            $req[$key] = $value;
        }

        $data_query = http_build_query($req, '', '&');

        $sign = hash_hmac('sha512', $data_query, self::$secret_key);

        $headers = array(
            'public: ' . self::$public_key,
            'sign: ' . $sign,
        );

        $url = self::$api . $endpoint;

        $ch = curl_init();

        if ($type == 'POST') {

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_query);
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

        return $data;
    }

    public static function public_currency()
    {
        return self::request_public("/currency");
    }

    public static function public_system_info()
    {
        return self::request_public("/system/info");
    }
}
