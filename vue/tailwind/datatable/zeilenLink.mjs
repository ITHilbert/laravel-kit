/**
 * Hilfen für den Zeilen-Link der DataTable-Komponente.
 * Reines ES-Modul ohne Abhängigkeiten (per `node --test` prüfbar).
 */

/**
 * Setzt die ID der Zeile in die Vorlage ein (jedes `:id`, URL-kodiert).
 * @returns {string|null} null ohne Vorlage oder ohne ID
 */
export function zeilenUrl(vorlage, daten) {
    const id = daten?.id;
    if (!vorlage || id === undefined || id === null || id === '') {
        return null;
    }
    return String(vorlage).split(':id').join(encodeURIComponent(String(id)));
}

/** Ein Klick auf Link, Button oder Formularelement (oder darin) ist kein Zeilenklick. */
export function klickIstZeilenKlick(ziel) {
    return !ziel?.closest('a, button, input, select, textarea, label');
}
