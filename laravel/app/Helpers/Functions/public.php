<?php

use App\Models\Domain;

/**
 * @return mixed
 */
function get_domains()
{
    return Domain::where('inactive', '=', '0')->get();
}

/**
 * @return array
 */
function get_menus(): array
{
    return [
        'domains' => get_domains(),
    ];
}

/**
 * @param $name
 * @param string $folder
 * @return string
 */
function get_domain_storage_path($name, string $folder = ''): string
{
    return $name . ($folder ? DIRECTORY_SEPARATOR . $folder : null);
}

/**
 * @param $size
 * @param string $unit
 * @return string
 */
function human_readable_file_size($size, string $unit = ''): string
{
    if ((!$unit && $size >= 1 << 30) || $unit == 'گیگابایت')
    {
        return number_format($size / (1 << 30), 2) . ' ' . 'گیگابایت';
    }
    if ((!$unit && $size >= 1 << 20) || $unit == 'مگابایت')
    {
        return number_format($size / (1 << 20), 2) . ' ' . 'مگابایت';
    }
    if ((!$unit && $size >= 1 << 10) || $unit == 'کیلوبایت')
    {
        return number_format($size / (1 << 10), 2) . ' ' . 'کیلوبایت';
    }

    return number_format($size) . ' ' . 'بایت';
}

/**
 * @param $row
 * @param $count
 * @return string
 */
function auto_row_numbering($row, $count): string
{
    if ($count < 10)
    {
        $length = 1;
    } elseif ($count < 100)
    {
        $length = 2;
    } elseif ($count < 1000)
    {
        $length = 3;
    } else
    {
        $length = 1;
    }

    return str_pad($row + 1, $length, '0', STR_PAD_LEFT);
}
