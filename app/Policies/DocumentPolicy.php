<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_documents');
    }

    public function view(User $user, Document $document): bool
    {
        return $user->can('view_documents');
    }

    public function create(User $user): bool
    {
        return $user->can('upload_documents');
    }

    public function download(User $user, Document $document): bool
    {
        return $user->can('download_documents');
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->can('delete_documents');
    }
}
