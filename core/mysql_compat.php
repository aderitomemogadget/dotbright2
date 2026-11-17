<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit;
}

/* Compatibility for isolated legacy admin tools that still call mysql_*. */
if (!function_exists('mysql_connect')) {
    if (!defined('MYSQL_ASSOC')) define('MYSQL_ASSOC', MYSQLI_ASSOC);
    if (!defined('MYSQL_NUM')) define('MYSQL_NUM', MYSQLI_NUM);
    if (!defined('MYSQL_BOTH')) define('MYSQL_BOTH', MYSQLI_BOTH);

    $GLOBALS['_legacy_mysql_default_link'] = null;

    function _legacy_mysql_link($link = null)
    {
        return $link ?: $GLOBALS['_legacy_mysql_default_link'];
    }

    function mysql_connect($server = null, $username = null, $password = null, $newLink = false, $clientFlags = 0)
    {
        mysqli_report(MYSQLI_REPORT_OFF);
        $link = @mysqli_connect($server, $username, $password);
        if ($link) $GLOBALS['_legacy_mysql_default_link'] = $link;
        return $link;
    }

    function mysql_pconnect($server = null, $username = null, $password = null, $clientFlags = 0)
    {
        $server = $server && strpos($server, 'p:') !== 0 ? 'p:'.$server : $server;
        return mysql_connect($server, $username, $password, false, $clientFlags);
    }

    function mysql_select_db($database, $link = null)
    {
        $link = _legacy_mysql_link($link);
        return $link ? @mysqli_select_db($link, $database) : false;
    }

    function mysql_query($query, $link = null)
    {
        $link = _legacy_mysql_link($link);
        return $link ? @mysqli_query($link, $query) : false;
    }

    function mysql_fetch_array($result, $resultType = MYSQL_BOTH) { return mysqli_fetch_array($result, $resultType); }
    function mysql_fetch_assoc($result) { return mysqli_fetch_assoc($result); }
    function mysql_num_rows($result) { return mysqli_num_rows($result); }
    function mysql_num_fields($result) { return mysqli_num_fields($result); }
    function mysql_data_seek($result, $rowNumber) { return mysqli_data_seek($result, $rowNumber); }
    function mysql_free_result($result) { mysqli_free_result($result); return true; }

    function mysql_insert_id($link = null)
    {
        $link = _legacy_mysql_link($link);
        return $link ? mysqli_insert_id($link) : false;
    }

    function mysql_affected_rows($link = null)
    {
        $link = _legacy_mysql_link($link);
        return $link ? mysqli_affected_rows($link) : -1;
    }

    function mysql_error($link = null)
    {
        $link = _legacy_mysql_link($link);
        return $link ? mysqli_error($link) : mysqli_connect_error();
    }

    function mysql_errno($link = null)
    {
        $link = _legacy_mysql_link($link);
        return $link ? mysqli_errno($link) : mysqli_connect_errno();
    }

    function mysql_real_escape_string($value, $link = null)
    {
        $link = _legacy_mysql_link($link);
        return $link ? mysqli_real_escape_string($link, $value) : addslashes($value);
    }

    function mysql_escape_string($value) { return addslashes($value); }

    function mysql_close($link = null)
    {
        $link = _legacy_mysql_link($link);
        if (!$link) return false;
        if ($GLOBALS['_legacy_mysql_default_link'] === $link) $GLOBALS['_legacy_mysql_default_link'] = null;
        return mysqli_close($link);
    }

    function mysql_set_charset($charset, $link = null)
    {
        $link = _legacy_mysql_link($link);
        return $link ? mysqli_set_charset($link, $charset) : false;
    }

    function mysql_get_server_info($link = null)
    {
        $link = _legacy_mysql_link($link);
        return $link ? mysqli_get_server_info($link) : false;
    }

    function mysql_stat($link = null)
    {
        $link = _legacy_mysql_link($link);
        return $link ? mysqli_stat($link) : false;
    }

    function mysql_list_fields($database, $table, $link = null)
    {
        $link = _legacy_mysql_link($link);
        if (!$link || !preg_match('/^[A-Za-z0-9_]+$/', $database) || !preg_match('/^[A-Za-z0-9_]+$/', $table)) return false;
        return mysqli_query($link, 'SELECT * FROM `'.$database.'`.`'.$table.'` LIMIT 0');
    }

    function mysql_field_name($result, $fieldOffset)
    {
        $field = mysqli_fetch_field_direct($result, $fieldOffset);
        return $field ? $field->name : false;
    }

    function mysql_field_table($result, $fieldOffset)
    {
        $field = mysqli_fetch_field_direct($result, $fieldOffset);
        return $field ? $field->table : false;
    }

    function mysql_field_len($result, $fieldOffset)
    {
        $field = mysqli_fetch_field_direct($result, $fieldOffset);
        return $field ? $field->length : false;
    }

    function mysql_field_type($result, $fieldOffset)
    {
        $field = mysqli_fetch_field_direct($result, $fieldOffset);
        if (!$field) return false;
        $types = array(
            MYSQLI_TYPE_TINY => 'int', MYSQLI_TYPE_SHORT => 'int', MYSQLI_TYPE_LONG => 'int',
            MYSQLI_TYPE_LONGLONG => 'int', MYSQLI_TYPE_INT24 => 'int', MYSQLI_TYPE_DECIMAL => 'real',
            MYSQLI_TYPE_NEWDECIMAL => 'real', MYSQLI_TYPE_FLOAT => 'real', MYSQLI_TYPE_DOUBLE => 'real',
            MYSQLI_TYPE_DATE => 'date', MYSQLI_TYPE_TIME => 'time', MYSQLI_TYPE_DATETIME => 'datetime',
            MYSQLI_TYPE_TIMESTAMP => 'timestamp', MYSQLI_TYPE_YEAR => 'year', MYSQLI_TYPE_BLOB => 'blob',
            MYSQLI_TYPE_TINY_BLOB => 'blob', MYSQLI_TYPE_MEDIUM_BLOB => 'blob', MYSQLI_TYPE_LONG_BLOB => 'blob',
        );
        return isset($types[$field->type]) ? $types[$field->type] : 'string';
    }
}
