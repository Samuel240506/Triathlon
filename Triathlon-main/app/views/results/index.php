<div class="page-title">
    <h1>Gestion des résultats</h1>
</div>

<div class="filters">
    <div class="filter-group">
        <label>Sélectionner un triathlon</label>
        <select id="triathlon-select" class="form-control">
            <option value="">Choisir un triathlon</option>
            <?php if (isset($data['triathlons'])): ?>
                <?php foreach ($data['triathlons'] as $triathlon): ?>
                    <option value="<?= $triathlon['id'] ?>" <?= (isset($_GET['triathlon_id']) && $_GET['triathlon_id'] == $triathlon['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($triathlon['name']) ?> - <?= htmlspecialchars($triathlon['location']) ?> (<?= date('d/m/Y', strtotime($triathlon['event_date'])) ?>)
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
</div>

<?php if (isset($_GET['triathlon_id']) && !empty($_GET['triathlon_id'])): ?>
    <?php
    $triathlonId = $_GET['triathlon_id'];
    $triathlon = null;
    if (isset($data['triathlons'])) {
        foreach ($data['triathlons'] as $t) {
            if ($t['id'] == $triathlonId) {
                $triathlon = $t;
                break;
            }
        }
    }
    ?>

    <?php if ($triathlon): ?>
        <div class="triathlon-info">
            <h2>Résultats : <?= htmlspecialchars($triathlon['name']) ?></h2>
            <p><strong>Lieu :</strong> <?= htmlspecialchars($triathlon['location']) ?> | <strong>Date :</strong> <?= date('d/m/Y', strtotime($triathlon['event_date'])) ?> | <strong>Type :</strong> <?= htmlspecialchars($triathlon['type']) ?></p>
        </div>

        <div class="table-container">
            <div style="padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h3>Classement général</h3>
                <div>
                    <button class="btn" onclick="exportResults()">
                        <i class="fas fa-download"></i> Exporter
                    </button>
                    <button class="btn" onclick="importResults()">
                        <i class="fas fa-upload"></i> Importer
                    </button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Dossard</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Club</th>
                        <th>Catégorie</th>
                        <th>Temps total</th>
                        <th>Natation</th>
                        <th>Vélo</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="results-body">
                    <?php if (isset($data['results']) && !empty($data['results'])): ?>
                        <?php foreach ($data['results'] as $index => $result): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($result['bib_number'] ?? '') ?></td>
                                <td><?= htmlspecialchars($result['last_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($result['first_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($result['club_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($result['category'] ?? '') ?></td>
                                <td class="time-cell"><?= htmlspecialchars($result['total_time'] ?? '') ?></td>
                                <td class="time-cell"><?= htmlspecialchars($result['swim_time'] ?? '') ?></td>
                                <td class="time-cell"><?= htmlspecialchars($result['bike_time'] ?? '') ?></td>
                                <td class="time-cell"><?= htmlspecialchars($result['run_time'] ?? '') ?></td>
                                <td class="actions">
                                    <button class="action-btn edit" onclick="editResult(<?= $result['id'] ?? 0 ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn delete" onclick="deleteResult(<?= $result['id'] ?? 0 ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 40px; color: var(--dark-gray);">
                                Aucun résultat enregistré pour ce triathlon.<br>
                                <button class="btn" style="margin-top: 15px;" onclick="addResult()">
                                    <i class="fas fa-plus"></i> Ajouter un résultat
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="background: #fee; color: #c33; padding: 20px; border-radius: 4px; text-align: center;">
            Triathlon non trouvé.
        </div>
    <?php endif; ?>
<?php else: ?>
    <div style="background: #f9f9f9; padding: 60px; text-align: center; border-radius: 8px; margin-top: 30px;">
        <i class="fas fa-chart-bar" style="font-size: 4rem; color: var(--fftri-blue); margin-bottom: 20px;"></i>
        <h3>Sélectionnez un triathlon</h3>
        <p>Choisissez un triathlon dans la liste ci-dessus pour consulter et gérer ses résultats.</p>
    </div>
<?php endif; ?>

<!-- Modal d'ajout/édition de résultat -->
<div id="resultModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
    <div style="background: white; border-radius: 8px; width: 90%; max-width: 600px; max-height: 90%; overflow-y: auto;">
        <div style="padding: 20px; border-bottom: 1px solid #eee;">
            <h3 id="modal-title">Ajouter un résultat</h3>
            <button onclick="closeModal()" style="float: right; background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form id="resultForm" style="padding: 20px;">
            <input type="hidden" id="result_id" name="result_id">
            <input type="hidden" id="triathlon_id" name="triathlon_id" value="<?= htmlspecialchars($_GET['triathlon_id'] ?? '') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="licencie_id">Licencié *</label>
                    <select id="licencie_id" name="licencie_id" class="form-control" required>
                        <option value="">Sélectionner un licencié</option>
                        <?php if (isset($data['licencies'])): ?>
                            <?php foreach ($data['licencies'] as $licencie): ?>
                                <option value="<?= $licencie['id'] ?>">
                                    <?= htmlspecialchars($licencie['first_name'] . ' ' . $licencie['last_name']) ?> (<?= htmlspecialchars($licencie['license_number']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="bib_number">Numéro de dossard *</label>
                    <input type="number" id="bib_number" name="bib_number" class="form-control" required min="1">
                </div>

                <div class="form-group">
                    <label for="swim_time">Temps natation (HH:MM:SS)</label>
                    <input type="text" id="swim_time" name="swim_time" class="form-control" placeholder="00:15:30">
                </div>

                <div class="form-group">
                    <label for="bike_time">Temps vélo (HH:MM:SS)</label>
                    <input type="text" id="bike_time" name="bike_time" class="form-control" placeholder="01:20:45">
                </div>

                <div class="form-group">
                    <label for="run_time">Temps course (HH:MM:SS)</label>
                    <input type="text" id="run_time" name="run_time" class="form-control" placeholder="00:45:20">
                </div>

                <div class="form-group">
                    <label for="total_time">Temps total (HH:MM:SS)</label>
                    <input type="text" id="total_time" name="total_time" class="form-control" placeholder="02:21:35">
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<style>
.filters {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.filter-group {
    margin-bottom: 15px;
}

.filter-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.triathlon-info {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.triathlon-info h2 {
    color: var(--fftri-blue);
    margin-bottom: 10px;
}

.time-cell {
    font-family: 'Courier New', monospace;
    font-weight: bold;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.btn-secondary {
    background-color: var(--dark-gray);
    color: white;
}

.btn-secondary:hover {
    background-color: #333;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.getElementById('triathlon-select').addEventListener('change', function() {
    const triathlonId = this.value;
    if (triathlonId) {
        window.location.href = `index.php?module=resultats&triathlon_id=${triathlonId}`;
    } else {
        window.location.href = 'index.php?module=resultats';
    }
});

function addResult() {
    document.getElementById('modal-title').textContent = 'Ajouter un résultat';
    document.getElementById('resultForm').reset();
    document.getElementById('result_id').value = '';
    document.getElementById('resultModal').style.display = 'flex';
}

function editResult(resultId) {
    // Ici, vous devriez charger les données du résultat via AJAX
    document.getElementById('modal-title').textContent = 'Modifier le résultat';
    document.getElementById('result_id').value = resultId;
    document.getElementById('resultModal').style.display = 'flex';
    // TODO: Charger les données existantes
}

function deleteResult(resultId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce résultat ?')) {
        // TODO: Implémenter la suppression
        console.log('Supprimer résultat:', resultId);
    }
}

function closeModal() {
    document.getElementById('resultModal').style.display = 'none';
}

function exportResults() {
    // TODO: Implémenter l'export
    alert('Fonctionnalité d\'export à implémenter');
}

function importResults() {
    // TODO: Implémenter l'import
    alert('Fonctionnalité d\'import à implémenter');
}

// Fermer la modal en cliquant en dehors
document.getElementById('resultModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
