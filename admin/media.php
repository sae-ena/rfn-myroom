<?php
require_once "leftSidebar.php";
require "dbConnect.php";  

// Get the filter value from the dropdown (Active or Inactive)
$statusFilter = isset($_GET['status']) ? $_GET['status'] : ''; // Default to showing all media

// Build the query with a filter if set
$query = "SELECT * FROM media";
if ($statusFilter !== '') {
    $query .= " WHERE status = " . (int)$statusFilter;
}
$query .= " ORDER BY created_at DESC;";

// Fetch media data
$result = $conn->query($query);

if (!$result) exit("Connection failed to fetch Data");

// Handle delete operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mediaUid'])) {
    $mediaId = $_POST['mediaUid'];
    $query = "UPDATE media SET status = 0 WHERE id = $mediaId;";
    $deleteResult = $conn->query($query);

    if (!$deleteResult) exit("Connection failed to fetch Data");

    $successfullyDeleted = "Media has been deleted";
}

// Handle status change operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statusValue'])) {
    $mediaId = $_POST['statusChange'];
    $mediaCurrentStatus = (int)$_POST['statusValue'];
    $mediaCurrentStatus = ($mediaCurrentStatus == 1) ? 0 : 1;
    $query = "UPDATE media SET status = '$mediaCurrentStatus' WHERE id = $mediaId;";
    $statusChangeResult = $conn->query($query);

    if (!$statusChangeResult) exit("Connection failed to fetch Data");

    $successfullyDeleted = "Image status updated ";
}
?>

<?php if (isset($successfullyDeleted)): ?>
    <div class="success-notify" style="padding: 10px; background-color: green; color: white; margin: 10px 0; text-align: center;">
        <span><?php echo $successfullyDeleted; ?></span>
    </div>
<?php endif; ?>

<div class="dashboard-content" style="text-align: center;">
    <h1 class="roomH1" style="color:white;">Media Manager</h1>
    
    <!-- Filter dropdown -->
    <div style="text-align: center; margin-bottom: 20px;">
        <form action="media.php" method="GET">
            <label for="status" style="color:white; position:absolute;left:300px;top:79px">Filter by Status:</label>
            <select name="status" id="status" onchange="this.form.submit()" style="padding: 8px 15px; border-radius: 5px; font-size: 14px;">
                <option value="">All</option>
                <option value="1" <?php echo ($statusFilter == '1') ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo ($statusFilter == '0') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </form>
    </div>

    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-top: 20px;">
        <div style="text-align: right; width: 100%; margin-bottom: 20px;">
            <button>
                <a href="addMedia.php" class="add-room-button" style="background-color: #007BFF; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 16px;">
                    New Image
                </a>
            </button>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($media = $result->fetch_assoc()): ?>
                <div class="card23" style="width: 250px; background-color: #f9f9f9; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                    <img src="/admin/<?php echo $media['image_path']; ?>" alt="Image" style="width: 100%; height: 200px; object-fit: cover; border-bottom: 2px solid #ddd;">
                    <div style="padding: 15px; text-align: center;">
                        <h4 style="margin-bottom: 15px;">Image <?php echo $media['id']; ?></h4>

                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" style="margin-bottom: 10px;">
                            <input type="hidden" value="<?php echo $media['id']; ?>" name="statusChange" />
                            <input type="hidden" value="<?php echo $media['status']; ?>" name="statusValue" />
                            <?php if ($media['status'] == 1): ?>
                                <button type="submit" class="edit-button" style="background-color: green; color: white; border: none; padding: 5px 15px; cursor: pointer; font-size: 14px;">
                                    Active
                                </button>
                            <?php else: ?>
                                <button type="submit" class="delete-button" style="background-color: red; color: white; border: none; padding: 5px 15px; cursor: pointer; font-size: 14px;">
                                    InActive
                                </button>
                            <?php endif; ?>
                        </form>

                        <a href="/admin/<?php echo $media['image_path']; ?>" target="_blank" style="background-color:aqua; color: black; padding: 8px 20px; text-decoration: none; border-radius: 5px; font-size: 14px;">
                            View Full Image
                        </a>
                    </div>

                    <div style="text-align: center; background-color: #f1f1f1; padding: 8px;">
                        <small style="color: #888;"><?php echo $media['created_at']; ?></small>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="width: 100%; text-align: center; padding: 20px;">
                <p>No media found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    <?php if (isset($successfullyDeleted)): ?>
        setTimeout(function() {
            window.location.href = 'media.php'; 
        }, 500);
    <?php endif; ?>
</script>
</body>
</html>
