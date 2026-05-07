<div class="page-title">
    <div>
        <h1>Paramètres</h1>
        <div class="page-title-sub">Gérez votre profil et vos préférences</div>
    </div>
</div>

<div class="settings-wrapper">
    <nav class="settings-nav">
        <ul>
            <li class="active" data-tab="profile">
                <i class="fas fa-user"></i> Profil
            </li>
            <li data-tab="security">
                <i class="fas fa-lock"></i> Sécurité
            </li>
            <li data-tab="preferences">
                <i class="fas fa-palette"></i> Préférences
            </li>
        </ul>
    </nav>

    <div class="settings-body">
        <!-- ===== PROFIL ===== -->
        <div id="profile-tab" class="settings-tab active">
            <h2>Mon profil</h2>

            <?php if (isset($data['error'])): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>
            <?php if (isset($data['success'])): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($data['success']) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?module=settings&action=updateProfile">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="<?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Rôle</label>
                        <input type="text" class="form-control" readonly
                               value="<?= ($_SESSION['user']['role'] ?? '') === 'admin' ? 'Administrateur' : 'Responsable Club' ?>">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Mettre à jour le profil
                    </button>
                </div>
            </form>
        </div>

        <!-- ===== SÉCURITÉ ===== -->
        <div id="security-tab" class="settings-tab">
            <h2>Sécurité</h2>

            <?php if (isset($data['error'])): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <div class="settings-section">
                <h3><i class="fas fa-key"></i> Changer le mot de passe</h3>
                <form method="POST" action="index.php?module=settings&action=changePassword">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">Nouveau mot de passe</label>
                            <input type="password" id="new_password" name="new_password"
                                   class="form-control" required minlength="8">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                            <input type="password" id="confirm_password" name="confirm_password"
                                   class="form-control" required minlength="8">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">
                            <i class="fas fa-key"></i> Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>

            <div class="settings-section">
                <h3><i class="fas fa-desktop"></i> Session en cours</h3>
                <div class="session-info">
                    <div class="session-row">
                        <span class="session-label">Adresse IP</span>
                        <span><?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '—') ?></span>
                    </div>
                    <div class="session-row">
                        <span class="session-label">Navigateur</span>
                        <span style="font-size: 0.8rem; max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?= htmlspecialchars(substr($_SERVER['HTTP_USER_AGENT'] ?? '—', 0, 80)) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== PRÉFÉRENCES ===== -->
        <div id="preferences-tab" class="settings-tab">
            <h2>Préférences</h2>

            <form method="POST" action="index.php?module=settings&action=updatePreferences">
                <div class="settings-section">
                    <h3><i class="fas fa-palette"></i> Thème de l'interface</h3>
                    <?php $themePref = $_SESSION['user']['preferences']['theme'] ?? 'light'; ?>
                    <div class="theme-selector">
                        <label class="theme-option <?= $themePref === 'light' ? 'selected' : '' ?>">
                            <input type="radio" name="theme" value="light"
                                   <?= $themePref === 'light' ? 'checked' : '' ?>>
                            <div class="theme-preview light-preview">
                                <div class="preview-header"></div>
                                <div class="preview-content"></div>
                            </div>
                            <span>Thème clair</span>
                        </label>
                        <label class="theme-option <?= $themePref === 'dark' ? 'selected' : '' ?>">
                            <input type="radio" name="theme" value="dark"
                                   <?= $themePref === 'dark' ? 'checked' : '' ?>>
                            <div class="theme-preview dark-preview">
                                <div class="preview-header"></div>
                                <div class="preview-content"></div>
                            </div>
                            <span>Thème sombre</span>
                        </label>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Enregistrer les préférences
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.settings-wrapper {
    display: flex;
    gap: 0;
    background: var(--white);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--medium-gray);
    overflow: hidden;
}

.settings-nav {
    width: 220px;
    background: var(--light-gray);
    border-right: 1px solid var(--medium-gray);
    flex-shrink: 0;
}

.settings-nav ul {
    list-style: none;
    padding: 12px 0;
}

.settings-nav li {
    padding: 12px 20px;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}

.settings-nav li:hover {
    background: rgba(0,51,160,0.05);
    color: var(--fftri-blue);
}

.settings-nav li.active {
    background: white;
    color: var(--fftri-blue);
    font-weight: 600;
    border-left-color: var(--fftri-blue);
}

.settings-body {
    flex: 1;
    padding: 32px;
    min-width: 0;
}

.settings-tab { display: none; }
.settings-tab.active { display: block; }

.settings-tab h2 {
    font-size: 1.2rem;
    color: var(--text-color);
    margin-bottom: 24px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--fftri-blue);
}

.settings-section {
    margin-bottom: 32px;
    padding-bottom: 28px;
    border-bottom: 1px solid var(--medium-gray);
}

.settings-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.settings-section h3 {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.session-info {
    background: var(--light-gray);
    border-radius: var(--radius-sm);
    padding: 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.session-row {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 0.875rem;
}

.session-label {
    font-weight: 600;
    color: var(--text-muted);
    min-width: 120px;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

/* Theme selector */
.theme-selector {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.theme-option {
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    font-weight: 500;
}

.theme-option input { display: none; }

.theme-preview {
    width: 120px;
    height: 80px;
    border-radius: 8px;
    border: 2px solid var(--medium-gray);
    overflow: hidden;
    transition: all 0.2s;
}

.theme-option.selected .theme-preview,
.theme-option:has(input:checked) .theme-preview {
    border-color: var(--fftri-blue);
    box-shadow: 0 0 0 3px rgba(0,51,160,0.15);
}

.light-preview {
    background: #f4f6fa;
}

.light-preview .preview-header {
    height: 20px;
    background: #0033A0;
}

.light-preview .preview-content {
    height: 60px;
    background: white;
    margin: 6px;
    border-radius: 4px;
}

.dark-preview {
    background: #1a1a2e;
}

.dark-preview .preview-header {
    height: 20px;
    background: #0056b3;
}

.dark-preview .preview-content {
    height: 60px;
    background: #2c2c4a;
    margin: 6px;
    border-radius: 4px;
}

@media (max-width: 768px) {
    .settings-wrapper { flex-direction: column; }
    .settings-nav { width: 100%; border-right: none; border-bottom: 1px solid var(--medium-gray); }
    .settings-nav ul { display: flex; overflow-x: auto; padding: 0; }
    .settings-nav li { white-space: nowrap; border-left: none; border-bottom: 3px solid transparent; }
    .settings-nav li.active { border-bottom-color: var(--fftri-blue); border-left-color: transparent; }
    .settings-body { padding: 20px; }
}
</style>

<script>
document.querySelectorAll('.settings-nav li').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.settings-nav li').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.settings-tab').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.dataset.tab + '-tab').classList.add('active');
    });
});

document.querySelectorAll('.theme-option input').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.theme-option').forEach(o => o.classList.remove('selected'));
        this.closest('.theme-option').classList.add('selected');
    });
});
</script>
