<?php
$page_title = 'Mentions Légales';
require_once 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">⚖️ Mentions Légales</h1>
        <p class="page-subtitle">
            Informations légales concernant le site Le petit agenais
        </p>
    </div>

    <div class="legal-content">
        <div class="legal-section">
            <h2>📋 Informations générales</h2>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Nom du site :</strong> Le petit agenais
                </div>
                <div class="info-item">
                    <strong>URL :</strong> <?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST']; ?>
                </div>
                <div class="info-item">
                    <strong>Nature :</strong> Site d'information touristique et culturelle
                </div>
            </div>
        </div>

        <div class="legal-section">
            <h2>🏢 Éditeur du site</h2>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Dénomination :</strong> Le petit agenais
                </div>
                <div class="info-item">
                    <strong>Adresse :</strong> Agen, Lot-et-Garonne (47000), France
                </div>
                <div class="info-item">
                    <strong>Email :</strong> admin@lepetitagenais.fr
                </div>
                <div class="info-item">
                    <strong>Téléphone :</strong> 05 53 XX XX XX
                </div>
            </div>
        </div>

        <div class="legal-section">
            <h2>🌐 Hébergement</h2>
            <p>
                Ce site est hébergé par un prestataire technique assurant la disponibilité et la sécurité des données.
                Les informations d'hébergement sont disponibles sur demande à l'adresse : 
                <a href="mailto:contact@guide-agen.fr">contact@guide-agen.fr</a>
            </p>
        </div>

        <div class="legal-section">
            <h2>🎨 Conception et développement</h2>
            <p>
                Site web conçu et développé spécialement pour promouvoir le patrimoine touristique, 
                gastronomique et culturel d'Agen et de ses environs.
            </p>
        </div>

        <div class="legal-section">
            <h2>©️ Propriété intellectuelle</h2>
            
            <h3>Contenu du site</h3>
            <p>
                L'ensemble du contenu de ce site (textes, images, vidéos, logos, graphismes, icônes) 
                est protégé par le droit d'auteur et appartient à Le petit agenais ou à ses partenaires.
            </p>
            
            <h3>Images et photographies</h3>
            <p>
                Les photographies utilisées sur ce site proviennent de diverses sources :
            </p>
            <ul>
                <li><strong>Unsplash :</strong> Images libres de droits utilisées selon leur licence</li>
                <li><strong>Partenaires locaux :</strong> Images fournies avec autorisation</li>
                <li><strong>Domaine public :</strong> Images du patrimoine public local</li>
            </ul>
            
            <h3>Utilisation</h3>
            <p>
                Toute reproduction, représentation, modification, publication ou adaptation de tout ou partie 
                des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite sans 
                l'autorisation écrite préalable de Le petit agenais.
            </p>
        </div>

        <div class="legal-section">
            <h2>⚠️ Responsabilité</h2>
            
            <h3>Informations fournies</h3>
            <p>
                Le petit agenais s'efforce de fournir des informations exactes et à jour concernant :
            </p>
            <ul>
                <li>Les restaurants et leurs spécialités</li>
                <li>Les activités touristiques et culturelles</li>
                <li>Les événements et manifestations</li>
                <li>Les horaires et coordonnées des établissements</li>
            </ul>
            
            <h3>Limitations</h3>
            <p>
                Cependant, nous ne pouvons garantir l'exactitude, la complétude ou l'actualité de toutes les informations.
                Les horaires, tarifs et disponibilités peuvent changer sans préavis. Nous recommandons de vérifier 
                directement auprès des établissements concernés.
            </p>
            
            <h3>Liens externes</h3>
            <p>
                Le site peut contenir des liens vers des sites externes (sites de réservation, sites officiels d'établissements, etc.).
                Le petit agenais n'est pas responsable du contenu de ces sites tiers.
            </p>
        </div>

        <div class="legal-section">
            <h2>🔒 Données personnelles</h2>
            <p>
                La collecte et le traitement des données personnelles sont régis par notre 
                <a href="politique-confidentialite.php">Politique de Confidentialité</a>, 
                conforme au Règlement Général sur la Protection des Données (RGPD).
            </p>
        </div>

        <div class="legal-section">
            <h2>🍪 Cookies</h2>
            <p>
                Ce site utilise des cookies pour améliorer l'expérience utilisateur :
            </p>
            <ul>
                <li><strong>Cookies fonctionnels :</strong> Mémorisation des favoris, préférences d'affichage</li>
                <li><strong>Cookies analytiques :</strong> Statistiques de fréquentation anonymes</li>
            </ul>
            <p>
                Vous pouvez désactiver les cookies dans les paramètres de votre navigateur.
            </p>
        </div>

        <div class="legal-section">
            <h2>⚖️ Droit applicable</h2>
            <p>
                Les présentes mentions légales sont soumises au droit français. 
                En cas de litige, les tribunaux français seront seuls compétents.
            </p>
        </div>

        <div class="legal-section">
            <h2>📞 Contact</h2>
            <p>
                Pour toute question concernant ces mentions légales ou le site en général :
            </p>
            <div class="contact-info">
                <p>📧 <a href="mailto:contact@guide-agen.fr">admin@lepetitagenais.fr</a></p>
                <p>📱 05 53 XX XX XX</p>
                <p>📍 Agen, Lot-et-Garonne, France</p>
            </div>
        </div>

        <div class="update-date">
            <p><strong>Dernière mise à jour :</strong> <?php echo date('d/m/Y'); ?></p>
        </div>
    </div>
</div>

<style>
.legal-content {
    max-width: 800px;
    margin: 0 auto;
}

.legal-section {
    background: var(--bg-card);
    backdrop-filter: blur(10px);
    border-radius: 1rem;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px var(--shadow-color);
    border: 1px solid var(--border-color);
}

.legal-section h2 {
    color: var(--text-primary);
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--border-color);
}

.legal-section h3 {
    color: var(--text-primary);
    font-size: 1.2rem;
    font-weight: 600;
    margin: 1.5rem 0 1rem 0;
}

.legal-section p {
    color: var(--text-secondary);
    line-height: 1.7;
    margin-bottom: 1rem;
}

.legal-section ul {
    color: var(--text-secondary);
    line-height: 1.7;
    margin: 1rem 0;
    padding-left: 2rem;
}

.legal-section li {
    margin-bottom: 0.5rem;
}

.info-grid {
    display: grid;
    gap: 1rem;
    margin: 1rem 0;
}

.info-item {
    padding: 1rem;
    background: var(--input-bg);
    border-radius: 0.5rem;
    border-left: 4px solid var(--text-accent);
    color: var(--text-primary);
}

.contact-info {
    background: var(--input-bg);
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin: 1rem 0;
}

.contact-info p {
    margin-bottom: 0.5rem;
    color: var(--text-secondary);
}

.update-date {
    text-align: center;
    margin: 2rem 0;
    padding: 1rem;
    background: var(--input-bg);
    border-radius: 0.5rem;
    font-size: 0.9rem;
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
}

.legal-section a {
    color: var(--text-accent);
    text-decoration: none;
    font-weight: 500;
}

.legal-section a:hover {
    text-decoration: underline;
    opacity: 0.8;
}

@media (max-width: 768px) {
    .legal-content {
        padding: 0 1rem;
    }
    
    .legal-section {
        padding: 1.5rem;
    }
    
    .legal-section h2 {
        font-size: 1.3rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>