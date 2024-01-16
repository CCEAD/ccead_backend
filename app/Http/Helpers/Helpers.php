<?php
    use Illuminate\Support\Facades\DB;

    function message($string)
    {
        $json = json_decode(file_get_contents(public_path() . '/message.json'));
        return $json->$string;
    }

    function fullTextWildcardsInitEnd($term)
    {
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);
 
        // $words = explode(' ', $term);
 
        // foreach($words as $key => $word) {
        //     if(strlen($word) >= 1) {
        //         $words[$key] = '+' . $word . '*';
        //     }
        // }
 
        // $searchTerm = implode( ' ', $words);
        // $searchTerm = '+' . $term . '*';
 
        // return $searchTerm;
        if(strlen($term) >= 3) {
            $searchTerm = '"+' . $term . '*"';
        } else if(strlen($term) === 0) {
            $searchTerm = '';
        } else {
            $searchTerm = '+' . $term . '*';
        }

        return $searchTerm;
    }

    function gen_uuid($len=8) {

        $hex = md5("ccead" . uniqid("", true));
    
        $pack = pack('H*', $hex);
        $tmp =  base64_encode($pack);
    
        $uid = preg_replace("#(*UTF8)[^A-Za-z0-9]#", "", $tmp);
    
        $len = max(4, min(128, $len));
    
        while (strlen($uid) < $len)
            $uid .= gen_uuid(22);
    
        return substr($uid, 0, $len);
    }

    function mask_telefono($number)
    {
        return substr($number, 0, 1) . '****' . substr($number, -3);
    }

    function select_ubigeos_disponibles($cajas)
    {
        $total = collect($cajas)->count();
        $ubigeos = DB::table('ubigeos')->where('estado', false)->limit($total)->get();

        return $ubigeos;
    }

    function get_user_agencia()
    {
        return auth()->user()->agencia_id;
    }

    function verificar_estado_salida($salida)
    {
        if (!$salida->estado == 0) {
            return true;
        }
    }

    function verificar_estado_ingreso($ingreso)
    {
        if (!$ingreso->estado == 0) {
            return true;
        }
    }

    function verificar_agencia()
    {
        if(getPermissionsTeamId() == 1) {
            return true;
        }

        return false;
    }
?>