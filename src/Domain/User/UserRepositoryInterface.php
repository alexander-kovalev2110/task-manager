<?php

namespace App\Domain\User;

interface UserRepositoryInterface
{
    /**
     * Guaranteed to return the user or throw an exception
     */
    public function getById(int $id): User;

    /**
     * Used in login, registration and uniqueness checks
     */
    public function findByEmail(Email $email): ?User;

    /**
     * Checking existence is cheaper than loading Entity
     */
    public function existsByEmail(Email $email): bool;

    /**
     * Saving an object
     */
    public function save(User $user): void;

    /**
     * УRemoval (not always necessary, but better to plan for)
     */
    public function remove(User $user): void;
}