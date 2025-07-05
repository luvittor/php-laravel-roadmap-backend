# Database Isolation and Column Handling

Our application expects a relational database that supports transactions. The default setups use SQLite for development and testing, but MySQL/PostgreSQL can also be used in production. A minimum of the standard **READ COMMITTED** isolation level is assumed so that each transaction sees committed rows from others.

## Unique columns

Columns are unique for each `year`, `month` and `user_id`. This is enforced at the database level by the `columns_year_month_user_unique` index.

When multiple requests attempt to create the same column concurrently, the unique constraint may be triggered. `ColumnService::firstOrCreate` handles this race condition transparently.

```php
try {
    return Column::firstOrCreate([
        'year'    => $year,
        'month'   => $month,
        'user_id' => $userId,
    ]);
} catch (\Illuminate\Database\QueryException $e) {
    if ($e->getCode() === '23000') {
        return Column::where([
            'year'    => $year,
            'month'   => $month,
            'user_id' => $userId,
        ])->first();
    }
    throw $e;
}
```

The service first tries to create the row. If a duplicate key violation occurs (SQL state `23000`), it performs a second query to return the previously inserted row instead of failing. This approach ensures concurrent requests always resolve to a single column record.
