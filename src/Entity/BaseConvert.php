<?php


namespace App\Entity;


class BaseConvert {
    /** Base 16. */
    const BASE16 = '0123456789abcdef';
    /** Base 54, same as base 62 (0-9a-zA-Z) without characters '0oO1iIlL'. */
    const BASE54 = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
    /**
     * Convert a number from any base to any other base.
     * @param int|string $value The number to convert.
     * @param string $inDigits The input base's digits.
     * @param string $outDigits The output base's digits.
     * @return string The converted number.
     * @throws Exception If a digit is outside the base.
     * @see http://www.technischedaten.de/pmwiki2/pmwiki.php?n=Php.BaseConvert
     */
    static public function convertBase($value, $inDigits, $outDigits) {
        $inBase = strlen($inDigits);
        $outBase = strlen($outDigits);
        $decimal = '0';
        $level = 0;
        $result = '';
        $value = trim((string)$value, "\r\n\t +");
        $signe = ($value{0} === '-') ? '-' : '';
        $value = ltrim($value, '-0');
        $len = strlen($value);
        for ($i = 0; $i < $len; $i++) {
            $newValue = strpos($inDigits, $value{$len - 1 - $i});
            if ($newValue === false)
                throw new \Exception('Bad Char in input 1', E_USER_ERROR);
            if ($newValue >= $inBase)
                throw new \Exception('Bad Char in input 2', E_USER_ERROR);
            $decimal = bcadd($decimal, bcmul(bcpow($inBase, $i), $newValue));
        }
        if ($outBase == 10)
            return ($signe.$decimal); // shortcut
        while (bccomp(bcpow($outBase, $level++), $decimal) !== 1)
            ;
        for ($i = ($level - 2); $i >= 0; $i--) {
            $factor = bcpow($outBase, $i);
            $number = bcdiv($decimal, $factor, 0);
            $decimal = bcmod($decimal, $factor);
            $result .= $outDigits{$number};
        }
        $result = empty($result) ? '0' : $result;
        return ($signe.$result);
    }
}