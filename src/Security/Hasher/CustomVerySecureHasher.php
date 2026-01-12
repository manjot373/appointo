<?php
// src/Security/Hasher/CustomVerySecureHasher.php

namespace App\Security\Hasher;

use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class CustomVerySecureHasher implements PasswordHasherInterface
{
    use CheckPasswordLengthTrait;

    public function hash(string $plainPassword): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new InvalidPasswordException();
        }

        return $this->myHash($plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        if ('' === $plainPassword || $this->isPasswordTooLong($plainPassword)) {
            return false;
        }

        return hash_equals($hashedPassword, $this->myHash($plainPassword));
    }

    public function needsRehash(string $hashedPassword): bool
    {
        // No rehashing needed for this hasher
        return false;
    }
    public function myHash($plainPassword){
        $hashed = password_hash($plainPassword, PASSWORD_DEFAULT);

        return $hashed;

    }
}
