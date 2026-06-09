# Ticket Management API PL

## 🛠 Wymagania
* [Docker](https://docs.docker.com/get-docker/) & [Docker Compose](https://docs.docker.com/compose/install/)
* [Make](https://www.gnu.org/software/make/) (do uruchomienia skryptów instalacyjnych)

## 🚀 Instalacja i uruchomienie

1. **Sklonuj repozytorium:**
   ```bash
   git clone https://github.com/hightt/api_platform_service_request_management
   cd api_platform_service_request_management
   cp .env.template .env
   make init

Komenda make init automatycznie konfiguruje całe środowisko, uruchamiając kontenery Dockera i instalując wszystkie niezbędne zależności projektu. Następnie przygotowuje bazę danych poprzez wykonanie migracji i załadowanie danych testowych oraz generuje niezbędne klucze bezpieczeństwa JWT.

## 🚀 Testowanie API
Opcja 1. Przeglądarka: Wejdź pod adres: http://localhost:8080/api

Opcja 2. Postman: W katalogu _external/ znajduje się plik service_management_system.postman_collection.json. Zaimportuj go do swojego klienta API, aby przetestować gotowe punkty końcowe.

# Ticket Management API EN

## 🛠 Prerequisites
* [Docker](https://docs.docker.com/get-docker/) & [Docker Compose](https://docs.docker.com/compose/install/)
* [Make](https://www.gnu.org/software/make/) (for running installation scripts)

## 🚀 Installation and Setup

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/hightt/api_platform_service_request_management](https://github.com/hightt/api_platform_service_request_management)
   cd api_platform_service_request_management
   cp .env.template .env
   make init

The make init command automatically configures the entire environment, launching Docker containers and installing all necessary project dependencies. It then prepares the database by migrating and loading test data, and generates the necessary JWT security keys.

## 🚀 API Testing
Option 1. Browser: Go to: http://localhost:8080/api

Option 2. Postman: In the _external/ directory, there's a file called service_management_system.postman_collection.json. Import it into your API client to test the finished endpoints.