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

    // Reusable search function: search across multiple columns in any table
    public static function searchData($table, $searchColumns, $searchWord, $additionalConditions = '', $orderBy = '') {
        global $conn;
        
        if (empty($table) || empty($searchColumns) || empty($searchWord)) {
            return false;
        }

        // Build search conditions for multiple columns
        $searchConditions = [];
        foreach ($searchColumns as $column) {
            $searchConditions[] = "`$column` LIKE '%" . $conn->real_escape_string($searchWord) . "%'";
        }
        
        $searchClause = implode(' OR ', $searchConditions);
        
        // Build the complete query
        $sql = "SELECT * FROM `$table` WHERE ($searchClause)";
        
        // Add additional conditions if provided
        if (!empty($additionalConditions)) {
            $sql .= " AND ($additionalConditions)";
        }
        
        // Add order by if provided
        if (!empty($orderBy)) {
            $sql .= " ORDER BY $orderBy";
        }
        
        $result = $conn->query($sql);
        
        if (!$result) {
            return false;
        }
        
        // Return data as array
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        return $data;
    }

    // Get total count for search results (useful for pagination or showing "no results found")
    public static function getSearchCount($table, $searchColumns, $searchWord, $additionalConditions = '') {
        global $conn;
        
        if (empty($table) || empty($searchColumns) || empty($searchWord)) {
            return 0;
        }

        // Build search conditions for multiple columns
        $searchConditions = [];
        foreach ($searchColumns as $column) {
            $searchConditions[] = "`$column` LIKE '%" . $conn->real_escape_string($searchWord) . "%'";
        }
        
        $searchClause = implode(' OR ', $searchConditions);
        
        // Build the complete query
        $sql = "SELECT COUNT(*) as total FROM `$table` WHERE ($searchClause)";
        
        // Add additional conditions if provided
        if (!empty($additionalConditions)) {
            $sql .= " AND ($additionalConditions)";
        }
        
        $result = $conn->query($sql);
        
        if (!$result) {
            return 0;
        }
        
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }
} 