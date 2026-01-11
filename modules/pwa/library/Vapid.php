<?php

namespace NukeViet\Module\Pwa\Library;

class Vapid
{
    /**
     * @return array ['publicKey' => '...', 'privateKey' => '...']
     */
    public static function createVapidKeys()
    {
        if (!function_exists('openssl_pkey_new')) {
            return ['error' => 'OpenSSL extension missing'];
        }

        // Clear any previous errors
        while (openssl_error_string());

        // Check for curve support (PHP 7.1+)
        if (function_exists('openssl_get_curve_names')) {
            $curves = \openssl_get_curve_names();
            if ($curves && !in_array('prime256v1', $curves)) {
                return ['error' => 'Server OpenSSL does not support prime256v1 curve. Please generate keys manually.'];
            }
        }

        // Check if EC constant is defined
        if (!defined('OPENSSL_KEYTYPE_EC')) {
             return ['error' => 'OPENSSL_KEYTYPE_EC is not defined. PHP OpenSSL extension might be too old. Please generate keys manually.'];
        }

        $configArgs = array(
            "digest_alg" => "sha256",
            "private_key_type" => OPENSSL_KEYTYPE_EC,
            "curve_name" => "prime256v1",
        );

        // Attempt 1: Standard system config
        // Suppress warning "Unsupported private key type" if EC is missing in lib
        $res = @\openssl_pkey_new($configArgs);

        // Attempt 2: Fallback to bundled config using putenv + config arg (aggressive override)
        if (!$res) {
             // Clear errors from first attempt
             while (\openssl_error_string());

             $bundledConfig = str_replace('\\', '/', __DIR__ . '/openssl.cnf');
             if (file_exists($bundledConfig)) {
                 // Save old env var
                 $oldEnv = getenv('OPENSSL_CONF');

                 // Force OpenSSL to use our config by setting env var AND passing config arg
                 putenv("OPENSSL_CONF={$bundledConfig}");
                 $configArgs['config'] = $bundledConfig;

                 $res = @\openssl_pkey_new($configArgs);

                 // Restore old env var (best effort)
                 if ($oldEnv !== false) {
                     putenv("OPENSSL_CONF={$oldEnv}");
                 } else {
                     putenv("OPENSSL_CONF"); // Unset
                 }
             }
        }

        if (!$res) {
            $sslErr = '';
            while ($msg = \openssl_error_string()) {
                $sslErr .= $msg . '; ';
            }
            // If no specific OpenSSL error string, it might be the "Unsupported key type" which we suppressed
            if (empty($sslErr)) {
                $sslErr = "Unsupported key type or configuration error.";
            }

            // Return a clean error message urging manual generation
            return ['error' => 'Auto-generation failed (' . trim($sslErr, '; ') . '). Please use the Manual Generation link below.'];
        }

        $exported = @\openssl_pkey_export($res, $privKeyPEM, null, $configArgs);
        if (!$exported) {
             $sslErr = '';
            while ($msg = \openssl_error_string()) {
                $sslErr .= $msg . '; ';
            }
            return ['error' => 'OpenSSL export failed: ' . $sslErr];
        }

        $keyDetails = \openssl_pkey_get_details($res);
        $pubKeyPEM = $keyDetails['key'];

        return [
            'publicKey' => self::pemToBin($pubKeyPEM, true),
            'privateKey' => self::pemToBin($privKeyPEM, false)
        ];
    }

    private static function pemToBin($pem, $isPublic)
    {
        // Remove PEM headers and newlines
        $pem = preg_replace('/-{5}.*-{5}/', '', $pem);
        $pem = str_replace(["\r", "\n", " "], '', $pem);

        // Decode base64
        $bin = base64_decode($pem);

        if ($isPublic) {
            // Public Key (SubjectPublicKeyInfo)
            // Look for the 65-byte uncompressed point starting with 0x04.
            // It is usually at the end of the structure.
            if (strlen($bin) > 65) {
                 $possibleKey = substr($bin, -65);
                 if (ord($possibleKey[0]) == 0x04) {
                     return self::base64UrlEncode($possibleKey);
                 }
            }
        } else {
            // Private Key (ECPrivateKey)
            // Structure: Sequence -> Version -> Octet String (Private Key, 32 bytes)
            // We search for the pattern: 04 20 [32 bytes]
            // 0x04 is Octet String tag, 0x20 is length (32 decimal).
            $hex = bin2hex($bin);
            if (preg_match('/0420([0-9a-f]{64})/', $hex, $matches)) {
                return self::base64UrlEncode(hex2bin($matches[1]));
            }
        }

        return '';
    }

    public static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
