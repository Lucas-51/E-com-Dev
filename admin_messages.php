<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?redirect=admin_messages.php');
    exit;
}

// Vérifier le rôle admin
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Traitement des actions (marquer comme lu, supprimer)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['message_id'])) {
        $messageId = (int)$_POST['message_id'];
        
        if ($_POST['action'] === 'mark_read') {
            $stmt = $pdo->prepare("UPDATE messages_contact SET lu = TRUE WHERE id = ?");
            $stmt->execute([$messageId]);
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM messages_contact WHERE id = ?");
            $stmt->execute([$messageId]);
        }
        
        header('Location: admin_messages.php');
        exit;
    }
}

// Récupérer tous les messages
$stmt = $pdo->query("SELECT * FROM messages_contact ORDER BY date_creation DESC");
$messages = $stmt->fetchAll();

// Compter les messages non lus
$stmt = $pdo->query("SELECT COUNT(*) FROM messages_contact WHERE lu = FALSE");
$nonLus = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Messages de Contact</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .messages-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .messages-table th,
        .messages-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .messages-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .message-row.unread {
            background-color: #fff3cd;
        }
        
        .message-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-read { background: #28a745; color: white; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-back { background: #6c757d; color: white; }
        .btn-reply { background: #007bff; color: white; }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-read { background: #d4edda; color: #155724; }
        .status-unread { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-envelope"></i> Administration - Messages de Contact</h1>
            <a href="index.php" class="btn-small btn-back">
                <i class="fas fa-arrow-left"></i> Retour au site
            </a>
        </div>
        
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($messages) ?></div>
                <div>Messages total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $nonLus ?></div>
                <div>Messages non lus</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count($messages) - $nonLus ?></div>
                <div>Messages lus</div>
            </div>
        </div>
        
        <?php if (empty($messages)): ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <i class="fas fa-inbox" style="font-size: 3em; margin-bottom: 20px;"></i>
                <h3>Aucun message reçu</h3>
                <p>Les messages de contact apparaîtront ici.</p>
            </div>
        <?php else: ?>
            <table class="messages-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr class="message-row <?= !$message['lu'] ? 'unread' : '' ?>">
                            <td><?= date('d/m/Y H:i', strtotime($message['date_creation'])) ?></td>
                            <td><strong><?= htmlspecialchars($message['nom']) ?></strong></td>
                            <td><?= htmlspecialchars($message['email']) ?></td>
                            <td class="message-preview" title="<?= htmlspecialchars($message['message']) ?>">
                                <?= htmlspecialchars($message['message']) ?>
                            </td>
                            <td>
                                <span class="status-badge <?= $message['lu'] ? 'status-read' : 'status-unread' ?>">
                                    <?= $message['lu'] ? 'Lu' : 'Non lu' ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <a href="mailto:<?= htmlspecialchars($message['email']) ?>?subject=Re: Message de contact&body=Bonjour <?= htmlspecialchars($message['nom']) ?>,%0D%0A%0D%0AVotre message:%0D%0A<?= htmlspecialchars($message['message']) ?>%0D%0A%0D%0ANotre réponse:%0D%0A" 
                                   class="btn-small btn-reply" title="Répondre">
                                    <i class="fas fa-reply"></i>
                                </a>
                                <?php if (!$message['lu']): ?>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                        <button type="submit" class="btn-small btn-read" title="Marquer comme lu">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form method="post" style="display: inline;" onsubmit="return confirm('Supprimer ce message ?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                    <button type="submit" class="btn-small btn-delete" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
