<?php

namespace App\Helpers;


class Area
{
    public static function short(string $area): string
    {
        $areas = explode('-', $area);
        $area = '';

        foreach ($areas as $area_prefix)
            $area .= mb_substr($area_prefix, 0, 1);
            
        $area = mb_strtoupper($area) . 'АО';

        return $area;
    }


}
