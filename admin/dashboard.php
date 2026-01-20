<?php
// admin/dashboard.php
require_once '../assets/php/auth_check.php';
require_once '../assets/php/db_config.php';

try {
    // Fetch Messages
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll();

    // Fetch Subscribers
    $nStmt = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC");
    $subscribers = $nStmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LeO Marketing</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f4f6f9;
            padding: 20px;
            font-family: 'Inter', sans-serif;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            color: #666;
            transition: all 0.3s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .tab-btn.active {
            background: var(--primary-color, #4a90e2);
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .message-card {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border-left: 5px solid #ccc;
            transition: all 0.3s ease;
        }

        .message-card.unread {
            border-left-color: var(--primary-color, #4a90e2);
            background: #fbfdff;
        }

        .message-card.unread h3 {
            font-weight: 700;
            color: #000;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: #666;
        }

        .meta-tags {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .badge {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge.service {
            background: #e3f2fd;
            color: #0d47a1;
        }

        .badge.page {
            background: #fff3e0;
            color: #e65100;
        }

        .btn-action {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            color: #666;
            transition: color 0.2s;
            margin-left: 10px;
        }

        .btn-action:hover {
            color: #333;
        }

        .btn-delete:hover {
            color: #dc3545;
        }

        .btn-toggle-read:hover {
            color: #4a90e2;
        }

        .filter-bar {
            margin-bottom: 15px;
        }

        .filter-btn {
            background: none;
            border: 1px solid #ddd;
            padding: 5px 15px;
            border-radius: 20px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 0.9rem;
        }

        .filter-btn.active {
            background: #6c757d;
            color: white;
            border-color: #6c757d;
        }

        .extra-details {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 0.9rem;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .detail-item strong {
            color: #555;
        }
    </style>
</head>

<body>
    <div class="admin-header">
        <h1>Dashboard</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="../assets/php/logout.php" style="color: #dc3545; text-decoration: none; margin-left: 15px; font-weight: 600;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('messages')">Messages (<?php echo count($messages); ?>)</button>
            <button class="tab-btn" onclick="switchTab('newsletter')">Newsletter (<?php echo count($subscribers); ?>)</button>
        </div>

        <!-- Messages Tab -->
        <div id="messages" class="tab-content active">
            <div class="filter-bar">
                <button class="filter-btn active" onclick="filterMessages('all')">All</button>
                <button class="filter-btn" onclick="filterMessages('unread')">Unread</button>
            </div>

            <?php if (count($messages) === 0): ?>
                <p>No messages found.</p>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message-card <?php echo (!$msg['is_read']) ? 'unread' : ''; ?>" data-id="<?php echo $msg['id']; ?>" data-read="<?php echo $msg['is_read']; ?>">
                        <div class="message-header">
                            <div>
                                <strong><?php echo htmlspecialchars($msg['name']); ?></strong> &lt;<?php echo htmlspecialchars($msg['email']); ?>&gt;
                                <?php if ($msg['company']): ?>
                                    <br><span style="font-size: 0.85rem; color: #666;">Company: <?php echo htmlspecialchars($msg['company']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div style="text-align: right;">
                                <div class="meta-tags" style="justify-content: flex-end;">
                                    <span class="badge service"><?php echo htmlspecialchars($msg['service'] ?: 'General'); ?></span>
                                    <?php if ($msg['page']): ?><span class="badge page"><?php echo htmlspecialchars($msg['page']); ?></span><?php endif; ?>
                                </div>
                                <div style="margin-top: 5px; font-size: 0.8rem; color: #888;">
                                    <?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?>
                                </div>
                                <div style="margin-top: 5px;">
                                    <button class="btn-action btn-toggle-read" onclick="toggleRead(<?php echo $msg['id']; ?>, this)" title="<?php echo $msg['is_read'] ? 'Mark as Unread' : 'Mark as Read'; ?>">
                                        <i class="fas <?php echo $msg['is_read'] ? 'fa-envelope-open' : 'fa-envelope'; ?>"></i>
                                    </button>
                                    <a href="delete_message.php?id=<?php echo $msg['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Delete this message?');" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="extra-details">
                            <?php if ($msg['department']): ?><div class="detail-item"><strong>Dept:</strong> <?php echo htmlspecialchars($msg['department']); ?></div><?php endif; ?>
                            <?php if ($msg['budget']): ?><div class="detail-item"><strong>Budget:</strong> <?php echo htmlspecialchars($msg['budget']); ?></div><?php endif; ?>
                            <?php if ($msg['timeline']): ?><div class="detail-item"><strong>Timeline:</strong> <?php echo htmlspecialchars($msg['timeline']); ?></div><?php endif; ?>
                            <?php if ($msg['phone']): ?><div class="detail-item"><strong>Phone:</strong> <?php echo htmlspecialchars($msg['phone']); ?></div><?php endif; ?>
                        </div>

                        <h3 style="margin: 0 0 10px 0; font-size: 1.1rem;"><?php echo htmlspecialchars($msg['subject'] ?: 'No Subject'); ?></h3>
                        <p style="white-space: pre-wrap; margin: 0; color: #333; line-height: 1.5;"><?php echo htmlspecialchars($msg['message']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Newsletter Tab -->
        <div id="newsletter" class="tab-content">
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <?php if (count($subscribers) > 0): ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; background: #f8f9fa;">
                                <th style="padding: 10px; border-bottom: 2px solid #eee;">Email</th>
                                <th style="padding: 10px; border-bottom: 2px solid #eee;">Date Subscribed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subscribers as $sub): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 10px;"><?php echo htmlspecialchars($sub['email']); ?></td>
                                    <td style="padding: 10px;"><?php echo date('M d, Y h:i A', strtotime($sub['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No newsletter subscribers yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }

        function filterMessages(filter) {
            document.querySelectorAll('.filter-btn').forEach(el => el.classList.remove('active'));
            event.target.classList.add('active');

            const cards = document.querySelectorAll('.message-card');
            cards.forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'block';
                } else if (filter === 'unread') {
                    card.style.display = card.classList.contains('unread') ? 'block' : 'none';
                }
            });
        }

        async function toggleRead(id, btn) {
            const card = btn.closest('.message-card');
            const isRead = card.classList.contains('unread') ? false : true; // Current state (stored in UI logic somewhat inversely)
            // Wait, logic: if 'unread' class exists, it is currently UNREAD (0). We want to make it READ (1).
            // If 'unread' class does NOT exist, it is READ (1). We want to make it UNREAD (0).

            const newState = card.classList.contains('unread') ? 1 : 0;

            try {
                const response = await fetch('../assets/php/mark_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id,
                        read: newState
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (newState === 1) {
                        card.classList.remove('unread');
                        btn.innerHTML = '<i class="fas fa-envelope-open"></i>';
                        btn.title = "Mark as Unread";
                    } else {
                        card.classList.add('unread');
                        btn.innerHTML = '<i class="fas fa-envelope"></i>';
                        btn.title = "Mark as Read";
                    }
                } else {
                    alert('Error updating status: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error');
            }
        }
    </script>
</body>

</html>