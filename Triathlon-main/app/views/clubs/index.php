<div class="page-title">
    <div>
        <h1>Gestion des clubs</h1>
        <div class="page-title-sub"><?= count($data['clubs'] ?? []) ?> club(s) enregistré(s)</div>
    </div>
    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
    <a href="index.php?module=clubs&action=create" class="btn">
        <i class="fas fa-plus"></i> Ajouter un club
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
                <th>Nom du club</th>
                <th>Ville</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Licenciés</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['clubs'])): ?>
                <?php foreach ($data['clubs'] as $club): ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: var(--text-color);"><?= htmlspecialchars($club['name']) ?></div>
                        <?php if (!empty($club['address'])): ?>
                        <div style="font-size: 0.78rem; color: var(--text-muted);"><?= htmlspecialchars($club['address']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-blue"><?= htmlspecialchars($club['city'] ?? '—') ?></span></td>
                    <td><?= htmlspecialchars($club['phone'] ?? '—') ?></td>
                    <td>
                        <?php if (!empty($club['email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($club['email']) ?>" style="color: var(--fftri-blue); font-size: 0.85rem;">
                            <?= htmlspecialchars($club['email']) ?>
                        </a>
                        <?php else: ?>
                        <span style="color: var(--text-muted);">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="font-weight: 700; font-size: 1rem; color: var(--fftri-blue);">
                            <?= $club['licencies_count'] ?? 0 ?>
                        </span>
                    </td>
                    <td class="actions">
                        <?php if ($_SESSION['user']['role'] === 'admin' ||
                                  ($_SESSION['user']['role'] === 'club_manager' && ($_SESSION['user']['id_club'] ?? null) == $club['id'])): ?>
                        <a href="index.php?module=clubs&action=edit&id=<?= $club['id'] ?>"
                           class="action-btn edit" title="Modifier">
                            <i class="fas fa-pen"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <a href="index.php?module=clubs&action=delete&id=<?= $club['id'] ?>"
                           class="action-btn delete" title="Supprimer"
                           onclick="return confirm('Supprimer le club « <?= htmlspecialchars(addslashes($club['name'])) ?> » ?')">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-shield-halved" style="font-size: 2.5rem; display:block; margin-bottom: 12px; opacity: 0.3;"></i>
                        Aucun club trouvé
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
