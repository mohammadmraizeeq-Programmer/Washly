<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../../includes/config.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Public/Registration/log_in.php');
    exit();
}


$user_id = $_SESSION['user_id'];
$conversation_id = null;
$messages = [];

// Check for an existing conversation
$stmt = $conn->prepare("SELECT id FROM conversations WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $conversation = $result->fetch_assoc();
    $conversation_id = $conversation['id'];
    // Fetch all messages for this user's conversation
    // The LEFT JOIN is still useful for potential future features, but the logic below is what truly matters
    $stmt_messages = $conn->prepare("SELECT m.*, u.full_name FROM messages m LEFT JOIN users u ON m.sender_id = u.id WHERE m.conversation_id = ? ORDER BY m.created_at ASC");
    $stmt_messages->bind_param("i", $conversation_id);
    $stmt_messages->execute();
    $messages = $stmt_messages->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_message'])) {
    $message_content = trim($_POST['new_message']);
    if (!empty($message_content)) {
        if ($conversation_id === null) {
            // Create a new conversation if one doesn't exist
            $stmt_new_conv = $conn->prepare("INSERT INTO conversations (user_id) VALUES (?)");
            $stmt_new_conv->bind_param("i", $user_id);
            $stmt_new_conv->execute();
            $conversation_id = $conn->insert_id;
        }

        // Insert the new message
        $stmt_insert_msg = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, message_content) VALUES (?, ?, ?)");
        $stmt_insert_msg->bind_param("iis", $conversation_id, $user_id, $message_content);
        $stmt_insert_msg->execute();

        // Update the conversation's timestamp
        $stmt_update_conv = $conn->prepare("UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt_update_conv->bind_param("i", $conversation_id);
        $stmt_update_conv->execute();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle message deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_messages'])) {
    if ($conversation_id) {
        $stmt_delete = $conn->prepare("DELETE FROM messages WHERE conversation_id = ?");
        $stmt_delete->bind_param("i", $conversation_id);
        $stmt_delete->execute();
        
        // Also delete the conversation row
        $stmt_delete_conv = $conn->prepare("DELETE FROM conversations WHERE id = ?");
        $stmt_delete_conv->bind_param("i", $conversation_id);
        $stmt_delete_conv->execute();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Messages — Washly</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/messages.css">

    <style>body{background:linear-gradient(180deg,#fbfbff,#f7fbff)}</style>
</head>
<body>
<div class="user-chat mx-auto shadow-sm" data-aos="fade-up">
    <header class="user-chat-header d-flex align-items-center px-3 py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-chat-left-text display-6 me-3 text-primary"></i>
            <div>
                <h5 class="mb-0">Admin Support</h5>
                <small class="text-muted">We reply within 24 hours</small>
            </div>
        </div>
        <div class="ms-auto">
            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this entire conversation?');">
                <input type="hidden" name="delete_messages" value="1">
                <button type="submit" class="btn btn-sm btn-outline-secondary" id="clearChatBtn">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </form>
        </div>
    </header>

    <section class="user-chat-body p-3" id="messagesArea">
        <?php if (empty($messages)): ?>
            <div class="welcome-tip p-4 text-center text-muted" data-aos="zoom-in">
                <i class="bi bi-emoji-smile display-4"></i>
                <p class="mt-3 mb-0">Welcome! Send your first message to Admin Support.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($messages as $message): ?>
            <?php $isMe = ($message['sender_id'] == $user_id); ?>
            <div class="message-row <?php echo $isMe ? 'me' : 'other'; ?>" data-aos="fade-up">
                <div class="message <?php echo $isMe ? 'msg-me' : 'msg-other'; ?>">
                    <?php if (!$isMe): ?>
                        <div class="sender-name">Admin</div>
                    <?php endif; ?>
                    <div class="msg-content"><?php echo nl2br(htmlspecialchars($message['message_content'])); ?></div>
                    <div class="msg-time"><?php echo (new DateTime($message['created_at']))->format('h:i A'); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <footer class="user-chat-composer p-3 border-top">
        <form method="POST" action="" class="d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-light btn-sm" title="Emoji"><i class="fa-regular fa-face-smile"></i></button>
            <textarea name="new_message" class="form-control auto-grow" placeholder="Type your message..." rows="1" required></textarea>
            <button type="submit" class="btn btn-gradient d-flex align-items-center"><i class="bi bi-send-fill me-2"></i>Send</button>
        </form>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 400 });

    // auto grow textarea
    document.querySelectorAll('.auto-grow').forEach(tx=>{
        tx.addEventListener('input', ()=> {
            tx.style.height = 'auto';
            tx.style.height = (tx.scrollHeight) + 'px';
        });
    });
</script>
</body>
</html>