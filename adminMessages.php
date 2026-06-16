<?php
session_start();
include "DBConn.php";

// Verify admin session access
if (!isset($_SESSION["adminID"])) {
    header("Location: adminLogin.php");
    exit();
}

$msg = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["send_message"])) {
    $receiverID = intval($_POST["receiverID"]);
    // Using mysqli_real_escape_string ensures punctuation like "Levi's" won't crash your SQL string!
    $msgText = mysqli_real_escape_string($conn, trim($_POST["messageText"])); 
    
    $sql = "INSERT INTO tblMessage (senderID, receiverID, messageText) VALUES (0, $receiverID, '$msgText')";
    if (mysqli_query($conn, $sql)) {
        $msg = "Message broadcasted successfully.";
    } else {
        $msg = "Database Error: " . mysqli_error($conn);
    }
}

// Fetch dynamic selection and layout list items from database
$users = mysqli_query($conn, "SELECT userID, fullName, username FROM tblUser");
$chats = mysqli_query($conn, "SELECT m.*, u.username FROM tblMessage m JOIN tblUser u ON m.receiverID = u.userID WHERE m.senderID = 0 ORDER BY m.messageDate DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Communication Centre</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div style="padding:20px; max-width:600px; margin: 0 auto; font-family: sans-serif;">
    
    <h1>Admin Communication</h1>
    <p>Logged in as Admin: <strong><?php echo htmlspecialchars($_SESSION["adminName"]); ?></strong></p>

    <?php if(!empty($msg)): ?>
        <p style="color:green; font-weight:bold;"><?= $msg ?></p>
    <?php endif; ?>

    <form method="POST" action="adminMessages.php">

        <label style="font-weight:bold;">Send To:</label><br>
        <select name="receiverID" style="width:100%; padding:8px; margin-top:5px; border-radius:4px;" required>
            <option value="" disabled selected>-- Select a Target User --</option>
            <?php while($u = mysqli_fetch_assoc($users)): ?>
                <option value="<?= $u['userID'] ?>">
                    <?= htmlspecialchars($u['fullName']) ?> (<?= htmlspecialchars($u['username']) ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <br><br>

        <label style="font-weight:bold;">Message:</label><br>
        <textarea name="messageText" rows="6" cols="40" placeholder="Type delivery verification instructions or quality status questions here..." style="width:100%; padding:8px; margin-top:5px; border-radius:4px; box-sizing:border-box;" required></textarea>

        <br><br>

        <input type="submit" name="send_message" value="Send Message" style="padding:10px 20px; background-color:black; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">

    </form>

    <hr style="margin:30px 0;">
    
    <h3>Sent Logs Archive</h3>
    <?php if (mysqli_num_rows($chats) > 0): ?>
        <ul style="list-style-type: none; padding-left: 0;">
            <?php while($chat = mysqli_fetch_assoc($chats)): ?>
                <li style="background:#f9f9f9; padding:12px; margin-bottom:10px; border-radius:5px; border-left:4px solid blue;">
                    <strong>To: <?= htmlspecialchars($chat['username']) ?></strong> 
                    <span style="font-size:11px; color:#888; float:right;"><?= $chat['messageDate'] ?></span><br>
                    <p style="margin:5px 0 0 0; font-style:italic; color:#333;">"<?= htmlspecialchars($chat['messageText']) ?>"</p>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p style="color:gray; font-style:italic;">No outbox logs found in communications history tables.</p>
    <?php endif; ?>

    <br>
    <a href="verifyUsers.php" style="text-decoration:none; color:blue; font-weight:bold;">← Back to Dashboard Management</a>
</div>

</body>
</html>