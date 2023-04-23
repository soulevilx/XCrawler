<?php

namespace App\Modules\Core\XClient\Response;

class JsonResponse extends AbstractBaseResponse
{
    public function getData(): ?array
    {
        if (! isset($this->data)) {
            $this->data = json_decode($this->raw, true);
        }

        return $this->data;
    }
}
