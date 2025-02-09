<?php
require "leftSidebar.php";
require "dbConnect.php";  

// Fetch media data
$query = "SELECT * from media;";
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
    <div class="success-notify">
        <span><?php echo $successfullyDeleted; ?></span>
    </div>
<?php endif; ?>

<div class="dashboard-content">
    <h1 class="roomH1" style="color:white; text-align: center;">Media Manager</h1>
    <table class="room-table" style="margin: 20px 0; border-collapse: collapse; border: 1px solid #ddd;">
    <thead>
        <tr>
            <th style="padding: 10px;">ID</th>
            <th style="padding: 10px;">Image</th>
            <th style="padding: 10px;">Status</th>
            <th style="padding: 10px;">View</th> 
            <th style="padding: 10px;">Created At</th>
        </tr>
    </thead>
    <tbody>
        <div style="text-align: right; margin-bottom: 20px;">
            <button>
                <a href="addMedia.php" class="add-room-button" style=" color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 16px;">
                    New Image
                </a>
            </button>
        </div>
        <?php if ($result->num_rows > 0): 
        $i=1;?>
            <?php while ($media = $result->fetch_assoc()): ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $i++; ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <img src="/admin/<?php echo $media['image_path']; ?>" alt="Image" style="width: 220px; height: 160px; object-fit: cover; border-radius: 8px;">
                    </td>

                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="text" hidden value="<?php echo $media['id']; ?>" name="statusChange" />
                            <input type="text" hidden value="<?php echo $media['status']; ?>" name="statusValue" />
                            <?php if ($media['status'] == 1): ?>
                                <button type="submit" class="edit-button" style="background-color: green; color: white; border: none; padding: 5px 10px; cursor: pointer;">Active</button>
                            <?php else: ?>
                                <button type="submit" class="delete-button" style="background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;">InActive</button>
                            <?php endif; ?>
                        </form>
                    </td>

                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <a href="/admin/<?php echo $media['image_path']; ?>" target="_blank" style="background-color:aqua; color: black;padding: 5px 10px; text-decoration: none;">View Full Image</a>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $media['created_at']; ?></td>

                   
                   
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="padding: 10px; text-align: center;">No media found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>
</body>

<script>
    <?php if (isset($successfullyDeleted)): ?>
        setTimeout(function() {
            window.location.href = 'media.php'; 
        }, 500);
    <?php endif; ?>
</script>
</html>
