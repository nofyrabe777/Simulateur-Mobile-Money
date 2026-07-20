PRAGMA foreign_keys = ON;


CREATE TABLE IF NOT EXISTS operateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(100) NOT NULL,
    type_reseau VARCHAR(20) NOT NULL DEFAULT 'externe'
);


CREATE TABLE IF NOT EXISTS prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe VARCHAR(10) NOT NULL UNIQUE,
    id_operateur INTEGER NOT NULL,
    FOREIGN KEY (id_operateur) REFERENCES operateurs(id)
);


CREATE TABLE IF NOT EXISTS compte (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    telephone VARCHAR(15) NOT NULL UNIQUE,
    solde REAL NOT NULL DEFAULT 0.0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS type_operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(50) NOT NULL UNIQUE
);


CREATE TABLE IF NOT EXISTS baremes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation INTEGER NOT NULL,
    montant_min REAL NOT NULL,
    montant_max REAL NOT NULL,
    frais REAL NOT NULL,
    FOREIGN KEY (id_type_operation) REFERENCES type_operations(id)
);


CREATE TABLE IF NOT EXISTS interoperator_commissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur INTEGER NOT NULL,
    pourcentage REAL NOT NULL DEFAULT 0.0,
    FOREIGN KEY (id_operateur) REFERENCES operateurs(id)
);


CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation INTEGER NOT NULL,
    id_compte_expediteur INTEGER NOT NULL,
    id_compte_destinataire INTEGER NULL,
    montant REAL NOT NULL,
    frais REAL NOT NULL DEFAULT 0.0,
    frais_retrait_inclus REAL NOT NULL DEFAULT 0.0,
    commission_interoperateur REAL NOT NULL DEFAULT 0.0,
    id_operateur_destinataire INTEGER NULL,
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_type_operation) REFERENCES type_operations(id),
    FOREIGN KEY (id_compte_expediteur) REFERENCES compte(id),
    FOREIGN KEY (id_compte_destinataire) REFERENCES compte(id),
    FOREIGN KEY (id_operateur_destinataire) REFERENCES operateurs(id)
);


INSERT OR IGNORE INTO operateurs (id, nom, type_reseau) VALUES
(1, 'Reseau Propre', 'propre'),
(2, 'Operateur 032', 'externe'),
(3, 'Operateur 031', 'externe');


INSERT OR IGNORE INTO prefixes (prefixe, id_operateur) VALUES
('033', 1),
('037', 1),
('034', 1),
('032', 2),
('031', 3);


INSERT OR IGNORE INTO type_operations (id, nom) VALUES 
(1, 'Dépôt'),
(2, 'Retrait'),
(3, 'Transfert');


INSERT OR IGNORE INTO interoperator_commissions (id_operateur, pourcentage) VALUES
(2, 2.5),
(3, 3.0);


INSERT INTO baremes (id_type_operation, montant_min, montant_max, frais) VALUES
(2, 100, 1000, 50),
(2, 1001, 5000, 50),
(2, 5001, 10000, 100),
(2, 10001, 25000, 200),
(2, 25001, 50000, 400),
(2, 50010, 100000, 800),
(2, 100001, 250000, 1500),
(2, 250001, 500000, 1500),
(2, 500001, 1000000, 2500),
(2, 1000001, 2000000, 3000),

(3, 100, 1000, 50),
(3, 1001, 5000, 50),
(3, 5001, 10000, 100),
(3, 100001, 250000, 1500);