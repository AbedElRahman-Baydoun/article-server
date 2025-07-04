<?php
abstract class Model {
    protected static mysqli $mysqli;
    protected static string $table;
    protected static string $primary_key = "id";

    public static function setConnection(mysqli $connection): void {
        static::$mysqli = $connection;
    }

    public static function find(int $id): ?static {
        $sql = sprintf("SELECT * FROM %s WHERE %s = ?", static::$table, static::$primary_key);
        $stmt = static::$mysqli->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? new static($result) : null;
    }
    public static function all(): array {
        $sql = sprintf("SELECT * FROM %s", static::$table);
        $stmt = static::$mysqli->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $objects = [];
        while ($row = $result->fetch_assoc()) {
            $objects[] = new static($row);
        }
        return $objects;
    }
    public static function create(array $data): ?static {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $types = str_repeat("s", count($data));
        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", static::$table, $columns, $placeholders);
        $stmt = static::$mysqli->prepare($sql);
        $stmt->bind_param($types, ...array_values($data));

        if ($stmt->execute()) {
            $data[static::$primary_key] = static::$mysqli->insert_id;
            return new static($data);
        }

        return null;
    }
    public function update(): bool {
        $data = $this->toDB();
        $pk = static::$primary_key;
        unset($data[$pk]);
        $columns = array_keys($data);
        $assignments = implode(", ", array_map(fn($col) => "$col = ?", $columns));
        $values = array_values($data);
        $values[] = $this->$pk;
        $types = str_repeat("s", count($values));
        $sql = sprintf("UPDATE %s SET %s WHERE %s = ?", static::$table, $assignments, $pk);
        $stmt = static::$mysqli->prepare($sql);
        $stmt->bind_param($types, ...$values);

        return $stmt->execute();
    }
    public function delete(): bool {
        $pk = static::$primary_key;
        $sql = sprintf("DELETE FROM %s WHERE %s = ?", static::$table, $pk);
        $stmt = static::$mysqli->prepare($sql);
        $stmt->bind_param("i", $this->$pk);
        return $stmt->execute();
    }
    abstract public function toDB(): array;
}