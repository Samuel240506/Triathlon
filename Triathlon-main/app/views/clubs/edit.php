<div class="page-title">
    <div>
        <h1>Modifier le club</h1>
        <?php if (isset($data['club'])): ?>
        <div class="page-title-sub"><?= htmlspecialchars($data['club']['name']) ?></div>
        <?php endif; ?>
    </div>
    <a href="index.php?module=clubs" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<?php if (isset($data['error'])): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
<?php endif; ?>

<?php if (isset($data['club'])): ?>
<div class="form-container">
    <form method="POST" action="index.php?module=clubs&action=edit&id=<?= $data['club']['id'] ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Nom du club <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" name="name" id="name" class="form-control" required
                       value="<?= htmlspecialchars($_POST['name'] ?? $data['club']['name']) ?>">
            </div>
            <div class="form-group">
                <label for="city">Ville <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" name="city" id="city" class="form-control" required
                       value="<?= htmlspecialchars($_POST['city'] ?? $data['club']['city'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" name="address" id="address" class="form-control"
                       value="<?= htmlspecialchars($_POST['address'] ?? $data['club']['address'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input type="text" name="phone" id="phone" class="form-control"
                       value="<?= htmlspecialchars($_POST['phone'] ?? $data['club']['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="email">Email de contact</label>
                <input type="email" name="email" id="email" class="form-control"
                       value="<?= htmlspecialchars($_POST['email'] ?? $data['club']['email'] ?? '') ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
            <a href="index.php?module=clubs" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
<?php else: ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> Club non trouvé.</div>
<?php endif; ?>
