<div class="page-title">
    <div>
        <h1>Ajouter un club</h1>
    </div>
    <a href="index.php?module=clubs" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<?php if (isset($data['error'])): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="index.php?module=clubs&action=create">
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Nom du club <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" name="name" id="name" class="form-control"
                       placeholder="Ex: Triathlon Club Orléans" required
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="city">Ville <span style="color: var(--fftri-red)">*</span></label>
                <input type="text" name="city" id="city" class="form-control"
                       placeholder="Ex: Orléans" required
                       value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" name="address" id="address" class="form-control"
                       placeholder="Ex: 12 Rue de la Loire"
                       value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input type="text" name="phone" id="phone" class="form-control"
                       placeholder="Ex: 02 38 42 00 00"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="email">Email de contact</label>
                <input type="email" name="email" id="email" class="form-control"
                       placeholder="contact@club.fr"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Créer le club
            </button>
            <a href="index.php?module=clubs" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
