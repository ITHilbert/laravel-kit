# ITHilbert Laravel Kit

**Beschreibung**: Tools für das Programmieren mit Laravel.

## Dokumentation

Die ausführliche Dokumentation befindet sich im Ordner `docs/`:
- [Kontext & Zielsetzung](docs/00_README_Kontext.md)
- [Architekturübersicht](docs/01_Architekturübersicht.md)
- [Modulstruktur](docs/07_Modulstruktur.md)
- [API-Definitionen](docs/02_API-Definitionen.md)

## Installation

```bash
composer require ithilbert/laravel-kit
```

## Namespace
`ITHilbert\LaravelKit`

## Remote & MCP Integration

Das Laravel-Kit bringt gebündelte Tools mit, um das Cloud-Deployment und Remote-Debugging des verbundenen Live-Servers (insbesondere für Laravel Boost MCP) zu vereinfachen.

Trage zuerst in dem Projekt, welches dieses Kit nutzt, folgende Ziel-Parameter in die `.env` ein (Voraussetzung: Dein lokaler SSH-Key ist auf dem Zielserver hinterlegt):

```env
# MCP Live Server Dev Sync
LIVE_SERVER_HOST="123.123.123.12"
LIVE_SERVER_USER="root"
LIVE_SERVER_PATH="/var/www/meineapp"
LIVE_SERVER_PORT=22
```

Damit werden in deiner lokalen Umgebung automatisch folgende Features aktiviert:

### Interaktive Developer Commands
- `php artisan ssh:open` - Öffnet dir ein natives, interaktives SSH-Terminal das direkt in den konfigurierten Projektordner auf deinem Ziel-Linux einspringt.
- `php artisan ftp:open` - Öffnet dir blitzschnell eine interaktive SFTP-Sitzung am exakt richtigen Ablageort des Servers.

### AI-Agent Tools (MCP Server)
- **`RemoteExecuteTool`**: Verleiht deinem KI-Agenten die Macht, asynchrone Shell-Befehle (z.B. Optimierungen, Migrations) per sicherer `Process`-Facade auf dem Host auszuführen.
- **`RemoteSyncTool`**: Verleiht der KI einen gezielten Datei-Transfer Push (via natives `rsync`), um Modifikationen sofort remote live zu nehmen, ohne dass du den vollen Cloud-Deploy Button im Dashboard drücken musst!
