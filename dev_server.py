#!/usr/bin/env python3
"""
Serveur de développement simple pour l'application PHP
Le petit agenais
"""

import http.server
import socketserver
import subprocess
import sys
import os
from urllib.parse import urlparse

class PHPHandler(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        parsed_path = urlparse(self.path)
        path = parsed_path.path
        query = parsed_path.query
        
        # Rediriger vers index.php si demande de la racine
        if path == '/':
            path = '/index.php'
        
        # Vérifier si c'est un fichier PHP
        if path.endswith('.php'):
            file_path = f"/app{path}"
            if os.path.exists(file_path):
                # Exécuter PHP et retourner le résultat
                try:
                    env = os.environ.copy()
                    env['REQUEST_METHOD'] = 'GET'
                    env['QUERY_STRING'] = query
                    env['REQUEST_URI'] = self.path
                    
                    result = subprocess.run(
                        ['php', file_path],  
                        cwd='/app',
                        capture_output=True, 
                        text=True,
                        env=env
                    )
                    
                    if result.returncode == 0:
                        self.send_response(200)
                        self.send_header('Content-type', 'text/html; charset=utf-8')
                        self.end_headers()
                        self.wfile.write(result.stdout.encode('utf-8'))
                    else:
                        self.send_error(500, f"PHP Error: {result.stderr}")
                except Exception as e:
                    self.send_error(500, f"Server Error: {str(e)}")
            else:
                self.send_error(404, "File not found")
        else:
            # Servir les fichiers statiques normalement
            super().do_GET()
    
    def do_POST(self):
        parsed_path = urlparse(self.path)
        path = parsed_path.path
        
        if path.endswith('.php'):
            file_path = f"/app{path}"
            if os.path.exists(file_path):
                try:
                    # Lire le corps de la requête POST
                    content_length = int(self.headers.get('Content-Length', 0))
                    post_data = self.rfile.read(content_length)
                    
                    env = os.environ.copy()
                    env['REQUEST_METHOD'] = 'POST'
                    env['CONTENT_TYPE'] = self.headers.get('Content-Type')
                    env['CONTENT_LENGTH'] = str(content_length)
                    env['REQUEST_URI'] = self.path
                    
                    result = subprocess.run(
                        ['php', file_path],
                        cwd='/app',
                        input=post_data,
                        capture_output=True,
                        env=env
                    )
                    
                    if result.returncode == 0:
                        self.send_response(200)
                        self.send_header('Content-type', 'application/json; charset=utf-8')
                        self.send_header('Access-Control-Allow-Origin', '*')
                        self.end_headers()
                        self.wfile.write(result.stdout)
                    else:
                        self.send_error(500, f"PHP Error: {result.stderr}")
                except Exception as e:
                    self.send_error(500, f"Server Error: {str(e)}")
            else:
                self.send_error(404, "File not found")
        else:
            self.send_error(405, "Method not allowed")

if __name__ == "__main__":
    PORT = 3000
    os.chdir('/app')
    
    with socketserver.TCPServer(("", PORT), PHPHandler) as httpd:
        print(f"🚀 Serveur PHP lancé sur http://localhost:{PORT}")
        print("📁 Répertoire racine: /app")
        print("⚡ Prêt pour les tests d'authentification!")
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            print("\n🛑 Serveur arrêté")
            httpd.shutdown()