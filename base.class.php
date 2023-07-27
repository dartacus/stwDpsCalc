<?php

class base
{

    /**
     * Can a var be iterated over
     * @param $var
     * @return bool
     */
    public function canLoop($var)
    {

        return ($var && is_array($var) && count($var) > 0);

    }

    public function roundDownToNearestPointFive($x)
    {

        $x = floor($x * 2) / 2;

        return $x;

    }

    public
    function makeIntArraySafe(array $intArray)
    {

        //make array safe
        $safeIntArr = array();

        foreach ($intArray as $elem) {

            if ($elem !== false && $elem != "") {

                $safeIntArr[] = intval($elem);

            }

        }

        return $safeIntArr;

    }

    public
    function makeFloatArraySafe(array $floatArray)
    {

        //make array safe
        $safeFloatArr = array();

        foreach ($floatArray as $elem) {

            if ($elem !== false && $elem != "") {

                $safeFloatArr[] = floatval($elem);

            }

        }

        return $safeFloatArr;

    }

    /**
     * Wrapper for number_format which intercepts any strings passed in
     * @param unknown $stringOrDecimal
     * @param number $precision
     * @return unknown|string
     */
    public function safe_number_format($stringOrDecimal, $precision = 2, $noCommas = false)
    {

        if (is_numeric($stringOrDecimal) && $stringOrDecimal == 0 && $precision == 2) {

            return "0.00";

        }

        //filter out very small numbers which can cause rounding errors and display oddly
        if (is_numeric($stringOrDecimal) && abs($stringOrDecimal) < 0.0001) {

            return "0.00";

        }

        if ($stringOrDecimal == "" || preg_match("/[a-z]/i", $stringOrDecimal)) {

            //it's a string - return it as is
            return $stringOrDecimal;

        }

        //process numbers as strings, as long as they are just floats
        if (!is_numeric($stringOrDecimal) && preg_match("/^[0-9]*\.[0-9]*$/i", $stringOrDecimal)) {

            if (strpos($stringOrDecimal, ",") > 0) {

                $stringOrDecimal = str_replace(",", "", $stringOrDecimal);

            }

            $stringOrDecimal = floatval($stringOrDecimal);

        }

        //otherwise, process as a decimal
        if (strpos($stringOrDecimal, ",") > 0) {

            $stringOrDecimal = str_replace(",", "", $stringOrDecimal);

        }

        if ($noCommas) {

            return number_format($stringOrDecimal, $precision, '.', '');

        } else {

            return number_format($stringOrDecimal, $precision);

        }

    }

}

?>
