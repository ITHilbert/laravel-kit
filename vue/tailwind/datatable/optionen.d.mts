export interface OptionenEingabe {
    columns: unknown[];
    hasActions: boolean;
    pageLength: number;
    language?: Record<string, unknown> | null;
    datatableOptions?: Record<string, unknown> | null;
    ajaxUrl?: string;
    rows?: unknown[];
    aktionsSpalte?: string;
}

export function baueOptionen(
    eingabe: OptionenEingabe,
    defaultLanguage: Record<string, unknown>,
): Record<string, unknown>;
