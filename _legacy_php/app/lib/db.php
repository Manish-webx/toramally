<?php
/**
 * Database access. Every query uses prepared statements, which is what
 * protects the site from SQL injection. Never build SQL by joining strings
 * with user input; pass values in the $params array instead.
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $c = $GLOBALS['config']['db'];
        $dsn = 'mysql:host=' . $c['host'] . ';dbname=' . $c['name'] . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, $c['user'], $c['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('DB connection failed: ' . $e->getMessage());
            http_response_code(503);
            exit('The site is resting for a moment. Please try again shortly.');
        }
    }
    return $pdo;
}

/** Run a query and return all rows. */
function db_all(string $sql, array $params = []): array
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

/** Run a query and return the first row, or null. */
function db_one(string $sql, array $params = []): ?array
{
    $st = db()->prepare($sql);
    $st->execute($params);
    $row = $st->fetch();
    return $row === false ? null : $row;
}

/** Run a query and return a single value. */
function db_val(string $sql, array $params = [])
{
    $st = db()->prepare($sql);
    $st->execute($params);
    $v = $st->fetchColumn();
    return $v === false ? null : $v;
}

/** Run an INSERT/UPDATE/DELETE. Returns affected rows. */
function db_exec(string $sql, array $params = []): int
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->rowCount();
}

/** Insert a row from an associative array. Returns the new id. */
function db_insert(string $table, array $data): int
{
    $cols = array_keys($data);
    foreach ($cols as $c) {
        if (!preg_match('/^[a-z0-9_]+$/', $c)) throw new InvalidArgumentException('Bad column');
    }
    $sql = 'INSERT INTO `' . $table . '` (`' . implode('`,`', $cols) . '`) VALUES (' . rtrim(str_repeat('?,', count($cols)), ',') . ')';
    db_exec($sql, array_values($data));
    return (int) db()->lastInsertId();
}
