# Nofy

création de base de données `base.sql`

**creation table :**

 - prefixe : (id, prefixe)
 - compte : (id, tel, solde)
 - type_operation : (id, nom)
 - transaction : (id, compte_source, compte_destination, type_operation, montant, frais, date_operation)
 - baremes : (id, type_operation, montant_min, montant_max, frais)

**insertion des données par défaut :**

 - préfixes (033, 037)
 - types d'opérations (Dépôt, Retrait, Transfert)

**creation model :**

 - `TransactionModel.php`

**creation controller :**

 - `TransactionController.php`

**fonctionnalités :**
 - enregistrement des opérations
 - historique des opérations
 - calcul et enregistrement des frais
 - affichage du solde
 - dépôt
 - retrait
 - transfert

**tests et correction de la base de données**

---

# Eric

**creation models :**

 - `PrefixeModel.php`
 - `CompteModel.php`
 - `TypeOperationModel.php`
 - `BaremesModel.php`

**creation controllers :**

 - `AuthController.php`
 - `ClientController.php`
 - `HistoriqueController.php`
 - `PrefixeController.php`
 - `BaremeController.php`

**fonctionnalités :**

 - login automatique avec le numéro de téléphone
 - création automatique du compte client
 - vérification du préfixe
 - consultation de l'historique
 - gestion des préfixes
 - gestion des barèmes
 - calcul automatique des frais
 - situation des comptes clients
 - situation des gains (retrait et transfert)

**interface :**

 - création des vues Bootstrap
 - navigation entre les pages
 - validation des formulaires

**tests, intégration et correction des bugs**

**création du tag v1**
