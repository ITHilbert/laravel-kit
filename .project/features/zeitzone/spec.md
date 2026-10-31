# Spec: MyDateTime ohne globale Zeitzone (Dashboard #92)

## Ausgangslage
`src/Helpers/MyDateTime.php` ruft beim Laden der Datei `date_default_timezone_set("Europe/Berlin")` auf. Sobald die Klasse geladen wird, gilt mitten im Request Berlin statt `config('app.timezone')` der App. 13 von 14 Projekten mit laravel-kit stehen auf `UTC` (Stand 2026-10-31). Die Zeitzone hing also davon ab, ob ein Request die Klasse geladen hat.
`HForm::date()` importiert die Klasse unter dem falschen Namespace (`ithilbert\LaravelKit\MyDateTime`) und wirft bei gesetztem Wert `Class "…\MyDateTime" not found`.

## Akzeptanzkriterien
- **AK1 — Zeitzone der App bleibt:** Gegeben `config('app.timezone')` = `UTC` (ebenso `America/New_York`) und die PHP-Zeitzone entsprechend gesetzt — Wenn `MyDateTime` geladen, ein Objekt erzeugt (`new MyDateTime('now')`, `new MyDateTime('2026-01-01 10:00:00')`) oder `HForm::date()` mit Wert aufgerufen wird — Dann ist `date_default_timezone_get()` danach unverändert gleich `config('app.timezone')`.
- **AK2 — Rechnen in der Zeitzone der App:** Gegeben `config('app.timezone')` = `UTC` — Wenn `new MyDateTime('2026-01-01 00:00:00')` erzeugt wird — Dann liefert `getTimestamp()` `1767225600`. Bei `Europe/Berlin` liefert derselbe Aufruf `1767222000`. `getDateTimeDB()` gibt in beiden Fällen `2026-01-01 00:00:00` zurück.
- **AK3 — HForm::date mit Wert:** Gegeben ein Wert `2026-03-15 10:00:00` — Wenn `HForm::date('lieferdatum', '2026-03-15 10:00:00')` aufgerufen wird — Dann enthält die Ausgabe `value="15.03.2026"` und `name="lieferdatum"`, ohne Exception.

## Randfälle
- Leerer Wert in `HForm::date()` → Feld mit `value=""` wie bisher, MyDateTime wird nicht erzeugt.
- Die bestehenden Charakterisierungstests in `tests/Unit/MyDateTimeTest.php` beschreiben das Verhalten unter der Testzeitzone; sie bleiben grün (Wochentage, Kalenderwochen, Addieren, Differenzen).

## Nicht-Ziele
- `setlocale(LC_TIME, "de_DE.utf8")` beim Laden der Datei (ebenfalls global, eigener Punkt).
- Umbau von MyDateTime auf Carbon.
- Rollout: Projekte, die Berlin-Zeit brauchen, setzen `APP_TIMEZONE`/`config('app.timezone')` selbst — Entscheidung je Projekt durch den User.
- Release/Tag.

## Entscheidungsspielraum
- Keine neuen Pakete.
- Ob MyDateTime die Zeitzone gar nicht mehr anfasst oder explizit `config('app.timezone')` an `DateTime` übergibt, entscheidet der Coder — Hauptsache AK1 und AK2.
- Spec selbst freigegeben (Nachtschicht 2026-10-31); AK3 ergänzt, weil `HForm::date()` der einzige Aufrufer im Kit ist und AK1 dort sonst nicht prüfbar wäre.
