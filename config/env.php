<?php
// Loader minimalista de .env — sem dependencias externas.
// Formato: CHAVE=valor (comentarios com #, aspas opcionais).

if (!function_exists('env')) {
    function env($chave, $padrao = null) {
        static $carregado = false;
        static $vars = [];

        if (!$carregado) {
            $carregado = true;
            $caminho = __DIR__ . '/../.env';
            if (is_file($caminho)) {
                foreach (file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
                    $linha = trim($linha);
                    if ($linha === '' || $linha[0] === '#') continue;
                    $pos = strpos($linha, '=');
                    if ($pos === false) continue;
                    $k = trim(substr($linha, 0, $pos));
                    $v = trim(substr($linha, $pos + 1));
                    if (strlen($v) >= 2 && ($v[0] === '"' || $v[0] === "'") && substr($v, -1) === $v[0]) {
                        $v = substr($v, 1, -1);
                    }
                    $vars[$k] = $v;
                }
            }
        }

        if (array_key_exists($chave, $vars)) return $vars[$chave];
        $envVar = getenv($chave);
        if ($envVar !== false) return $envVar;
        return $padrao;
    }
}
