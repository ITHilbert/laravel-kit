# Spec boost-tools-request (#2470)
Risiko: normal — ändert nur die Signatur der Tool-Methode `handle()` zweier MCP-Tools; Logik, Schema und Konfiguration bleiben
Art: Bugfix (Fund corlog-online.de 28.11.: `RemoteExecuteTool::handle(): Argument #1 ($command) must be of type string, Laravel\Mcp\Request given`)
Status: freigegeben (Task #2470 `freigabe`, Modus umsetzung; Moderator selbst im automode)
Rückbau: entfällt

## Akzeptanzkriterien
- **AK1** Gegeben ein `Laravel\Mcp\Request` mit `command`, Wenn `RemoteExecuteTool::handle($request)` aufgerufen wird (wie Boost), Dann läuft der Befehl mit dem Wert aus dem Request (kein TypeError); mit fehlender Remote-Konfiguration kommt die Konfigurations-Fehlermeldung als `Response`.
- **AK2** Gegeben ein `Request` ohne `command` oder mit leerem `command`, Wenn `RemoteExecuteTool::handle()` aufgerufen wird, Dann kommt eine Fehlermeldung als `Response` und es wird kein Prozess gestartet.
- **AK3** Gegeben ein `Request` mit `files` (Array), Wenn `RemoteSyncTool::handle($request)` aufgerufen wird, Dann werden die Dateien aus dem Request verarbeitet (kein TypeError); ohne `files` oder mit leerem Array kommt die Fehlermeldung „Mindestens eine Datei …“ als `Response`.
- **AK4** Gegeben `BackupSeedersTool`, Wenn geprüft, Dann nimmt `handle()` bereits einen `Request` (unverändert); ein Test ruft alle drei Tools im Boost-Aufrufstil mit einem `Request` auf.

## Nicht-Ziele
Neue Funktionen der Tools, SSH-Härtung, Änderung der Schemas.

## Entscheidungsspielraum
Auslesen per `$request->get('command')` bzw. `$request->get('files', [])`, Testaufbau, Process-Fake.
