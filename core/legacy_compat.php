<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit;
}

require_once __DIR__.'/mysql_compat.php';

/*
 * Temporary compatibility bridge for legacy modules on PHP 8.
 * Every function is declared only when the runtime no longer provides it,
 * so the production PHP 7 runtime keeps its native behaviour.
 */

if (!function_exists('each')) {
    function each(&$array)
    {
        $key = key($array);
        if ($key === null) {
            return false;
        }
        $value = current($array);
        next($array);
        return array(0 => $key, 1 => $value, 'key' => $key, 'value' => $value);
    }
}

if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc()
    {
        return false;
    }
}

if (!function_exists('get_magic_quotes_runtime')) {
    function get_magic_quotes_runtime()
    {
        return false;
    }
}

if (!function_exists('set_magic_quotes_runtime')) {
    function set_magic_quotes_runtime($newSetting)
    {
        return false;
    }
}

/*
 * TCPDF 5.x still builds six callbacks with create_function().  PHP 8 removed
 * that API.  Keep this bridge deliberately restricted to the exact callback
 * bodies used by the bundled TCPDF version; never evaluate arbitrary PHP.
 */
if (!function_exists('create_function')) {
    function create_function($arguments, $body)
    {
        if (trim($arguments) !== '$matches') {
            throw new LogicException('Unsupported legacy callback signature');
        }

        if (strpos($body, 'global $x_diff, $w_diff;') !== false) {
            return function ($matches) {
                global $x_diff, $w_diff;
                $newx = sprintf('%F', floatval($matches[1]) + $x_diff);
                $neww = sprintf('%F', floatval($matches[3]) + $w_diff);
                return $newx.' '.$matches[2].' '.$neww.' '.$matches[4].' x*#!#*x'.$matches[5].$matches[6];
            };
        }

        if (strpos($body, '$newx1 = sprintf') !== false) {
            return function ($matches) {
                global $spacew;
                $newx1 = sprintf('%F', floatval($matches[1]) + $spacew);
                $newx2 = sprintf('%F', floatval($matches[3]) + $spacew);
                $newx3 = sprintf('%F', floatval($matches[5]) + $spacew);
                return $newx1.' '.$matches[2].' '.$newx2.' '.$matches[4].' '.$newx3.' '.$matches[6].' x*#!#*x'.$matches[7].$matches[8];
            };
        }

        if (strpos($body, 'str_replace("#!#OP#!#"') !== false) {
            return function ($matches) {
                global $spacew;
                $matches[1] = str_replace('#!#OP#!#', '(', $matches[1]);
                $matches[1] = str_replace('#!#CP#!#', ')', $matches[1]);
                return '[('.str_replace(chr(0).chr(32), ') '.sprintf('%F', $spacew).' (', $matches[1]).')]';
            };
        }

        if (strpos($body, 'global $spacew;') !== false) {
            return function ($matches) {
                global $spacew;
                $newx = sprintf('%F', floatval($matches[1]) + $spacew);
                return $newx.' '.$matches[2].' x*#!#*x'.$matches[3].$matches[4];
            };
        }

        if (strpos($body, 'global $jfrompage, $jtopage;') !== false) {
            return function ($matches) {
                global $jfrompage, $jtopage;
                $pagenum = intval($matches[3]) + 1;
                if (($pagenum >= $jtopage) && ($pagenum < $jfrompage)) {
                    $newpage = $pagenum + 1;
                } elseif ($pagenum == $jfrompage) {
                    $newpage = $jtopage;
                } else {
                    $newpage = $pagenum;
                }
                --$newpage;
                return "this.addField('".$matches[1]."','".$matches[2]."',".$newpage;
            };
        }

        if (strpos($body, 'global $jpage;') !== false) {
            return function ($matches) {
                global $jpage;
                $pagenum = intval($matches[3]) + 1;
                if ($pagenum >= $jpage) {
                    $newpage = $pagenum - 1;
                } elseif ($pagenum == $jpage) {
                    $newpage = 1;
                } else {
                    $newpage = $pagenum;
                }
                --$newpage;
                return "this.addField('".$matches[1]."','".$matches[2]."',".$newpage;
            };
        }

        throw new LogicException('Unsupported legacy create_function callback');
    }
}

/* APC was replaced by APCu; PHPExcel 1.8 still calls the old user-cache API. */
if (!function_exists('apc_store') && function_exists('apcu_store')) {
    function apc_store($key, $value, $ttl = 0)
    {
        return apcu_store($key, $value, $ttl);
    }
}

if (!function_exists('apc_fetch') && function_exists('apcu_fetch')) {
    function apc_fetch($key, &$success = null)
    {
        return apcu_fetch($key, $success);
    }
}

if (!function_exists('apc_delete') && function_exists('apcu_delete')) {
    function apc_delete($key)
    {
        return apcu_delete($key);
    }
}

if (!function_exists('apc_sma_info') && function_exists('apcu_sma_info')) {
    function apc_sma_info($limited = false)
    {
        return apcu_sma_info($limited);
    }
}

if (!function_exists('_legacy_posix_pattern')) {
    function _legacy_posix_pattern($pattern, $caseInsensitive = false)
    {
        return '~'.str_replace('~', '\\~', $pattern).'~'.($caseInsensitive ? 'i' : '');
    }
}

if (!function_exists('ereg')) {
    function ereg($pattern, $string, &$registers = null)
    {
        $matched = preg_match(_legacy_posix_pattern($pattern), $string, $matches);
        if (!$matched) {
            return false;
        }
        $registers = $matches;
        return max(1, strlen($matches[0]));
    }
}

if (!function_exists('eregi')) {
    function eregi($pattern, $string, &$registers = null)
    {
        $matched = preg_match(_legacy_posix_pattern($pattern, true), $string, $matches);
        if (!$matched) {
            return false;
        }
        $registers = $matches;
        return max(1, strlen($matches[0]));
    }
}

if (!function_exists('ereg_replace')) {
    function ereg_replace($pattern, $replacement, $string)
    {
        return preg_replace(_legacy_posix_pattern($pattern), $replacement, $string);
    }
}

if (!function_exists('eregi_replace')) {
    function eregi_replace($pattern, $replacement, $string)
    {
        return preg_replace(_legacy_posix_pattern($pattern, true), $replacement, $string);
    }
}

if (!function_exists('split')) {
    function split($pattern, $string, $limit = -1)
    {
        return preg_split(_legacy_posix_pattern($pattern), $string, $limit ?: -1);
    }
}

if (!function_exists('spliti')) {
    function spliti($pattern, $string, $limit = -1)
    {
        return preg_split(_legacy_posix_pattern($pattern, true), $string, $limit ?: -1);
    }
}
