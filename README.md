Aufgaben-Management-System (RESTful API)

Dieses Projekt ist eine RESTful API zur Verwaltung von Aufgaben (Tasks), Benutzern (Users) und Projekten (Projects). Die API wurde mit Laravel erstellt und setzt auf Sanctum für die Authentifizierung. Das Projekt umfasst sowohl die Grundprüfung (Basis-Funktionen wie CRUD für Aufgaben) als auch die erweiterte Prüfung (u.a. Rollen, Deadlines, Notifications).

1. Projektbeschreibung

Dieses Projekt stellt eine RESTful API für ein Aufgaben-Management-System bereit.
Funktionen im Überblick:

    Grundprüfung (Basismodul):
        CRUD-Operationen für Aufgaben (Tasks).
        Authentifizierung mittels Laravel Sanctum.
        Validierung von Eingaben.
        PHPUnit-Tests.

    Erweiterte Prüfung:
        Integration von Benutzern (Users) und Projekten (Projects).
        Beziehung: Ein Benutzer kann mehrere Aufgaben haben, eine Aufgabe gehört zu einem Projekt.
        Deadline-Feld für Aufgaben und Abfrage überfälliger Tasks.
        Rollen- und Berechtigungssystem (z.B. Admin-Rolle).
        Event-Listener und Notification bei abgelaufenen Deadlines.

2. Systemvoraussetzungen

PHP: ^8.1
Laravel: ^10.10
MySQL (oder eine kompatible Datenbank)
Composer (zum Installieren der PHP-Abhängigkeiten)

3. Installation

Repository klonen
git clone 

Abhängigkeiten installieren
composer install

.env-Datei anlegen
Erstelle eine Kopie der .env.example und benenne sie in .env um.

Applikationsschlüssel generieren
php artisan key:generate

Datenbank-Migrationen ausführen
php artisan migrate

Seed-Daten einspielen
php artisan db:seed

Es wird ein Admin und ein normaler User angelegt.
User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('password1'),
            'role' => 'user',
        ]);

Über die Login Route 
http://127.0.0.1:8000/api/login
wird der Token zurückgegeben.

Content-Type: application/json

4. Konfiguration

Laravel Sanctum:
Das Projekt wurde mit Breeze API-only erstellt und nutzt Laravel Sanctum für die Authentifizierung.
Für reine Token-Authentifizierung (API-Token) kannst du im Regelfall so starten, wie es ist.

Rollen:
Admin-Rollen und User-Rollen werden in der Datenbank verwaltet. Beim Seeding wird ein Admin-Benutzer erstellt (siehe UserSeeder).

Formatierung:
Das Project wurde mit Pint formatiert.

5. Funktionen

CRUD-Operationen

    Für User, Task und Project wurden jeweils Resource Controller angelegt.

Query-Opperationen

    Für spezielle Abfragen wurden Single Action Controller angelegt.
    GetAllExpiredTasks bspw.

Struktur

    Das Projekt hat eine organisierte Schichtenarchitektur:
        DTO
        Mapper
        Model
        Controller
        Service
        Repository und Interface
        Policy
        RequestKlassen


Beziehungen & Rollen

    Benutzer (User) – kann Aufgabe/n haben (Task)
    Aufgabe (Task) – kann zu einem Projekt gehören (Project)
    Projekt (Project) – kann viele Aufgaben (Task) haben
    Rollen:
        Standard-User: Kann nur eigene Tasks bearbeiten.
        Admin-User: Kann Aufgaben anderer Benutzer mit überfälligen Deadlines verwalten.
        Hierfür wurde ein Feld role der Tabelle Benutzer (User) hinzugefügt und in der App ein entsprechender Enum angelegt.

Deadline & Benachrichtigungen

    Aufgaben besitzen ein Feld deadline (hier wurde Date verwendet).
    Überfällige Aufgaben (deadline < now()) können mit dem Endpunkt /tasks/all/expired abgefragt werden.
    Ein Event-Listener prüft bei Aktualisierung eines Task, ob das deadline-Datum abgelaufen ist und der Status nicht done ist. Ist das der Fall, wird eine Benachrichtigung (Notification) ausgelöst (Datenbankeintrag).
    Hierfür wurde eine notifications Tabelle angelegt die von einem Frontend konsumiert werden könnte. Aus der Aufgabenstellung geht nicht hervor wie der Benutzer benachrichtigt werden soll.
    Desweiteren wurde eine schedule eingerichtet die nach Datumswechsel alle Tasks auf Überfälligkeit prüft und ggf. DB einträge erzeugt.
    Für Events wurde ein command eingerichtet:
        php artisan event:task-expired
        Hier kann nach migration und seeding ein event ausgelöst werden.

Middleware

    auth:sanctum: Authentifizierung über Sanctum.
    admin: Prüft, ob der User Admin ist.
    own.task: Prüft, ob der aktuelle Benutzer Besitzer der angefragten Task ist. (User)
    expired.task: Prüft, ob der Benutzer berechtigt ist, einen abgelaufenen Task zu bearbeiten. (Admin)

Validierung

Die Validierungsregeln werden in Request-Klassen (z.B. TaskStoreRequest, TaskUpdateRequest) umgesetzt. 
Wichtige Regeln:

    Titel:
        Erforderlich
        Max. 255 Zeichen

    Beschreibung:
        Erforderlich

    Status:
        Erlaubte Werte: todo, in_progress, done (Enum)

    Deadline:
        Muss ein gültiges Datum in der Zukunft sein (Date)

6. Tests

Das Projekt enthält PHPUnit-Tests. Um die Tests auszuführen:
    php artisan test

Feature-Tests prüfen die API-Endpunkte inkl. Authentifizierung. (ControllerTests, RepositoryTests)
Unit-Tests prüfen Services oder einzelne Logik-Bausteine. (EventTests, ListenerTests, PolicyTests, ServiceTests)

