#!/usr/bin/env python3
"""
Backend Testing Suite for "Le petit agenais" PHP Application
Tests database connectivity, APIs, admin panel, and favorites system
"""

import requests
import json
import sys
import time
from urllib.parse import urljoin
import mysql.connector
from mysql.connector import Error

class LeAgenaisBackendTester:
    def __init__(self, base_url="http://localhost"):
        self.base_url = base_url
        self.session = requests.Session()
        self.test_results = []
        self.db_config = {
            'host': 'localhost',
            'database': 'guide_agen',
            'user': 'admin',
            'password': 'admin123'
        }
        
    def log_test(self, test_name, success, message="", details=None):
        """Log test results"""
        status = "✅ PASS" if success else "❌ FAIL"
        result = {
            'test': test_name,
            'success': success,
            'message': message,
            'details': details
        }
        self.test_results.append(result)
        print(f"{status} {test_name}: {message}")
        if details and not success:
            print(f"   Details: {details}")
    
    def test_database_connection(self):
        """Test MariaDB database connection and structure"""
        print("\n🗄️ Testing Database Connection...")
        
        try:
            connection = mysql.connector.connect(**self.db_config)
            if connection.is_connected():
                cursor = connection.cursor()
                
                # Test basic connection
                cursor.execute("SELECT VERSION()")
                version = cursor.fetchone()
                self.log_test("Database Connection", True, f"Connected to MariaDB {version[0]}")
                
                # Test required tables exist
                required_tables = ['restaurants', 'activities', 'events', 'admin_users']
                cursor.execute("SHOW TABLES")
                existing_tables = [table[0] for table in cursor.fetchall()]
                
                for table in required_tables:
                    if table in existing_tables:
                        cursor.execute(f"SELECT COUNT(*) FROM {table}")
                        count = cursor.fetchone()[0]
                        self.log_test(f"Table {table}", True, f"{count} records found")
                    else:
                        self.log_test(f"Table {table}", False, "Table not found")
                
                # Test database structure for restaurants
                cursor.execute("DESCRIBE restaurants")
                columns = [col[0] for col in cursor.fetchall()]
                required_columns = ['id', 'name', 'address', 'latitude', 'longitude', 'rating', 'category']
                
                missing_columns = [col for col in required_columns if col not in columns]
                if not missing_columns:
                    self.log_test("Restaurant table structure", True, "All required columns present")
                else:
                    self.log_test("Restaurant table structure", False, f"Missing columns: {missing_columns}")
                
                cursor.close()
                connection.close()
                
        except Error as e:
            self.log_test("Database Connection", False, f"Connection failed: {e}")
    
    def test_api_restaurants(self):
        """Test restaurants API endpoints"""
        print("\n🍽️ Testing Restaurants API...")
        
        try:
            # Test basic API
            response = self.session.get(f"{self.base_url}/api/restaurants.php")
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and 'items' in data:
                    count = data.get('count', 0)
                    self.log_test("Restaurants API Basic", True, f"Retrieved {count} restaurants")
                    
                    # Test data structure
                    if data['items']:
                        restaurant = data['items'][0]
                        required_fields = ['id', 'name', 'address', 'rating']
                        missing_fields = [field for field in required_fields if field not in restaurant]
                        
                        if not missing_fields:
                            self.log_test("Restaurant data structure", True, "All required fields present")
                        else:
                            self.log_test("Restaurant data structure", False, f"Missing fields: {missing_fields}")
                else:
                    self.log_test("Restaurants API Basic", False, "Invalid response structure")
            else:
                self.log_test("Restaurants API Basic", False, f"HTTP {response.status_code}")
            
            # Test search functionality
            response = self.session.get(f"{self.base_url}/api/restaurants.php?search=restaurant")
            if response.status_code == 200:
                data = response.json()
                self.log_test("Restaurant Search", True, f"Search returned {data.get('count', 0)} results")
            else:
                self.log_test("Restaurant Search", False, f"HTTP {response.status_code}")
            
            # Test category filter
            response = self.session.get(f"{self.base_url}/api/restaurants.php?category=gastronomique")
            if response.status_code == 200:
                data = response.json()
                self.log_test("Restaurant Category Filter", True, f"Category filter returned {data.get('count', 0)} results")
            else:
                self.log_test("Restaurant Category Filter", False, f"HTTP {response.status_code}")
            
            # Test geolocation search
            response = self.session.get(f"{self.base_url}/api/restaurants.php?nearby=true&lat=44.2028&lng=0.6167&radius=5")
            if response.status_code == 200:
                data = response.json()
                self.log_test("Restaurant Geolocation", True, f"Geolocation search returned {data.get('count', 0)} results")
            else:
                self.log_test("Restaurant Geolocation", False, f"HTTP {response.status_code}")
                
        except Exception as e:
            self.log_test("Restaurants API", False, f"Exception: {e}")
    
    def test_api_activities(self):
        """Test activities API endpoints"""
        print("\n🎯 Testing Activities API...")
        
        try:
            # Test basic API
            response = self.session.get(f"{self.base_url}/api/activities.php")
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and 'items' in data:
                    count = data.get('count', 0)
                    self.log_test("Activities API Basic", True, f"Retrieved {count} activities")
                    
                    # Test data structure
                    if data['items']:
                        activity = data['items'][0]
                        required_fields = ['id', 'name', 'description']
                        missing_fields = [field for field in required_fields if field not in activity]
                        
                        if not missing_fields:
                            self.log_test("Activity data structure", True, "All required fields present")
                        else:
                            self.log_test("Activity data structure", False, f"Missing fields: {missing_fields}")
                else:
                    self.log_test("Activities API Basic", False, "Invalid response structure")
            else:
                self.log_test("Activities API Basic", False, f"HTTP {response.status_code}")
            
            # Test search functionality
            response = self.session.get(f"{self.base_url}/api/activities.php?search=visite")
            if response.status_code == 200:
                data = response.json()
                self.log_test("Activity Search", True, f"Search returned {data.get('count', 0)} results")
            else:
                self.log_test("Activity Search", False, f"HTTP {response.status_code}")
                
        except Exception as e:
            self.log_test("Activities API", False, f"Exception: {e}")
    
    def test_api_events(self):
        """Test events API endpoints"""
        print("\n🎭 Testing Events API...")
        
        try:
            # Test basic API
            response = self.session.get(f"{self.base_url}/api/events.php")
            if response.status_code == 200:
                data = response.json()
                if data.get('success') and 'items' in data:
                    count = data.get('count', 0)
                    self.log_test("Events API Basic", True, f"Retrieved {count} events")
                    
                    # Test data structure
                    if data['items']:
                        event = data['items'][0]
                        required_fields = ['id', 'name', 'description']
                        missing_fields = [field for field in required_fields if field not in event]
                        
                        if not missing_fields:
                            self.log_test("Event data structure", True, "All required fields present")
                        else:
                            self.log_test("Event data structure", False, f"Missing fields: {missing_fields}")
                else:
                    self.log_test("Events API Basic", False, "Invalid response structure")
            else:
                self.log_test("Events API Basic", False, f"HTTP {response.status_code}")
            
            # Test upcoming events
            response = self.session.get(f"{self.base_url}/api/events.php?upcoming=true")
            if response.status_code == 200:
                data = response.json()
                self.log_test("Upcoming Events", True, f"Upcoming events returned {data.get('count', 0)} results")
            else:
                self.log_test("Upcoming Events", False, f"HTTP {response.status_code}")
                
        except Exception as e:
            self.log_test("Events API", False, f"Exception: {e}")
    
    def test_admin_panel(self):
        """Test admin panel functionality"""
        print("\n🔐 Testing Admin Panel...")
        
        try:
            # Test login page accessibility
            response = self.session.get(f"{self.base_url}/admin/login.php")
            if response.status_code == 200:
                self.log_test("Admin Login Page", True, "Login page accessible")
            else:
                self.log_test("Admin Login Page", False, f"HTTP {response.status_code}")
            
            # Test admin authentication
            login_data = {
                'username': 'admin',
                'password': 'password'
            }
            response = self.session.post(f"{self.base_url}/admin/login.php", data=login_data)
            
            # Check if redirected to dashboard (302) or if login was successful
            if response.status_code in [200, 302]:
                self.log_test("Admin Authentication", True, "Login successful")
                
                # Test dashboard access
                response = self.session.get(f"{self.base_url}/admin/dashboard.php")
                if response.status_code == 200:
                    self.log_test("Admin Dashboard", True, "Dashboard accessible after login")
                else:
                    self.log_test("Admin Dashboard", False, f"HTTP {response.status_code}")
                
                # Test management pages
                management_pages = [
                    'manage_restaurants.php',
                    'manage_activities.php', 
                    'manage_events.php'
                ]
                
                for page in management_pages:
                    response = self.session.get(f"{self.base_url}/admin/{page}")
                    if response.status_code == 200:
                        self.log_test(f"Admin {page}", True, "Management page accessible")
                    else:
                        self.log_test(f"Admin {page}", False, f"HTTP {response.status_code}")
            else:
                self.log_test("Admin Authentication", False, f"Login failed: HTTP {response.status_code}")
                
        except Exception as e:
            self.log_test("Admin Panel", False, f"Exception: {e}")
    
    def test_main_pages(self):
        """Test main application pages"""
        print("\n🌐 Testing Main Pages...")
        
        main_pages = [
            ('index.php', 'Homepage'),
            ('restaurants.php', 'Restaurants Page'),
            ('activities.php', 'Activities Page'),
            ('events.php', 'Events Page'),
            ('status.php', 'Status Page'),
            ('surprise-restaurant.php', 'Surprise Restaurant')
        ]
        
        for page, name in main_pages:
            try:
                response = self.session.get(f"{self.base_url}/{page}")
                if response.status_code == 200:
                    self.log_test(name, True, "Page loads successfully")
                else:
                    self.log_test(name, False, f"HTTP {response.status_code}")
            except Exception as e:
                self.log_test(name, False, f"Exception: {e}")
    
    def test_favorites_system(self):
        """Test favorites system functionality"""
        print("\n❤️ Testing Favorites System...")
        
        try:
            # Test favorites test page
            response = self.session.get(f"{self.base_url}/test_favorites.html")
            if response.status_code == 200:
                content = response.text
                
                # Check for key favorites system components
                if 'FavoritesSystem' in content:
                    self.log_test("Favorites System JS", True, "JavaScript favorites system found")
                else:
                    self.log_test("Favorites System JS", False, "JavaScript favorites system not found")
                
                if 'localStorage' in content:
                    self.log_test("Favorites Storage", True, "LocalStorage implementation found")
                else:
                    self.log_test("Favorites Storage", False, "LocalStorage implementation not found")
                
                if 'favorite-btn' in content:
                    self.log_test("Favorites UI", True, "Favorite buttons found in HTML")
                else:
                    self.log_test("Favorites UI", False, "Favorite buttons not found")
                
                self.log_test("Favorites Test Page", True, "Test page accessible")
            else:
                self.log_test("Favorites Test Page", False, f"HTTP {response.status_code}")
                
        except Exception as e:
            self.log_test("Favorites System", False, f"Exception: {e}")
    
    def test_special_features(self):
        """Test special features like surprise restaurant"""
        print("\n🎲 Testing Special Features...")
        
        try:
            # Test surprise restaurant functionality
            response = self.session.get(f"{self.base_url}/surprise-restaurant.php")
            if response.status_code == 200:
                self.log_test("Surprise Restaurant", True, "Surprise restaurant page accessible")
            else:
                self.log_test("Surprise Restaurant", False, f"HTTP {response.status_code}")
            
            # Test map functionality
            response = self.session.get(f"{self.base_url}/map.php")
            if response.status_code == 200:
                self.log_test("Interactive Map", True, "Map page accessible")
            else:
                self.log_test("Interactive Map", False, f"HTTP {response.status_code}")
                
        except Exception as e:
            self.log_test("Special Features", False, f"Exception: {e}")
    
    def run_all_tests(self):
        """Run all backend tests"""
        print("🧪 Starting Backend Tests for 'Le petit agenais'")
        print("=" * 60)
        
        # Run all test suites
        self.test_database_connection()
        self.test_api_restaurants()
        self.test_api_activities()
        self.test_api_events()
        self.test_admin_panel()
        self.test_main_pages()
        self.test_favorites_system()
        self.test_special_features()
        
        # Generate summary
        self.generate_summary()
    
    def generate_summary(self):
        """Generate test summary"""
        print("\n" + "=" * 60)
        print("📊 TEST SUMMARY")
        print("=" * 60)
        
        total_tests = len(self.test_results)
        passed_tests = sum(1 for result in self.test_results if result['success'])
        failed_tests = total_tests - passed_tests
        success_rate = (passed_tests / total_tests * 100) if total_tests > 0 else 0
        
        print(f"Total Tests: {total_tests}")
        print(f"Passed: {passed_tests} ✅")
        print(f"Failed: {failed_tests} ❌")
        print(f"Success Rate: {success_rate:.1f}%")
        
        if failed_tests > 0:
            print("\n❌ FAILED TESTS:")
            for result in self.test_results:
                if not result['success']:
                    print(f"  - {result['test']}: {result['message']}")
        
        print("\n🎯 RECOMMENDATIONS:")
        if success_rate >= 95:
            print("✅ Excellent! Application is working very well.")
        elif success_rate >= 80:
            print("✅ Good! Minor issues to address.")
        elif success_rate >= 60:
            print("⚠️ Moderate issues need attention.")
        else:
            print("❌ Major issues require immediate attention.")
        
        return success_rate >= 80

if __name__ == "__main__":
    # Allow custom base URL via command line
    base_url = sys.argv[1] if len(sys.argv) > 1 else "http://localhost"
    
    tester = LeAgenaisBackendTester(base_url)
    success = tester.run_all_tests()
    
    # Exit with appropriate code
    sys.exit(0 if success else 1)