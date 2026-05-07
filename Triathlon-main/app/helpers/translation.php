<?php
$TRANSLATIONS = [
    'fr' => [
        'settings.title'               => 'Paramètres',
        'profile.title'                => 'Mon profil',
        'profile.name'                 => 'Nom complet',
        'profile.email'                => 'Adresse email',
        'profile.role'                 => 'Rôle',
        'profile.phone'                => 'Téléphone',
        'profile.club_name'            => 'Club',
        'profile.update'               => 'Mettre à jour',
        'security.title'               => 'Sécurité',
        'security.change_password'     => 'Changer le mot de passe',
        'security.current_password'    => 'Mot de passe actuel',
        'security.new_password'        => 'Nouveau mot de passe',
        'security.confirm_password'    => 'Confirmer le nouveau mot de passe',
        'security.sessions'            => 'Session en cours',
        'security.current_session'     => 'Connecté depuis',
        'security.ip'                  => 'Adresse IP',
        'security.browser'             => 'Navigateur',
        'security.logout_others'       => 'Déconnecter les autres sessions',
        'preferences.title'            => 'Préférences',
        'preferences.theme'            => 'Thème de l\'interface',
        'preferences.theme.light'      => 'Thème clair',
        'preferences.theme.dark'       => 'Thème sombre',
        'preferences.language'         => 'Langue',
        'preferences.language.fr'      => 'Français',
        'preferences.language.en'      => 'English',
        'preferences.save'             => 'Enregistrer les préférences',
        // Errors
        'error.not_authenticated'      => 'Vous n\'êtes pas connecté.',
        'error.missing_fields'         => 'Tous les champs obligatoires doivent être remplis.',
        'error.user_not_found'         => 'Utilisateur introuvable.',
        'error.update_failed'          => 'Erreur lors de la mise à jour.',
        'error.password_mismatch'      => 'Les mots de passe ne correspondent pas.',
        'error.password_short'         => 'Le mot de passe doit contenir au moins 8 caractères.',
        'error.password_incorrect'     => 'Mot de passe actuel incorrect.',
        'error.change_password_failed' => 'Erreur lors du changement de mot de passe.',
        // Success
        'message.profile_updated'      => 'Profil mis à jour avec succès.',
        'message.password_changed'     => 'Mot de passe modifié avec succès.',
    ],
];

function t(string $key): string {
    global $TRANSLATIONS;
    $lang = $_SESSION['user']['preferences']['language'] ?? 'fr';
    return $TRANSLATIONS[$lang][$key] ?? $TRANSLATIONS['fr'][$key] ?? $key;
}

function format_date($dateStr, bool $withTime = false): string {
    if (!$dateStr) return '';
    $timestamp = is_numeric($dateStr) ? (int)$dateStr : strtotime($dateStr);
    if (!$timestamp) return '';
    return date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $timestamp);
}
?>
