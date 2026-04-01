<?php
session_start();
include '../../includes/config.php';
include '../../includes/functions.php';
include '../includes/admin_nav.php';
// The CORRECTED security check: now it checks for $_SESSION['role']
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}
$active_conversation_id = $_GET['conversation_id'] ?? null;
$conversations = [];
$messages = [];
$active_user_name = "User";
$admin_id = $_SESSION['user_id'];

// Fetch all conversations with unread message counts
$stmt_convs = $conn->prepare("
    SELECT 
        c.id, 
        u.full_name, 
        c.created_at, 
        c.updated_at,
        (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND is_read = 0 AND sender_id != ?) AS unread_count
    FROM conversations c
    JOIN users u ON c.user_id = u.id
    ORDER BY c.updated_at DESC
");
$stmt_convs->bind_param("i", $admin_id);
$stmt_convs->execute();
$conversations = $stmt_convs->get_result()->fetch_all(MYSQLI_ASSOC);

// If an active conversation is selected, fetch its messages
if ($active_conversation_id) {
    foreach ($conversations as $conv) {
        if ($conv['id'] == $active_conversation_id) {
            $active_user_name = $conv['full_name'];
            break;
        }
    }

    $stmt_messages = $conn->prepare("
        SELECT 
            m.sender_id, 
            m.message_content, 
            m.created_at, 
            u.full_name AS sender_name
        FROM messages m
        LEFT JOIN users u ON m.sender_id = u.id
        WHERE m.conversation_id = ?
        ORDER BY m.created_at ASC
    ");
    $stmt_messages->bind_param("i", $active_conversation_id);
    $stmt_messages->execute();
    $messages = $stmt_messages->get_result()->fetch_all(MYSQLI_ASSOC);

    // Mark messages as read, but only those NOT sent by the admin
    $stmt_mark_read = $conn->prepare("
        UPDATE messages SET is_read = 1
        WHERE conversation_id = ? AND is_read = 0 AND sender_id != ?
    ");
    $stmt_mark_read->bind_param("ii", $active_conversation_id, $admin_id);
    $stmt_mark_read->execute();
}

// Handle admin reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $reply_content = trim($_POST['reply_message']);
    if (!empty($reply_content) && $active_conversation_id) {
        $stmt_reply = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, message_content) VALUES (?, ?, ?)");
        $stmt_reply->bind_param("iis", $active_conversation_id, $admin_id, $reply_content);
        $stmt_reply->execute();
        
        $stmt_update_conv = $conn->prepare("UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt_update_conv->bind_param("i", $active_conversation_id);
        $stmt_update_conv->execute();

        header("Location: " . $_SERVER['PHP_SELF'] . "?conversation_id=" . $active_conversation_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Messages — Washly</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin_messages.css">

    <style>
        body { background: #f3f6fb; }
    </style>
</head>
<body>
<div class="admin-chat-wrap">
    <aside class="sidebar" data-aos="fade-right">
        <div class="sidebar-top px-3 pt-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><i class="fa-solid fa-headset me-2"></i>Support</h5>
            <a href="#" class="text-muted small"><i class="fa-solid fa-gear"></i></a>
        </div>

        <div class="sidebar-search p-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                <input type="search" class="form-control rounded-pill" placeholder="Search conversations..." id="convSearch">
            </div>
        </div>

        <div class="conversation-list p-2">
            <?php foreach ($conversations as $conv): ?>
                <a href="?conversation_id=<?php echo $conv['id']; ?>" class="conversation-item d-flex align-items-center p-2 mb-2 <?php echo $conv['id'] == $active_conversation_id ? 'active' : ''; ?>" data-aos="zoom-in" data-aos-delay="50">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-circle"><?php echo strtoupper(substr($conv['full_name'],0,1)); ?></span>
                        <?php if ($conv['unread_count'] > 0): ?>
                            <span class="unread-dot"><?php echo $conv['unread_count']; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="mb-0"><?php echo htmlspecialchars($conv['full_name']); ?></h6>
                            <small class="text-muted"><?php echo (new DateTime($conv['updated_at']))->format('M d, h:i A'); ?></small>
                        </div>
                        <p class="mb-0 small text-muted">Last message • <?php echo (new DateTime($conv['created_at']))->format('M d'); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (empty($conversations)): ?>
                <div class="p-3 text-center text-muted small">No conversations yet.</div>
            <?php endif; ?>
        </div>
    </aside>

    <main class="chat-main d-flex flex-column" data-aos="fade-left">
        <?php if ($active_conversation_id): ?>
            <header class="chat-top d-flex align-items-center px-4 py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="avatar-lg me-3"><span class="avatar-circle-lg"><?php echo strtoupper(substr($active_user_name,0,1)); ?></span></div>
                    <div>
                        <h5 class="mb-0"><?php echo htmlspecialchars($active_user_name); ?></h5>
                        <small class="text-muted">Active conversation</small>
                    </div>
                </div>
                <div class="ms-auto d-flex gap-3 align-items-center">
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-info-circle"></i> Details</button>
                </div>
            </header>

            <section class="chat-body flex-grow-1 overflow-auto p-4">
                <?php if (empty($messages)): ?>
                    <div class="no-msgs text-center text-muted py-5">
                        <i class="bi bi-chat-left-dots display-4"></i>
                        <p class="mt-3 mb-0">No messages — send the first reply.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($messages as $message): ?>
                    <?php $isAdmin = ($message['sender_id'] == $admin_id); ?>
                    <div class="message-row d-flex mb-3 <?php echo $isAdmin ? 'justify-content-end' : 'justify-content-start'; ?>" data-aos="fade-up">
                        <?php if (!$isAdmin): ?>
                            <div class="message-meta me-2 text-muted text-center small">
                                <div class="avatar-sm"><?php echo strtoupper(substr($message['sender_name'],0,1)); ?></div>
                                <div><?php echo htmlspecialchars($message['sender_name']); ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="message-bubble <?php echo $isAdmin ? 'sent' : 'received'; ?>">
                            <div class="message-text"><?php echo nl2br(htmlspecialchars($message['message_content'])); ?></div>
                            <div class="message-footer text-end small text-muted"><?php echo (new DateTime($message['created_at']))->format('h:i A'); ?></div>
                        </div>

                        <?php if ($isAdmin): ?>
                            <div class="message-meta ms-2 text-muted text-center small">
                                <div class="avatar-sm">A</div>
                                <div>Admin</div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </section>

            <footer class="chat-composer p-3 border-top">
                <form method="POST" action="?conversation_id=<?php echo $active_conversation_id; ?>" class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-light btn-icon" title="Attach"><i class="fa-solid fa-paperclip"></i></button>
                    <textarea name="reply_message" class="form-control composer-textarea" placeholder="Reply to <?php echo htmlspecialchars($active_user_name); ?>..." rows="1" required></textarea>
                    <button type="submit" class="btn btn-primary btn-send"><i class="bi bi-send-fill"></i></button>
                </form>
            </footer>
        <?php else: ?>
            <div class="empty-state d-flex align-items-center justify-content-center flex-column h-100">
                <i class="bi bi-people display-3 text-muted"></i>
                <h4 class="mt-3 text-muted">No conversation selected</h4>
                <p class="text-muted">Choose a conversation from the left to view and reply</p>
            </div>
        <?php endif; ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="../assets/js/messages.js"></script>

</body>
</html>