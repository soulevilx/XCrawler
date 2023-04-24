<?php

namespace App\Modules\Core\XClient\Response;

class FlickrResponse extends JsonResponse
{
    public function isSuccess(): bool
    {
        return parent::isSuccess()
            && isset($this->getData()['stat'])
            && $this->getData()['stat'] === 'ok';
    }

    public function getData(): array
    {
        $this->data = $this->cleanTextNodes(parent::getData());

        return $this->data ?? [];
    }

    private function cleanTextNodes($arr)
    {
        if (! is_array($arr)) {
            return $arr;
        } elseif (count($arr) == 0) {
            return $arr;
        } elseif (count($arr) == 1 && array_key_exists('_content', $arr)) {
            return $arr['_content'];
        } else {
            foreach ($arr as $key => $element) {
                $arr[$key] = $this->cleanTextNodes($element);
            }

            return $arr;
        }
    }
}
