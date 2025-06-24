<?php
require "leftSidebar.php";
require('../helperFunction/helpers.php');
require "dbConnect.php";

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Count total templates
$totalSql = "SELECT COUNT(*) as total FROM email_templates";
$totalResult = $conn->query($totalSql);
$totalTemplates = $totalResult ? (int)$totalResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalTemplates / $perPage);

// Handle Add/Edit/Delete actions
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$popupMessage = '';
$popupType = '';

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_title = mysqli_real_escape_string($conn, $_POST['subject_title']);
    $user_message = mysqli_real_escape_string($conn, $_POST['user_message']);
    $admin_mail = mysqli_real_escape_string($conn, $_POST['admin_mail']);
    $admin_message = mysqli_real_escape_string($conn, $_POST['admin_message']);
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1;
    $template_variables = mysqli_real_escape_string($conn, $_POST['template_variables']);
    $now = date('Y-m-d H:i:s');

    if (isset($_POST['template_id']) && $_POST['template_id'] !== '') {
        // Edit
        $template_id = intval($_POST['template_id']);
        $sql = "UPDATE email_templates SET subject_title=?, user_message=?, admin_mail=?, admin_message=?, status=?, template_variables=?, updated_at=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssissi", $subject_title, $user_message, $admin_mail, $admin_message, $status, $template_variables, $now, $template_id);
        if ($stmt->execute()) {
            $popupMessage = "Email template updated successfully.";
            $popupType = "success";
        } else {
            $popupMessage = "Failed to update email template.";
            $popupType = "error";
        }
        $stmt->close();
    } else {
        // Add
        $sql = "INSERT INTO email_templates (subject_title, user_message, admin_mail, admin_message, status, template_variables, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssisss", $subject_title, $user_message, $admin_mail, $admin_message, $status, $template_variables, $now, $now);
        if ($stmt->execute()) {
            $popupMessage = "Email template added successfully.";
            $popupType = "success";
        } else {
            $popupMessage = "Failed to add email template.";
            $popupType = "error";
        }
        $stmt->close();
    }
}

// Handle Delete
if ($action === 'delete' && $id) {
    $sql = "DELETE FROM email_templates WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $popupMessage = "Email template deleted successfully.";
        $popupType = "success";
    } else {
        $popupMessage = "Failed to delete email template.";
        $popupType = "error";
    }
    $stmt->close();
}

// Fetch paginated templates
$templates = [];
$sql = "SELECT * FROM email_templates ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
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
?>
<div class="dashboard-content">
    <div class="form-container" style="margin-left: 260px; padding: 5px;">
        <h1>Email Template Manager</h1>
        <button onclick="showAddModal()" class="upload-btn">Add New Template</button>
        <table border="1" cellpadding="6" style="width:100%;max-width:900px;margin-top:20px;">
            <thead><tr><th>ID</th><th>Subject</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($templates as $template): ?>
                <tr>
                    <td><?= $template['id'] ?></td>
                    <td><?= htmlspecialchars($template['subject_title']) ?></td>
                    <td><?= $template['status'] ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <button onclick="showEditModal(<?= $template['id'] ?>)" class="upload-btn">Edit</button>
                        <button onclick="confirmDelete(<?= $template['id'] ?>)" class="upload-btn" style="background:#d32f2f;">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
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
    <div class="modal-content">
        <span class="close" onclick="closeTemplateModal()">&times;</span>
        <h2 id="modalTitle">Add Email Template</h2>
        <form id="templateForm" method="POST" action="emailTemplate.php">
            <input type="hidden" name="template_id" id="template_id">
            <label>Subject Title:<br><input type="text" name="subject_title" id="subject_title" required></label><br><br>
            <label>User Message:<br><textarea name="user_message" id="user_message" rows="6"></textarea></label><br><br>
            <label>Admin Mail:<br><input type="email" name="admin_mail" id="admin_mail"></label><br><br>
            <label>Admin Message:<br><textarea name="admin_message" id="admin_message" rows="6"></textarea></label><br><br>
            <label>Status:<br>
                <select name="status" id="status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </label><br><br>
            <label>Template Variables (comma separated):<br><input type="text" name="template_variables" id="template_variables" placeholder="e.g. name,email,otp"></label><br><br>
            <button type="submit" class="upload-btn">Save Template</button>
        </form>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content" style="background:#ffebee;">
        <span class="close" onclick="closeDeleteModal()">&times;</span>
        <h2 style="color:#b71c1c;">Delete Confirmation</h2>
        <p>Are you sure you want to delete this email template?</p>
        <button id="deleteConfirmBtn" class="upload-btn" style="background:#d32f2f;">Delete</button>
        <button onclick="closeDeleteModal()" class="upload-btn">Cancel</button>
    </div>
</div>
<!-- Popup Message Modal -->
<div id="popupModal" class="modal" style="display:<?= $popupMessage ? 'block' : 'none' ?>;">
    <div class="modal-content" style="background:<?= $popupType === 'success' ? '#e8f5e9' : '#ffebee' ?>;">
        <span class="close" onclick="closePopupModal()">&times;</span>
        <h3 style="color:<?= $popupType === 'success' ? '#2e7d32' : '#b71c1c' ?>;">
            <?= $popupType === 'success' ? 'Success' : 'Error' ?>
        </h3>
        <hr style="border: 2px solid <?= $popupType === 'success' ? '#388e3c' : '#d32f2f' ?>; width: 100%;">
        <p><?= htmlspecialchars($popupMessage) ?></p>
    </div>
</div>
<script>
// Modal logic
function showAddModal() {
    document.getElementById('modalTitle').innerText = 'Add Email Template';
    document.getElementById('template_id').value = '';
    document.getElementById('subject_title').value = '';
    document.getElementById('user_message').value = '';
    document.getElementById('admin_mail').value = '';
    document.getElementById('admin_message').value = '';
    document.getElementById('status').value = '1';
    document.getElementById('template_variables').value = '';
    document.getElementById('templateModal').style.display = 'block';
}
function showEditModal(id) {
    // Fetch template data from PHP array (rendered as JS object)
    var templates = <?php echo json_encode($templates); ?>;
    var template = templates.find(t => t.id == id);
    if (template) {
        document.getElementById('modalTitle').innerText = 'Edit Email Template';
        document.getElementById('template_id').value = template.id;
        document.getElementById('subject_title').value = template.subject_title;
        document.getElementById('user_message').value = template.user_message;
        document.getElementById('admin_mail').value = template.admin_mail;
        document.getElementById('admin_message').value = template.admin_message;
        document.getElementById('status').value = template.status;
        document.getElementById('template_variables').value = template.template_variables;
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
function closePopupModal() {
    document.getElementById('popupModal').style.display = 'none';
    window.location.href = 'emailTemplate.php';
}
// Close modals if clicked outside
window.onclick = function(event) {
    var templateModal = document.getElementById('templateModal');
    var deleteModal = document.getElementById('deleteModal');
    var popupModal = document.getElementById('popupModal');
    if (event.target == templateModal) templateModal.style.display = 'none';
    if (event.target == deleteModal) deleteModal.style.display = 'none';
    if (event.target == popupModal) popupModal.style.display = 'none';
}
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
    border: 1px solid #888;
    width: 60%;
    border-radius: 18px;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
    position: relative;
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
.upload-btn {
    background-color: #4CAF50;
    color: white;
    padding: 8px 18px;
    margin: 5px;
    border: none;
    cursor: pointer;
    text-align: center;
    border-radius: 5px;
}
.upload-btn:hover {
    background-color: #45a049;
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
</style> 