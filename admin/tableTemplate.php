<?php
require_once __DIR__ . '/../helperFunction/helpers.php';
$tableDataTextColor = getBackendSettingValue('table-data-text-color') ?: '#333';
// Usage: include this file and pass $tableTitle, $addUrl, $deleteUrl, $searchQuery, $tableHeaders (array), $tableRows (array of arrays)
?>
<div class="admin-table-card">
  <div class="admin-table-header">
    <h2 class="admin-table-title"><?= htmlspecialchars($tableTitle ?? 'Table Title') ?></h2>
    <div class="admin-table-add-row">
      <a href="<?= htmlspecialchars($addUrl ?? '#') ?>" class="admin-btn admin-btn-add">+ Add</a>
    </div>
    <div class="admin-table-actions-row">
      <div class="admin-table-actions-left">
        <form method="GET" class="admin-table-search-form" id="filterForm">
          <input type="text" id="titleSearch" name="title" placeholder="Search by Title ,ID ,Location" value="<?= htmlspecialchars($searchQuery ?? '') ?>" />
          <select name="status" id="status" onchange="this.form.submit()" class="admin-table-status-filter">
            <option value="">All Status</option>
            <option value="active" <?= (isset($_GET['status']) && $_GET['status'] === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inActive" <?= (isset($_GET['status']) && $_GET['status'] === 'inActive') ? 'selected' : '' ?>>Inactive</option>
          </select>
          <button type="submit" class="admin-btn admin-btn-search">🔍</button>
        </form>
      </div>
      <div class="admin-table-actions-right">
        <button type="button" class="admin-btn admin-btn-refresh" title="Refresh" onclick="window.location.href=window.location.pathname">&#x21bb;</button>
        <button type="button" class="admin-btn admin-btn-active" onclick="submitBulkAction('activate')">Active</button>
        <button type="button" class="admin-btn admin-btn-inactive" onclick="submitBulkAction('inactivate')">Inactive</button>
        <button type="button" class="admin-btn admin-btn-delete" onclick="submitBulkAction('delete')">🗑 Delete</button>
        <!-- Future: <button class="admin-btn">Export</button> <button class="admin-btn">Import</button> -->
      </div>
    </div>
  </div>
  
  <div class="admin-table-responsive">
    <form id="deleteForm" method="POST">
      <input type="hidden" name="bulk_action" id="bulk_action" value="">
      <table class="admin-table">
        <thead>
          <tr>
            <th><input type="checkbox" onclick="toggleAllCheckboxes(this)"></th>
            <?php foreach (($tableHeaders ?? []) as $header): ?>
              <th><?= htmlspecialchars($header) ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($tableRows ?? []) as $row): ?>
            <tr>
              <td><input type="checkbox" name="delete_ids[]" value="<?= htmlspecialchars($row['id'] ?? '') ?>"></td>
              <?php foreach ($row as $key => $value): if ($key === 'id') continue; ?>
                <?php if ($key === 'status'): ?>
                  <td>
                    <!-- Per-row status toggle form OUTSIDE the bulk form -->
                    <form method="POST" style="display:inline;" onsubmit="event.stopPropagation();">
                      <?= $value ?>
                    </form>
                  </td>
                <?php elseif ($key === 'action'): ?>
                  <td><?= $value ?></td>
                <?php else: ?>
                  <td><?= htmlspecialchars($value) ?></td>
                <?php endif; ?>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </form>
  </div>
</div>

<!-- Popup Notification -->
<div id="popup-notify" class="popup-notify" style="display:none;">
  <div class="popup-content">
    <span id="popup-message"></span>
    <button class="popup-close" onclick="closePopup()">&times;</button>
  </div>
</div>

<style>
.admin-table-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.07);
  padding: 24px 18px 18px 18px;
  margin: 32px auto;
  max-width: 98vw;
}
.admin-table-header {
  display: flex;
  flex-direction: column;
  align-items: stretch;
  margin-bottom: 18px;
  gap: 8px;
}
.admin-table-title {
  font-size: 1.4rem;
  font-weight: 600;
  color: #1a3c6b;
  margin: 0 0 4px 0;
}
.admin-table-actions-row {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  gap: 0;
  width: 100%;
  margin-bottom: 0;
}
.admin-table-actions-left {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}
.admin-table-actions-right {
  display: flex;
  gap: 8px;
  align-items: center;
  flex-wrap: wrap;
  min-width: 0;
}
.admin-btn {
  border: none;
  border-radius: 6px;
  padding: 7px 16px;
  font-size: 1rem;
  cursor: pointer;
  background: #1a3c6b;
  color: #fff;
  transition: background 0.2s;
  text-decoration: none;
  display: inline-block;
}
.admin-btn-add {
  background: #1769aa;
}
.admin-btn-add:hover {
  background: #0d4e7a;
}
.admin-btn-delete {
  background: #e74c3c;
}
.admin-btn-delete:hover {
  background: #c0392b;
}
.admin-table-responsive {
  width: 100%;
  overflow-x: auto;
}
.admin-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
  background: #fff;
}
.admin-table th, .admin-table td {
  padding: 10px 12px;
  border-bottom: 1px solid #e6eaf0;
  text-align: left;
  font-size: 1rem;
}
.admin-table th {
  background: #1769aa;
  color: #fff;
  font-weight: 600;
  position: sticky;
  top: 0;
  z-index: 1;
}
.admin-table tr:hover {
  background: #f4f6fa;
}
.admin-table input[type="checkbox"] {
  width: 16px;
  height: 16px;
}
@media (max-width: 700px) {
  .admin-table-actions-row {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }
  .admin-table-actions-left, .admin-table-actions-right {
    width: 100%;
    justify-content: stretch;
  }
  .admin-table-actions-right {
    justify-content: flex-end;
  }
  .admin-table-search-form {
    width: 100%;
    flex-wrap: wrap;
    gap: 8px;
  }
}
.badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 12px;
  font-size: 0.95em;
  font-weight: 600;
  color: #fff;
}
.badge-success { background: #27ae60; }
.badge-danger { background: #e74c3c; }
.action-btn {
  display: inline-block;
  padding: 5px 14px;
  border-radius: 6px;
  font-size: 0.98em;
  font-weight: 500;
  color: #fff;
  background: #1769aa;
  text-decoration: none;
  margin-right: 4px;
  transition: background 0.2s;
}
.action-btn.edit { background: #1769aa; }
.action-btn.edit:hover { background: #0d4e7a; }
.action-btn.delete { background: #e74c3c; }
.action-btn.delete:hover { background: #c0392b; }
.switch {
  position: relative;
  display: inline-block;
  width: 38px;
  height: 22px;
  margin-right: 8px;
  vertical-align: middle;
}
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #e74c3c; /* default to red for inactive */
  transition: .4s;
  border-radius: 22px;
}
.switch-active .slider {
  background-color: #27ae60 !important;
}
.switch-inactive .slider {
  background-color: #e74c3c !important;
}
.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider {
  background-color: #27ae60;
}
input:checked + .slider:before {
  transform: translateX(16px);
}
.admin-table-search-form {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  min-width: 0;
}
.admin-table-search-form input[type="text"] {
  border: 1px solid #c3d0e6;
  border-radius: 6px;
  padding: 6px 10px;
  font-size: 1rem;
  min-width: 120px;
  max-width: 350px;
  flex: 1 1 0;
}
.admin-table-status-filter {
  border: 1px solid #c3d0e6;
  border-radius: 6px;
  padding: 6px 10px;
  font-size: 1rem;
  background: #fff;
  color: #1a3c6b;
  min-width: 110px;
  flex-shrink: 0;
}
.admin-btn-search {
  white-space: nowrap;
  padding: 7px 12px;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
}
.admin-table td {
  color: <?= htmlspecialchars($tableDataTextColor) ?>;
}
.admin-table-filter-btns {
  display: flex;
  gap: 8px;
  align-items: center;
  margin-right: 12px;
}
.admin-btn-refresh {
  background: #f3f6fa;
  color: #1769aa;
  border: 1px solid #c3d0e6;
  font-size: 1.1rem;
  border-radius: 6px;
  padding: 7px 14px;
  cursor: pointer;
  transition: background 0.2s;
}
.admin-btn-active {
  background: #eafaf1;
  color: #27ae60;
  border: 1px solid #27ae60;
  border-radius: 6px;
  padding: 7px 14px;
  cursor: pointer;
  transition: background 0.2s;
}
.admin-btn-inactive {
  background: #faeaea;
  color: #e74c3c;
  border: 1px solid #e74c3c;
  border-radius: 6px;
  padding: 7px 14px;
  cursor: pointer;
  transition: background 0.2s;
}
.admin-btn-refresh:hover,
.admin-btn-active:hover,
.admin-btn-inactive:hover {
  opacity: 0.85;
}
.admin-table-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  margin-bottom: 12px;
}
.admin-table-add-row {
  display: flex;
  justify-content: flex-end;
  width: 100%;
  margin-bottom: 0;
}
.popup-notify {
  position: fixed;
  top: 30px;
  right: 30px;
  left: auto;
  z-index: 3000;
  min-width: 320px;
  max-width: 90vw;
  background: transparent;
  display: flex;
  justify-content: flex-end;
  pointer-events: none;
}
@media (max-width: 600px) {
  .popup-notify {
    left: 0;
    right: 0;
    top: 10px;
    justify-content: center;
    min-width: 0;
  }
}
.popup-content {
  background: #fff;
  color: #222;
  border-radius: 12px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.18);
  padding: 18px 32px 18px 22px;
  font-size: 1.08rem;
  font-family: 'Segoe UI', Arial, sans-serif;
  display: flex;
  align-items: center;
  gap: 18px;
  min-width: 220px;
  max-width: 100vw;
  border-left: 6px solid #27ae60;
  animation: popupSlideIn 0.4s cubic-bezier(.4,1.4,.6,1) 1;
  pointer-events: auto;
  position: relative;
}
.popup-notify.success .popup-content { border-left-color: #27ae60; }
.popup-notify.danger .popup-content { border-left-color: #e74c3c; }
.popup-close {
  background: none;
  border: none;
  color: #888;
  font-size: 1.6rem;
  cursor: pointer;
  margin-left: 8px;
  transition: color 0.2s;
  position: absolute;
  right: 12px;
  top: 10px;
}
.popup-close:hover { color: #e74c3c; }
@keyframes popupSlideIn {
  from { opacity: 0; transform: translateY(-30px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
<script>
function toggleAllCheckboxes(source) {
  const checkboxes = document.querySelectorAll('input[name="delete_ids[]"]');
  for (let cb of checkboxes) {
    cb.checked = source.checked;
  }
}
function setStatusFilter(status) {
  const form = document.getElementById('filterForm');
  if (form) {
    form.status.value = status;
    form.submit();
  }
}
function submitBulkAction(action) {
  const form = document.getElementById('deleteForm');
  document.getElementById('bulk_action').value = action;
  form.submit();
}
function closePopup() {
  var popup = document.getElementById('popup-notify');
  if (popup) {
    popup.style.opacity = '0';
    setTimeout(function() { popup.style.display = 'none'; }, 300);
  }
}
function showPopup(msg, type) {
  var popup = document.getElementById('popup-notify');
  var popupMsg = document.getElementById('popup-message');
  if (popup && popupMsg) {
    popupMsg.textContent = msg;
    popup.className = 'popup-notify ' + (type === 'success' ? 'success' : 'danger');
    popup.style.display = 'flex';
    popup.style.opacity = '1';
    setTimeout(function() {
      closePopup();
    }, 3000);
  }
}
</script> 