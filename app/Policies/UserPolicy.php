<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
  public function delete(User $user)
  {
    return $user->name == 'a';
  }
  public function IsAdmin(User $user)
  {
    return $user->name == 'a';
  }
}
