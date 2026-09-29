<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class User extends Model
{
    public static function findByEmail(string $email): ?array
    {
        return self::queryOne(
            "SELECT * FROM users WHERE email = :email AND status != 'banned' LIMIT 1",
            ['email' => strtolower(trim($email))]
        );
    }

    public static function findById(int $id): ?array
    {
        return self::queryOne(
            "SELECT id, name, email, phone, role, profile_image, gender, address_line, area_locality, city, state, pincode, landmark, status, created_at, updated_at 
             FROM users WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public static function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM users WHERE email = :email";
        $params = ['email' => strtolower(trim($email))];
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        $sql .= " LIMIT 1";
        return self::queryOne($sql, $params) !== null;
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO users (name, email, phone, password_hash, role, gender, address_line, area_locality, city, state, pincode, landmark, status)
                VALUES (:name, :email, :phone, :password_hash, :role, :gender, :address_line, :area_locality, :city, :state, :pincode, :landmark, 'active')";
        
        self::execute($sql, [
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'phone' => $data['phone'],
            'password_hash' => $data['password_hash'],
            'role' => $data['role'] ?? 'customer',
            'gender' => $data['gender'] ?? null,
            'address_line' => $data['address_line'] ?? null,
            'area_locality' => $data['area_locality'] ?? null,
            'city' => $data['city'] ?? 'Mumbai',
            'state' => $data['state'] ?? 'Maharashtra',
            'pincode' => $data['pincode'] ?? null,
            'landmark' => $data['landmark'] ?? null,
        ]);

        return self::lastInsertId();
    }

    public static function updateProfile(int $id, array $data): int
    {
        $sql = "UPDATE users SET 
                    name = :name,
                    phone = :phone,
                    gender = :gender,
                    address_line = :address_line,
                    area_locality = :area_locality,
                    city = :city,
                    state = :state,
                    pincode = :pincode,
                    landmark = :landmark
                WHERE id = :id";

        return self::execute($sql, [
            'id' => $id,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'gender' => $data['gender'] ?? null,
            'address_line' => $data['address_line'] ?? null,
            'area_locality' => $data['area_locality'] ?? null,
            'city' => $data['city'] ?? 'Mumbai',
            'state' => $data['state'] ?? 'Maharashtra',
            'pincode' => $data['pincode'] ?? null,
            'landmark' => $data['landmark'] ?? null,
        ]);
    }

    public static function updatePassword(int $id, string $passwordHash): int
    {
        return self::execute(
            "UPDATE users SET password_hash = :hash WHERE id = :id",
            ['id' => $id, 'hash' => $passwordHash]
        );
    }
}
