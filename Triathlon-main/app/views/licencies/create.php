<?php
$errors = $data['errors'] ?? [];
$old    = $data['old'] ?? [];
$clubs  = $data['clubs'] ?? [];
?>

<div class="page-title">
    <div>
        <h1>Ajouter un licencié</h1>
    </div>
    <a href="index.php?module=licencies" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="index.php?module=licencies&action=create">
        <div class="form-grid">
            <div class="form-group">
                <label for="license_number">Numéro de licence <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" name="license_number" id="license_number" class="form-control"
                       placeholder="Ex: FFTRI2025001" required
                       value="<?= htmlspecialchars($old['license_number'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="first_name">Prénom <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" name="first_name" id="first_name" class="form-control"
                       placeholder="Ex: Pierre" required
                       value="<?= htmlspecialchars($old['first_name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="last_name">Nom <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" name="last_name" id="last_name" class="form-control"
                       placeholder="Ex: Martin" required
                       value="<?= htmlspecialchars($old['last_name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="birth_date">Date de naissance</label>
                <input type="date" name="birth_date" id="birth_date" class="form-control"
                       value="<?= htmlspecialchars($old['birth_date'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="gender">Genre</label>
                <select name="gender" id="gender" class="form-control">
                    <option value="M" <?= ($old['gender'] ?? '') === 'M' ? 'selected' : '' ?>>♂ Homme</option>
                    <option value="F" <?= ($old['gender'] ?? '') === 'F' ? 'selected' : '' ?>>♀ Femme</option>
                </select>
            </div>

            <div class="form-group">
                <label for="category">Catégorie</label>
                <select name="category" id="category" class="form-control">
                    <?php foreach (['Junior', 'Senior', 'Vétéran'] as $cat): ?>
                        <option value="<?= $cat ?>" <?= ($old['category'] ?? 'Senior') === $cat ? 'selected' : '' ?>>
                            <?= $cat ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="license_type">Type de licence</label>
                <select name="license_type" id="license_type" class="form-control">
                    <?php foreach (['Compétition', 'Loisir'] as $type): ?>
                        <option value="<?= $type ?>" <?= ($old['license_type'] ?? '') === $type ? 'selected' : '' ?>>
                            <?= $type ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="club_id">Club <span style="color: var(--fftri-red)">*</span></label>
                <select name="club_id" id="club_id" class="form-control">
                    <option value="">— Choisir un club —</option>
                    <?php foreach ($clubs as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"
                            <?= ($old['club_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input type="text" name="phone" id="phone" class="form-control"
                       placeholder="Ex: 06 12 34 56 78"
                       value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Ajouter le licencié
            </button>
            <a href="index.php?module=licencies" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
