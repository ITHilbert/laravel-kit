export interface ColumnDefSource {
    key: string;
    sortable?: boolean;
    html?: boolean;
    defaultContent?: string;
    render?: (data: unknown, type: string, row: Record<string, unknown>) => string;
}

export function buildColumnDefs(columns: ColumnDefSource[], hasActions: boolean): unknown[];
