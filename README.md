# PILOT CONTROL

## Présentation

**PILOT CONTROL** est un **SaaS de gestion commerciale et de stock**, destiné principalement aux entreprises du **BTP** et du **commerce de détail**. Il permet de gérer facilement :

- La **génération de factures** et le suivi des paiements.
- La création et le suivi des **devis/quotations**.
- La gestion du **stock et des articles**.
- Les **abonnements et plans de souscription** pour les clients.

L'objectif est de fournir une solution complète pour suivre l'activité commerciale et la logistique, tout en étant simple d'utilisation pour des petites et moyennes structures.

---

## Fonctionnalités principales

### Gestion des devis
- Création de devis détaillés avec plusieurs items.
- Possibilité de générer automatiquement une facture à partir d’un devis.
- Suivi des statuts des devis (envoyé, accepté, expiré).

### Facturation
- Création de factures liées à un devis ou directement à un client.
- Gestion des paiements et des références de paiement.
- Suivi des dates d’émission (`issueDate`) et des échéances (`dueDate`).
- Gestion des factures partielles ou des paiements fractionnés.

### Gestion des articles et du stock
- Suivi des items avec quantité, unités et seuil d’alerte.
- Gestion des articles vendables ou consommables par projet ou devis.
- Possibilité de gérer des items mesurés en unités entières ou fractionnaires.

### Gestion des projets
- Chaque projet peut être associé à un ou plusieurs utilisateurs.
- Suivi des utilisateurs pouvant créer ou éditer un projet.
- Suivi des coûts et des articles utilisés par projet.

### Gestion des abonnements
- Support des différents plans d’abonnement.
- Chaque souscription est liée à un plan unique.
- Suivi des abonnements actifs, expirés ou en renouvellement.

---

## Technologies utilisées

- **Backend** : PHP 8.x avec Symfony
- **ORM** : Doctrine pour la gestion des entités et des relations
- **Base de données** : MySQL / PostgreSQL
- **Frontend** : NextJs, TypeScript, ChakraUI
- **Gestion des utilisateurs** : Symfony Security pour l’authentification et les rôles
- **Enumérations** : pour gérer les types de maintenance, unités d’articles, statuts, etc.

---

## Modèle de données simplifié

- **User** : utilisateurs de la plateforme (créateurs, éditeurs, clients).  
- **Project** : projets BTP ou commerciaux, associés à un ou plusieurs utilisateurs.  
- **Item** : articles ou matériaux, avec quantité, unité et alertes.  
- **Quotation / QuotationItem** : devis et lignes de devis.  
- **Invoice / Payment** : factures et paiements associés.  
- **Subscription / SubscriptionPlan** : abonnements et plans tarifaires.

---

## Objectifs du projet

- Fournir un outil flexible pour gérer **devis, factures et stock**.  
- Permettre un **suivi précis des projets et matériaux** pour le BTP ou le commerce.  
- Offrir une **solution SaaS évolutive**, capable de gérer plusieurs utilisateurs, permissions et abonnements.  
- Préparer la structure pour des **rapports, statistiques et alertes automatiques**.

---

