<div class="page-title">
    <div>
        <h1>Modifier le triathlon</h1>
        <?php if (isset($data['triathlon'])): ?>
        <div class="page-title-sub"><?= htmlspecialchars($data['triathlon']['name']) ?></div>
        <?php endif; ?>
    </div>
    <a href="index.php?module=triathlons" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<?php if (isset($data['error'])): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
<?php endif; ?>

<?php if (isset($data['triathlon'])): ?>
<?php $t = $data['triathlon']; ?>
<div class="form-container">
    <form method="POST" action="index.php?module=triathlons&action=edit&id=<?= $t['id'] ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Nom du triathlon <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" id="name" name="name" class="form-control" required
                       value="<?= htmlspecialchars($_POST['name'] ?? $t['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="type">Type d'épreuve <span style="color: var(--fftri-red)">*</span></label>
                <select id="type" name="type" class="form-control" required>
                    <option value="">— Sélectionner —</option>
                    <?php foreach ($data['types'] ?? [] as $type): ?>
                        <?php $sel = (($_POST['type'] ?? $t['type'] ?? '') === $type['codeType']); ?>
                        <option value="<?= htmlspecialchars($type['codeType']) ?>" <?= $sel ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['codeType']) ?> — <?= htmlspecialchars($type['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="location">Lieu <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" id="location" name="location" class="form-control" required
                       value="<?= htmlspecialchars($_POST['location'] ?? $t['location'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="event_date">Date de l'épreuve <span style="color: var(--fftri-red)">*</span></label>
                <input type="date" id="event_date" name="event_date" class="form-control" required
                       value="<?= htmlspecialchars($_POST['event_date'] ?? $t['event_date'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="max_participants">Participants maximum</label>
                <input type="number" id="max_participants" name="max_participants" class="form-control" min="1"
                       value="<?= htmlspecialchars($_POST['max_participants'] ?? $t['max_participants'] ?? '') ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
            <a href="index.php?module=triathlons" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
<?php else: ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> Triathlon non trouvé.</div>
<?php endif; ?>
