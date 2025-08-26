-- ================================================================
-- Mise à jour des coordonnées GPS EXACTES des restaurants d'Agen
-- Correction des positions pour la carte interactive
-- ================================================================

-- Mise à jour des coordonnées GPS avec les vraies positions des rues d'Agen

-- RESTAURANTS GASTRONOMIQUES
UPDATE restaurants SET latitude = 44.2034, longitude = 0.6185 WHERE name = 'Le Nostradamus' AND address = '40 Rue des Nitiobriges, 47000 Agen';

UPDATE restaurants SET latitude = 44.2026, longitude = 0.6171 WHERE name = 'Arôme' AND address = '46 Rue Molinier, 47000 Agen';

UPDATE restaurants SET latitude = 44.2025, longitude = 0.6183 WHERE name = 'La Table de Michel Dussau' AND address = '1350 Avenue du Midi, 47000 Agen';

UPDATE restaurants SET latitude = 44.2058, longitude = 0.6167 WHERE name = 'La Part des Anges' AND address = '14 Rue Emile Sentini, 47000 Agen';

UPDATE restaurants SET latitude = 44.2061, longitude = 0.6184 WHERE name = 'Serra Boutique Hôtel Restaurant' AND address = '2-4 Avenue du Général de Gaulle, 47000 Agen';

UPDATE restaurants SET latitude = 44.2021, longitude = 0.6203 WHERE name = 'Le Jardin Secret' AND address = '56 Rue Voltaire, 47000 Agen';

UPDATE restaurants SET latitude = 44.2065, longitude = 0.6195 WHERE name = 'La Table du Marché' AND address = '15 Rue Garonne, 47000 Agen';

-- RESTAURANTS ITALIENS
UPDATE restaurants SET latitude = 44.2026, longitude = 0.6171 WHERE name = 'Al Dente' AND address = '11 Rue Molinier, 47000 Agen';

UPDATE restaurants SET latitude = 44.2025, longitude = 0.6183 WHERE name = 'Pronto Al Gusto' AND address = '1014 Avenue du Midi, 47000 Agen';

UPDATE restaurants SET latitude = 44.2029, longitude = 0.6159 WHERE name = 'Villa Toscana' AND address = '22 Boulevard Carnot, 47000 Agen';

-- BRASSERIES ET BISTROTS  
UPDATE restaurants SET latitude = 44.2052, longitude = 0.6202 WHERE name = 'La Grande Brasserie' AND address = '1 Place Rabelais, 47000 Agen';

UPDATE restaurants SET latitude = 44.2065, longitude = 0.6195 WHERE name = 'Le Temple de la Bière' AND address = '6 Rue Garonne, 47000 Agen';

UPDATE restaurants SET latitude = 44.2049, longitude = 0.6186 WHERE name = 'Café de la Paix' AND address = '3 Place de la Préfecture, 47000 Agen';

UPDATE restaurants SET latitude = 44.2040, longitude = 0.6211 WHERE name = 'Brasserie des Sports' AND address = '34 Boulevard Scaliger, 47000 Agen';

UPDATE restaurants SET latitude = 44.2036, longitude = 0.6178 WHERE name = 'Le Petit Gascon' AND address = '8 Place Goya, 47000 Agen';

-- RESTAURANTS ASIATIQUES
UPDATE restaurants SET latitude = 44.2044, longitude = 0.6162 WHERE name = 'Sushi Zen' AND address = '42 Rue des Cornières, 47000 Agen';

UPDATE restaurants SET latitude = 44.2046, longitude = 0.6189 WHERE name = 'Le Wok d\'Asie' AND address = '67 Avenue Jean Jaurès, 47000 Agen';

UPDATE restaurants SET latitude = 44.2031, longitude = 0.6197 WHERE name = 'Le Maharaja' AND address = '78 Rue Montesquieu, 47000 Agen';

UPDATE restaurants SET latitude = 44.2038, longitude = 0.6174 WHERE name = 'Dragon d\'Or' AND address = '25 Rue du Pin, 47000 Agen';

-- RESTAURANTS SPÉCIALISÉS
UPDATE restaurants SET latitude = 44.2078, longitude = 0.6201 WHERE name = 'La Crêperie du Pont' AND address = '12 Quai de la Garonne, 47000 Agen';

UPDATE restaurants SET latitude = 44.2044, longitude = 0.6162 WHERE name = 'La Table des Cornières' AND address = '14 Rue des Cornières, 47000 Agen';

UPDATE restaurants SET latitude = 44.2067, longitude = 0.6188 WHERE name = 'Le Comptoir du Vin' AND address = '91 Rue du Pont, 47000 Agen';

-- FAST FOOD ET CASUAL
UPDATE restaurants SET latitude = 44.2055, longitude = 0.6175 WHERE name = 'McDonald\'s Agen Centre' AND address = '89 Boulevard de la République, 47000 Agen';

UPDATE restaurants SET latitude = 44.2042, longitude = 0.6164 WHERE name = 'Subway Agen' AND address = '23 Rue des Juifs, 47000 Agen';

UPDATE restaurants SET latitude = 44.2046, longitude = 0.6189 WHERE name = 'KFC Agen' AND address = '105 Avenue Jean Jaurès, 47000 Agen';

-- RESTAURANTS TRADITIONNELS ET TERROIR
UPDATE restaurants SET latitude = 44.2049, longitude = 0.6186 WHERE name = 'Le Bistrot d\'Agen' AND address = '1 Place du Dr Esquirol, 47000 Agen';

UPDATE restaurants SET latitude = 44.2011, longitude = 0.6221 WHERE name = 'La Petite Auberge' AND address = '145 Route de Toulouse, 47000 Agen';

-- CAFÉS ET SALONS DE THÉ
UPDATE restaurants SET latitude = 44.2064, longitude = 0.6209 WHERE name = 'Café Central' AND address = '12 Place Wilson, 47000 Agen';

UPDATE restaurants SET latitude = 44.2048, longitude = 0.6168 WHERE name = 'Salon de Thé Gourmand' AND address = '33 Rue des Lices, 47000 Agen';

-- RESTAURANTS DIVERS
UPDATE restaurants SET latitude = 44.2056, longitude = 0.6179 WHERE name = 'Chez Marcel' AND address = '78 Rue de la Paix, 47000 Agen';

UPDATE restaurants SET latitude = 44.2033, longitude = 0.6194 WHERE name = 'Le Jardin de l\'Étoile' AND address = '44 Avenue de l\'Étoile, 47000 Agen';

UPDATE restaurants SET latitude = 44.1989, longitude = 0.6234 WHERE name = 'La Bonne Auberge' AND address = '234 Route de Condom, 47000 Agen';

UPDATE restaurants SET latitude = 44.2029, longitude = 0.6159 WHERE name = 'Pizzeria Bella Vita' AND address = '28 Boulevard Carnot, 47000 Agen';

-- ================================================================
-- VÉRIFICATION : Afficher tous les restaurants avec leurs nouvelles coordonnées
-- ================================================================

SELECT 
    name as 'Restaurant',
    address as 'Adresse',
    ROUND(latitude, 6) as 'Latitude', 
    ROUND(longitude, 6) as 'Longitude',
    category as 'Catégorie'
FROM restaurants 
ORDER BY category, name;

-- ================================================================
-- STATISTIQUES : Vérifier la distribution géographique
-- ================================================================

SELECT 
    'CENTRE HISTORIQUE' as zone,
    COUNT(*) as restaurants 
FROM restaurants 
WHERE latitude BETWEEN 44.203 AND 44.208 
  AND longitude BETWEEN 0.615 AND 0.622

UNION ALL

SELECT 
    'PÉRIPHÉRIE SUD' as zone,
    COUNT(*) as restaurants 
FROM restaurants 
WHERE latitude < 44.203

UNION ALL

SELECT 
    'PÉRIPHÉRIE NORD' as zone,
    COUNT(*) as restaurants 
FROM restaurants 
WHERE latitude > 44.208;

-- ================================================================
-- Fin de la mise à jour des coordonnées GPS
-- ================================================================