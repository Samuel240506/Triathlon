# TODO: Supprimer tout ce qui concerne le comité

- [x] Supprimer `app/models/Comite.php` (fichier non trouvé, peut-être déjà supprimé)
- [x] Modifier `app/models/Club.php` : Retirer le JOIN Comite dans `getAll`
- [x] Modifier `app/controllers/ClubController.php` : Retirer `id_comite` de `create` et `edit`
- [x] Modifier `app/views/clubs/index.php` : Retirer la colonne "Comité" du tableau
- [x] Mettre à jour `TODO.md`
