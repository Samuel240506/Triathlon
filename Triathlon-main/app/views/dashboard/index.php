<?php
$isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';
?>

<div class="page-title">
    <div>
        <h1><?= $isAdmin ? 'Tableau de bord Admin' : 'Mon tableau de bord' ?></h1>
        <div class="page-title-sub">Vue d'ensemble de l'activité FFTRI</div>
    </div>
</div>

<?php if ($isAdmin): ?>
<!-- ======================== ADMIN DASHBOARD ======================== -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-shield-halved"></i>
        </div>
        <div class="stat-content">
            <h3><?= $data['clubs_count'] ?? 0 ?></h3>
            <p>Clubs affiliés</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-id-card"></i>
        </div>
        <div class="stat-content">
            <h3><?= $data['licencies_count'] ?? 0 ?></h3>
            <p>Licenciés actifs</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <i class="fas fa-trophy"></i>
        </div>
        <div class="stat-content">
            <h3><?= $data['triathlons_count'] ?? 0 ?></h3>
            <p>Triathlons à venir</p>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3><i class="fas fa-shield-halved" style="color: var(--fftri-blue); margin-right: 8px;"></i>Derniers clubs ajoutés</h3>
        <a href="index.php?module=clubs&action=create" class="btn">
            <i class="fas fa-plus"></i> Ajouter un club
        </a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nom du club</th>
                <th>Ville</th>
                <th>Téléphone</th>
                <th>Licenciés</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['recent_clubs'])): ?>
                <?php foreach ($data['recent_clubs'] as $club): ?>
                <tr>
                    <td>
                        <div style="font-weight: 600;"><?= htmlspecialchars($club['name']) ?></div>
                    </td>
                    <td>
                        <span class="badge badge-blue"><?= htmlspecialchars($club['city']) ?></span>
                    </td>
                    <td><?= htmlspecialchars($club['phone'] ?? '—') ?></td>
                    <td>
                        <span style="font-weight: 700; color: var(--fftri-blue);"><?= $club['licencies_count'] ?? 0 ?></span>
                    </td>
                    <td class="actions">
                        <a href="index.php?module=clubs&action=edit&id=<?= $club['id'] ?>" class="action-btn edit" title="Modifier">
                            <i class="fas fa-pen"></i>
                        </a>
                        <a href="index.php?module=clubs&action=delete&id=<?= $club['id'] ?>" class="action-btn delete" title="Supprimer"
                           onclick="return confirm('Supprimer ce club ?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 32px; color: var(--text-muted);">
                        <i class="fas fa-inbox" style="font-size: 2rem; display:block; margin-bottom: 8px; opacity: 0.4;"></i>
                        Aucun club trouvé
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php else: ?>
<!-- ======================== CLUB MANAGER DASHBOARD ======================== -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3><?= $data['licencies_count'] ?? 0 ?></h3>
            <p>Licenciés de mon club</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
            <h3><?= $data['registrations_count'] ?? 0 ?></h3>
            <p>Inscriptions en cours</p>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 4px;">
    <div class="table-container">
        <div class="table-header">
            <h3><i class="fas fa-users" style="color: var(--fftri-blue); margin-right: 8px;"></i>Mes licenciés</h3>
            <a href="index.php?module=licencies&action=create" class="btn">
                <i class="fas fa-plus"></i> Ajouter
            </a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Licence</th>
                    <th>Catégorie</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['my_licencies'])): ?>
                    <?php foreach ($data['my_licencies'] as $licencie): ?>
                    <tr>
                        <td style="font-weight: 600;"><?= htmlspecialchars($licencie['last_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($licencie['first_name'] ?? '') ?></td>
                        <td><code style="font-size: 0.78rem; background: var(--light-gray); padding: 2px 6px; border-radius: 4px;"><?= htmlspecialchars($licencie['license_number'] ?? '') ?></code></td>
                        <td>
                            <?php
                            $cat = $licencie['category'] ?? '';
                            $catClass = $cat === 'Junior' ? 'badge-amber' : ($cat === 'Vétéran' ? 'badge-gray' : 'badge-blue');
                            ?>
                            <span class="badge <?= $catClass ?>"><?= htmlspecialchars($cat) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 24px; color: var(--text-muted);">
                            Aucun licencié
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h3><i class="fas fa-trophy" style="color: var(--fftri-red); margin-right: 8px;"></i>Inscriptions triathlons</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Triathlon</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['my_registrations'])): ?>
                    <?php foreach ($data['my_registrations'] as $reg): ?>
                    <tr>
                        <td style="font-weight: 600;"><?= htmlspecialchars($reg['triathlon_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($reg['event_date'] ?? 'now'))) ?></td>
                        <td>
                            <?php
                            $status = $reg['status'] ?? '';
                            $statusClass = $status === 'Terminé' ? 'badge-gray' : ($status === 'Forfait' ? 'badge-red' : 'badge-green');
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($status) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center; padding: 24px; color: var(--text-muted);">
                            Aucune inscription
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
<?php endif; ?>
