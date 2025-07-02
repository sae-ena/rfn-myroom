<?php
session_start();
// Handle all POST actions and redirects FIRST
require_once('../helperFunction/helpers.php');
require_once "dbConnect.php";

// Bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['delete_ids'])) {
    $ids = array_map('intval', $_POST['delete_ids']);
    $action = $_POST['bulk_action'];
    $success = false;
    $msg = '';
    if ($action === 'activate') {
        $in = implode(',', $ids);
        $sql = "UPDATE form_managers SET status=1 WHERE form_id IN ($in)";
        $success = $conn->query($sql);
        $msg = $success ? 'Selected forms activated successfully.' : 'Failed to activate selected forms.';
    } elseif ($action === 'inactivate') {
        $in = implode(',', $ids);
        $sql = "UPDATE form_managers SET status=0 WHERE form_id IN ($in)";
        $success = $conn->query($sql);
        $msg = $success ? 'Selected forms inactivated successfully.' : 'Failed to inactivate selected forms.';
    } elseif ($action === 'delete') {
        $in = implode(',', $ids);
        $sql = "DELETE FROM form_managers WHERE form_id IN ($in)";
        $success = $conn->query($sql);
        $msg = $success ? 'Selected forms deleted successfully.' : 'Failed to delete selected forms.';
    }
    $_SESSION['popupMessage'] = $msg;
    $_SESSION['popupType'] = $success ? 'success' : 'error';
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle status toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statusChange']) && isset($_POST['statusValue'])) {
    $formId = $_POST['statusChange'];
    $currentStatus = intval($_POST['statusValue']);
    $newStatus = $currentStatus ? 0 : 1;
    $sql = "UPDATE form_managers SET status=? WHERE form_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $newStatus, $formId);
    if ($stmt->execute()) {
        $_SESSION['popupMessage'] = "Form status updated successfully.";
        $_SESSION['popupType'] = "success";
    } else {
        $_SESSION['popupMessage'] = "Failed to update form status.";
        $_SESSION['popupType'] = "error";
    }
    $stmt->close();
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Get popup message from session and clear it
$popupMessage = $_SESSION['popupMessage'] ?? '';
$popupType = $_SESSION['popupType'] ?? '';
unset($_SESSION['popupMessage'], $_SESSION['popupType']);

// Now include files that output HTML
require_once "leftSidebar.php";

// Search logic
$search = $_GET['title'] ?? '';
$search = convertToNullIfEmpty($search);
$where = [];
if ($search) {
    $searchEsc = $conn->real_escape_string($search);
    $where[] = "form_name LIKE '%$searchEsc%'";
}

// Status filter
$status = isset($_GET['status']) ? $_GET['status'] : '';
if ($status !== '') {
    if ($status === 'active') {
        $where[] = "status = 1";
    } elseif ($status === 'inActive') {
        $where[] = "status = 0";
    }
}

$whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

// Fetch forms with search and filters
$forms = [];
$sql = "SELECT * FROM form_managers $whereSql ORDER BY updated_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $forms[] = $row;
    }
}

// Prepare table data for reusable component
$tableTitle = "DynaForm Manager";
$addUrl = "../dynaform/dynaform.php";
$searchPlaceholder = "Search forms...";
$tableHeaders = ['S.N', 'Title', 'Status', 'Updated Date', 'Action'];
$tableRows = [];

foreach ($forms as $index => $form) {
    $formattedDate = date("Y-m-d", strtotime($form['updated_at']));
    
    // Status toggle switch
    $isActive = $form['status'] == 1;
    $statusButton = '<form method="POST" style="display:inline;" onsubmit="event.stopPropagation();">'
        . '<input type="hidden" name="statusChange" value="' . $form['form_id'] . '" />'
        . '<input type="hidden" name="statusValue" value="' . $form['status'] . '" />'
        . '<label class="switch ' . ($isActive ? 'switch-active' : 'switch-inactive') . '">' 
        . '<input type="checkbox" name="toggleStatus" onchange="this.form.submit()" ' . ($isActive ? 'checked' : '') . '>'
        . '<span class="slider round"></span>'
        . '</label>'
        . '</form>';
    
    // Action buttons
    $actionButtons = '<a href="../dynaform/dynaform.php?formId=' . $form['form_id'] . '">
        <button class="edit-button" style="background-color:rgb(9, 184, 253);">Edit</button>
    </a>
    <a href="../dynaform/form.php?formid=' . $form['form_id'] . '">
        <button class="edit-button">View Form</button>
    </a>';
    
    $tableRows[] = [
        'id' => $form['form_id'],
        'S.N' => $index + 1,
        'Title' => $form['form_name'],
        'Status' => $statusButton,
        'Updated Date' => $formattedDate,
        'Action' => $actionButtons
    ];
}

// Include the reusable table component inside dashboard-content for proper alignment
?>
<div class="dashboard-content">
<?php
include "tableTemplate.php";
?>
</div>

<script>
// Bulk action submission
function submitBulkAction(action) {
    const checkboxes = document.querySelectorAll('input[name="delete_ids[]"]:checked');
    if (checkboxes.length === 0) {
        alert('Please select at least one form to perform this action.');
        return;
    }
    
    if (action === 'delete' && !confirm('Are you sure you want to delete the selected forms?')) {
        return;
    }
    
    document.getElementById('bulk_action').value = action;
    document.getElementById('deleteForm').submit();
}

// Toggle all checkboxes
function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('input[name="delete_ids[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}
</script>
</body>
</html>
