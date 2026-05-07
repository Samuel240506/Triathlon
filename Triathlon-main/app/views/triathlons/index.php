<div class="page-title">
    <div>
        <h1>Gestion des triathlons</h1>
        <div class="page-title-sub"><?= count($data['triathlons'] ?? []) ?> épreuve(s) enregistrée(s)</div>
    </div>
    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
    <a href="index.php?module=triathlons&action=create" class="btn">
        <i class="fas fa-plus"></i> Créer un triathlon
    </a>
    <?php endif; ?>
</div>

<?php if (isset($data['error'])): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Nom de l'épreuve</th>
                <th>Type</th>
                <th>Lieu</th>
                <th>Date</th>
                <th>Participants max</th>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['triathlons'])): ?>
                <?php foreach ($data['triathlons'] as $triathlon): ?>
                <?php
                $eventDate = strtotime($triathlon['event_date'] ?? 'now');
                $isPast = $eventDate < time();
                ?>
                <tr>
                    <td>
                        <div style="font-weight: 600;"><?= htmlspecialchars($triathlon['name']) ?></div>
                        <?php if ($isPast): ?>
                        <div style="font-size: 0.72rem; color: var(--text-muted);">Épreuve passée</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-blue"><?= htmlspecialchars($triathlon['type'] ?? '—') ?></span>
                    </td>
                    <td>
                        <i class="fas fa-location-dot" style="color: var(--fftri-red); margin-right: 4px;"></i>
                        <?= htmlspecialchars($triathlon['location'] ?? '—') ?>
                    </td>
                    <td>
                        <span class="badge <?= $isPast ? 'badge-gray' : 'badge-green' ?>">
                            <?= date('d/m/Y', $eventDate) ?>
                        </span>
                    </td>
                    <td>
                        <?= $triathlon['max_participants'] ? htmlspecialchars($triathlon['max_participants']) . ' places' : '<span style="color: var(--text-muted);">—</span>' ?>
                    </td>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <td class="actions">
                        <a href="index.php?module=triathlons&action=edit&id=<?= $triathlon['id'] ?>"
                           class="action-btn edit" title="Modifier">
                            <i class="fas fa-pen"></i>
                        </a>
                        <a href="index.php?module=triathlons&action=delete&id=<?= $triathlon['id'] ?>"
                           class="action-btn delete" title="Supprimer"
                           onclick="return confirm('Supprimer le triathlon « <?= htmlspecialchars(addslashes($triathlon['name'])) ?> » ?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $_SESSION['user']['role'] === 'admin' ? 6 : 5 ?>"
                        style="text-align:center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-trophy" style="font-size: 2.5rem; display:block; margin-bottom: 12px; opacity: 0.3;"></i>
                        Aucun triathlon trouvé
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
