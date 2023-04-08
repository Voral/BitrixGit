<?php
/** @bxnolanginspection */

namespace Vasoft\Git\Infrastructure\Settings\Fields;

class TextField extends Field
{
    protected int $size = 20;

    public function renderInput(): string
    {
        $value = htmlspecialchars(($this->getter)());
        return <<<HTML
<input type="text" size="$this->size" maxlength="255" value="$value" name="$this->code"> 
HTML;
    }

    public function configureSize(): self
    {
        return $this;
    }
}