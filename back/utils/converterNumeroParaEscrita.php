<?php
function converterNumeroParaEscrita($number)
{

    $hyphen      = '-';
    $conjunction = ' e ';
    $separator   = ', ';
    $negative    = 'menos ';
    $decimal     = ' ponto ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'um',
        2                   => 'dois',
        3                   => 'três',
        4                   => 'quatro',
        5                   => 'cinco',
        6                   => 'seis',
        7                   => 'sete',
        8                   => 'oito',
        9                   => 'nove',
        10                  => 'dez',
        11                  => 'onze',
        12                  => 'doze',
        13                  => 'treze',
        14                  => 'quatorze',
        15                  => 'quinze',
        16                  => 'dezesseis',
        17                  => 'dezessete',
        18                  => 'dezoito',
        19                  => 'dezenove',
        20                  => 'vinte',
        30                  => 'trinta',
        40                  => 'quarenta',
        50                  => 'cinquenta',
        60                  => 'sessenta',
        70                  => 'setenta',
        80                  => 'oitenta',
        90                  => 'noventa',
        100                 => 'cento',
        200                 => 'duzentos',
        300                 => 'trezentos',
        400                 => 'quatrocentos',
        500                 => 'quinhentos',
        600                 => 'seiscentos',
        700                 => 'setecentos',
        800                 => 'oitocentos',
        900                 => 'novecentos',
        1000                => 'mil',
        1000000             => array('milhão', 'milhões'),
        1000000000          => array('bilhão', 'bilhões'),
        1000000000000       => array('trilhão', 'trilhões'),
        1000000000000000    => array('quatrilhão', 'quatrilhões'),
        1000000000000000000 => array('quinquilhão', 'quinquilhões')
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'converterNumeroParaEscrita só aceita números entre ' . PHP_INT_MAX . ' à ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . converterNumeroParaEscrita(abs($number));
    }

    $string = $fraction = null;
    
    
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $conjunction . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = floor($number / 100) * 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds];
            if ($remainder) {
                $string .= $conjunction . converterNumeroParaEscrita($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            if ($baseUnit == 1000) {
                $string = converterNumeroParaEscrita($numBaseUnits) . ' ' . $dictionary[1000];
            } elseif ($numBaseUnits == 1) {
                $string = converterNumeroParaEscrita($numBaseUnits) . ' ' . $dictionary[$baseUnit][0];
            } else {
                $string = converterNumeroParaEscrita($numBaseUnits) . ' ' . $dictionary[$baseUnit][1];
            }
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= converterNumeroParaEscrita($remainder);
            }
            break;
    }
    
    if (null !== $fraction && is_numeric($fraction) && ($fraction != "00" || $fraction != 00)) {
        $string .= $decimal;
        // $words = array();
        // foreach (str_split((string) $fraction) as $number) {
        //     $words[] = $dictionary[$number];
        // }
        // $string .= implode(' ', $words);

        switch (true) {
            case $fraction < 21:
                $string .= $dictionary[$fraction];
                break;
            case $fraction < 100:
                $tens   = ((int) ($fraction / 10)) * 10;
                $units  = $fraction % 10;
                $string .= $dictionary[$tens];
                if ($units) {
                    $string .= $conjunction . $dictionary[$units];
                }
                break;
            case $fraction < 1000:
                $hundreds  = floor($fraction / 100) * 100;
                $remainder = $fraction % 100;
                $string .= $dictionary[$hundreds];
                if ($remainder) {
                    $string .= $conjunction . converterNumeroParaEscrita($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($fraction, 1000)));
                $numBaseUnits = (int) ($fraction / $baseUnit);
                $remainder = $fraction % $baseUnit;
                if ($baseUnit == 1000) {
                    $string .= converterNumeroParaEscrita($numBaseUnits) . ' ' . $dictionary[1000];
                } elseif ($numBaseUnits == 1) {
                    $string .= converterNumeroParaEscrita($numBaseUnits) . ' ' . $dictionary[$baseUnit][0];
                } else {
                    $string .= converterNumeroParaEscrita($numBaseUnits) . ' ' . $dictionary[$baseUnit][1];
                }
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= converterNumeroParaEscrita($remainder);
                }
                break;
        }
    }

    return $string;
}
