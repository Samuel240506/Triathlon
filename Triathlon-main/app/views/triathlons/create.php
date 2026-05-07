<div class="page-title">
    <div>
        <h1>Créer un triathlon</h1>
    </div>
    <a href="index.php?module=triathlons" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<?php if (isset($data['error'])): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="index.php?module=triathlons&action=create">
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Nom du triathlon <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" id="name" name="name" class="form-control"
                       placeholder="Ex: Triathlon d'Orléans" required
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="type">Type d'épreuve <span style="color: var(--fftri-red)">*</span></label>
                <select id="type" name="type" class="form-control" required>
                    <option value="">— Sélectionner —</option>
                    <?php foreach ($data['types'] ?? [] as $type): ?>
                        <option value="<?= htmlspecialchars($type['codeType']) ?>"
                            <?= (($_POST['type'] ?? '') === $type['codeType']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['codeType']) ?> — <?= htmlspecialchars($type['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="location">Lieu <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" id="location" name="location" class="form-control"
                       placeholder="Ex: Orléans" required
                       value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="event_date">Date de l'épreuve <span style="color: var(--fftri-red)">*</span></label>
                <input type="date" id="event_date" name="event_date" class="form-control" required
                       value="<?= htmlspecialchars($_POST['event_date'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="max_participants">Participants maximum</label>
                <input type="number" id="max_participants" name="max_participants" class="form-control" min="1"
                       placeholder="Ex: 200"
                       value="<?= htmlspecialchars($_POST['max_participants'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="registration_deadline">Date limite d'inscription</label>
                <input type="date" id="registration_deadline" name="registration_deadline" class="form-control"
                       value="<?= htmlspecialchars($_POST['registration_deadline'] ?? '') ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Créer le triathlon
            </button>
            <a href="index.php?module=triathlons" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
