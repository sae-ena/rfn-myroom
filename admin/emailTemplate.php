<!DOCTYPE html>
<?php
session_start();
// Handle all POST actions and redirects FIRST
require('../helperFunction/helpers.php');
require "dbConnect.php";
?>
<head>
<link rel="icon" href="data:,">
<!-- <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script> -->
<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/cckd9abl4v6grfo5d4t8yo9d26pfqvb0ds95y4of6160brqd/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<?php
// Bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['delete_ids'])) {
    $ids = array_map('intval', $_POST['delete_ids']);
    $action = $_POST['bulk_action'];
    $success = false;
    $msg = '';
    if ($action === 'activate') {
        $in = implode(',', $ids);
        $sql = "UPDATE email_templates SET status=1 WHERE id IN ($in)";
        $success = $conn->query($sql);
        $msg = $success ? 'Selected templates activated successfully.' : 'Failed to activate selected templates.';
    } elseif ($action === 'inactivate') {
        $in = implode(',', $ids);
        $sql = "UPDATE email_templates SET status=0 WHERE id IN ($in)";
        $success = $conn->query($sql);
        $msg = $success ? 'Selected templates inactivated successfully.' : 'Failed to inactivate selected templates.';
    } elseif ($action === 'delete') {
        $in = implode(',', $ids);
        $sql = "DELETE FROM email_templates WHERE id IN ($in)";
        $success = $conn->query($sql);
        $msg = $success ? 'Selected templates deleted successfully.' : 'Failed to delete selected templates.';
    }
    $_SESSION['popupMessage'] = $msg;
    $_SESSION['popupType'] = $success ? 'success' : 'error';
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle Add/Edit Form Submission (status toggle)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statusChange']) && isset($_POST['statusValue'])) {
    $template_id = intval($_POST['statusChange']);
    $current_status = intval($_POST['statusValue']);
    $new_status = $current_status ? 0 : 1;
    $sql = "UPDATE email_templates SET status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $new_status, $template_id);
    if ($stmt->execute()) {
        $_SESSION['popupMessage'] = "Status updated successfully.";
        $_SESSION['popupType'] = "success";
    } else {
        $_SESSION['popupMessage'] = "Failed to update status.";
        $_SESSION['popupType'] = "error";
    }
    $stmt->close();
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle Add/Edit Form Submission (add/edit template)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_title']) && isset($_POST['slug'])) {
    $slug = trim($_POST['slug']);
    $subject_title = trim($_POST['subject_title']);
    $user_message = $_POST['user_message']; // Save HTML as-is
    $admin_mail = trim($_POST['admin_mail']);
    $admin_message = $_POST['admin_message']; // Save HTML as-is
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1;
    $now = date('Y-m-d H:i:s');

    // Validation
    $validationErrors = [];
    if ($slug === '') {
        $validationErrors[] = 'Slug is required.';
    }
    if ($subject_title === '') {
        $validationErrors[] = 'Subject Title is required.';
    }
    if (trim(strip_tags($user_message)) === '') {
        $validationErrors[] = 'User Message is required.';
    }
    if ($admin_mail !== '' && !filter_var($admin_mail, FILTER_VALIDATE_EMAIL)) {
        $validationErrors[] = 'Admin Mail must be a valid email address.';
    }
    // Check for unique slug
    if ($slug !== '') {
        $slugEscaped = mysqli_real_escape_string($conn, $slug);
        $slugCheckSql = "SELECT id FROM email_templates WHERE slug = '$slugEscaped'";
        if (isset($_POST['template_id']) && $_POST['template_id'] !== '') {
            $template_id = intval($_POST['template_id']);
            $slugCheckSql .= " AND id != $template_id";
        }
        $slugCheckResult = $conn->query($slugCheckSql);
        if ($slugCheckResult && $slugCheckResult->num_rows > 0) {
            $validationErrors[] = 'Slug must be unique. This slug is already used.';
        }
    }

    if (!empty($validationErrors)) {
        $_SESSION['popupMessage'] = '<ul style="margin:0 0 0 18px;padding:0 0 0 10px;">';
        foreach ($validationErrors as $err) {
            $_SESSION['popupMessage'] .= '<li style="color:#b71c1c;font-size:1.05em;line-height:1.7;">' . htmlspecialchars($err) . '</li>';
        }
        $_SESSION['popupMessage'] .= '</ul>';
        $_SESSION['popupType'] = 'error';
    } else {
        if (isset($_POST['template_id']) && $_POST['template_id'] !== '') {
            // Edit
            $template_id = intval($_POST['template_id']);
            $sql = "UPDATE email_templates SET subject_title=?, slug=?, user_message=?, admin_mail=?, admin_message=?, status=?,  updated_at=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssisi", $subject_title, $slug, $user_message, $admin_mail, $admin_message, $status, $now, $template_id);
            if ($stmt->execute()) {
                $_SESSION['popupMessage'] = "Email template updated successfully.";
                $_SESSION['popupType'] = "success";
            } else {
                $_SESSION['popupMessage'] = "Failed to update email template.";
                $_SESSION['popupType'] = "error";
            }
            $stmt->close();
        } else {
            // Add
            $sql = "INSERT INTO email_templates (subject_title, slug, user_message, admin_mail, admin_message, status,  created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssissss", $subject_title, $slug, $user_message, $admin_mail, $admin_message, $status, $now, $now);
            if ($stmt->execute()) {
                $_SESSION['popupMessage'] = "Email template added successfully.";
                $_SESSION['popupType'] = "success";
            } else {
                $_SESSION['popupMessage'] = "Failed to add email template.";
                $_SESSION['popupType'] = "error";
            }
            $stmt->close();
        }
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// Handle Delete
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
if ($action === 'delete' && $id) {
    $sql = "DELETE FROM email_templates WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['popupMessage'] = "Email template deleted successfully.";
        $_SESSION['popupType'] = "success";
    } else {
        $_SESSION['popupMessage'] = "Failed to delete email template.";
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

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Search logic
$search = $_GET['title'] ?? '';
$search = convertToNullIfEmpty($search);
$where = [];
if ($search) {
    $searchEsc = $conn->real_escape_string($search);
    $where[] = "(subject_title LIKE '%$searchEsc%' OR slug LIKE '%$searchEsc%')";
}
// Add status filter logic
$status = isset($_GET['status']) ? $_GET['status'] : '';
if ($status !== '') {
    if ($status === 'active') {
        $where[] = "status = 1";
    } elseif ($status === 'inActive') {
        $where[] = "status = 0";
    }
}
$whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

// Count total templates (with search)
$totalSql = "SELECT COUNT(*) as total FROM email_templates $whereSql";
$totalResult = $conn->query($totalSql);
$totalTemplates = $totalResult ? (int)$totalResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalTemplates / $perPage);

// Fetch paginated templates (with search)
$templates = [];
$sql = "SELECT * FROM email_templates $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $templates[] = $row;
    }
}
// Fetch single template for edit
$editTemplate = null;
if ($action === 'edit' && $id) {
    $sql = "SELECT * FROM email_templates WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows === 1) {
        $editTemplate = $result->fetch_assoc();
    }
    $stmt->close();
}

// Prepare data for the modular table template
$tableTitle = 'Email Template Manager';
$addUrl = '#';
$showBulkActions = true;
$searchQuery = $_GET['title'] ?? '';
$searchPlaceholder = 'Search by Subject, Slug';
$tableHeaders = ['S.N', 'Subject', 'Slug', 'Status', 'Actions'];
$tableRows = [];
$key = 0;
foreach ($templates as $template) {
    $isActive = $template['status'] == 1;
    $statusSwitch = '<form method="POST" style="display:inline;" onsubmit="event.stopPropagation();">'
        . '<input type="hidden" name="statusChange" value="' . $template['id'] . '">' 
        . '<input type="hidden" name="statusValue" value="' . $template['status'] . '">' 
        . '<label class="switch ' . ($isActive ? 'switch-active' : 'switch-inactive') . '">' 
        . '<input type="checkbox" name="toggleStatus" onchange="this.form.submit()" ' . ($isActive ? 'checked' : '') . '>'
        . '<span class="slider round"></span>'
        . '</label>'
        . '</form>';
    $actions =
        '<button type="button" onclick="event.stopPropagation(); showEditModal(' . $template['id'] . ')" class="edit-button">Edit</button>'
        . '<button type="button" onclick="event.stopPropagation(); confirmDelete(' . $template['id'] . ')" class="delete-button">Delete</button>'
        . '<button type="button" onclick="event.stopPropagation(); showPreviewModal(' . $template['id'] . ')" class="view-button" title="Preview">'
        . '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="vertical-align:middle;">'
        . '<path stroke-width="2" d="M1.5 12s3.5-7 10.5-7 10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12z"/>'
        . '<circle cx="12" cy="12" r="3.5" stroke-width="2"/>'
        . '</svg>'
        . '</button>';
    $tableRows[] = [
        'sn' => ++$key,
        'subject_title' => htmlspecialchars($template['subject_title'] ?? ''),
        'slug' => htmlspecialchars($template['slug'] ?? ''),
        'status' => $statusSwitch,
        'action' => $actions
    ];
}
?>
<div class="dashboard-content">
   
    <div class="form-container">
        <script>
        // Make the Add New button in the table header trigger the modal
        document.addEventListener('DOMContentLoaded', function() {
            var addBtn = document.querySelector('.admin-btn-add');
            if (addBtn) {
                addBtn.onclick = function(e) { e.preventDefault(); showAddModal(); };
            }
        });
        </script>
        <?php
        include 'tableTemplate.php';
        ?>
        <!-- Pagination Controls -->
        <div style="margin-top:20px;text-align:center;">
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page-1 ?>" class="page-btn">&laquo; Prev</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="page-btn<?= $i == $page ? ' active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page+1 ?>" class="page-btn">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Add/Edit Modal -->
<div id="templateModal" class="modal">
    <div class="modal-content modern-modal compact-modal">
        <div class="modal-header">
            <h2 id="modalTitle">Add Email Template</h2>
            <span class="close" onclick="closeTemplateModal()">&times;</span>
        </div>
        <form id="templateForm" method="POST" action="emailTemplate.php">
            <input type="hidden" name="template_id" id="template_id">
            <div class="form-group">
                <label for="subject_title">Subject Title:</label>
                <input type="text" name="subject_title" id="subject_title" required class="input-field">
            </div>
            <div class="form-group">
                <label for="slug">Slug:</label>
                <input type="text" name="slug" id="slug" required class="input-field">
            </div>
            <div class="form-group">
                <label for="user_message">User Message:</label>
                <textarea name="user_message" id="user_message" rows="6" class="input-field"></textarea>
            </div>
            <div class="form-group">
                <label for="admin_mail">Admin Mail:</label>
                <input type="email" name="admin_mail" id="admin_mail" class="input-field">
            </div>
            <div class="form-group">
                <label for="admin_message">Admin Message:</label>
                <textarea name="admin_message" id="admin_message" rows="6" class="input-field"></textarea>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" id="status" class="input-field">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="edit-button modern-btn">Save Template</button>
            </div>
        </form>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content" style="background:#ffebee; max-width: 400px;">
        <span class="close" onclick="closeDeleteModal()">&times;</span>
        <h2 style="color:#b71c1c;">Delete Confirmation</h2>
        <p>Are you sure you want to delete this email template?</p>
        <button id="deleteConfirmBtn" class="delete-button">Delete</button>
        <button onclick="closeDeleteModal()" class="edit-button" style="background:#ccc; color:#222;">Cancel</button>
    </div>
</div>
<!-- Preview Modal -->
<div id="previewModal" class="modal">
    <div class="modal-content compact-modal" style="max-width:700px;">
        <span class="close" onclick="closePreviewModal()">&times;</span>
        <h2 style="margin-bottom:10px;">Email Template Preview</h2>
        <div id="previewUserMessage" style="border:1px solid #eee; padding:18px; margin-bottom:18px; background:#fafafa; border-radius:8px;"></div>
        <div id="previewAdminMessage" style="border:1px solid #eee; padding:18px; background:#f5f5f5; border-radius:8px;"></div>
    </div>
</div>
<script>

// Modal logic
function showAddModal() {
    document.getElementById('modalTitle').innerText = 'Add Email Template';
    document.getElementById('template_id').value = '';
    document.getElementById('subject_title').value = '';
    document.getElementById('admin_mail').value = '';
    document.getElementById('status').value = '1';
    document.getElementById('slug').value = '';
    document.getElementById('templateModal').style.display = 'block';
}
function showEditModal(id) {
    var templates = <?php echo json_encode($templates); ?>;
    var template = templates.find(t => t.id == id);
    if (template) {
        document.getElementById('modalTitle').innerText = 'Edit Email Template';
        document.getElementById('template_id').value = template.id;
        document.getElementById('subject_title').value = template.subject_title;
        document.getElementById('admin_mail').value = template.admin_mail;
        document.getElementById('status').value = template.status;
        document.getElementById('slug').value = template.slug;
        // Set TinyMCE content for user_message and admin_message
        if (tinymce.get('user_message')) {
            tinymce.get('user_message').setContent(template.user_message || '');
        }
        if (tinymce.get('admin_message')) {
            tinymce.get('admin_message').setContent(template.admin_message || '');
        }
        document.getElementById('templateModal').style.display = 'block';
    }
}
function closeTemplateModal() {
    document.getElementById('templateModal').style.display = 'none';
}
function confirmDelete(id) {
    document.getElementById('deleteModal').style.display = 'block';
    document.getElementById('deleteConfirmBtn').onclick = function() {
        window.location.href = 'emailTemplate.php?action=delete&id=' + id;
    };
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
function showPreviewModal(id) {
    var templates = <?php echo json_encode($templates); ?>;
    var template = templates.find(t => t.id == id);
    if (template) {
        document.getElementById('previewUserMessage').innerHTML = template.user_message;
        document.getElementById('previewAdminMessage').innerHTML = template.admin_message;
        document.getElementById('previewModal').style.display = 'block';
    }
}
function closePreviewModal() {
    document.getElementById('previewModal').style.display = 'none';
}
// Close modals if clicked outside
window.onclick = function(event) {
    var templateModal = document.getElementById('templateModal');
    var deleteModal = document.getElementById('deleteModal');
    var previewModal = document.getElementById('previewModal');
    if (event.target == templateModal) templateModal.style.display = 'none';
    if (event.target == deleteModal) deleteModal.style.display = 'none';
    if (event.target == previewModal) previewModal.style.display = 'none';
}
tinymce.init({
    selector: 'textarea',
    plugins: [
      // Core editing features
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
      // Your account includes a free trial of TinyMCE premium features
      // Try the most popular premium features until Jul 11, 2025:
      'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
  });
</script>
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0; top: 0; width: 100%; height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}
.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 30px 40px;
    border: 1px solid #e0e0e0;
    width: 90%;
    max-width: 800px;
    border-radius: 18px;
    box-shadow: 0px 8px 32px rgba(44,44,46,0.18), 0px 1.5px 6px rgba(0,0,0,0.08);
    position: relative;
    font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
    transition: box-shadow 0.2s;
}

/* Modern modal header */
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(90deg, #ff6600 60%, #ffb347 100%);
    padding: 18px 30px 12px 30px;
    border-radius: 16px 16px 0 0;
    margin: -30px -40px 20px -40px;
}
.modal-header h2 {
    color: #fff;
    font-size: 1.5rem;
    margin: 0;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.modal-header .close {
    color: #fff;
    font-size: 2rem;
    font-weight: bold;
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.2s;
    position: static;
    float: none;
}
.modal-header .close:hover {
    color: #222;
}

/* Modern form fields */
.input-field {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
    margin-top: 6px;
    margin-bottom: 10px;
    background: #fafafa;
    transition: border 0.2s;
}
.input-field:focus {
    border: 1.5px solid #ff6600;
    outline: none;
    background: #fff;
}

/* Modern button */
.modern-btn {
    background: linear-gradient(90deg, #ff6600 60%, #ffb347 100%);
    color: #fff;
    padding: 10px 28px;
    border: none;
    border-radius: 6px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(255,102,0,0.08);
    transition: background 0.2s, box-shadow 0.2s;
    margin-top: 10px;
}
.modern-btn:hover {
    background: linear-gradient(90deg, #ffb347 60%, #ff6600 100%);
    color: #222;
    box-shadow: 0 4px 16px rgba(255,102,0,0.15);
}

/* Responsive modal */
@media (max-width: 700px) {
    .modal-content, .modern-modal {
        width: 99% !important;
        padding: 18px 8px;
    }
    .modal-header {
        padding: 12px 10px 8px 10px;
        margin: -18px -8px 16px -8px;
    }
}
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    position: absolute;
    right: 20px;
    top: 10px;
    cursor: pointer;
}
.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
.edit-button {
    background-color: #4CAF50;
    color: white;
    padding: 8px 18px;
    margin: 5px;
    border: none;
    cursor: pointer;
    text-align: center;
    border-radius: 5px;
}
.edit-button:hover {
    background-color: #45a049;
}
.delete-button {
    background-color: #d32f2f;
    color: white;
    padding: 8px 18px;
    margin: 5px;
    border: none;
    cursor: pointer;
    text-align: center;
    border-radius: 5px;
}
.delete-button:hover {
    background-color: #c62828;
}
.pagination {
    display: inline-block;
}
.page-btn {
    color: #4CAF50;
    float: none;
    padding: 8px 16px;
    text-decoration: none;
    border: 1px solid #4CAF50;
    margin: 0 2px;
    border-radius: 4px;
    background: #fff;
    transition: background 0.2s, color 0.2s;
}
.page-btn.active, .page-btn:hover {
    background: #4CAF50;
    color: #fff;
}
.form-container {
    margin: 0 auto;
    max-width: 1100px;
    width: 100%;
    padding: 5px;
    box-sizing: border-box;
}
.compact-modal {
    max-width: 700px !important;
    width: 99% !important;
}
.email-template-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}
.page-title {
    color: #fff;
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    padding-left: 0;
}
.add-btn-top {
    background: linear-gradient(90deg, #ff6600 60%, #ffb347 100%);
    color: #fff;
    padding: 10px 22px;
    border: none;
    border-radius: 6px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(255,102,0,0.08);
    transition: background 0.2s, box-shadow 0.2s;
}
.add-btn-top:hover {
    background: linear-gradient(90deg, #ffb347 60%, #ff6600 100%);
    color: #222;
    box-shadow: 0 4px 16px rgba(255,102,0,0.15);
}
.status-toggle-btn {
    padding: 6px 18px;
    border-radius: 5px;
    border: none;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    background: #e0e0e0;
    color: #222;
    transition: background 0.2s, color 0.2s;
}
.status-toggle-btn.active-status {
    background: #2ac000;
    color: #fff;
}
.status-toggle-btn.inactive-status {
    background: #d32f2f;
    color: #fff;
}
.status-toggle-btn:hover {
    opacity: 0.85;
}
.email-template-bg, .white-bg {
    background: none !important;
    box-shadow: none !important;
    border-radius: 0 !important;
}
.align-title-table {
    max-width: 1100px;
    margin: 0 auto 0 auto;
    padding-left: 0;
    padding-right: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.form-container {
    margin-top: 0;
    padding-top: 18px;
    color: #fff;
    max-width: 1100px;
    margin-left: auto;
    margin-right: auto;
}
.room-table th, .room-table td {
    color: #222;
}
.room-table th {
    background: #2c2c2e;
    color: #fff;
}
.add-btn-top {
    background: linear-gradient(90deg, #ff6600 60%, #ffb347 100%);
    color: #fff;
}
.add-btn-top:hover {
    background: linear-gradient(90deg, #ffb347 60%, #ff6600 100%);
    color: #222;
}
.form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 18px;
}
.form-group label {
    font-weight: 500;
    margin-bottom: 6px;
    color: #333;
}
.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 18px;
}
.view-button {
    background-color: #2196f3;
    color: white;
    padding: 8px 18px;
    margin: 5px;
    border: none;
    cursor: pointer;
    text-align: center;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.view-button:hover {
    background-color: #1976d2;
}
.view-button svg {
    vertical-align: middle;
    width: 20px;
    height: 20px;
}
</style> 