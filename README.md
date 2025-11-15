Wymagania dotyczące API i backendu
- Backend udostępnia REST API zgodne ze specyfikacją OpenAPI 3.0.
- Dokumentacja API dostępna jest pod endpointem /api/documentation (OpenAPI/Swagger).
- API umożliwia eksport aktywności do pliku .gpx.

## Konfiguracja lokalna API
1. `cp .env.example .env` i uzupełnij dane (domyślnie MySQL `apistrava_user` / `apistrava_pass` / baza `apistrava` na `127.0.0.1:3306`).
2. `php artisan key:generate`
3. `php artisan migrate` – uruchamia podstawowe migracje Laravel.
4. `php artisan serve` – start lokalnego serwera developerskiego.

Wkrótce dodamy `docker-compose.yml`, aby każdy członek zespołu mógł uruchomić MySQL/Redis jednym poleceniem.
