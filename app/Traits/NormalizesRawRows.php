<?php

namespace App\Traits;

trait NormalizesRawRows
{
    protected function normalizeRow($row)
    {
        if (!$row) {
            return null;
        }

        return (object) array_change_key_case((array) $row, CASE_LOWER);
    }

    protected function normalizeRows(array $rows)
    {
        return array_map(fn ($row) => $this->normalizeRow($row), $rows);
    }
}