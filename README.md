# MiniStrava

System do rejestrowania i analizy aktywności fizycznych. Zakres:
- aplikacja mobilna Flutter/Dart (Android/iOS, PL/EN),
- panel administracyjny web,
- backend REST API (Laravel, MySQL, Swagger).

## Kamienie milowe backendu
1. Inicjalizacja repo i środowiska (`git init`, opis projektu, `.gitignore`).
2. Stworzenie szkieletu Laravel + konfiguracja `.env` i bazy.
3. Moduł uwierzytelniania (rejestracja, logowanie, reset hasła, profile).
4. Obsługa aktywności + feed, statystyki, relacje społeczne.
5. Endpointy panelu admina oraz dokumentacja OpenAPI.
6. Dane przykładowe oraz integracje powiadomień (mail, push).

## Notatki organizacyjne
- Baza danych: MySQL (hosting hostido).
- Maile: SMTP Gmail (hasło aplikacyjne).
- Pierwsze commity prowadzone iteracyjnie, tak aby łatwo śledzić postęp.
