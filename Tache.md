# Nofy
## v1
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

**interface :**

 - `dashboard.php`

**tests et correction de la base de données**

## v2
- **Mise à jour de la Base de Données (`base.sql`) :**
    
    - Modification de la table `prefixe` ou ajout d'un champ/table pour identifier l'opérateur (réseau propre vs réseau externe).
        
    - Ajout de la table ou configuration du pourcentage (%) de commission inter-opérateur.
        
    - Mettre à jour la table/vue des transactions pour intégrer les commissions et frais d'envois externes.
        
- **Développement Côté Opérateur :**
    
    - **Configuration des préfixes externes :** Gestion des préfixes des autres opérateurs (ex: `032`, `031`).
        
    - **Commission inter-opérateur :** Formulaire et logique de paramétrage du % de commission sur les transferts externes.
        
    - **Rapports & Vues Opérateur :**
        
        - Séparation des gains sur le réseau propre et sur les autres opérateurs dans la _"Situation des gains"_.
            
        - Vue _"Situation inter-opérateurs"_ : Affichage du bilan des montants à transférer/reverser à chaque opérateur externe.
            
- **Tests & Recette :**
    
    - Validation des calculs des commissions inter-opérateurs.

    Promotion en % sur le frait de transfert au meme operateur
---

# Eric
## v1
**creation models :**

 - `PrefixeModel.php`
 - `CompteModel.php`
 - `TypeOperationModel.php`
 - `BaremesModel.php`

**creation controllers :**

 - AuthController.php
 - ClientController.php
 - HistoriqueController.php
 - PrefixeController.php
 - BaremeController.php


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

 - `layout.php`
 - `login.php`
 - 

**tests, intégration et correction des bugs**

**création du tag v1**
## v2

- **Développement Côté Client (Logique & Transversal) :**
    
    - **Option "Inclure les frais de retrait" :**
        
        - Ajout de la case à cocher / option lors de l'envoi.
            
        - Calcul automatique du supplément (frais de retrait) à prélever sur l'expéditeur et à transférer au destinataire.
            
        - Prise en compte de la règle de gestion : _pas de frais de retrait pour les numéros externes_.
            
    - **Envoi multiple :**
        
        - Interface pour saisir plusieurs numéros destinataires.
            
        - Vérification et validation que tous les numéros appartiennent au **même opérateur**.
            
        - Division équitable du montant total saisi entre chaque destinataire et traitement du transfert groupé.
            
- **Intégration & Livraison :**
    
    - Mise à jour des interfaces client (modales / formulaires de transfert).
        
    - Tests d'intégration et correction des bugs.
        
    - Création et publication du tag `v2` sur Git.

     