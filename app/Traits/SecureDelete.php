<?php

namespace App\Traits;

trait SecureDelete
{
    public function secureDelete()
    {
        $hasRelation = false;

        foreach($this->relationships as $relation) {
            if ($this->$relation()->count()) {
                $hasRelation = true;
                break;
            }
        }

        if ($hasRelation) {
            return true;
        } else {
            $this->delete();
            return false;
        }
    }

    public function secureDeleteState()
    {
        $hasRelation = false;

        if ($this->estado_id != 0) {
            return true;
        } else {
            $this->delete();
            return false;
        }
    }
}