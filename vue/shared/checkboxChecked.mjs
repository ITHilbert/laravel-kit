/**
 * Gemeinsame Regel der Vue-Checkboxen (tailwind und bootstrap5): Ist das Häkchen gesetzt?
 *
 * checked gesetzt (nicht undefined): true bei true, 1 und den Strings '1', 'checked', 'true'
 * (Groß-/Kleinschreibung egal), sonst false – auch bei value 1.
 * checked nicht gesetzt: wie bisher value == 1 oder value === true.
 */
export function istAngehakt(checked, value) {
    if (checked !== undefined) {
        if (checked === true || checked === 1) {
            return true;
        }
        if (typeof checked === 'string') {
            return ['1', 'checked', 'true'].includes(checked.toLowerCase());
        }
        return false;
    }

    return value == 1 || value === true;
}
