<div class="page-title">
    <div>
        <h1>Modifier le licencié</h1>
        <?php if (isset($data['licencie'])): ?>
        <div class="page-title-sub"><?= htmlspecialchars($data['licencie']['name'] ?? '') ?></div>
        <?php endif; ?>
    </div>
    <a href="index.php?module=licencies" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<?php if (isset($data['error'])): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
<?php endif; ?>

<?php if (isset($data['licencie'])): ?>
<?php $l = $data['licencie']; ?>
<div class="form-container">
    <form method="POST" action="index.php?module=licencies&action=edit&id=<?= htmlspecialchars($l['id'] ?? '') ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="license_number">Numéro de licence <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" id="license_number" name="license_number" class="form-control" required
                       value="<?= htmlspecialchars($_POST['license_number'] ?? $l['license_number'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="first_name">Prénom <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" id="first_name" name="first_name" class="form-control" required
                       value="<?= htmlspecialchars($_POST['first_name'] ?? $l['first_name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="last_name">Nom <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" id="last_name" name="last_name" class="form-control" required
                       value="<?= htmlspecialchars($_POST['last_name'] ?? $l['last_name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="birth_date">Date de naissance</label>
                <input type="date" id="birth_date" name="birth_date" class="form-control"
                       value="<?= htmlspecialchars($_POST['birth_date'] ?? $l['birth_date'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="gender">Genre</label>
                <select id="gender" name="gender" class="form-control">
                    <?php $g = $_POST['gender'] ?? $l['gender'] ?? 'M'; ?>
                    <option value="M" <?= $g === 'M' ? 'selected' : '' ?>>♂ Homme</option>
                    <option value="F" <?= $g === 'F' ? 'selected' : '' ?>>♀ Femme</option>
                </select>
            </div>

            <div class="form-group">
                <label for="category">Catégorie</label>
                <select id="category" name="category" class="form-control">
                    <?php $selectedCat = $_POST['category'] ?? $l['category'] ?? 'Senior'; ?>
                    <?php foreach (['Junior', 'Senior', 'Vétéran'] as $cat): ?>
                        <option value="<?= $cat ?>" <?= $selectedCat === $cat ? 'selected' : '' ?>>
                            <?= $cat ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="license_type">Type de licence</label>
                <select id="license_type" name="license_type" class="form-control">
                    <?php $selectedType = $_POST['license_type'] ?? $l['license_type'] ?? 'Loisir'; ?>
                    <?php foreach (['Compétition', 'Loisir'] as $type): ?>
                        <option value="<?= $type ?>" <?= $selectedType === $type ? 'selected' : '' ?>>
                            <?= $type ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="club_id">Club <span style="color: var(--fftri-red)">*</span></label>
                <select id="club_id" name="club_id" class="form-control">
                    <option value="">— Choisir un club —</option>
                    <?php foreach ($data['clubs'] ?? [] as $club): ?>
                        <?php $sel = (($_POST['club_id'] ?? $l['club_id'] ?? null) == $club['id']); ?>
                        <option value="<?= $club['id'] ?>" <?= $sel ? 'selected' : '' ?>>
                            <?= htmlspecialchars($club['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input type="text" id="phone" name="phone" class="form-control"
                       value="<?= htmlspecialchars($_POST['phone'] ?? $l['phone'] ?? '') ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
            <a href="index.php?module=licencies" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
<?php else: ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> Licencié non trouvé.</div>
<?php endif; ?>
