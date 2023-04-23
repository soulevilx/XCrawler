<?php

namespace App\Modules\Core\XClient\Response;

class BaseResponse extends AbstractBaseResponse
{
    public function getData(): mixed
    {
        return $this->raw;
    }
}
