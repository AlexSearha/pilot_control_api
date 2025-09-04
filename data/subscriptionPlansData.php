<?php

return [
    [
        'code' => 'free',
        'name' => 'Gratuit',
        'description' => 'Accès limité aux fonctionnalités de base, idéal pour tester le service.',
        'price' => 0.00,
        'currency' => 'EUR',
        'maxProjects' => 1,
        'maxUsers' => 1,
        'features' => ['Création de devis', 'Facturation basique', 'Gestion de 1 projet'],
    ],
    [
        'code' => 'starter',
        'name' => 'Starter',
        'description' => 'Fonctionnalités essentielles pour les petites entreprises.',
        'price' => 59.99,
        'currency' => 'EUR',
        'maxProjects' => 5,
        'maxUsers' => 3,
        'features' => ['Création de devis', 'Facturation complète', 'Gestion des stocks', 'Support email'],
    ],
    [
        'code' => 'pro',
        'name' => 'Pro',
        'description' => 'Plan complet pour les entreprises en croissance.',
        'price' => 149.99,
        'currency' => 'EUR',
        'maxProjects' => 20,
        'maxUsers' => 10,
        'features' => ['Tout dans Starter', 'Rapports avancés', 'Multi-projets', 'Export PDF/Excel'],
    ],
    [
        'code' => 'enterprise',
        'name' => 'Enterprise',
        'description' => 'Solution sur mesure pour les grandes entreprises avec support prioritaire.',
        'price' => 249.99,
        'currency' => 'EUR',
        'maxProjects' => null, // illimité
        'maxUsers' => null,    // illimité
        'features' => ['Tout dans Pro', 'Support prioritaire', 'Personnalisation avancée', 'API complète'],
    ],
];
