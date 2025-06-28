<?php
require_once __DIR__ . '/../admin/dbConnect.php';

class FormTableHelper {
    // Bulk activate: set status='active' for given IDs
    public static function bulkActivate($table, $column, $ids, $statusColumn = 'status', $activeValue = 'active') {
        global $conn;
        if (empty($table) || empty($column) || empty($ids) || !is_array($ids)) return false;
        $idList = implode(",", array_map('intval', $ids));
        $sql = "UPDATE `$table` SET `$statusColumn`='" . $conn->real_escape_string($activeValue) . "' WHERE `$column` IN ($idList)";
        return $conn->query($sql);
    }

    // Bulk inactivate: set status='inActive' for given IDs
    public static function bulkInactivate($table, $column, $ids, $statusColumn = 'status', $inactiveValue = 'inActive') {
        global $conn;
        if (empty($table) || empty($column) || empty($ids) || !is_array($ids)) return false;
        $idList = implode(",", array_map('intval', $ids));
        $sql = "UPDATE `$table` SET `$statusColumn`='" . $conn->real_escape_string($inactiveValue) . "' WHERE `$column` IN ($idList)";
        return $conn->query($sql);
    }

    // Bulk soft delete: set deleted_at=NOW() for given IDs
    public static function bulkDelete($table, $column, $ids) {
        global $conn;
        if (empty($table) || empty($column) || empty($ids) || !is_array($ids)) return false;
        $idList = implode(",", array_map('intval', $ids));
        $sql = "UPDATE `$table` SET deleted_at=NOW() WHERE `$column` IN ($idList)";
        return $conn->query($sql);
    }
} 