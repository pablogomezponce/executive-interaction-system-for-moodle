<?php


namespace SallePW\Model;


interface ProfileRepository
{
    public function save(User $user);
    public function login(string $password, string $id);
    public function update(User $u);
    public function deleteAccount(string $id);
}