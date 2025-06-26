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

// Function to clean up message content
function clean_message($message) {
    // Normalize all line endings to \n
    $message = str_replace(["\r\n", "\r"], "\n", $message);
    // Remove multiple consecutive blank lines (more than 2 newlines)
    $message = preg_replace("/\n{3,}/", "\n\n", $message);
    // Trim leading/trailing whitespace and newlines
    $message = trim($message);
    return $message;
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slug = trim($_POST['slug']);
    $subject_title = trim($_POST['subject_title']);
    $user_message = trim($_POST['user_message']);
    $admin_mail = trim($_POST['admin_mail']);
    $admin_message = clean_message($_POST['admin_message']);
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
    if ($user_message === '') {
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
        $popupMessage = '<ul style="margin:0 0 0 18px;padding:0 0 0 10px;">';
        foreach ($validationErrors as $err) {
            $popupMessage .= '<li style="color:#b71c1c;font-size:1.05em;line-height:1.7;">' . htmlspecialchars($err) . '</li>';
        }
        $popupMessage .= '</ul>';
        $popupType = 'error';
    } else {
        $slug = mysqli_real_escape_string($conn, $slug);
        $subject_title = mysqli_real_escape_string($conn, $subject_title);
        $user_message = clean_message($user_message);
        $user_message = mysqli_real_escape_string($conn, $user_message);
        $admin_mail = mysqli_real_escape_string($conn, $admin_mail);
        $admin_message = mysqli_real_escape_string($conn, $admin_message);
        if (isset($_POST['template_id']) && $_POST['template_id'] !== '') {
            // Edit
            $template_id = intval($_POST['template_id']);
            $sql = "UPDATE email_templates SET subject_title=?, slug=?, user_message=?, admin_mail=?, admin_message=?, status=?,  updated_at=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssisssi", $subject_title, $slug, $user_message, $admin_mail, $admin_message, $status, $now, $template_id);
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
            $sql = "INSERT INTO email_templates (subject_title, slug, user_message, admin_mail, admin_message, status,  created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssissss", $subject_title, $slug, $user_message, $admin_mail, $admin_message, $status, $now, $now);
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
    <div class="email-template-header align-title-table">
        <h1 class="page-title">Email Template Manager</h1>
        <button onclick="showAddModal()" class="edit-button add-btn-top">Add New Template</button>
    </div>
    <div class="form-container">
        <table class="room-table" style="width:100%;margin-top:20px;">
            <thead><tr><th>ID</th><th>Subject</th><th>Slug</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($templates as $template): ?>
                <tr>
                    <td><?= $template['id'] ?></td>
                    <td><?= htmlspecialchars($template['subject_title']) ?></td>
                    <td><?= htmlspecialchars($template['slug']) ?></td>
                    <td>
                        <form action="emailTemplate.php" method="POST" style="display:inline;">
                            <input type="hidden" name="statusChange" value="<?= $template['id'] ?>">
                            <input type="hidden" name="statusValue" value="<?= $template['status'] ?>">
                            <?php if($template['status']) {
                                echo '<button class="status-toggle-btn active-status" type="submit">Active</button>';
                            } else {
                                echo '<button class="status-toggle-btn inactive-status" type="submit">Inactive</button>';
                            } ?>
                        </form>
                    </td>
                    <td>
                        <button onclick="showEditModal(<?= $template['id'] ?>)" class="edit-button">Edit</button>
                        <button onclick="confirmDelete(<?= $template['id'] ?>)" class="delete-button">Delete</button>
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
    <div class="modal-content modern-modal compact-modal">
        <div class="modal-header">
            <h2 id="modalTitle">Add Email Template</h2>
            <span class="close" onclick="closeTemplateModal()">&times;</span>
        </div>
        <form id="templateForm" method="POST" action="emailTemplate.php">
            <input type="hidden" name="template_id" id="template_id">
            <label>Subject Title:<br><input type="text" name="subject_title" id="subject_title" required class="input-field"></label><br>
            <label>User Message:<br><textarea name="user_message" id="user_message" rows="6" class="input-field"></textarea></label><br>
            <label>Admin Mail:<br><input type="email" name="admin_mail" id="admin_mail" class="input-field"></label><br><br>
            <label>Admin Message:<br><textarea name="admin_message" id="admin_message" rows="6" class="input-field"></textarea></label><br>
            <label>Status:<br>
                <select name="status" id="status" class="input-field">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </label><br>
           
            <label>Slug:<br><input type="text" name="slug" id="slug" required class="input-field"></label><br><br>
            <button type="submit" class="edit-button modern-btn">Save Template</button>
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
<!-- Popup Message Modal -->
<div id="popupModal" class="modal" style="display:<?= $popupMessage ? 'block' : 'none' ?>;">
    <div class="modal-content" style="background:<?= $popupType === 'success' ? '#e8f5e9' : '#ffebee' ?>; max-width: 400px;">
        <span class="close" onclick="closePopupModal()">&times;</span>
        <h3 style="color:<?= $popupType === 'success' ? '#2e7d32' : '#b71c1c' ?>;">
            <?= $popupType === 'success' ? 'Success' : 'Error' ?>
        </h3>
        <hr style="border: 2px solid <?= $popupType === 'success' ? '#388e3c' : '#d32f2f' ?>; width: 100%;">
        <p><?= htmlspecialchars($popupMessage) ?></p>
    </div>
</div>
<?php if (isset($popupMessage) && $popupMessage && $popupType === 'success'): ?>
<script>
    setTimeout(function() {
        window.location.href = 'emailTemplate.php';
    }, 300);
</script>
<?php endif; ?>
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
    document.getElementById('slug').value = '';
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
        document.getElementById('slug').value = template.slug;
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
    border: 1px solid #e0e0e0;
    width: 70%;
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
    max-width: 520px !important;
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
</style> 