#!/bin/bash
# Diagnostic Git pour Emergent

echo "=== DIAGNOSTIC GIT POUR GITHUB PUSH ==="
echo ""

echo "1. Utilisateur actuel:"
whoami
id

echo ""
echo "2. Propriétaire du répertoire /app:"
ls -la / | grep -E "app$"

echo ""
echo "3. Permissions .git:"
ls -la /app/.git | head -5

echo ""
echo "4. Configuration Git globale:"
git config --global --list | grep -E "(safe|user)" || echo "Aucune config globale"

echo ""
echo "5. Configuration Git locale:"
cd /app && git config --local --list | grep -E "(safe|user)" || echo "Aucune config locale"

echo ""
echo "6. Configuration Git système:"
git config --system --list | grep -E "(safe|user)" 2>/dev/null || echo "Aucune config système"

echo ""
echo "7. Test Git status:"
cd /app && git status 2>&1 | head -3

echo ""
echo "8. Variables d'environnement Git:"
env | grep -i git || echo "Aucune variable Git"

echo ""
echo "=== FIN DU DIAGNOSTIC ==="