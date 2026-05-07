<div class="page-title">
    <div>
        <h1>Gestion des licenciés</h1>
        <div class="page-title-sub"><?= count($data['licencies'] ?? []) ?> licencié(s) trouvé(s)</div>
    </div>
    <a href="index.php?module=licencies&action=create" class="btn">
        <i class="fas fa-plus"></i> Ajouter un licencié
    </a>
</div>

<?php if (isset($data['error'])): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
<?php endif; ?>

<!-- Filtres -->
<form method="GET" action="index.php">
    <input type="hidden" name="module" value="licencies">
    <div class="filters">
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <div class="filter-group">
            <label>Club</label>
            <select name="club_id" class="form-control">
                <option value="">Tous les clubs</option>
                <?php foreach ($data['clubs'] ?? [] as $club): ?>
                    <option value="<?= $club['id'] ?>"
                        <?= ($data['filters']['club_id'] ?? '') == $club['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($club['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="filter-group">
            <label>Catégorie</label>
            <select name="category" class="form-control">
                <option value="">Toutes</option>
                <?php foreach (['Junior', 'Senior', 'Vétéran'] as $cat): ?>
                    <option value="<?= $cat ?>" <?= ($data['filters']['category'] ?? '') === $cat ? 'selected' : '' ?>>
                        <?= $cat ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Type de licence</label>
            <select name="license_type" class="form-control">
                <option value="">Tous</option>
                <?php foreach (['Compétition', 'Loisir'] as $type): ?>
                    <option value="<?= $type ?>" <?= ($data['filters']['license_type'] ?? '') === $type ? 'selected' : '' ?>>
                        <?= $type ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group" style="flex: 0;">
            <label>&nbsp;</label>
            <button type="submit" class="btn">
                <i class="fas fa-filter"></i> Filtrer
            </button>
        </div>
        <?php if (!empty($data['filters'])): ?>
        <div class="filter-group" style="flex: 0; align-self: flex-end;">
            <a href="index.php?module=licencies" class="btn btn-secondary">
                <i class="fas fa-xmark"></i> Réinitialiser
            </a>
        </div>
        <?php endif; ?>
    </div>
</form>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Licencié</th>
                <th>N° Licence</th>
                <th>Genre</th>
                <th>Catégorie</th>
                <th>Type</th>
                <th>Club</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['licencies'])): ?>
                <?php foreach ($data['licencies'] as $licencie): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 34px; height: 34px; border-radius: 50%;
                                background: linear-gradient(135deg, var(--fftri-blue), var(--fftri-blue-light));
                                display: flex; align-items: center; justify-content: center;
                                color: white; font-size: 0.75rem; font-weight: 700; flex-shrink: 0;">
                                <?= strtoupper(substr($licencie['name'] ?? 'X', 0, 1)) ?>
                            </div>
                            <div style="font-weight: 600;"><?= htmlspecialchars($licencie['name'] ?? '') ?></div>
                        </div>
                    </td>
                    <td>
                        <code style="font-size: 0.78rem; background: var(--light-gray); padding: 3px 8px; border-radius: 4px;">
                            <?= htmlspecialchars($licencie['license_number'] ?? $licencie['id'] ?? '') ?>
                        </code>
                    </td>
                    <td>
                        <?php $gender = $licencie['gender'] ?? ''; ?>
                        <span class="badge <?= $gender === 'F' ? 'badge-red' : 'badge-blue' ?>">
                            <?= $gender === 'F' ? '♀ Femme' : '♂ Homme' ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $cat = $licencie['category'] ?? '';
                        $catClass = $cat === 'Junior' ? 'badge-amber' : ($cat === 'Vétéran' ? 'badge-gray' : 'badge-blue');
                        ?>
                        <span class="badge <?= $catClass ?>"><?= htmlspecialchars($cat) ?></span>
                    </td>
                    <td>
                        <?php $ltype = $licencie['license_type'] ?? ''; ?>
                        <span class="badge <?= $ltype === 'Compétition' ? 'badge-red' : 'badge-green' ?>">
                            <?= htmlspecialchars($ltype) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($licencie['club_name'] ?? '—') ?></td>
                    <td class="actions">
                        <a href="index.php?module=licencies&action=edit&id=<?= htmlspecialchars($licencie['id'] ?? '') ?>"
                           class="action-btn edit" title="Modifier">
                            <i class="fas fa-pen"></i>
                        </a>
                        <a href="index.php?module=licencies&action=delete&id=<?= htmlspecialchars($licencie['id'] ?? '') ?>"
                           class="action-btn delete" title="Supprimer"
                           onclick="return confirm('Supprimer ce licencié ?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-users" style="font-size: 2.5rem; display:block; margin-bottom: 12px; opacity: 0.3;"></i>
                        Aucun licencié trouvé
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
